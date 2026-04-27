@props(['columnName' => ''])

<td {{ $attributes }}>
    @if ($columnName)
        <span class="md:hidden">{{ $columnName }}:</span>
    @endif
    <span class="font-bold">{{ $slot }}</span>
</td>
