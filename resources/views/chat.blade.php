@extends('layouts.app')

@section('title', 'In-App Messages & Chat | Khedma')

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <!-- Chat Container -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xl overflow-hidden flex flex-col md:flex-row h-[calc(100vh-140px)] min-h-[580px]">

        <!-- Left Column: Conversations List -->
        <div class="w-full md:w-80 lg:w-96 border-r border-slate-200 dark:border-slate-800 flex flex-col shrink-0 bg-slate-50/50 dark:bg-slate-900/50">
            <!-- Search & Filter Header -->
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-chat-dots-fill text-blue-600 dark:text-blue-400"></i>
                        <span>Messages</span>
                    </h2>
                    <span id="unreadTotalBadge" class="hidden px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300">
                        0 unread
                    </span>
                </div>
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-2.5 text-xs text-slate-400"></i>
                    <input type="text" id="conversationSearchInput" oninput="filterConversations(this.value)" placeholder="Search conversations..."
                        class="w-full pl-8 pr-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Conversations Scrollable Area -->
            <div id="conversationsLoading" class="p-8 text-center text-xs text-slate-400">
                <div class="inline-block animate-spin rounded-full h-6 w-6 border-2 border-blue-600 border-t-transparent mb-2"></div>
                <div>Loading conversations...</div>
            </div>
            <div id="conversationsEmpty" class="hidden p-8 text-center text-xs text-slate-400">
                <i class="bi bi-chat-square-text text-3xl mb-2 text-slate-300 dark:text-slate-600 block"></i>
                No messages yet.<br>Contact a provider or client to start a chat!
            </div>
            <div id="conversationsList" class="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60"></div>
        </div>

        <!-- Right Column: Active Conversation Messages Area -->
        <div class="flex-1 flex flex-col bg-white dark:bg-slate-900 overflow-hidden">
            <!-- Chat Header -->
            <div id="chatHeader" class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-slate-900 z-10">
                <div class="flex items-center gap-3 min-w-0">
                    <div id="chatAvatar" class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-amber-500 text-white font-extrabold text-sm flex items-center justify-center shrink-0 shadow-sm overflow-hidden">
                        U
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 id="chatParticipantName" class="text-sm font-extrabold text-slate-900 dark:text-white truncate">Select a conversation</h3>
                            <span id="chatParticipantRole" class="hidden px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300">Provider</span>
                        </div>
                        <p id="chatContextSubtitle" class="text-[11px] text-slate-400 truncate">Choose a chat from the left pane</p>
                    </div>
                </div>
            </div>

            <!-- Message Feed -->
            <div id="messageFeed" class="flex-1 p-4 sm:p-6 overflow-y-auto space-y-3 bg-slate-50/30 dark:bg-slate-950/20">
                <div class="h-full flex flex-col items-center justify-center text-center text-slate-400 p-8">
                    <div class="w-16 h-16 rounded-3xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl mb-3 shadow-inner">
                        <i class="bi bi-chat-text-fill"></i>
                    </div>
                    <h4 class="font-extrabold text-sm text-slate-700 dark:text-slate-300">Your Messages</h4>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm">Select an existing conversation on the left, or message a service provider from the dashboard or proposal view.</p>
                </div>
            </div>

            <!-- Message Input Bar -->
            <form id="messageForm" onsubmit="handleSendMessage(event)" class="p-3 sm:p-4 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center gap-2">
                <input type="text" id="messageInput" placeholder="Type your message here..." autocomplete="off" disabled
                    class="flex-1 px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                <button type="submit" id="sendBtn" disabled
                    class="px-4 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-extrabold text-xs transition flex items-center gap-1.5 shadow-md shadow-blue-500/20 shrink-0">
                    <span>Send</span>
                    <i class="bi bi-send-fill text-xs"></i>
                </button>
            </form>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    let activeConversationId = null;
    let conversationsData = [];
    let currentUserId = null;
    let pollInterval = null;

    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('khedma_token');
        if (!token) {
            window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);
            return;
        }

        try {
            const userStr = localStorage.getItem('khedma_user');
            if (userStr) {
                const u = JSON.parse(userStr);
                currentUserId = u.id;
            }

            // Check URL query parameters for direct conversation navigation
            const urlParams = new URLSearchParams(window.location.search);
            const targetConvId = urlParams.get('conversation_id');
            const targetProviderId = urlParams.get('provider_id');

            await loadConversations();

            if (targetConvId) {
                openConversation(parseInt(targetConvId, 10));
            } else if (targetProviderId) {
                const provId = parseInt(targetProviderId, 10);
                const existing = conversationsData.find(c => c.participant && c.participant.id === provId);
                if (existing) {
                    openConversation(existing.id);
                } else {
                    await startConversationWithProvider(provId);
                }
            } else if (conversationsData.length > 0) {
                openConversation(conversationsData[0].id);
            }

            // Start polling every 3 seconds for active chat updates
            pollInterval = setInterval(async () => {
                if (activeConversationId) {
                    await fetchMessagesSilently(activeConversationId);
                }
            }, 3000);

        } catch (e) {
            console.error('Chat initialization error:', e);
        }
    });

    async function loadConversations() {
        const token = localStorage.getItem('khedma_token');
        try {
            const res = await fetch('/api/v1/conversations', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                const data = await res.json();
                conversationsData = data.conversations || [];
                renderConversationsList(conversationsData);
            }
        } catch (e) {
            console.error(e);
        } finally {
            document.getElementById('conversationsLoading').classList.add('hidden');
        }
    }

    async function startConversationWithProvider(providerId) {
        const token = localStorage.getItem('khedma_token');
        try {
            const res = await fetch('/api/v1/conversations', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ provider_id: providerId })
            });

            if (res.ok) {
                const data = await res.json();
                if (data.conversation) {
                    await loadConversations();
                    openConversation(data.conversation.id);
                }
            } else {
                console.error('Failed to start conversation with provider', await res.text());
            }
        } catch (e) {
            console.error('Error starting conversation:', e);
        }
    }

    function renderConversationsList(list) {
        const container = document.getElementById('conversationsList');
        const empty = document.getElementById('conversationsEmpty');
        container.innerHTML = '';

        if (list.length === 0) {
            empty.classList.remove('hidden');
            return;
        }
        empty.classList.add('hidden');

        let totalUnread = 0;

        list.forEach(conv => {
            totalUnread += (conv.unread_count || 0);

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.id = `conv-btn-${conv.id}`;
            btn.onclick = () => openConversation(conv.id);
            btn.className = `w-full text-left p-3.5 flex items-start gap-3 hover:bg-slate-100 dark:hover:bg-slate-800/70 transition cursor-pointer ${activeConversationId === conv.id ? 'bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-600' : ''}`;

            const avatarHtml = conv.participant.avatar_url
                ? `<img src="${conv.participant.avatar_url}" alt="${conv.participant.name}" class="w-10 h-10 rounded-xl object-cover">`
                : `<div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-sm flex items-center justify-center">${(conv.participant.name || 'U').charAt(0).toUpperCase()}</div>`;

            const lastMsgText = conv.last_message ? conv.last_message.body : 'No messages yet';
            const timeText = conv.last_message_at ? new Date(conv.last_message_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';

            btn.innerHTML = `
                <div class="relative shrink-0">
                    ${avatarHtml}
                    ${conv.unread_count > 0 ? `<span class="absolute -top-1 -right-1 w-3 h-3 bg-blue-600 rounded-full border-2 border-white dark:border-slate-900"></span>` : ''}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-1">
                        <span class="font-extrabold text-xs text-slate-900 dark:text-white truncate">${escapeHtml(conv.participant.name)}</span>
                        <span class="text-[10px] text-slate-400 shrink-0">${timeText}</span>
                    </div>
                    <div class="flex items-center justify-between gap-1 mt-0.5">
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate ${conv.unread_count > 0 ? 'font-black text-slate-900 dark:text-white' : ''}">${escapeHtml(lastMsgText)}</p>
                        ${conv.unread_count > 0 ? `<span class="px-1.5 py-0.2 rounded-full text-[9px] font-black bg-blue-600 text-white">${conv.unread_count}</span>` : ''}
                    </div>
                </div>
            `;
            container.appendChild(btn);
        });

        const badge = document.getElementById('unreadTotalBadge');
        if (totalUnread > 0) {
            badge.textContent = `${totalUnread} unread`;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    async function openConversation(convId) {
        activeConversationId = convId;
        const token = localStorage.getItem('khedma_token');

        // Update active UI styling
        document.querySelectorAll('[id^="conv-btn-"]').forEach(el => {
            el.className = el.className.replace('bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-600', '');
        });
        const activeBtn = document.getElementById(`conv-btn-${convId}`);
        if (activeBtn) {
            activeBtn.className += ' bg-blue-50/70 dark:bg-blue-950/40 border-l-4 border-blue-600';
        }

        // Enable inputs
        document.getElementById('messageInput').disabled = false;
        document.getElementById('sendBtn').disabled = false;
        document.getElementById('messageInput').focus();

        try {
            const res = await fetch(`/api/v1/conversations/${convId}/messages`, {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                const data = await res.json();
                renderChatHeader(data.participant, data.job_request, data.booking);
                renderMessages(data.messages || []);
                scrollToBottom();

                // Refresh conversation list to clear unread badge
                await loadConversations();
            }
        } catch (e) {
            console.error('Error loading messages:', e);
        }
    }

    async function fetchMessagesSilently(convId) {
        const token = localStorage.getItem('khedma_token');
        try {
            const res = await fetch(`/api/v1/conversations/${convId}/messages`, {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });
            if (res.ok) {
                const data = await res.json();
                renderMessages(data.messages || []);
            }
        } catch (e) {}
    }

    function renderChatHeader(participant, jobRequest, booking) {
        document.getElementById('chatParticipantName').textContent = participant.name;
        const roleBadge = document.getElementById('chatParticipantRole');
        roleBadge.textContent = participant.role ? participant.role.toUpperCase() : 'USER';
        roleBadge.classList.remove('hidden');

        const avatarContainer = document.getElementById('chatAvatar');
        if (participant.avatar_url) {
            avatarContainer.innerHTML = `<img src="${participant.avatar_url}" alt="${participant.name}" class="w-full h-full object-cover">`;
        } else {
            avatarContainer.textContent = (participant.name || 'U').charAt(0).toUpperCase();
        }

        const sub = document.getElementById('chatContextSubtitle');
        if (jobRequest) {
            sub.textContent = `Regarding Request: "${jobRequest.title}" (${jobRequest.budget} TND)`;
        } else if (booking) {
            sub.textContent = `Regarding Booking #${booking.booking_number}: ${booking.service_title}`;
        } else {
            sub.textContent = `Direct in-app conversation`;
        }
    }

    function renderMessages(messages) {
        const feed = document.getElementById('messageFeed');
        if (messages.length === 0) {
            feed.innerHTML = `
                <div class="h-full flex flex-col items-center justify-center text-center text-slate-400 p-8">
                    <p class="text-xs">No messages yet. Send a greeting to start chatting!</p>
                </div>
            `;
            return;
        }

        feed.innerHTML = messages.map(msg => {
            const isMe = msg.sender_id === currentUserId;
            const timeStr = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

            return `
                <div class="flex flex-col ${isMe ? 'items-end' : 'items-start'}">
                    <div class="max-w-[75%] sm:max-w-md px-4 py-2.5 rounded-2xl text-xs leading-relaxed shadow-xs ${
                        isMe
                            ? 'bg-blue-600 text-white rounded-br-none'
                            : 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white rounded-bl-none border border-slate-200/80 dark:border-slate-700/80'
                    }">
                        <p class="whitespace-pre-wrap break-words">${escapeHtml(msg.body)}</p>
                    </div>
                    <div class="flex items-center gap-1 mt-1 text-[10px] text-slate-400">
                        <span>${timeStr}</span>
                        ${isMe ? (msg.read_at ? '<i class="bi bi-check2-all text-blue-500"></i>' : '<i class="bi bi-check2"></i>') : ''}
                    </div>
                </div>
            `;
        }).join('');
    }

    async function handleSendMessage(e) {
        e.preventDefault();
        const input = document.getElementById('messageInput');
        const text = input.value.trim();
        if (!text || !activeConversationId) return;

        input.value = '';
        const token = localStorage.getItem('khedma_token');

        try {
            const res = await fetch(`/api/v1/conversations/${activeConversationId}/messages`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ body: text })
            });

            if (res.ok) {
                await fetchMessagesSilently(activeConversationId);
                scrollToBottom();
                await loadConversations();
            }
        } catch (e) {
            console.error(e);
        }
    }

    function scrollToBottom() {
        const feed = document.getElementById('messageFeed');
        feed.scrollTop = feed.scrollHeight;
    }

    function filterConversations(val) {
        const q = (val || '').toLowerCase().trim();
        const filtered = conversationsData.filter(c => c.participant.name.toLowerCase().includes(q));
        renderConversationsList(filtered);
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }
</script>
@endsection
