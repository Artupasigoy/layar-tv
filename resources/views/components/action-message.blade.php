@props(['on'])

<div x-data="{ show: false, timeout: null }"
    x-init="@this.on('{{ $on }}', () => { clearTimeout(timeout); show = true; timeout = setTimeout(() => { show = false }, 2000); })"
    x-show="show" x-transition:leave.opacity.duration.1500ms style="display: none;" {{ $attributes->merge(['class' => 'text-sm text-gray-600']) }}>
    {{ $slot }}
</div>