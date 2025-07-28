<div class="space-y-6">
    <div class="border-b pb-4">
        <h4 class="font-semibold text-lg text-gray-900 mb-2">Transaction ID</h4>
        <p class="text-sm text-gray-600 font-mono bg-gray-50 px-3 py-2 rounded">{{ $transaction->transaction_id }}</p>
    </div>

    <div class="border-b pb-4">
        <h4 class="font-semibold text-lg text-gray-900 mb-3">Pet Information</h4>
        <div class="flex items-center bg-gray-50 p-4 rounded-lg">
            <img class="h-16 w-16 rounded-full object-cover border-2 border-blue-200" 
                 src="{{ $transaction->application->pet->image_url ?? asset('images/default-pet.png') }}" 
                 alt="{{ $transaction->application->pet->name }}">
            <div class="ml-4">
                <p class="text-lg font-semibold text-gray-900">{{ $transaction->application->pet->name }}</p>
                <p class="text-sm text-gray-600">{{ $transaction->application->pet->breed }}</p>
            </div>
        </div>
    </div>

    <div class="border-b pb-4">
        <h4 class="font-semibold text-lg text-gray-900 mb-3">Payment Details</h4>
        <div class="bg-blue-50 p-4 rounded-lg space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-700">Total Amount:</span>
                <span class="text-lg font-bold text-blue-600">₱{{ number_format($transaction->total_amount, 2) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">PawMatch Commission (20%):</span>
                <span class="text-sm font-medium text-gray-500">₱{{ number_format($transaction->pawmatch_commission, 2) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Provider Amount (80%):</span>
                <span class="text-sm font-medium text-gray-500">₱{{ number_format($transaction->provider_amount, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="border-b pb-3">
        <h4 class="font-medium text-gray-900">Payment Information</h4>
        <div class="mt-2 space-y-1">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Status:</span>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                    {{ $transaction->payment_status === 'completed' ? 'bg-green-100 text-green-800' : 
                       ($transaction->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                       'bg-red-100 text-red-800') }}">
                    {{ ucfirst($transaction->payment_status) }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Payment Date:</span>
                <span class="text-sm text-gray-600">
                    @if($transaction->payment_date)
                        @if($transaction->payment_date instanceof \Carbon\Carbon)
                            {{ $transaction->payment_date->format('M d, Y h:i A') }}
                        @else
                            {{ \Carbon\Carbon::parse($transaction->payment_date)->format('M d, Y h:i A') }}
                        @endif
                    @else
                        N/A
                    @endif
                </span>
            </div>
            @if($transaction->payment_method)
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Payment Method:</span>
                <span class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</span>
            </div>
            @endif
            @if($transaction->maya_payment_id)
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Maya Payment ID:</span>
                <span class="text-sm text-gray-600 font-mono">{{ $transaction->maya_payment_id }}</span>
            </div>
            @endif
        </div>
    </div>

    @if($transaction->notes)
    <div class="border-b pb-3">
        <h4 class="font-medium text-gray-900">Notes</h4>
        <p class="text-sm text-gray-600 mt-2">{{ $transaction->notes }}</p>
    </div>
    @endif

    <div class="border-b pb-3">
        <h4 class="font-medium text-gray-900">Adopter Information</h4>
        <div class="mt-2">
            <p class="text-sm text-gray-600">{{ $transaction->adopter->user->name }}</p>
            <p class="text-sm text-gray-500">{{ $transaction->adopter->user->email }}</p>
        </div>
    </div>

    @if($transaction->shelter || $transaction->rescuer)
    <div>
        <h4 class="font-medium text-gray-900">Provider Information</h4>
        <div class="mt-2">
            @if($transaction->shelter)
                <p class="text-sm text-gray-600">{{ $transaction->shelter->shelter_name }}</p>
            @elseif($transaction->rescuer)
                <p class="text-sm text-gray-600">{{ $transaction->rescuer->organization_name }}</p>
            @endif
        </div>
    </div>
    @endif
</div> 