<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-black text-gray-100 flex items-center justify-center min-h-screen flex-col">
    <livewire:viewer />
    <div x-cloak>
        <span class="text-[32px]">32px</span>
        <span class="text-[34px]">34px</span>
        <span class="text-[36px]">36px</span>
        <span class="text-[38px]">38px</span>
        <span class="text-[40px]">40px</span>
        <span class="text-[42px]">42px</span>
        <span class="text-[44px]">44px</span>
        <span class="text-[46px]">46px</span>
        <span class="text-[48px]">48px</span>
        <span class="text-[50px]">50px</span>
        <span class="text-[52px]">52px</span>
        <span class="text-[54px]">54px</span>
        <span class="text-[56px]">56px</span>
        <span class="text-[58px]">58px</span>
        <span class="text-[60px]">60px</span>
        <span class="text-[62px]">62px</span>
        <span class="text-[64px]">64px</span>
        <span class="text-[66px]">66px</span>
        <span class="text-[68px]">68px</span>
        <span class="text-[70px]">70px</span>
        <span class="text-[72px]">72px</span>
        <span class="text-[74px]">74px</span>
        <span class="text-[76px]">76px</span>
        <span class="text-[78px]">78px</span>
        <span class="text-[80px]">80px</span>
    </div>
</body>

</html>
