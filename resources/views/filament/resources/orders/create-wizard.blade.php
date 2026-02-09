<div>
    <x-filament::app-header :title="$title" :breadcrumbs="static::getBreadcrumbs()" />

    <x-filament::app-content>
        <div class="space-y-6">
            <!-- Wizard Progress -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    @foreach(['Pelanggan', 'Detail', 'Item', 'Keuangan', 'Struk'] as $index => $label)
                        <span class="{{ $step === ($index + 1) ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500' }}
                                     whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            {{ $label }}
                        </span>
                    @endforeach
                </nav>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <!-- Step 1: Customer Selection -->
                @if ($step === 1)
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Pilih Pelanggan</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pelanggan</label>
                            <select wire:model="data.customer_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                <option value="">-- Pilih Pelanggan --</option>
                                @foreach($customers as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('data.customer_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endif

                <!-- Step 2: Order Details (Fragrance -> Type -> Duration/Weight) -->
                @if ($step === 2)
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Detail Pesanan</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pewangi</label>
                            <select wire:model="data.fragrance_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                 <option value="">-- Pilih Pewangi --</option>
                                 @foreach($fragrances as $id => $name)
                                     <option value="{{ $id }}">{{ $name }}</option>
                                 @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipe Pesanan</label>
                            <select wire:model="data.order_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                <option value="kiloan">Kiloan</option>
                                <option value="satuan">Satuan</option>
                            </select>
                        </div>

                        @if($this->data['order_type'] === 'kiloan')
                            <div class="border-t pt-4 mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Durasi Layanan</label>
                                    <select wire:model="data.service_duration_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                        <option value="">-- Pilih Durasi --</option>
                                        @foreach($durations as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tipe Layanan</label>
                                    <select wire:model="data.service_type_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                        <option value="">-- Pilih Tipe Layanan --</option>
                                        @foreach($serviceTypes as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Berat (kg)</label>
                                    <input type="number" step="0.01" wire:model.lazy="data.weight" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        @else
                            <div class="mt-4 p-4 bg-gray-50 rounded text-center">
                                <p class="text-sm text-gray-500">Item satuan akan ditambahkan di langkah berikutnya.</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Step 3: Items & Review -->
                @if ($step === 3)
                    <div class="space-y-6">
                         <h3 class="text-lg font-medium leading-6 text-gray-900">
                            Item & Review
                         </h3>

                         <!-- Kiloan Details Summary -->
                         @if($this->data['order_type'] === 'kiloan')
                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-md">
                                <h4 class="text-md font-bold text-blue-900 mb-2">Detail Kiloan</h4>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <p><strong>Berat:</strong> {{ $data['weight'] ?? 0 }} kg</p>
                                    <p><strong>Harga/kg:</strong> Rp {{ number_format($this->pricePerKg, 0, ',', '.') }}</p>
                                    <p class="col-span-2 text-lg font-bold text-blue-800 mt-2">Subtotal Kiloan: Rp {{ number_format($this->pricePerKg * ($data['weight'] ?? 0), 0, ',', '.') }}</p>
                                </div>
                            </div>
                         @endif

                         <!-- Satuan Items Section (Available for ALL types) -->
                         <div class="p-4 border rounded bg-gray-50 space-y-4">
                            <h4 class="text-md font-bold text-gray-900 border-b pb-2">
                                {{ $this->data['order_type'] === 'kiloan' ? 'Item Satuan Tambahan (Opsional)' : 'Item Satuan' }}
                            </h4>
                            
                            <!-- Add Item Form -->
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500">Tipe Unit</label>
                                    <select wire:model="newItem.unit_type_id" class="block w-full text-sm border-gray-300 rounded">
                                        <option value="">Pilih...</option>
                                        @foreach($unitTypes as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">Jumlah</label>
                                    <input type="number" wire:model="newItem.quantity" class="block w-full text-sm border-gray-300 rounded">
                                </div>
                                <div class="flex items-end">
                                    <button wire:click="addItem" class="bg-primary-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                        Tambah Item
                                    </button>
                                </div>
                            </div>
                        
                            <!-- List of Added Items -->
                            @if(count($orderItems) > 0)
                                <div class="mt-4">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                                <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                                <th class="px-2 py-2"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($orderItems as $idx => $item)
                                                <tr>
                                                    <td class="px-2 py-2 text-sm">{{ $item['name'] }} (x{{ $item['quantity'] }})</td>
                                                    <td class="px-2 py-2 text-sm text-right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                                    <td class="px-2 py-2 text-right">
                                                        <button wire:click="removeItem({{ $idx }})" class="text-red-500 hover:text-red-700 font-bold">x</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-sm text-gray-400 italic mt-2">Belum ada item satuan.</p>
                            @endif
                        </div>
                        
                        <div class="text-right">
                            <p class="text-xl font-bold">Estimasi Total: Rp {{ number_format($this->subtotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Step 4: Financials -->
                @if ($step === 4)
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Keuangan & Pembayaran</h3>

                         <div>
                            <label class="block text-sm font-medium text-gray-700">Diskon</label>
                            <select wire:model="data.discount_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                <option value="">-- Pilih Diskon --</option>
                                @foreach($discounts as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="p-4 bg-gray-100 rounded text-right space-y-1">
                            <p class="text-sm">Subtotal: Rp {{ number_format($this->subtotal, 0, ',', '.') }}</p>
                            <p class="text-sm text-green-600">Diskon: - Rp {{ number_format($this->discountAmount, 0, ',', '.') }}</p>
                            <p class="text-sm text-red-600">Biaya Tambahan: + Rp {{ number_format($this->surchargeAmount, 0, ',', '.') }}</p>
                            <p class="text-xl font-bold text-gray-900">Total: Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status Pembayaran</label>
                            <div class="mt-2 space-x-4">
                                <button type="button" wire:click="$set('data.payment_status', 'unpaid')" class="inline-flex items-center px-4 py-2 border {{ $data['payment_status'] === 'unpaid' ? 'border-primary-500 ring-2 ring-primary-500' : 'border-gray-300' }} rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    Bayar Nanti (Belum Lunas)
                                </button>
                                <button type="button" wire:click="$set('data.payment_status', 'paid')" class="inline-flex items-center px-4 py-2 border {{ $data['payment_status'] === 'paid' ? 'border-primary-500 ring-2 ring-primary-500' : 'border-gray-300' }} rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    Bayar Sekarang (Lunas)
                                </button>
                            </div>
                            @if($data['payment_status'] === 'paid')
                                <div class="mt-2">
                                    <label class="block text-sm font-medium text-gray-700">Jumlah Bayar</label>
                                    <input type="number" wire:model.defer="data.paid_amount" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            @endif
                        </div>
                        
                        <div>
                             <label class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                             <textarea wire:model.defer="data.notes" rows="3" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                        </div>

                    </div>
                @endif

                <!-- Step 5: Receipt Preview -->
                @if ($step === 5)
                    <div class="flex flex-col items-center justify-center space-y-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 no-print">Pratinjau Struk</h3>
                        
                        <!-- Receipt Container -->
                        <div id="receipt-area" class="bg-white border p-4 shadow-sm w-[380px] text-xs font-mono leading-tight">
                            <div class="text-center mb-4">
                                <h2 class="text-base font-bold uppercase">Laundry App</h2>
                                <p>Jl. Contoh Laundry No. 123</p>
                                <p>Telp: 0812-3456-7890</p>
                            </div>
                            
                            <div class="mb-2 border-b border-dashed pb-2">
                                <p>Tanggal: {{ now()->format('d/m/Y H:i') }}</p>
                                <p>Pelanggan: {{ $customers[$data['customer_id']] ?? '-' }}</p>
                                <p>Tipe: {{ ucfirst($data['order_type']) }}</p>
                            </div>

                            <div class="mb-2 border-b border-dashed pb-2">
                                @if($data['order_type'] === 'kiloan')
                                    <div class="mb-1">
                                        <p class="font-bold">{{ $serviceTypes[$data['service_type_id']] ?? 'Service' }}</p>
                                        <div class="flex justify-between">
                                            <span>{{ number_format((float)($data['weight'] ?? 0), 2) }} kg x {{ number_format($this->pricePerKg, 0) }}</span>
                                            <span>{{ number_format($this->pricePerKg * (float)($data['weight'] ?? 0), 0) }}</span>
                                        </div>
                                        @if(!empty($data['service_duration_id']))
                                            <p class="italic text-[10px]">+ {{ $durations[$data['service_duration_id']] ?? '' }}</p>
                                        @endif
                                        @if(!empty($data['fragrance_id']))
                                            <p class="italic text-[10px]">+ {{ $fragrances[$data['fragrance_id']] ?? '' }}</p>
                                        @endif
                                    </div>
                                @endif

                                @foreach($orderItems as $item)
                                    <div class="mb-1 flex justify-between">
                                        <span>{{ $item['name'] }} x{{ $item['quantity'] }}</span>
                                        <span>{{ number_format($item['subtotal'], 0) }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-2 border-b border-dashed pb-2">
                                <div class="flex justify-between font-bold">
                                    <span>Subtotal</span>
                                    <span>{{ number_format($this->subtotal, 0) }}</span>
                                </div>
                                @if($this->surchargeAmount > 0)
                                    <div class="flex justify-between text-[10px]">
                                        <span>Biaya Tambahan</span>
                                        <span>{{ number_format($this->surchargeAmount, 0) }}</span>
                                    </div>
                                @endif
                                @if($this->discountAmount > 0)
                                    <div class="flex justify-between text-[10px]">
                                        <span>Diskon</span>
                                        <span>-{{ number_format($this->discountAmount, 0) }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between font-bold text-sm mt-1">
                                    <span>TOTAL</span>
                                    <span>{{ number_format($this->grandTotal, 0) }}</span>
                                </div>
                            </div>

                            <div class="text-center mb-4">
                                <p>Status: <span class="uppercase font-bold">{{ $data['payment_status'] === 'paid' ? 'LUNAS' : ($data['payment_status'] === 'unpaid' ? 'BELUM LUNAS' : $data['payment_status']) }}</span></p>
                                @if($data['payment_status'] === 'paid')
                                    <p>Dibayar: {{ number_format($data['paid_amount'] ?: $this->grandTotal, 0) }}</p>
                                @endif
                            </div>

                            <div class="text-center text-[10px]">
                                <p>Terima kasih atas kepercayaan Anda!</p>
                            </div>
                        </div>

                        <!-- Print Button -->
                         <button onclick="window.print()" type="button" class="no-print inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Struk
                        </button>
                    </div>
                    
                    <style>
                        @media print {
                            body * {
                                visibility: hidden;
                            }
                            #receipt-area, #receipt-area * {
                                visibility: visible;
                            }
                            #receipt-area {
                                position: absolute;
                                left: 0;
                                top: 0;
                                width: 100%; /* or fixed 80mm */
                                margin: 0;
                                padding: 0;
                                box-shadow: none;
                                border: none;
                            }
                            /* Filament layout reset */
                            .filament-app-layout, header, nav, aside {
                                display: none !important;
                            }
                        }
                    </style>
                @endif

                <!-- Navigation Buttons -->
                <div class="mt-8 flex justify-between border-t pt-4 no-print">
                    <div>
                        @if ($step > 1)
                            <x-filament::button color="white" wire:click="previousStep">
                                Kembali
                            </x-filament::button>
                        @endif
                    </div>

                    <div>
                        @if ($step < 5)
                            <x-filament::button color="primary" wire:click="nextStep">
                                Lanjut
                            </x-filament::button>
                        @else
                            <x-filament::button color="primary" type="submit" wire:click="create">
                                Konfirmasi & Buat Pesanan
                            </x-filament::button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-filament::app-content>
</div>
