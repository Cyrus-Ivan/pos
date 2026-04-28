@props(['id' => null])

<div x-data="{
    item: null,
    confirmText: '',
    get canDelete() {
        return this.confirmText === 'confirm';
    },
    init() {
        window.addEventListener('close-modal', event => {
            if (event.detail.id === '{{ $id }}') {
                this.item = null;
                this.confirmText = '';
                $refs.form.reset();
            }
        });
    }
}"
    @open-modal.window="
    if ($event.detail.id === '{{ $id }}') {
        item = $event.detail.item;
        confirmText = '';
        $refs.form.reset();
    }
">
    <x-modal id="{{ $id }}" title="Confirm Deletion" :reset="true">
        <form x-ref="form" action="{{ route('inventory.destroy') }}" method="POST" id="item-delete-form">
            @csrf
            @method('DELETE')

            <input type="hidden" name="id" x-bind:value="item?.id">

            <div class="space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Are you sure you want to delete <span class="font-semibold text-gray-800 dark:text-gray-200"
                        x-text="item?.name"></span>?
                    This action cannot be undone. To continue, type <strong>confirm</strong> below:
                </p>

                <div>
                    <x-input-label for="confirm" :value="__('Type confirm to delete')" />
                    <x-text-input id="confirm" class="block mt-1 w-full" name="confirm" required x-model="confirmText"
                        autocomplete="off" />
                </div>
            </div>

            <x-slot:footer>
                <x-borderless-button type="submit"
                    @click="$dispatch('close-modal', { id: '{{ $id }}' })">Cancel</x-borderless-button>
                <x-danger-button form="item-delete-form-{{ $id }}" x-bind:disabled="!canDelete">
                    Delete
                </x-danger-button>
            </x-slot:footer>
        </form>
    </x-modal>
</div>
