@extends('layouts.app')

@section('title', 'Donate to PawMatch')

@section('content')
<section class="donate-wrap">
  <div class="donate-card">
    <h2>Support PawMatch</h2>
    <p class="subtitle">Your donation helps us connect more pets with loving homes and support rescuers and shelters.</p>

    @if ($errors->any())
      <div class="alert">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('donation.process') }}" class="donate-form">
      @csrf
      <label class="field">
        <span>Amount (PHP)</span>
        <input type="number" step="0.01" min="10" name="amount" value="{{ old('amount', 100) }}" required />
      </label>

      <label class="field">
        <span>Name</span>
        <input type="text" name="donor_name" value="{{ old('donor_name', isset($user) ? ($user->first_name . ' ' . $user->last_name) : '') }}" />
      </label>

      <label class="field">
        <span>Email</span>
        <input type="email" name="donor_email" value="{{ old('donor_email', isset($user) ? $user->email : '') }}" />
      </label>

      <label class="field">
        <span>Message (optional)</span>
        <textarea name="message" rows="3">{{ old('message') }}</textarea>
      </label>

      <button type="submit" class="primary-btn">Donate with Maya</button>
    </form>
  </div>
</section>

@push('styles')
<style>
  .donate-wrap {
    min-height: calc(100vh - 140px);
    display: flex; align-items: center; justify-content: center;
    padding: 24px;
    background: #f6f7fb;
  }
  .donate-card {
    width: 100%; max-width: 560px;
    background: #fff; border-radius: 12px; padding: 24px 24px 28px;
    box-shadow: 0 12px 32px rgba(16,24,40,.08);
  }
  .donate-card h2 { margin: 0 0 6px; font-size: 24px; }
  .donate-card .subtitle { margin: 0 0 18px; color: #6b7280; }
  .alert { background: #fff3cd; color: #664d03; border: 1px solid #ffecb5; padding: 10px 12px; border-radius: 8px; margin-bottom: 14px; }
  .donate-form .field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px; }
  .donate-form input, .donate-form textarea {
    width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px;
    outline: none; background: #fff;
  }
  .donate-form input:focus, .donate-form textarea:focus { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
  .primary-btn {
    width: 100%; border: 0; border-radius: 10px; padding: 12px 16px; font-weight: 700; cursor: pointer;
    background: #10b981; color: #fff; transition: transform .05s ease, box-shadow .2s ease, background .2s ease;
    box-shadow: 0 8px 18px rgba(16,185,129,.25);
  }
  .primary-btn:hover { background: #0ea371; }
</style>
@endpush
@endsection 