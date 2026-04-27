{{--
    USAGE:
        
    PROPS:
        
--}}

<tr
    {{ $attributes->merge(['class' => 'flex flex-col md:table-row mb-3 md:m-3 bg-gray-100/50 dark:bg-gray-700/50 hover:bg-gray-200/50 dark:hover:bg-gray-600/50 transition-colors']) }}>
    {{ $slot }}
</tr>
