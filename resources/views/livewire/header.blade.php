<header class="bg-white sticky top-0 z-50">
  <nav class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8" aria-label="Global">
    <div class="flex flex-1">
      <div class="hidden lg:flex lg:gap-x-12">
        <a href="#body" class="text-sm font-semibold leading-6 text-gray-900">Preguntas frecuentes</a>
        <a href="#features" class="text-sm font-semibold leading-6 text-gray-900">Funcionalidades</a>
      </div>
      <div class="flex lg:hidden">
        <button type="button" wire:click="toggle" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700">
          <span class="sr-only">Open main menu</span>
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
        </button>
      </div>
    </div>
    <a href="#" class="-m-1.5 p-1.5">
      <span class="sr-only">Talent Pitch</span>
      <img class="h-8 w-auto" src="{{ asset('assets/favicon/favicon-32x32.png') }}" alt="">
    </a>
  </nav>
@if($show)
    <!-- Mobile menu, show/hide based on menu open state. -->
    <div class="lg:hidden" role="dialog" aria-modal="true">
        <!-- Background backdrop, show/hide based on slide-over state. -->
        <div class="fixed inset-0 z-10"></div>
        <div class="fixed inset-y-0 left-0 z-10 w-full overflow-y-auto bg-white px-6 py-6">
        <div class="flex items-center justify-between">
            <div class="flex flex-1">
            <button type="button" wire:click="toggle" class="-m-2.5 rounded-md p-2.5 text-gray-700">
                <span class="sr-only">Close menu</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            </div>
            <a href="#" class="-m-1.5 p-1.5">
            <span class="sr-only">Talent Pitch</span>
            <img class="h-8 w-auto" src="{{ asset('assets/favicon/favicon-32x32.png') }}" alt="">
            </a>
        </div>
        <div class="mt-6 space-y-2">
            <a href="#body" wire:click="toggle" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Preguntas frecuentes</a>
            <a href="#features" wire:click="toggle" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Funcionalidades</a>
        </div>
        </div>
    </div>
@endif
</header>