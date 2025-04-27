<div>
    @if (session('toast'))
    <div x-data="{ show: true }"
        x-init="setTimeout(() => show = false, {{ session('toast.duration') ?? 5000 }})"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-4"
        class="fixed bottom-4 right-4 z-50">
        <div class="{{ session('toast.type') === 'success' ? 'bg-green-500' : 'bg-red-500' }} text-white px-6 py-3 rounded-lg shadow-lg">
            <div class="flex items-center">
                @if(session('toast.type') === 'success')
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                @else
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                @endif
                <span>{{ session('toast.message') }}</span>
            </div>
        </div>
    </div>
    @endif
</div>