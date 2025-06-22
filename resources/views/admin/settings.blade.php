@extends('layouts.admin')

@section('page-title', 'Settings')
@section('page-subtitle', 'Manage system settings and configurations')

@section('content')
    <div class="bg-white rounded-xl shadow p-6">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- General Settings -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">General Settings</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700">Site Name</label>
                        <input type="text" name="site_name" id="site_name" class="form-input mt-1" value="{{ old('site_name', 'PawMatch') }}">
                    </div>
                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                        <input type="email" name="contact_email" id="contact_email" class="form-input mt-1" value="{{ old('contact_email', 'contact@pawmatch.com') }}">
                    </div>
                </div>
            </div>

            <!-- System Settings -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">System Settings</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="maintenance_mode" id="maintenance_mode" class="form-checkbox" value="1">
                        <label for="maintenance_mode" class="ml-2 block text-sm text-gray-700">
                            Enable Maintenance Mode
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="notifications_enabled" id="notifications_enabled" class="form-checkbox" value="1" checked>
                        <label for="notifications_enabled" class="ml-2 block text-sm text-gray-700">
                            Enable Email Notifications
                        </label>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
@endsection 