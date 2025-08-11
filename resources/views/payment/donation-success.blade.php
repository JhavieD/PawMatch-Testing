@extends('layouts.app')

@section('title', 'Donation Success')

@section('content')
<section class="donation-result">
  <div class="result-card success">
    <div class="icon">
      <svg width="56" height="56" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <circle cx="12" cy="12" r="10" fill="#E7F8F0"/>
        <path d="M8 12.5l2.2 2.2L16.5 8.5" stroke="#10B981" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
    <h2>Thank you for your donation!</h2>
    @if(session('success'))
      <p class="lead">{{ session('success') }}</p>
    @endif
    @if(isset($donation))
      <div class="details">
        <div><span>Amount</span><strong>PHP {{ number_format($donation->amount, 2) }}</strong></div>
        @if($donation->maya_payment_id)
        <div><span>Reference</span><code>{{ $donation->maya_payment_id }}</code></div>
        @endif
      </div>
    @else
      <p class="lead">Your donation was processed. If you need assistance, please contact support.</p>
    @endif

    <div class="actions">
      <a href="{{ route('donate') }}" class="btn primary">Donate Again</a>
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
  .btn.primary{background:#10b981;color:#fff;box-shadow:0 8px 18px rgba(16,185,129,.25)}
  .btn.primary:hover{background:#0ea371}
  .btn.ghost{border:1px solid #93c5fd;color:#2563eb;background:#fff}
  .btn.ghost:hover{background:#f8fbff}
</style>
@endpush
@endsection 