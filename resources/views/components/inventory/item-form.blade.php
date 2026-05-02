@props(['id' => null, 'item' => null, 'branches' => null])

<div x-data="{
    item: null,
    isEdit: false,
    init() {
        // Reset item when modal closes
        window.addEventListener('close-modal', event => {
            if (event.detail.id === '{{ $id }}') {
                this.item = null;
                this.isEdit = false;
                $refs.form.reset();
            }
        });
    }
}"
    @open-modal.window="
    if ($event.detail.id === '{{ $id }}') {
        const titleEl = document.getElementById('modal-title-{{ $id }}');
        if ($event.detail.item) {
            item = $event.detail.item;
            isEdit = true;
            if (titleEl) titleEl.innerText = 'Update Item';
        } else {
            item = null;
            isEdit = false;
            if (titleEl) titleEl.innerText = 'Add New Item';
            $refs.form.reset();
        }
    }
">
    <x-modal id="{{ $id }}" title="Item" :reset="true">
        <form x-ref="form" action="{{ route('inventory.create') }}" method="POST" id="item-form-{{ $id }}"
            @keydown.enter="$event.preventDefault()">
            @csrf

            <template x-if="isEdit">
                <div>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="id" x-bind:value="item?.id">
                </div>
            </template>

            <div class="space-y-4">
                {{-- SKU --}}
                <div>
                    <x-input-label for="sku" :value="__('SKU')" />
                    <x-text-input id="sku" class="block mt-1 w-full" name="sku" required autofocus
                        x-bind:value="item?.sku || ''" x-bind:disabled="isEdit"
                        x-bind:class="{ 'pointer-events-none !cursor-default bg-slate-50 dark:bg-slate-800 text-slate-500': isEdit }" />
                </div>

                {{-- Item Name --}}
                <div>
                    <x-input-label for="name" :value="__('Item Name')" />
                    <x-text-input id="name" class="block mt-1 w-full" name="name" required
                        x-bind:value="item?.name || ''" x-bind:disabled="isEdit"
                        x-bind:class="{ 'pointer-events-none !cursor-default bg-slate-50 dark:bg-slate-800 text-slate-500': isEdit }" />
                </div>

                {{-- Item Cost & Selling Price (Side by Side) --}}
                <div class="flex gap-4">
                    <div class="flex-1">
                        <x-input-label for="item-cost" :value="__('Item Cost')" />
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-slate-500 dark:text-slate-400 sm:text-sm">₱</span>
                            </div>
                            <x-text-input id="item-cost" type="number" step="0.01" class="block w-full pl-8"
                                name="item-cost" required x-bind:value="item?.cost || ''" />
                        </div>
                    </div>

                    <div class="flex-1">
                        <x-input-label for="selling-price" :value="__('Selling Price')" />
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-slate-500 dark:text-slate-400 sm:text-sm">₱</span>
                            </div>
                            <x-text-input id="selling-price" type="number" step="0.01" class="block w-full pl-8"
                                name="selling-price" required x-bind:value="item?.selling_price || ''" />
                        </div>
                    </div>
                </div>

                {{-- Branches Stock (Scrollable List) --}}
                <div>
                    <x-input-label :value="__('Stocks per Branch')" class="mb-2" />

                    <div
                        class="max-h-60 overflow-y-auto border border-slate-300 dark:border-slate-700 rounded-md p-3 space-y-3 bg-slate-50 dark:bg-slate-900/50">
                        @foreach ($branches as $branch)
                            <div x-data="{
                                mode: 'add',
                                currentStock: 0,
                                inputValue: 0,
                                init() {
                                    $watch('item', (newItem) => {
                                        this.mode = 'add';
                                        this.inputValue = 0;
                                        if (newItem) {
                                            this.currentStock = newItem['{{ $branch->id }}'] || 0;
                                        } else {
                                            this.currentStock = 0;
                                        }
                                    });
                                    $watch('mode', (newMode) => {
                                        if (newMode === 'add') {
                                            this.inputValue = 0;
                                        } else {
                                            this.inputValue = this.currentStock;
                                        }
                                    });
                                    // Initially set the properties when component mounts
                                    this.mode = 'add';
                                    this.inputValue = 0;
                                }
                            }"
                                class="flex items-center justify-between gap-4 bg-white dark:bg-slate-800 p-2 rounded-md shadow-sm border border-slate-100 dark:border-slate-700">
                                {{-- Left: Branch Name & Current Stock in Edit --}}
                                <div class="flex-1 flex flex-col">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                        {{ $branch->name }}
                                    </span>
                                    <template x-if="isEdit">
                                        <span class="text-xs text-slate-500">Current: <span
                                                x-text="currentStock"></span></span>
                                    </template>
                                </div>

                                {{-- Middle: Toggle (Only in Edit Mode) --}}
                                <template x-if="isEdit">
                                    <div
                                        class="flex items-center gap-1 bg-slate-100 dark:bg-slate-700 p-1 rounded-md text-xs">
                                        <input type="hidden" name="stock_mode[{{ $branch->id }}]"
                                            x-bind:value="mode">
                                        <button type="button" @click="mode = 'add'"
                                            :class="mode === 'add' ?
                                                'bg-white dark:bg-slate-600 shadow-sm text-blue-600 dark:text-blue-400 font-medium' :
                                                'text-slate-500 hover:text-slate-700 dark:hover:text-slate-500/50'"
                                            class="px-2 py-1 rounded transition-colors duration-150">
                                            Add
                                        </button>
                                        <button type="button" @click="mode = 'set'"
                                            :class="mode === 'set' ?
                                                'bg-white dark:bg-slate-600 shadow-sm text-blue-600 dark:text-blue-400 font-medium' :
                                                'text-slate-500 hover:text-slate-700 dark:hover:text-slate-500/50'"
                                            class="px-2 py-1 rounded transition-colors duration-150">
                                            Change
                                        </button>
                                    </div>
                                </template>

                                {{-- Right: Stock Input --}}
                                <div class="w-32">
                                    <x-text-input type="number" class="block w-full text-center" required
                                        name="stocks[{{ $branch->id }}]" x-model="inputValue" value="0" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <x-slot:footer>
                <x-borderless-button type="button"
                    @click="$dispatch('close-modal', { id: '{{ $id }}' })">Cancel</x-borderless-button>
                <x-primary-button form="item-form-{{ $id }}">
                    <span x-text="isEdit ? 'Update' : 'Confirm'"></span>
                </x-primary-button>
            </x-slot:footer>
        </form>
    </x-modal>
</div>
