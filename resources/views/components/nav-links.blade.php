@props(['view'])

@auth
    <x-responsive-nav-link :view="$view" :href="route('pos')" :active="request()->routeIs('pos')">
        {{ __('POS') }}
    </x-responsive-nav-link>
@endauth

@auth
    <x-responsive-nav-link :view="$view" :href="route('sales')" :active="request()->routeIs('sales')">
        {{ __('Sales') }}
    </x-responsive-nav-link>
@endauth

@canany(['admin', 'owner'])
    <x-responsive-nav-link :view="$view" :href="route('inventory')" :active="request()->routeIs('inventory')">
        {{ __('Inventory') }}
    </x-responsive-nav-link>
@endcanany

@can(['owner'])
    <x-responsive-nav-link :view="$view" :href="route('employees')" :active="request()->routeIs('employees')">
        {{ __('Employees') }}
    </x-responsive-nav-link>
@endcan
