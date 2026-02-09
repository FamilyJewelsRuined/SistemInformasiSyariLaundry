<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/admin');
});

Route::group(['middleware' => ['web', 'auth:filament']], function () {
    Route::get('/orders/{order}/mark-ready', function (\App\Models\Order $order) {
        $order->update(['status' => 'ready']);
        return redirect()->back();
    })->name('filament.resources.orders.mark_ready');

    Route::get('/orders/{order}/mark-completed', function (\App\Models\Order $order) {
        $order->update(['status' => 'completed', 'completion_date' => now()]);
        return redirect()->back();
    })->name('filament.resources.orders.mark_completed');

    Route::get('/orders/{order}/mark-paid', function (\App\Models\Order $order) {
        // Trigger model observer saving logic by updating paid_amount
        $order->update(['paid_amount' => $order->total_amount]);
        return redirect()->back();
    })->name('filament.resources.orders.mark_paid');

    Route::get('/orders/print', function (Illuminate\Http\Request $request) {
        $start = $request->query('start');
        $end = $request->query('end');
        
        $orders = \App\Models\Order::query()
            ->whereDate('order_date', '>=', $start)
            ->whereDate('order_date', '<=', $end)
            ->with('customer')
            ->get();

        return view('print.orders', compact('orders', 'start', 'end'));
    })->name('orders.print');
    Route::get('/orders/{order}/receipt', function (\App\Models\Order $order) {
        $order->load(['customer', 'serviceType', 'serviceDuration', 'fragrance', 'orderItems.unitType', 'discount']);
        return view('print.receipt', compact('order'));
    })->name('orders.receipt');
});
