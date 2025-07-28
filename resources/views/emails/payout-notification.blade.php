<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payout Completed - PawMatch</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8fafc;
        }
        .container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
        }
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 15px;
        }
        .title {
            color: #3b82f6;
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .subtitle {
            color: #6b7280;
            font-size: 16px;
            margin: 5px 0 0 0;
        }
        .content {
            margin-bottom: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #374151;
        }
        .message {
            font-size: 16px;
            margin-bottom: 25px;
            color: #4b5563;
        }
        .details {
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .detail-label {
            font-weight: 600;
            color: #374151;
        }
        .detail-value {
            color: #6b7280;
        }
        .amount {
            font-size: 20px;
            font-weight: bold;
            color: #059669;
        }
        .pet-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 20px 0;
            padding: 15px;
            background: #f0f9ff;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }
        .pet-image {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }
        .pet-details h3 {
            margin: 0 0 5px 0;
            color: #1f2937;
        }
        .pet-details p {
            margin: 0;
            color: #6b7280;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 20px 0;
        }
        .button:hover {
            background: #2563eb;
        }
        .success-icon {
            color: #059669;
            font-size: 48px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="PawMatch Logo" class="logo">
            <h1 class="title">Payout Completed!</h1>
            <p class="subtitle">Your adoption fee has been processed</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hello {{ $provider->shelter_name ?? $provider->organization_name ?? 'Provider' }},
            </div>

            <div class="message">
                Great news! Your payout for the adoption of <strong>{{ $pet->name }}</strong> has been successfully processed and transferred to your bank account.
            </div>

            <div class="pet-info">
                <img src="{{ $pet->image_url ?? asset('images/default-pet.png') }}" alt="{{ $pet->name }}" class="pet-image">
                <div class="pet-details">
                    <h3>{{ $pet->name }}</h3>
                    <p>{{ $pet->breed }} • {{ $pet->age }} years old, {{ $pet->gender }}</p>
                </div>
            </div>

            <div class="details">
                <div class="detail-row">
                    <span class="detail-label">Transaction ID:</span>
                    <span class="detail-value">#{{ $transaction->transaction_id }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Adopter:</span>
                    <span class="detail-value">{{ $adopter->user->first_name }} {{ $adopter->user->last_name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Adoption Date:</span>
                    <span class="detail-value">{{ $transaction->payment_date->format('M d, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payout Date:</span>
                    <span class="detail-value">{{ $transaction->payout_date->format('M d, Y H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Adoption Fee:</span>
                    <span class="detail-value">₱{{ number_format($transaction->total_amount, 2) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">PawMatch Commission (20%):</span>
                    <span class="detail-value">₱{{ number_format($transaction->pawmatch_commission, 2) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Your Payout (80%):</span>
                    <span class="detail-value amount">₱{{ number_format($transaction->provider_amount, 2) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Bank Account:</span>
                    <span class="detail-value">{{ $provider->bank_name }} - {{ $provider->bank_account_number }}</span>
                </div>
            </div>

            <div class="message">
                The funds should appear in your bank account within 1-3 business days, depending on your bank's processing time.
            </div>

            <div style="text-align: center;">
                <a href="{{ route('shelter.dashboard') ?? route('rescuer.dashboard') }}" class="button">
                    View Dashboard
                </a>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for using PawMatch to help pets find their forever homes!</p>
            <p>If you have any questions about this payout, please contact our support team.</p>
            <p>&copy; {{ date('Y') }} PawMatch. All rights reserved.</p>
        </div>
    </div>
</body>
</html> 