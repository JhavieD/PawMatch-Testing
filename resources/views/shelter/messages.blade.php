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
            <div class="conversation {{ $receiver && $partner->user_id == $receiver->user_id ? 'active' : '' }}"
                onclick="window.location.href='{{ route('shelter.messages', ['receiver_id' => $partner->user_id]) }}'">
                <div class="conversation-header">
                    <span class="conversation-name"> {{ $partner->name }} </span>
                    <span class="conversation-time">
                        {{ $partner->last_message_time ? Carbon::parse($partner->last_message_time)->diffForHumans() : 'Now' }}
                    </span>
                </div>
                <div class="conversation-preview">
                    {{ Str::limit($partner->last_message ?? 'No messages yet.', 50) }}
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
        const messageInput = document.getElementById('message-input');
        const sendBtn = document.querySelector('.send-btn');

        if (receiverId && currentUserId) {

            // Load old messages
            fetch(`/messages?receiver_id=${receiverId}`)
                .then(res => res.json())
                .then(messages => {
                    messages.forEach(renderMessage);
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                });

            // Send message
            sendBtn?.addEventListener('click', () => {
                const content = messageInput.value.trim();
                if (!content) return;

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
                .then(message => {
                    renderMessage(message);
                    messageInput.value = '';
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                });
            });

            // Real-time updates
            if (window.Echo) {
                window.Echo.private(`chat.${currentUserId}`)
                    .listen('MessageSent', (e) => {
                        if (e.sender_id == receiverId) {
                            renderMessage(e);
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                        }
                    });
            } else {
                console.error("Echo is not defined.");
            }

            function renderMessage(message) {
                const bubble = document.createElement('div');
                bubble.classList.add('message');
                bubble.classList.add(message.sender_id == currentUserId ? 'sent' : 'received');

                const content = document.createElement('div');
                content.classList.add('message-content');
                content.textContent = message.message_content || message.content;

                const time = document.createElement('div');
                time.classList.add('message-time');
                time.textContent = new Date(message.sent_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                bubble.appendChild(content);
                bubble.appendChild(time);

                chatMessages.appendChild(bubble);
            }
        }
    });
</script>
@endsection