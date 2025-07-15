@extends ('layouts.rescuer-messages')

@section('title', 'Messages')

@section('rescuer-content')

    @php
        use Illuminate\Support\Str;
        use Carbon\Carbon;
    @endphp

    <div class="main-container">
        <!-- Sidebar Toggle Button (for mobile) -->
        <button id="sidebar-toggle" class="sidebar-toggle"
            style="background:#4a90e2; color:#fff; border:none; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.08); padding:8px 18px; cursor:pointer; font-weight:600; letter-spacing:0.5px; width:100%; margin-bottom:8px; display:none;">
            Hide Conversations
        </button>
        <!-- Conversations List -->
        <div class="conversations" id="sidebar-conversations">
            @forelse ($partners as $partner)
                <div class="conversation {{ $receiver && $partner->user_id == ($receiver->user_id ?? null) ? 'active' : '' }}"
                    onclick="window.location.href='{{ route('rescuer.messages', ['receiver_id' => $partner->user_id]) }}'">
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
            @empty
                <div class="no-conversations"
                    style="padding: 48px 0; text-align: center; color: #888; font-size: 1.1rem; letter-spacing: 0.5px;">
                    No conversations found.
                </div>
            @endforelse
        </div>

        <!-- Chat Area -->
        <div class="chat-area">
            <div class="chat-header">
                @if ($receiver)
                    <img src="{{ $receiver->profile_picture_url ?? asset('images/default-profile.png') }}"
                        alt="Profile Image" class="profile-image">
                    <div class="chat-name">{{ $receiver->name }} </div>
                    <button title="Delete Message" class="delete-message-btn"><i class="fa-solid fa-trash"></i></button>
                @else
                    <div class="no-active-chats"
                        style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 220px; width: 100%;">
                        <div class="chat-name" style="font-size: 1.3rem; color: #888; font-weight: 500;">No Active Chats
                        </div>
                    </div>
                @endif
            </div>

            <div class="chat-messages" id="chat-messages"></div>

            @if ($receiver)
                <div class="chat-input">
                    <textarea class="message-input" id="message-input" placeholder="Type your message..."></textarea>
                    <button class="attachments" id="attachments-btn" type="button"><i
                            class="fa-solid fa-upload"></i></button>
                    <input type="file" id="file-input" style="display:none;" accept="image/*,.pdf,.doc,.docx,.txt" />
                    <button class="send-btn" type="button"><i class="fa-solid fa-paper-plane"></i></button>
                </div>
            @endif
        </div>
    </div>

    <input type="hidden" id="receiver-id" value="{{ optional($receiver)->user_id }}">
    <input type="hidden" id="current-user-id" value="{{ auth()->id() }}">

    <!-- Confirmation Modal -->
    <div id="confirm-modal"
        style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); z-index:1000; align-items:center; justify-content:center;">
        <div
            style="background:#fff; border-radius:10px; padding:24px; min-width:320px; max-width:90vw; box-shadow:0 2px 16px rgba(0,0,0,0.2);">
            <div id="modal-preview" style="margin-bottom:16px;"></div>
            <div style="display:flex; gap:12px; justify-content:flex-end;">
                <button id="modal-cancel"
                    style="background:#eee; border:none; padding:8px 16px; border-radius:5px;">Cancel</button>
                <button id="modal-confirm"
                    style="background:rgb(173, 0, 0); color:#fff; border:none; padding:8px 16px; border-radius:5px;">Confirm</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const receiverId = document.getElementById('receiver-id')?.value;
            const currentUserId = document.getElementById('current-user-id')?.value;
            const chatMessages = document.getElementById('chat-messages');

            // Sidebar toggle/collapse logic
            let sidebarCollapsed = false;
            const sidebar = document.getElementById('sidebar-conversations');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const mainContainer = document.querySelector('.main-container');

            function setSidebarCollapsed(collapsed) {
                sidebarCollapsed = collapsed;
                if (window.innerWidth > 900) {
                    sidebar.classList.remove('collapsed');
                    sidebarToggle.textContent = 'Hide Conversations';
                    mainContainer.classList.remove('sidebar-collapsed');
                    return;
                }
                if (collapsed) {
                    sidebar.classList.add('collapsed');
                    sidebarToggle.textContent = 'Show Conversations';
                    mainContainer.classList.add('sidebar-collapsed');
                } else {
                    sidebar.classList.remove('collapsed');
                    sidebarToggle.textContent = 'Hide Conversations';
                    mainContainer.classList.remove('sidebar-collapsed');
                }
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    setSidebarCollapsed(!sidebarCollapsed);
                });
            }

            function handleResize() {
                if (window.innerWidth > 900) {
                    setSidebarCollapsed(false);
                } else {
                    setSidebarCollapsed(true);
                }
            }
            window.addEventListener('resize', handleResize);
            handleResize();

            if (receiverId && currentUserId) {

                // Load old messages
                fetch(`/messages?receiver_id=${receiverId}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(messages => {
                        renderMessages(messages, currentUserId);
                        if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
                        // Mark messages as read after loading
                        fetch('/messages/mark-as-read', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                            },
                            body: JSON.stringify({
                                sender_id: receiverId
                            })
                        });
                    });

                // Ensure send button is type button, not submit
                const sendBtn = document.querySelector('.send-btn');
                if (sendBtn) sendBtn.setAttribute('type', 'button');

                // Prevent Enter key from sending empty messages
                const messageInput = document.getElementById('message-input');
                if (messageInput) {
                    messageInput.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            const content = messageInput.value ? messageInput.value.trim() : '';
                            if (!content) {
                                e.preventDefault();
                            }
                        }
                    });
                }

                // Send message (no modal for text messages)
                const confirmModal = document.getElementById('confirm-modal');
                const modalPreview = document.getElementById('modal-preview');
                const modalCancel = document.getElementById('modal-cancel');
                const modalConfirm = document.getElementById('modal-confirm');
                let pendingAction = null;
                let pendingData = null;

                if (sendBtn) {
                    sendBtn.addEventListener('click', function(event) {
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
                        // Send message directly, no modal
                        fetch('/messages', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .content,
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
                                    document.getElementById('message-input').value = '';
                                    if (chatMessages) chatMessages.scrollTop = chatMessages
                                        .scrollHeight;
                                    // Update conversation preview and time in sidebar
                                    const activeConv = document.querySelector('.conversation.active');
                                    if (activeConv) {
                                        const preview = activeConv.querySelector(
                                            '.conversation-preview');
                                        if (preview) preview.textContent = message.message_content
                                            .length > 50 ? message.message_content.slice(0, 50) +
                                            '...' : message.message_content;
                                        const time = activeConv.querySelector('.conversation-time');
                                        if (time) time.textContent = timeAgo(message.sent_at);
                                    }
                                } else if (message && message.errors) {
                                    alert('Validation error: ' + Object.values(message.errors).join(
                                        '\n'));
                                } else {
                                    alert('An error occurred. Please try again.');
                                }
                            });
                    });
                }

                // File upload logic
                const attachmentsBtn = document.querySelector('.attachments');
                const fileInput = document.getElementById('file-input');
                let selectedFile = null;

                if (attachmentsBtn && fileInput) {
                    attachmentsBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        fileInput.click();
                    });

                    fileInput.addEventListener('change', function() {
                        if (!fileInput.files.length) return;
                        selectedFile = fileInput.files[0];
                        // Preview image or file name in modal
                        if (/image\/.*/.test(selectedFile.type)) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                modalPreview.innerHTML =
                                    `<div style='margin-bottom:8px;'>Send this image?</div><img src='${e.target.result}' style='max-width:200px; max-height:200px; display:block; margin-bottom:8px;'/><div>${selectedFile.name}</div>`;
                                confirmModal.style.display = 'flex';
                                pendingAction = 'upload-file';
                                pendingData = {
                                    file: selectedFile
                                };
                            };
                            reader.readAsDataURL(selectedFile);
                        } else {
                            modalPreview.innerHTML =
                                `<div style='margin-bottom:8px;'>Send this file?</div><div style='padding:12px; background:#f3f4f6; border-radius:6px;'>${selectedFile.name}</div>`;
                            confirmModal.style.display = 'flex';
                            pendingAction = 'upload-file';
                            pendingData = {
                                file: selectedFile
                            };
                        }
                    });
                }

                // Modal button logic
                if (modalCancel && modalConfirm) {
                    modalCancel.onclick = function() {
                        confirmModal.style.display = 'none';
                        pendingAction = null;
                        pendingData = null;
                        if (fileInput) fileInput.value = '';
                    };
                    modalConfirm.onclick = function() {
                        confirmModal.style.display = 'none';
                        if (pendingAction === 'upload-file' && pendingData && pendingData.file) {
                            const formData = new FormData();
                            formData.append('file', pendingData.file);
                            formData.append('receiver_id', receiverId);
                            fetch('/messages/upload', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                            .content
                                    },
                                    body: formData
                                })
                                .then(res => res.json())
                                .then(response => {
                                    if (response.success) {
                                        renderMessage(response.message);
                                    } else {
                                        alert(response.error || 'Upload failed.');
                                    }
                                    if (fileInput) fileInput.value = '';
                                })
                                .catch(() => {
                                    alert('Upload failed.');
                                    if (fileInput) fileInput.value = '';
                                });
                        }
                        pendingAction = null;
                        pendingData = null;
                    };
                }

                // Add delete chat logic using confirm modal
                const deleteBtn = document.querySelector('.delete-message-btn');
                if (deleteBtn) {
                    deleteBtn.addEventListener('click', function() {
                        // Use the existing confirm modal for confirmation
                        const confirmModal = document.getElementById('confirm-modal');
                        const modalPreview = document.getElementById('modal-preview');
                        const modalCancel = document.getElementById('modal-cancel');
                        const modalConfirm = document.getElementById('modal-confirm');
                        if (!confirmModal || !modalPreview || !modalCancel || !modalConfirm) return;
                        modalPreview.innerHTML =
                            `<div style='margin-bottom:8px;'>Are you sure you want to delete all messages in this chat?</div>`;
                        confirmModal.style.display = 'flex';
                        // Remove previous listeners to avoid stacking
                        modalCancel.onclick = function() {
                            confirmModal.style.display = 'none';
                        };
                        modalConfirm.onclick = function() {
                            confirmModal.style.display = 'none';
                            const receiverId = document.getElementById('receiver-id')?.value;
                            fetch(`/messages/${receiverId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content,
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        document.getElementById('chat-messages').innerHTML = '';
                                        showToast('All messages deleted!', 'success');
                                        setTimeout(() => window.location.reload(), 1200);
                                    } else {
                                        showToast('Failed to delete messages.', 'error');
                                    }
                                });
                        };
                    });
                }

                // Toast notification function
                function showToast(message, type = 'success') {
                    let toast = document.createElement('div');
                    toast.textContent = message;
                    toast.style.position = 'fixed';
                    toast.style.top = '32px';
                    toast.style.left = '50%';
                    toast.style.transform = 'translateX(-50%)';
                    toast.style.zIndex = 9999;
                    toast.style.background = type === 'success' ? '#ED3500' : '#e74c3c';
                    toast.style.color = '#fff';
                    toast.style.padding = '14px 28px';
                    toast.style.borderRadius = '8px';
                    toast.style.fontSize = '1rem';
                    toast.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
                    toast.style.opacity = '0';
                    toast.style.transition = 'opacity 0.3s';
                    document.body.appendChild(toast);
                    setTimeout(() => {
                        toast.style.opacity = '1';
                    }, 10);
                    setTimeout(() => {
                        toast.style.opacity = '0';
                        setTimeout(() => toast.remove(), 300);
                    }, 2200);
                }

                // Real-time updates
                window.Echo.private(`chat.${currentUserId}`)
                    .listen('MessageSent', (e) => {
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

        function renderMessages(messages, currentUserId) {
            const chatMessages = document.getElementById('chat-messages');
            chatMessages.innerHTML = '';
            let lastSentIndex = -1;
            // Find last sent index, skipping placeholder
            messages.forEach((msg, idx) => {
                if (msg.message_content === '[No messages found]') return;
                if (String(msg.sender_id) === String(currentUserId)) {
                    lastSentIndex = idx;
                }
            });
            messages.forEach((msg, idx) => {
                if (msg.message_content === '[No messages found]') return; // Skip placeholder
                renderMessage(msg, idx === lastSentIndex, currentUserId);
            });
            if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Update renderMessage to show attachments
        function renderMessage(message, isLastSent = false, currentUserId = null) {
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
            const isSent = String(message.sender_id) === String(currentUserId ?? document.getElementById('current-user-id')
                ?.value);
            bubble.classList.add(isSent ? 'sent' : 'received');

            const content = document.createElement('div');
            content.classList.add('message-content');

            if (message.attachments) {
                const fileUrl = message.file_url || (message.attachments.startsWith('http') ? message.attachments :
                    '/storage/' + message.attachments);
                const fileName = message.original_name || message.attachments.split('/').pop();
                if (/\.(jpg|jpeg|png|gif)$/i.test(fileUrl)) {
                    const img = document.createElement('img');
                    img.src = fileUrl;
                    img.alt = 'Attachment';
                    img.style.maxWidth = '200px';
                    img.style.maxHeight = '200px';
                    content.appendChild(img);
                } else {
                    const link = document.createElement('a');
                    link.href = fileUrl;
                    link.textContent = 'Download attachment';
                    link.target = '_blank';
                    content.appendChild(link);
                    const fileNameBox = document.createElement('div');
                    fileNameBox.style.background = '#f3f4f6';
                    fileNameBox.style.borderRadius = '6px';
                    fileNameBox.style.padding = '8px 12px';
                    fileNameBox.style.marginTop = '8px';
                    fileNameBox.style.fontSize = '14px';
                    fileNameBox.style.color = '#222';
                    fileNameBox.style.display = 'inline-block';
                    fileNameBox.textContent = fileName;
                    content.appendChild(fileNameBox);
                }
            } else {
                content.textContent = message.message_content;
            }

            const time = document.createElement('div');
            time.classList.add('message-time');
            time.textContent = message.sent_at ?
                new Date(message.sent_at).toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                }) :
                '';

            bubble.appendChild(content);
            bubble.appendChild(time);

            // Add 'Seen' indicator ONLY for the last sent message that is read
            if (isSent && isLastSent && (message.is_read === 1 || message.is_read === true)) {
                const seen = document.createElement('div');
                seen.classList.add('message-seen');
                seen.textContent = 'Seen';
                bubble.appendChild(seen);
            }

            chatMessages.appendChild(bubble);
        }
    </script>
@endsection
