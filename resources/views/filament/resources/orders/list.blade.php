<div>
    <x-filament::app-header :title="$title">
        <x-slot name="actions">
            @if ($this->canCreate())
                <x-filament::button
                    color="primary"
                    :href="static::getResource()::generateUrl($createRoute)"
                >
                    {{ __(static::$createButtonLabel) }}
                </x-filament::button>
            @endif
        </x-slot>
    </x-filament::app-header>

    <x-filament::app-content class="space-y-4">
        <!-- Date Filter & Actions -->
        <div class="bg-white p-4 rounded-lg shadow sm:flex sm:items-end sm:space-x-4 space-y-4 sm:space-y-0 relative z-10">
            <div class="w-full sm:w-auto">
                <label class="block text-sm font-medium text-gray-700">Tanggal Awal</label>
                <input type="date" wire:model="startDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>
            
            <div class="w-full sm:w-auto">
                <label class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                <input type="date" wire:model="endDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>

            <div class="flex space-x-2">
                @if($startDate && $endDate)
                    <button wire:click="export" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Ekspor Excel
                    </button>
                    <button wire:click="print" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Cetak
                    </button>
                @else
                   <div class="flex items-center text-sm text-gray-500 italic px-2">
                       Pilih tanggal untuk opsi lainnya.
                   </div>
                @endif
            </div>
            
            @if(!($startDate && $endDate))
             <!-- Warning Message if user tries to act (though buttons hidden, good to have visual cue) -->
            @endif
        </div>

        <div class="items-center justify-between space-y-4 sm:flex sm:space-y-0">
            <div>
                @if ($this->canDelete())
                    <x-tables::delete-selected :disabled="! $this->canDeleteSelected()" />
                @endif
            </div>

            <x-tables::filter :table="$this->getTable()" />
        </div>

        <x-tables::table
            :records="$records"
            :selected="$selected"
            :sort-column="$sortColumn"
            :sort-direction="$sortDirection"
            :table="$this->getTable()"
        />

        @if ($this->hasPagination())
            <x-tables::pagination.paginator :paginator="$records" />
        @endif
    </x-filament::app-content>
</div>
