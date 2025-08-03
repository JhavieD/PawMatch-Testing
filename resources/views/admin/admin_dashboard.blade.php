@extends('layouts.admin')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, ' . Auth::user()->first_name . ' ' . Auth::user()->last_name)

@section('content')
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            </div>
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-title">Total Users</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fa-solid fa-paw"></i></div>
            </div>
            <div class="stat-value">
                {{ $adoptionPipeline['pending'] + $adoptionPipeline['approved'] + $adoptionPipeline['rejected'] }}
            </div>
            <div class="stat-title">Adoption Applications</div>
            <div class="stat-desc" style="font-size: 0.9em;">
                Pending: {{ $adoptionPipeline['pending'] }}<br>
                Approved: {{ $adoptionPipeline['approved'] }}<br>
                Rejected: {{ $adoptionPipeline['rejected'] }}
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fa-solid fa-clipboard"></i></div>
            </div>
            <div class="stat-value">{{ $pendingReports }}</div>
            <div class="stat-title">Pending Reports</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fa-solid fa-user-plus"></i></div>
            </div>
            <div class="stat-value">{{ $newUsersToday }}</div>
            <div class="stat-title">New Users Today</div>
        </div>
    </div>

    <!-- Analytical Reports Grid -->
    
    <div class="stats-grid" style="margin-top: 2rem;">
    <!-- Pet Inventory Report -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon"><i class="fa-solid fa-dog"></i></div>
        </div>
        <div class="stat-value">{{ $petInventory['total'] ?? 0 }}</div>
        <div class="stat-title">Total Pets Listed</div>
        <div class="stat-desc" style="font-size: 0.9em;">
            Avail: {{ $petInventory['available'] ?? 0 }},
            In Process: {{ $petInventory['in_process'] ?? 0 }},
            Adopted: {{ $petInventory['adopted'] ?? 0 }}<br>
            Avg Stay: {{ $petInventory['avg_stay'] ?? 0 }} days<br>
        </div>
    </div>

    <!-- Communication & Response Rate Report -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon"><i class="fa-solid fa-comments"></i></div>
        </div>
        <div class="stat-value">{{ $commReport['avg_response_time'] ?? 'N/A' }}</div>
        <div class="stat-title">Avg Response Time</div>
        <div class="stat-desc" style="font-size: 0.9em;">
            Unanswered: {{ $commReport['unanswered'] ?? 0 }}<br>
            Peak: {{ $commReport['peak_time'] ?? 'N/A' }}
        </div>
    </div>

    <!-- Shelter Reputation & Feedback Report -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon"><i class="fa-solid fa-star"></i></div>
        </div>
        <div class="stat-value">{{ $feedbackReport['avg_rating'] ?? 'N/A' }}</div>
        <div class="stat-title">Avg Shelter Rating</div>
        <div class="stat-desc" style="font-size: 0.9em;">
            Positive: {{ $feedbackReport['positive'] ?? 0 }},
            Negative: {{ $feedbackReport['negative'] ?? 0 }}<br>
        </div>
    </div>

    <!-- Stray Reports Managed -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon"><i class="fa-solid fa-dove"></i></div>
        </div>
        <div class="stat-value">{{ $strayReport['total'] ?? 0 }}</div>
        <div class="stat-title">Stray Reports Managed</div>
        <div class="stat-desc" style="font-size: 0.9em;">
            Top Area: {{ $strayReport['top_area'] ?? 'N/A' }}
        </div>
    </div>

    <!-- Pet Demographics Report -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon"><i class="fa-solid fa-paw"></i></div>
        </div>
    <div class="stat-title">Pet Demographics</div>
    <div class="stat-desc">
        @foreach($petDemographics['by_species'] as $row)
            {{ $row->species }}: {{ $row->total }}<br>
        @endforeach
        Top Breeds:
        @foreach($petDemographics['top_breeds'] as $row)
            {{ $row->breed }} ({{ $row->total }})<br>
        @endforeach
    </div>
    </div>

    <!-- Rescuer Verifications Report -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon"><i class="fa-solid fa-user-shield"></i></div>
        </div>
        <div class="stat-title">Rescuer Verifications</div>
        <div class="stat-desc" style="font-size: 0.9em;">
            Approved: {{ $rescuerShelterPerformance['rescuer_verifications']['approved'] ?? 0 }}<br>
            Rejected: {{ $rescuerShelterPerformance['rescuer_verifications']['rejected'] ?? 0 }}
        </div>
    </div>

    <!-- Shelter Verifications Report -->
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon"><i class="fa-solid fa-user-shield"></i></div>
        </div>
        <div class="stat-title">Shelter Verifications</div>
        <div class="stat-desc" style="font-size: 0.9em;">
            Approved: {{ $rescuerShelterPerformance['shelter_verifications']['approved'] ?? 0 }}<br>
            Rejected: {{ $rescuerShelterPerformance['shelter_verifications']['rejected'] ?? 0 }}
        </div>
    </div>
