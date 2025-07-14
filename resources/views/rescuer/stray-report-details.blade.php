@php
    // Handle all cases: array, JSON string, or null
    if (is_array($report->image_url)) {
        $imageUrls = $report->image_url;
    } elseif ($report->image_url) {
        $imageUrls = json_decode($report->image_url, true);
    } else {
        $imageUrls = [];
    }

    // Ensure we have a valid array
    $imageUrls = is_array($imageUrls) ? $imageUrls : [];
    $firstImage = !empty($imageUrls) ? $imageUrls[0] : null;
@endphp

<div class="stray-report-details">
    <div class="report-header">
        <div class="report-images">
            <div class="main-image-container">
                <img src="{{ $firstImage ?? 'https://via.placeholder.com/400' }}" alt="Stray Animal" class="report-image">
            </div>
            @if(count($imageUrls) > 1)
                <div class="additional-images">
                    @foreach($imageUrls as $image)
                        <img src="{{ $image }}" alt="Stray Animal Image" class="gallery-image">
                    @endforeach
                </div>
            @endif
        </div>
        
        <div class="report-header-info">
            <div class="info-block">
                <div class="info-label">Report ID:</div>
                <div class="info-value">{{ $report->report_id }}</div>
            </div>
            <div class="status-badge status-{{ $report->status }}">
                {{ ucfirst($report->status) }}
            </div>
        </div>
    </div>

    <div class="report-info">
        <div class="info-block">
            <div class="info-label">📍 Location</div>
            <div class="info-value">{{ $report->location }}</div>
        </div>
        <div class="info-block">
            <div class="info-label">👤 Reporter</div>
            <div class="info-value">{{ $report->reporter_name ?? 'Anonymous' }}</div>
            <div class="info-value small-text">{{ $report->reporter_email ?? 'No contact information' }}</div>
        </div>
        <div class="info-block">
            <div class="info-label">📅 Date Reported</div>
            <div class="info-value">{{ $report->reported_at ? \Carbon\Carbon::parse($report->reported_at)->format('F d, Y') : 'Unknown' }}</div>
        </div>
        <div class="info-block">
            <div class="info-label">📨 Sent to You</div>
            <div class="info-value">{{ $report->sent_at ? \Carbon\Carbon::parse($report->sent_at)->format('F d, Y g:i A') : 'Unknown' }}</div>
        </div>
    </div>

    <div class="report-description">
        <h3>Description</h3>
        <p>{{ $report->description ?? 'No description provided' }}</p>
    </div>

    @if($report->statusLogs && $report->statusLogs->count() > 0)
        <div class="status-history">
            <h3>Status History</h3>
            <ul>
                @foreach($report->statusLogs as $log)
                    <li>
                        <strong>{{ ucfirst($log->status) }}</strong> 
                        on {{ $log->created_at->format('F d, Y g:i A') }}
                        @if($log->notes)
                            <p class="status-note">{{ $log->notes }}</p>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div> 