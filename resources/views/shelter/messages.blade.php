@extends ('layouts.messages')

@section('title', 'Messages')

@section('shelter-content')

@php 
    use Illuminate\Support\Str;
    use Carbon\Carbon;
@endphp

<div class="main-container">

    <!-- Conversations List -->
    <div class="conversations">
        @foreach ($partners as $partner)
            <div class="conversation {{ $receiver && $partner->user_id == ($receiver->user_id ?? null) ? 'active' : '' }}"
                onclick="window.location.href='{{ route('shelter.messages', ['receiver_id' => $partner->user_id]) }}'">
                <div class="conversation-header">
                    <span class="conversation-name"> {{ $partner->name }} </span>
                    <span class="conversation-time">
                        {{ $partner->last_message_time ? Carbon::parse($partner->last_message_time)->diffForHumans() : 'Now' }}
                    </span>
                </div>
                <div class="conversation-preview">
                    {{ Str::limit($partner->decrypted_last_message ?? 'No messages yet.', 50) }}
                </div>
            </div>
        @endforeach
    </div>

    <!-- Chat Area -->
    <div class="chat-area">
        <div class="chat-header">
            @if($receiver)
                <img src="{{ $receiver->profile_picture_url ?? asset('images/default-profile.png') }}" alt="Profile Image" class="profile-image">
                <div class="chat-name">{{ $receiver->name }} </div>
            @else
                <div class="chat-name">No Active Chats</div>
            @endif
        </div>

        <div class="chat-messages" id="chat-messages"></div>

        @if($receiver)
            <div class="chat-input">
                <textarea class="message-input" id="message-input" placeholder="Type your message..."></textarea>
                <button class="send-btn">Send</button>
            </div>
        @endif
    </div>
</div>


<input type="hidden" id="receiver-id" value="{{ optional($receiver)->user_id }}">

<input type="hidden" id="current-user-id" value="{{ auth()->id() }}">

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const receiverId = document.getElementById('receiver-id')?.value;
        const currentUserId = document.getElementById('current-user-id')?.value;
        const chatMessages = document.getElementById('chat-messages');

        if (receiverId && currentUserId) {

            // Load old messages
            fetch(`/messages?receiver_id=${receiverId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(res => res.json())
                .then(messages => {
                    messages.forEach(renderMessage);
                    if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
                });

            // Send message
            const sendBtn = document.querySelector('.send-btn');

            if (sendBtn) {
                sendBtn.addEventListener('click', function (event) {
                    // Always get the latest input element at click time
                    const input = document.getElementById('message-input');
                    if (!input || sendBtn.disabled || sendBtn.offsetParent === null) {
                        event.preventDefault();
                        return;
                    }
                    const content = input.value ? input.value.trim() : '';
                    if (!content) {
                        event.preventDefault();
                        return;
                    }

                    fetch('/messages', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            receiver_id: receiverId,
                            message: content
                        })
                    })
                    .then(res => res.json())
                    .then((message) => {
                        if (message && message.message_content && message.sender_id) {
                            renderMessage(message);
                            input.value = '';
                            if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;

                            // --- Update conversation preview and time in sidebar
                            const activeConv = document.querySelector('.conversation.active');
                            if (activeConv) {
                                const preview = activeConv.querySelector('.conversation-preview');
                                if (preview) preview.textContent = message.message_content.length > 50 ? message.message_content.slice(0, 50) + '...' : message.message_content;
                                const time = activeConv.querySelector('.conversation-time');
                                if (time) time.textContent = timeAgo(message.sent_at);
                            }

                        } else if (message && message.errors) {
                            alert('Validation error: ' + Object.values(message.errors).join('\n'));
                        } else {
                            alert('An error occurred. Please try again.');
                        }
                    });
                });
            }

            // Real-time updates
            window.Echo.private(`chat.${currentUserId}`)
            .listen('MessageSent', (e) => {
                console.log('Echo event payload:', e);

                if (String(e.sender_id) === String(receiverId)) {
                    if (typeof renderMessage === "function") {
                        renderMessage(e);
                        if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                }
            });
        }
    });

    // Helper to format time as 'x seconds/minutes/hours/days ago'
    function timeAgo(dateString) {
        const now = new Date();
        const date = new Date(dateString);
        const seconds = Math.floor((now - date) / 1000);
        if (seconds < 60) return 'just now';
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return minutes + (minutes === 1 ? ' minute ago' : ' minutes ago');
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return hours + (hours === 1 ? ' hour ago' : ' hours ago');
        const days = Math.floor(hours / 24);
        if (days === 1) return 'yesterday';
        return days + ' days ago';
    }

    function renderMessage(message) {
        if (!message || !message.message_content || !message.sender_id) {
            console.warn('Invalid message object:', message);
            return;
        }
        const chatMessages = document.getElementById('chat-messages');
        if (!chatMessages) {
            console.error('chatMessages element not found!');
            return;
        }

        const bubble = document.createElement('div');
        bubble.classList.add('message');
        bubble.classList.add(String(message.sender_id) === String(document.getElementById('current-user-id')?.value) ? 'sent' : 'received');

        const content = document.createElement('div');
        content.classList.add('message-content');
        content.textContent = message.message_content;

        const time = document.createElement('div');
        time.classList.add('message-time');
        time.textContent = message.sent_at
            ? new Date(message.sent_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            : '';

        bubble.appendChild(content);
        bubble.appendChild(time);

        chatMessages.appendChild(bubble);
    }
</script>
@endsection