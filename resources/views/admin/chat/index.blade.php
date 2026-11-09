@extends('layouts.admin')

@section('title', 'Monitoring Chat - SayaBantu.com')
@section('page-title', 'Monitoring Chat')
@section('page-subtitle', 'Pemantauan percakapan Customer dan Mitra yang tersimpan di sistem')

@section('hero')
<div class="px-5 pt-1 pb-0">
    {{-- STAT RINGKASAN MONITORING CHAT --}}
    <div class="p-4 rounded-[22px] bg-white/[0.08] backdrop-blur-md border border-white/15 shadow-xl">
        <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-white/10">
            <span class="text-xs font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-comments text-sky-400"></i>
                Pemeriksaan Komunikasi
            </span>
            <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                Data Percakapan
            </span>
        </div>

        <div class="grid grid-cols-3 gap-2 text-center">
            <div class="p-2 rounded-xl bg-white/[0.04] border border-white/10 text-center">
                <div class="text-xs font-black text-sky-400">{{ $conversationCount }}</div>
                <div class="text-[8px] text-gray-300 mt-0.5">Percakapan</div>
            </div>
            <div class="p-2 rounded-xl bg-white/[0.04] border border-white/10 text-center">
                <div class="text-xs font-black text-sky-300">{{ $customerMessageCount }}</div>
                <div class="text-[8px] text-gray-300 mt-0.5">Pesan Customer</div>
            </div>
            <div class="p-2 rounded-xl bg-white/[0.04] border border-white/10 text-center">
                <div class="text-xs font-black text-emerald-300">{{ $mitraMessageCount }}</div>
                <div class="text-[8px] text-gray-300 mt-0.5">Pesan Mitra</div>
            </div>
        </div>
    </div>

    <div class="mt-3 p-2.5 rounded-xl bg-[#0E1D31]/80 border border-sky-400/20 text-[9px] text-sky-200 flex items-center gap-2">
        <i class="fa-solid fa-lock text-sky-400 text-xs shrink-0"></i>
        <span>Percakapan Customer dan Mitra ditampilkan dari pesan yang tersimpan dan hanya dapat dibaca Admin.</span>
    </div>

    {{-- SEARCH BAR --}}
    <div class="mt-3">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input
                type="text"
                id="searchChatInput"
                placeholder="Cari ID chat, no suruhan, nama pihak..."
                onkeyup="searchChats(this.value)"
                class="w-full pl-9 pr-8 py-2.5 rounded-xl bg-white/10 text-white placeholder-gray-400 text-xs border border-white/15 focus:outline-none focus:border-sky-400 focus:bg-white/15 transition-all"
            >
            <button
                type="button"
                id="clearSearchChatBtn"
                onclick="clearSearchChats()"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white text-xs hidden"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-3" style="margin-top: 0.6cm;">

    <div id="chatList" class="space-y-3">
        @forelse($conversations as $conversation)
        <article
            class="chat-card p-4 bg-white rounded-[22px] border border-gray-200/80 shadow-sm"
            data-chat-id="{{ $conversation['chat_id'] }}"
            data-order-id="{{ $conversation['order_code'] }}"
            data-customer="{{ $conversation['customer_name'] }}"
            data-mitra="{{ $conversation['mitra_name'] }}"
            data-service="{{ $conversation['service'] }}"
            data-messages="{{ json_encode($conversation['messages'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
        >
            <button type="button" onclick="openChatModal(this.closest('.chat-card'))" class="w-full text-left">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black text-gray-900">{{ $conversation['order_code'] }}</span>
                            <span class="text-[9px] text-sky-700 bg-sky-50 border border-sky-100 rounded-full px-2 py-0.5">{{ $conversation['message_count'] }} pesan</span>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-1">{{ $conversation['service'] }} · {{ $conversation['status'] }}</p>
                    </div>
                    <span class="text-[9px] text-gray-400 shrink-0">{{ $conversation['last_time'] }}</span>
                </div>
                <div class="mt-3 flex items-center gap-2 text-[10px]">
                    <span class="font-bold text-sky-700">{{ $conversation['customer_name'] }}</span>
                    <i class="fa-solid fa-arrow-right text-[8px] text-gray-400"></i>
                    <span class="font-bold text-emerald-700">{{ $conversation['mitra_name'] }}</span>
                </div>
                <p class="mt-2 text-[10px] text-gray-700 line-clamp-2">{{ $conversation['last_message'] }}</p>
                <span class="mt-3 inline-flex items-center gap-1 text-[10px] font-bold text-sky-700">
                    Buka percakapan <i class="fa-solid fa-chevron-right text-[8px]"></i>
                </span>
            </button>
        </article>
        @empty
        <div class="p-8 text-center bg-white rounded-[22px] border border-gray-200/80 shadow-sm space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl mx-auto border border-sky-100">
                <i class="fa-solid fa-comments"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-gray-900">Belum Ada Percakapan</h4>
                <p class="text-[10px] text-gray-500 mt-0.5 leading-relaxed max-w-xs mx-auto">
                    Pesan antara Customer dan Mitra akan muncul di sini setelah mereka memulai percakapan.
                </p>
            </div>
        </div>
        @endforelse
    </div>

</div>

