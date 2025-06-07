<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex h-[600px]">
                    <!-- Conversations List -->
                    <div class="w-1/3 border-r border-gray-200">
                        <div class="p-4 border-b border-gray-200">
                            <div class="relative">
                                <input type="text" placeholder="Search conversations..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-y-auto h-[calc(600px-4rem)]">
                            <!-- Conversation Items -->
                            @for ($i = 1; $i <= 5; $i++)
                                <div class="p-4 border-b border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <img class="h-10 w-10 rounded-full" src="https://source.unsplash.com/random/100x100/?person" alt="">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $i === 1 ? 'Happy Paws Shelter' : 'User ' . $i }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                {{ $i === 1 ? 'Thank you for your interest in Max!' : 'Hello, I have a question about...' }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                New
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- Chat Area -->
                    <div class="flex-1 flex flex-col">
                        <!-- Chat Header -->
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex items-center space-x-3">
                                <img class="h-10 w-10 rounded-full" src="https://source.unsplash.com/random/100x100/?person" alt="">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">Happy Paws Shelter</h3>
                                    <p class="text-sm text-gray-500">Online</p>
                                </div>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div class="flex-1 overflow-y-auto p-4 space-y-4">
                            <!-- Received Message -->
                            <div class="flex items-start">
                                <img class="h-8 w-8 rounded-full" src="https://source.unsplash.com/random/100x100/?person" alt="">
                                <div class="ml-3">
                                    <div class="bg-gray-100 rounded-lg py-2 px-4">
                                        <p class="text-sm text-gray-900">Thank you for your interest in Max! We're excited to help you with the adoption process.</p>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">10:30 AM</p>
                                </div>
                            </div>

                            <!-- Sent Message -->
                            <div class="flex items-start justify-end">
                                <div class="mr-3">
                                    <div class="bg-indigo-600 rounded-lg py-2 px-4">
                                        <p class="text-sm text-white">Hi! I'm very interested in adopting Max. Could you tell me more about his personality?</p>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 text-right">10:32 AM</p>
                                </div>
                                <img class="h-8 w-8 rounded-full" src="https://source.unsplash.com/random/100x100/?person" alt="">
                            </div>

                            <!-- Received Message -->
                            <div class="flex items-start">
                                <img class="h-8 w-8 rounded-full" src="https://source.unsplash.com/random/100x100/?person" alt="">
                                <div class="ml-3">
                                    <div class="bg-gray-100 rounded-lg py-2 px-4">
                                        <p class="text-sm text-gray-900">Max is a very friendly and playful dog. He loves playing fetch and going for long walks. He's great with children and other dogs.</p>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">10:35 AM</p>
                                </div>
                            </div>
                        </div>

                        <!-- Message Input -->
                        <div class="p-4 border-t border-gray-200">
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <input type="text" placeholder="Type your message..." class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <button class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Send
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageInput = document.querySelector('input[type="text"]');
            const sendButton = document.querySelector('button');
            const messagesContainer = document.querySelector('.overflow-y-auto');
            
            sendButton.addEventListener('click', function() {
                const message = messageInput.value.trim();
                if (message) {
                    // Add message to chat
                    const messageHTML = `
                        <div class="flex items-start justify-end">
                            <div class="mr-3">
                                <div class="bg-indigo-600 rounded-lg py-2 px-4">
                                    <p class="text-sm text-white">${message}</p>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 text-right">${new Date().toLocaleTimeString()}</p>
                            </div>
                            <img class="h-8 w-8 rounded-full" src="https://source.unsplash.com/random/100x100/?person" alt="">
                        </div>
                    `;
                    messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
                    messageInput.value = '';
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            });

            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendButton.click();
                }
            });
        });
    </script>
    @endpush
</x-app-layout> 