@extends('layouts.app')

@section('title', 'Donation Failed')

@section('content')
<section class="donation-result">
  <div class="result-card failure">
    <div class="icon">
      <svg width="56" height="56" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <circle cx="12" cy="12" r="10" fill="#FEE2E2"/>
        <path d="M12 7v6M12 17h.01" stroke="#DC2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
    <h2>Donation Failed</h2>
    <p class="lead">Unfortunately, your donation could not be completed.</p>
    @if(isset($donation) && $donation->maya_payment_id)
      <div class="details">
        <div><span>Reference</span><code>{{ $donation->maya_payment_id }}</code></div>
      </div>
    @endif
    <div class="actions">
      <a href="{{ route('donate') }}" class="btn primary">Try Again</a>
      <a href="{{ route('home') }}" class="btn ghost">Back to Home</a>
    </div>
  </div>
</section>

@push('styles')
<style>
  .donation-result{min-height:calc(100vh - 140px);display:flex;align-items:center;justify-content:center;padding:24px;background:#f6f7fb}
  .result-card{width:100%;max-width:560px;background:#fff;border-radius:14px;padding:28px 24px;box-shadow:0 12px 32px rgba(16,24,40,.08);text-align:center}
  .result-card .icon{display:flex;align-items:center;justify-content:center;margin-bottom:10px}
  .result-card h2{margin:.25rem 0 .5rem}
  .lead{color:#6b7280;margin:0 0 .75rem}
  .details{display:grid;grid-template-columns:1fr;gap:8px;margin:10px 0 14px;text-align:left;background:#fafafa;border:1px solid #eee;border-radius:10px;padding:12px}
  .details span{color:#6b7280}
  .details code{background:#f3f4f6;padding:2px 6px;border-radius:6px}
  .actions{display:flex;gap:10px;justify-content:center;margin-top:6px}
  .btn{display:inline-block;padding:10px 16px;border-radius:10px;text-decoration:none;font-weight:700}
  .btn.primary{background:#2563eb;color:#fff;box-shadow:0 8px 18px rgba(37,99,235,.25)}
  .btn.primary:hover{background:#1e4ed8}
  .btn.ghost{border:1px solid #d1d5db;color:#111827;background:#fff}
  .btn.ghost:hover{background:#f8f9fb}
</style>
@endpush
@endsection 