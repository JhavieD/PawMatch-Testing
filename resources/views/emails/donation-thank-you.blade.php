<!DOCTYPE html>
<html>
  <body style="font-family: Arial, sans-serif; color: #111827;">
    <div style="max-width:600px;margin:0 auto;padding:24px;">
      <h2 style="margin:0 0 8px;">Thank you for your donation!</h2>
      <p style="margin:0 0 12px;">We appreciate your support for PawMatch and the animals we serve.</p>
      <p style="margin:0 0 8px;">
        <strong>Amount:</strong> PHP {{ number_format($donation->amount, 2) }}
      </p>
      @if($donation->maya_payment_id)
      <p style="margin:0 0 8px;">
        <strong>Reference:</strong> {{ $donation->maya_payment_id }}
      </p>
      @endif
      @if($donation->message)
      <p style="margin:0 0 8px;">Your message: “{{ $donation->message }}”</p>
      @endif
      <p style="margin:16px 0 0;">With gratitude,<br/>PawMatch Team</p>
    </div>
  </body>
</html>
