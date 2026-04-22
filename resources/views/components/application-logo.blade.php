{{--
    USAGE:
        <x-application-logo class="text-2xl font-bold" />
        
    PROPS:
        (None) - Accepts all standard HTML attributes via $attributes
--}}
<h1 {{ $attributes }}>
    {{ config('app.name', 'Cruz') }}</h1>
