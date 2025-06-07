<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pet Personality Quiz') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Progress Bar -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-2">
                            <span>Question 3 of 10</span>
                            <span>30% Complete</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: 30%"></div>
                        </div>
                    </div>

                    <!-- Question -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">How much time can you dedicate to a pet daily?</h3>
                        <p class="text-gray-600 mb-6">This helps us match you with a pet that fits your lifestyle.</p>

                        <div class="space-y-4">
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="time" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-3">
                                    <span class="block text-sm font-medium text-gray-900">Less than 2 hours</span>
                                    <span class="block text-sm text-gray-500">I have a busy schedule but can provide basic care</span>
                                </span>
                            </label>

                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="time" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-3">
                                    <span class="block text-sm font-medium text-gray-900">2-4 hours</span>
                                    <span class="block text-sm text-gray-500">I can provide regular exercise and attention</span>
                                </span>
                            </label>

                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="time" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-3">
                                    <span class="block text-sm font-medium text-gray-900">4-6 hours</span>
                                    <span class="block text-sm text-gray-500">I can provide extensive care and training</span>
                                </span>
                            </label>

                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="time" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-3">
                                    <span class="block text-sm font-medium text-gray-900">More than 6 hours</span>
                                    <span class="block text-sm text-gray-500">I can provide constant attention and care</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex justify-between">
                        <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Previous Question
                        </button>
                        <button class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Next Question
                        </button>
                    </div>

                    <!-- Quiz Tips -->
                    <div class="mt-8 p-4 bg-indigo-50 rounded-lg">
                        <h4 class="text-sm font-medium text-indigo-800 mb-2">Why we ask this</h4>
                        <p class="text-sm text-indigo-700">
                            Different pets require different amounts of attention and care. Some pets need more exercise and 
                            interaction, while others are more independent. Your available time helps us match you with a pet 
                            that will thrive in your care.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const radioButtons = document.querySelectorAll('input[type="radio"]');
            const nextButton = document.querySelector('button:last-child');
            
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    nextButton.disabled = false;
                });
            });
        });
    </script>
    @endpush
</x-app-layout> 