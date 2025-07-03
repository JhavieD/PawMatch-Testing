@extends('layouts.admin')

@section('title', 'Platform Settings')

@section('content')
<div class="container mt-4">
    <h2>General Settings</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        <div class="mb-3">
            <label for="site_name" class="form-label">Site Name</label>
            <input type="text" class="form-control" id="site_name" name="site_name" value="{{ $settings['site_name']->value ?? '' }}">
        </div>
        <div class="mb-3">
            <label for="contact_email" class="form-label">Contact Email</label>
            <input type="email" class="form-control" id="contact_email" name="contact_email" value="{{ $settings['contact_email']->value ?? '' }}">
        </div>
        <h4>System Settings</h4>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" value="1" {{ !empty($settings['email_notifications']) && $settings['email_notifications']->value == '1' ? 'checked' : '' }}>
            <label class="form-check-label" for="email_notifications">Enable Email Notifications</label>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
@endsection 