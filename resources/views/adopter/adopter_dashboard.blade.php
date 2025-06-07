<x-app-layout>
    <div class="flex min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white p-8 shadow-lg hidden md:block">
            <a href="/" class="text-2xl font-bold text-blue-500 flex items-center gap-2 mb-8">🐾 PawMatch</a>
            <ul class="space-y-2">
                <li><a href="{{ route('adopter.dashboard') }}" class="block px-4 py-2 rounded-lg font-medium text-blue-600 bg-blue-50">Dashboard</a></li>
                <li><a href="{{ route('pet-listings') }}" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100">Find Pets</a></li>
                <li><a href="{{ route('application-status') }}" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100">My Applications</a></li>
                <li><a href="{{ route('messages.index', [], false) }}" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100">Messages</a></li>
                <li><a href="#" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100">Profile</a></li>
                <li><a href="{{ route('logout') }}" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100">Logout</a></li>
            </ul>
        </aside>
        <!-- Main Content -->
        <main class="flex-1 p-6 md:p-12">
            <div class="max-w-5xl mx-auto">
                <!-- Top Bar -->
                <div class="bg-white rounded-xl shadow p-6 flex flex-col md:flex-row md:justify-between md:items-center mb-8">
                    <div class="mb-4 md:mb-0">
                        <h1 class="text-2xl font-semibold text-gray-900">Welcome, {{ Auth::user()->name ?? 'Adopter' }}!</h1>
                        <p class="text-gray-500">Here's what's happening with your pet adoption journey</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gray-200 overflow-hidden">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'A') }}" alt="Profile" class="w-full h-full object-cover">
                        </div>
                        <div class="font-semibold text-gray-800">{{ Auth::user()->name ?? 'Adopter' }}</div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Favorite Pets -->
                    <div class="bg-white rounded-xl shadow p-6 col-span-1 md:col-span-1">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">Favorite Pets</h2>
                            <a href="{{ route('pet-listings') }}" class="inline-block px-3 py-1 border border-blue-500 text-blue-600 rounded hover:bg-blue-50 transition">Find More</a>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            @forelse($favoritePets as $pet)
                                <div class="bg-gray-50 rounded-lg overflow-hidden shadow">
                                    <img src="{{ $pet->image_url ?? 'https://source.unsplash.com/160x120/?pet' }}" alt="Pet" class="w-full h-20 object-cover">
                                    <div class="p-2">
                                        <div class="font-semibold text-gray-800">{{ $pet->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $pet->breed ?? 'Unknown Breed' }} • {{ $pet->age ?? '?' }} years</div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-2 text-center text-gray-400 py-8">No favorite pets yet.</div>
                            @endforelse
                        </div>
                    </div>
                    <!-- Recent Applications -->
                    <div class="bg-white rounded-xl shadow p-6 col-span-1">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">Recent Applications</h2>
                            <a href="{{ route('application-status') }}" class="inline-block px-3 py-1 border border-blue-500 text-blue-600 rounded hover:bg-blue-50 transition">View All</a>
                        </div>
                        <ul>
                            @forelse($recentApplications as $application)
                                <li class="mb-4 pb-4 border-b border-gray-100">
                                    <div class="font-semibold text-gray-800">{{ $application->pet->name ?? 'Pet' }} - {{ $application->pet->breed ?? '' }}</div>
                                    <div class="text-xs text-gray-500">{{ $application->shelter->name ?? 'Shelter' }}</div>
                                    <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-medium {{ $application->status === 'approved' ? 'bg-green-100 text-green-800' : ($application->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-200 text-gray-600') }}">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </li>
                            @empty
                                <li class="text-center text-gray-400 py-8">No recent applications.</li>
                            @endforelse
                        </ul>
                    </div>
                    <!-- Recent Messages -->
                    <div class="bg-white rounded-xl shadow p-6 col-span-1">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">Recent Messages</h2>
                            <a href="{{ route('messages.index', [], false) }}" class="inline-block px-3 py-1 border border-blue-500 text-blue-600 rounded hover:bg-blue-50 transition">View All</a>
                        </div>
                        <ul>
                            @forelse($recentMessages as $message)
                                <li class="mb-4 pb-4 border-b border-gray-100">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="font-semibold text-gray-800">{{ $message->sender_name ?? 'Shelter' }}</span>
                                        <span class="text-xs text-gray-500">{{ $message->created_at ? $message->created_at->diffForHumans() : '' }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $message->content ?? '' }}</div>
                                </li>
                            @empty
                                <li class="text-center text-gray-400 py-8">No recent messages.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout> 