<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Find Your Match') }}
            </h2>
            <a href="{{ route('pet-listings') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                View All Pets
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Pet Card -->
                    <div class="max-w-md mx-auto">
                        <div class="relative">
                            <!-- Main Image -->
                            <div class="relative h-[600px] rounded-xl overflow-hidden">
                                <img src="https://source.unsplash.com/random/800x1200/?dog" alt="Pet" class="w-full h-full object-cover">
                                
                                <!-- Pet Info Overlay -->
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-6">
                                    <h3 class="text-2xl font-bold text-white">Max</h3>
                                    <p class="text-white text-lg">2 years old • Golden Retriever</p>
                                    <p class="text-white mt-2">Friendly and playful dog who loves long walks and playing fetch.</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="absolute bottom-[-60px] left-0 right-0 flex justify-center space-x-4">
                                <button class="p-4 rounded-full bg-white shadow-lg hover:shadow-xl transition-shadow">
                                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <button class="p-4 rounded-full bg-white shadow-lg hover:shadow-xl transition-shadow">
                                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="mt-20">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-500">Size</h4>
                                    <p class="mt-1 text-sm text-gray-900">Large</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-500">Gender</h4>
                                    <p class="mt-1 text-sm text-gray-900">Male</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-500">Good with</h4>
                                    <p class="mt-1 text-sm text-gray-900">Children, Other Dogs</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-500">Location</h4>
                                    <p class="mt-1 text-sm text-gray-900">Happy Paws Shelter</p>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-8">
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>Pets viewed</span>
                                <span>12/50</span>
                            </div>
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: 24%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Add swipe functionality here
        document.addEventListener('DOMContentLoaded', function() {
            const card = document.querySelector('.relative');
            let startX, startY, moveX, moveY;
            
            card.addEventListener('touchstart', function(e) {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
            });

            card.addEventListener('touchmove', function(e) {
                moveX = e.touches[0].clientX;
                moveY = e.touches[0].clientY;
                
                const diffX = moveX - startX;
                const diffY = moveY - startY;
                
                if (Math.abs(diffX) > Math.abs(diffY)) {
                    card.style.transform = `translateX(${diffX}px) rotate(${diffX * 0.1}deg)`;
                }
            });

            card.addEventListener('touchend', function() {
                const diffX = moveX - startX;
                
                if (Math.abs(diffX) > 100) {
                    // Swipe threshold reached
                    if (diffX > 0) {
                        // Swipe right - like
                        console.log('Liked');
                    } else {
                        // Swipe left - dislike
                        console.log('Disliked');
                    }
                }
                
                card.style.transform = '';
            });
        });
    </script>
    @endpush
</x-app-layout> 