</div>
<!-- End Analytical Reports Grid -->

    <!-- Payment Stats Grid -->
    <div class="stats-grid" style="margin-top: 2rem;">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
            </div>
            <div class="stat-value">₱{{ number_format($totalRevenue, 2) }}</div>
            <div class="stat-title">Total Revenue</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fa-solid fa-chart-line"></i></div>
            </div>
            <div class="stat-value">₱{{ number_format($totalCommission, 2) }}</div>
            <div class="stat-title">Commission Earned</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
            </div>
            <div class="stat-value">₱{{ number_format($pendingPayouts, 2) }}</div>
            <div class="stat-title">Pending Payouts</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fa-solid fa-percentage"></i></div>
            </div>
            <div class="stat-value">{{ $successRate }}%</div>
            <div class="stat-title">Payment Success Rate</div>
        </div>
    </div>

    <!-- Charts and Content Grid -->
    <div class="content-grid" style="grid-template-columns: 1fr 1fr; margin-top: 2rem;">
        <!-- Revenue Chart -->
        <div class="content-card">
            <div class="card-header">
                <h2>Monthly Revenue</h2>
            </div>
            <div class="chart-container" style="height: 300px; position: relative;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Provider Payouts -->
        <div class="content-card">
            <div class="card-header">
                <h2>Provider Payouts</h2>
            </div>
            <div class="payout-list">
                @if(isset($payoutSummary))
                    @foreach($payoutSummary['shelters'] ?? [] as $shelterPayout)
                    <div class="payout-item">
                        <div class="payout-info">
                            <div class="payout-name">{{ $shelterPayout->shelter->shelter_name ?? 'Unknown Shelter' }}</div>
                            <div class="payout-count">{{ $shelterPayout->transaction_count }} transactions</div>
                        </div>
                        <div class="payout-amount">₱{{ number_format($shelterPayout->total_payout, 2) }}</div>
                    </div>
                    @endforeach
                    
                    @foreach($payoutSummary['rescuers'] ?? [] as $rescuerPayout)
                    <div class="payout-item">
                        <div class="payout-info">
                            <div class="payout-name">{{ $rescuerPayout->rescuer->organization_name ?? 'Unknown Rescuer' }}</div>
                            <div class="payout-count">{{ $rescuerPayout->transaction_count }} transactions</div>
                        </div>
                        <div class="payout-amount">₱{{ number_format($rescuerPayout->total_payout, 2) }}</div>
                    </div>
                    @endforeach
                @else
                    <div class="payout-item">
                        <div class="payout-info">
                            <div class="payout-name">No payouts available</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="content-grid" style="grid-template-columns: 1fr; margin-top: 2rem;">
        <div class="content-card">
            <div class="card-header">
                <h2>Recent Transactions</h2>
                <a href="{{ route('admin.transactions') }}" class="btn btn-outline">View All</a>
            </div>
            @if (isset($recentTransactions) && count($recentTransactions) > 0)
                <ul class="transaction-list">
                    @foreach ($recentTransactions as $transaction)
                        <li class="list-item">
                            <div class="item-info">
                                <div class="item-title">
                                    {{ $transaction->application->pet->name ?? 'Unknown Pet' }}
                                    <span class="transaction-amount">₱{{ number_format($transaction->total_amount, 2) }}</span>
                                </div>
                                <div class="item-subtitle">{{ $transaction->provider_name ?? 'Unknown Provider' }}</div>
                                <div class="item-subtitle">{{ $transaction->payment_date ? $transaction->payment_date->format('M d, Y H:i') : 'N/A' }}</div>
                            </div>
                            <div class="btn-group">
                                <span class="status status-{{ $transaction->payment_status }}">
                                    {{ ucfirst($transaction->payment_status) }}
                                </span>
                                <button class="btn btn-outline" onclick="viewTransactionDetails({{ $transaction->transaction_id }})">
                                    View Details
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <ul class="transaction-list">
                    <li class="list-item">
                        <div class="item-info">
                            <div class="item-subtitle">No recent transactions</div>
                        </div>
                    </li>
                </ul>
            @endif
        </div>
    </div>

    <!-- Transaction Details Modal -->
    <div id="transactionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Transaction Details</h2>
                <button onclick="closeTransactionModal()" class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="transactionDetails">
                    <!-- Transaction details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Revenue Chart
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart');
            if (ctx) {
                const revenueChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($monthlyRevenue['months'] ?? []),
                        datasets: [{
                            label: 'Total Revenue',
                            data: @json($monthlyRevenue['revenue'] ?? []),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.1
                        }, {
                            label: 'Commission',
                            data: @json($monthlyRevenue['commission'] ?? []),
                            borderColor: 'rgb(16, 185, 129)',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '₱' + value.toLocaleString();
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ₱' + context.parsed.y.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });

        function approveVerification(verificationId, type) {
            if (confirm('Are you sure you want to approve this verification?')) {
                const url = type === 'shelter' ?
                    `/admin/verifications/shelter/${verificationId}/approve` :
                    `/admin/verifications/rescuer/${verificationId}/approve`;

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Error approving verification');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred');
                    });
            }
        }

        function rejectVerification(verificationId, type) {
            const reason = prompt('Please provide a reason for rejection:');
            if (reason) {
                const url = type === 'shelter' ?
                    `/admin/verifications/shelter/${verificationId}/reject` :
                    `/admin/verifications/rescuer/${verificationId}/reject`;

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            reason: reason
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Error rejecting verification');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred');
                    });
            }
        }

        // Transaction Details Modal
        function viewTransactionDetails(transactionId) {
            
            // Show loading state
            document.getElementById('transactionDetails').innerHTML = '<p>Loading...</p>';
            document.getElementById('transactionModal').style.display = 'flex';
            
            // Add CSRF token to headers
            const headers = {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            };
            
            fetch(`/admin/transactions/${transactionId}/details`, {
                method: 'GET',
                headers: headers,
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById('transactionDetails').innerHTML = data.html;
                    } else {
                        document.getElementById('transactionDetails').innerHTML = '<p>Error loading transaction details</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading transaction details:', error);
                    document.getElementById('transactionDetails').innerHTML = '<p>Error loading transaction details: ' + error.message + '</p>';
                });
        }

        function closeTransactionModal() {
            document.getElementById('transactionModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('transactionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTransactionModal();
            }
        });
    </script>
@endpush
