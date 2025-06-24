@extends('layouts.adopter-messages')


@section('title', 'Messages - PawMatch')

@section('adopter-content')

@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;
@endphp

    <div class="main-container">
        <!-- Conversations List -->
        <div class="conversations">
            @foreach ($partners as $partner)
                <div class="conversation {{ $receiver && $partner->user_id == $receiver->user_id ? 'active' : '' }}"
                    onclick="window.location.href='{{ route('adopter.messages', ['receiver_id' => $partner->user_id]) }}'">
                    <div class="conversation-header">
                        <span class="conversation-name">{{ $partner->shelterProfile->shelter_name ?? 'Unknown Shelter' }}</span>
                        <span class="conversation-time">
                            {{ $partner->last_message_time ? \Carbon\Carbon::parse($partner->last_message_time)->diffForHumans() : '' }}
                        </span>
                    </div>
                    <div class="conversation-preview">
                        {{ !empty($partner->last_message) ? \Illuminate\Support\Str::limit($partner->last_message, 50) : 'No messages yet.' }}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Chat Area -->
        <div class="chat-area">
            <div class="chat-header">
                @if($receiver)
                    <img src="{{ $receiver->profile_picture_url ?? 'https://via.placeholder.com/40' }}" alt="Profile Image" class="profile-image" />
                    <div class="chat-name">{{ $receiver->shelterProfile->shelter_name ?? 'Unknown Shelter' }}</div>
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

    <input type="hidden" id="receiver-id" value="{{ $receiver->user_id ?? '' }}">
    <input type="hidden" id="current-user-id" value="{{ auth()->id() }}">

<script>
        const receiverId = Number("{{ $receiver?->user_id ?? 0 }}");
        const currentUserId = Number("{{ auth()->id() }}");
        const chatMessages = document.getElementById('chat-messages');
        const sendBtn = document.querySelector('.send-btn');

        if (receiverId) {
            fetch(`/messages?receiver_id=${receiverId}`)
                .then(res => res.json())
                .then(messages => {
                    if (chatMessages) {
                        messages.forEach(message => {
                            if (message && message.message_content && message.sender_id) {
                                renderMessage(message);
                            }
                        });
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                });

            if (sendBtn) {
                sendBtn.addEventListener('click', function (event) {
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

                    // CSRF token null check
                    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                    const csrfToken = csrfTokenMeta ? csrfTokenMeta.content : '';

                    fetch('/messages', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            receiver_id: receiverId,
                            message: content
                        })
                    })
                    .then(res => res.json())
                    .then(message => {
                        if (message && message.message_content && message.sender_id) {
                            renderMessage(message);
                            input.value = '';
                            if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;

                            // --- Update conversation preview and time in sidebar ---
                            const activeConv = document.querySelector('.conversation.active');
                            if (activeConv) {
                                const preview = activeConv.querySelector('.conversation-preview');
                                if (preview) preview.textContent = message.message_content.length > 50 ? message.message_content.slice(0, 50) + '...' : message.message_content;
                                const time = activeConv.querySelector('.conversation-time');
                                if (time) time.textContent = timeAgo(message.sent_at);
                            }
                            // --------------------------------------------------------
                        } else if (message && message.errors) {
                            alert('Validation error: ' + Object.values(message.errors).join('\n'));
                        } else {
                            alert('An error occurred. Please try again.');
                        }
                    });
                });
            }

            window.Echo.private(`chat.${currentUserId}`)
                .listen('MessageSent', (e) => {
                    if (e.message && e.message.sender_id === receiverId) {
                        if (chatMessages && e.message && e.message.message_content && e.message.sender_id) {
                            renderMessage(e.message);
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                        }
                    }
                });

            function renderMessage(message) {
                if (!message || !message.message_content || !message.sender_id) return;
                if (!chatMessages) return;
                const bubble = document.createElement('div');
                bubble.classList.add('message');
                bubble.classList.add(message.sender_id === currentUserId ? 'sent' : 'received');

                const content = document.createElement('div');
                content.classList.add('message-content');
                content.textContent = message.message_content;

                const time = document.createElement('div');
                time.classList.add('message-time');
                time.textContent = new Date(message.sent_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                bubble.appendChild(content);
                bubble.appendChild(time);

                chatMessages.appendChild(bubble);
            }

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
        }
</script>
@endsection