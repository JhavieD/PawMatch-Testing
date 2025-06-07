<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Adoption Application') }}
            </h2>
            <a href="{{ route('pet-details') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Back to Pet Details
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Progress Steps -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex items-center relative">
                                    <div class="rounded-full h-8 w-8 py-1 px-3 bg-indigo-600 text-white">1</div>
                                    <div class="absolute top-0 -ml-10 text-center mt-16 w-32 text-xs font-medium text-indigo-600">Personal Info</div>
                                </div>
                                <div class="flex-auto border-t-2 transition duration-500 ease-in-out border-indigo-600"></div>
                            </div>
                            <div class="flex items-center">
                                <div class="flex items-center relative">
                                    <div class="rounded-full h-8 w-8 py-1 px-3 bg-gray-300 text-white">2</div>
                                    <div class="absolute top-0 -ml-10 text-center mt-16 w-32 text-xs font-medium text-gray-500">Living Situation</div>
                                </div>
                                <div class="flex-auto border-t-2 transition duration-500 ease-in-out border-gray-300"></div>
                            </div>
                            <div class="flex items-center">
                                <div class="flex items-center relative">
                                    <div class="rounded-full h-8 w-8 py-1 px-3 bg-gray-300 text-white">3</div>
                                    <div class="absolute top-0 -ml-10 text-center mt-16 w-32 text-xs font-medium text-gray-500">Experience</div>
                                </div>
                                <div class="flex-auto border-t-2 transition duration-500 ease-in-out border-gray-300"></div>
                            </div>
                            <div class="flex items-center">
                                <div class="flex items-center relative">
                                    <div class="rounded-full h-8 w-8 py-1 px-3 bg-gray-300 text-white">4</div>
                                    <div class="absolute top-0 -ml-10 text-center mt-16 w-32 text-xs font-medium text-gray-500">References</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <form class="space-y-6">
                        <!-- Personal Information -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
                            
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700">First name</label>
                                    <input type="text" name="first_name" id="first_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last name</label>
                                    <input type="text" name="last_name" id="last_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                                    <input type="email" name="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone number</label>
                                    <input type="tel" name="phone" id="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Street address</label>
                                    <input type="text" name="address" id="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                    <input type="text" name="city" id="city" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                                    <input type="text" name="state" id="state" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label for="zip" class="block text-sm font-medium text-gray-700">ZIP code</label>
                                    <input type="text" name="zip" id="zip" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between pt-6">
                            <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Previous
                            </button>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Next Step
                            </button>
                        </div>
                    </form>

                    <!-- Form Tips -->
                    <div class="mt-8 p-4 bg-indigo-50 rounded-lg">
                        <h4 class="text-sm font-medium text-indigo-800 mb-2">Tips for a successful application</h4>
                        <ul class="text-sm text-indigo-700 list-disc list-inside space-y-1">
                            <li>Please provide accurate and complete information</li>
                            <li>All fields marked with an asterisk (*) are required</li>
                            <li>You can save your progress and return later</li>
                            <li>Be prepared to provide references and proof of residence</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const nextButton = form.querySelector('button[type="submit"]');
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                // Add form validation and submission logic here
                console.log('Form submitted');
            });
        });
    </script>
    @endpush
</x-app-layout> 