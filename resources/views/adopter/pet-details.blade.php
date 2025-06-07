<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pet Details') }}
            </h2>
            <a href="{{ route('pet-listings') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Back to Listings
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Pet Images -->
                        <div>
                            <div class="relative">
                                <img src="https://source.unsplash.com/random/800x600/?pet" alt="Pet" class="w-full h-96 object-cover rounded-lg">
                                <div class="absolute top-4 right-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Available for Adoption
                                    </span>
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-4 mt-4">
                                @for ($i = 1; $i <= 4; $i++)
                                    <img src="https://source.unsplash.com/random/200x200/?pet" alt="Pet thumbnail" class="w-full h-24 object-cover rounded-lg cursor-pointer hover:opacity-75">
                                @endfor
                            </div>
                        </div>

                        <!-- Pet Information -->
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Max</h1>
                            <p class="mt-2 text-lg text-gray-600">Friendly and Playful Golden Retriever</p>
                            
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Age</h3>
                                    <p class="mt-1 text-sm text-gray-900">2 years old</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Gender</h3>
                                    <p class="mt-1 text-sm text-gray-900">Male</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Size</h3>
                                    <p class="mt-1 text-sm text-gray-900">Large</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Breed</h3>
                                    <p class="mt-1 text-sm text-gray-900">Golden Retriever</p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <h3 class="text-sm font-medium text-gray-500">About</h3>
                                <p class="mt-2 text-sm text-gray-900">
                                    Max is a friendly and playful Golden Retriever who loves to play fetch and go for long walks. 
                                    He's great with children and other dogs, and he's already house-trained. Max would make a 
                                    wonderful addition to any active family.
                                </p>
                            </div>

                            <div class="mt-6">
                                <h3 class="text-sm font-medium text-gray-500">Health Information</h3>
                                <ul class="mt-2 text-sm text-gray-900 list-disc list-inside">
                                    <li>Vaccinated</li>
                                    <li>Spayed/Neutered</li>
                                    <li>Microchipped</li>
                                    <li>Regular check-ups</li>
                                </ul>
                            </div>

                            <div class="mt-6">
                                <h3 class="text-sm font-medium text-gray-500">Shelter Information</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-900">Happy Paws Shelter</p>
                                    <p class="text-sm text-gray-600">123 Pet Street, City, State</p>
                                    <p class="text-sm text-gray-600">(555) 123-4567</p>
                                </div>
                            </div>

                            <div class="mt-8">
                                <a href="#" class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Start Adoption Process
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 