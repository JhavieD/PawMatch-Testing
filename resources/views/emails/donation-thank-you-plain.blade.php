Thank you for your donation to PawMatch!

Amount: PHP {{ number_format($donation->amount, 2) }}
@if($donation->maya_payment_id)
Reference: {{ $donation->maya_payment_id }}
@endif
@if($donation->message)
Your message: "{{ $donation->message }}"
@endif

With gratitude,
PawMatch Team