{{-- MODAL VIEWER TRANSKRIP PERCAKAPAN LENGKAP --}}
<div id="chatViewerModal" class="sb-overlay-backdrop fixed inset-0 z-[130] pointer-events-none opacity-0 invisible" aria-hidden="true">
    <div class="sb-backdrop-click absolute inset-0 bg-black/65 backdrop-blur-[2px]" onclick="closeChatModal()"></div>

    <div class="relative mx-auto w-full max-w-[430px] h-full pointer-events-none flex flex-col justify-end">
        <div id="chatViewerPanel" class="sb-sheet-panel relative w-full bg-[#0E1D31] text-white pointer-events-auto rounded-t-[26px] shadow-2xl p-5 border-t border-white/15 max-h-[92vh] flex flex-col translate-y-full transition-transform">
            
            {{-- HEADER VIEWER --}}
            <div class="pb-3 border-b border-white/10 shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm border border-emerald-500/30">
                            <i class="fa-solid fa-comments"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white leading-none" id="viewerChatId">Percakapan</h3>
                                                <p class="text-[10px] text-gray-400 mt-1" id="viewerPartiesInfo"></p>
                        </div>
                    </div>
                    <button type="button" onclick="closeChatModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>

                <div class="mt-2.5 p-2 rounded-xl bg-white/5 border border-white/10 text-[9.5px] flex items-center justify-between text-gray-300">
                    <span id="viewerSuruhanInfo">Suruhan</span>
                    <span class="text-emerald-400 font-mono text-[9px] flex items-center gap-1">
                        <i class="fa-solid fa-eye"></i> Baca saja
                    </span>
                </div>
            </div>

            {{-- KONTEN BUBBLE PERCAKAPAN (SCROLLABLE) --}}
            <div id="chatMessagesContainer" class="flex-1 overflow-y-auto py-4 space-y-3 custom-scrollbar text-xs">
                {{-- Filled by JS --}}
            </div>

            <div class="pt-3 border-t border-white/10 shrink-0">
                <p class="text-[9px] text-gray-400 text-center">Transkrip ditampilkan untuk pemantauan dan tidak dapat diubah dari halaman ini.</p>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.searchChats = searchChats;
    window.clearSearchChats = clearSearchChats;

    function searchChats(keyword) {
        keyword = keyword.toLowerCase().trim();
        const clearBtn = document.getElementById('clearSearchChatBtn');
        if (clearBtn) clearBtn.classList.toggle('hidden', keyword === '');

        const cards = document.querySelectorAll('.chat-card');
        cards.forEach(card => {
            const id = (card.getAttribute('data-chat-id') || '').toLowerCase();
            const orderId = (card.getAttribute('data-order-id') || '').toLowerCase();
            const cust = (card.getAttribute('data-customer') || '').toLowerCase();
            const mitra = (card.getAttribute('data-mitra') || '').toLowerCase();
            const service = (card.getAttribute('data-service') || '').toLowerCase();

            if (id.includes(keyword) || orderId.includes(keyword) || cust.includes(keyword) || mitra.includes(keyword) || service.includes(keyword)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function clearSearchChats() {
        const input = document.getElementById('searchChatInput');
        if (input) input.value = '';
        searchChats('');
    }

    function openChatModal(card) {
        document.getElementById('viewerChatId').innerText = card.getAttribute('data-chat-id');
        document.getElementById('viewerPartiesInfo').innerText = `${card.getAttribute('data-customer')} (Customer) & ${card.getAttribute('data-mitra')} (Mitra)`;
        document.getElementById('viewerSuruhanInfo').innerText = `Suruhan: ${card.getAttribute('data-order-id')} • ${card.getAttribute('data-service')}`;

        const container = document.getElementById('chatMessagesContainer');
        container.innerHTML = '';

        const rawMsgs = card.getAttribute('data-messages') || '[]';
        try {
            const messages = JSON.parse(rawMsgs);
            messages.forEach(msg => {
                const isCust = (msg.role === 'Customer');
                const bubbleBox = document.createElement('div');
                bubbleBox.className = `flex flex-col ${isCust ? 'items-start' : 'items-end'}`;

                const messageInfo = document.createElement('div');
                messageInfo.className = 'flex items-center gap-1.5 mb-1 text-[9px] text-gray-400';
                const sender = document.createElement('strong');
                sender.className = isCust ? 'text-sky-300' : 'text-emerald-300';
                sender.textContent = `${msg.sender} (${msg.role})`;
                const time = document.createElement('span');
                time.textContent = `• ${msg.time}`;
                messageInfo.append(sender, time);

                const messageText = document.createElement('div');
                messageText.className = `max-w-[85%] p-3 rounded-2xl ${isCust ? 'bg-white/10 text-white rounded-tl-sm border border-white/10' : 'bg-[#173B67] text-sky-100 rounded-tr-sm border border-sky-400/30'} shadow-sm leading-relaxed text-xs`;
                messageText.textContent = msg.text;

                bubbleBox.append(messageInfo, messageText);
                container.appendChild(bubbleBox);
            });
        } catch (e) {
            container.innerHTML = '<p class="text-gray-400 text-center py-6">Tidak ada transkrip pesan tersimpan.</p>';
        }

        const modal = document.getElementById('chatViewerModal');
        const panel = document.getElementById('chatViewerPanel');
        modal.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
        modal.classList.add('opacity-100', 'pointer-events-auto');
        panel.classList.remove('translate-y-full');
    }

    function closeChatModal() {
        const modal = document.getElementById('chatViewerModal');
        const panel = document.getElementById('chatViewerPanel');
        panel.classList.add('translate-y-full');
        setTimeout(() => {
            modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
            modal.classList.remove('opacity-100', 'pointer-events-auto');
        }, 200);
    }

</script>
@endpush
