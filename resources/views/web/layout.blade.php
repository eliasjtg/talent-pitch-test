<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" itemscope itemtype="https://schema.org/WebPage">
    <head>
@include('web.commons.meta')
@include('web.commons.head-scripts')
@include('web.commons.styles')
@yield('header-resources')

    </head>
    <body id="body">
        @livewire('header')
        <div class="relative">
            @yield('content')
        </div>
        @include('web.commons.body-scripts')
    </body>
</html>
