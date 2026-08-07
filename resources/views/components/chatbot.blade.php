@auth
<div
    x-data="pricelyChatbot()"
    x-init="init()"
    class="fixed bottom-6 right-6 z-[200] flex flex-col items-end gap-3 font-sans"
    id="pricely-chatbot"
>
    {{-- ===== CHAT WINDOW ===== --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        x-cloak
        class="w-[340px] sm:w-[380px] bg-white rounded-3xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden"
        style="height: 530px; max-height: calc(100vh - 100px);"
    >
        {{-- ===== HEADER ===== --}}
        <div class="px-4 py-3.5 flex items-center justify-between shrink-0"
             style="background: linear-gradient(135deg, #166534 0%, #15803d 40%, #047857 100%);">
            <div class="flex items-center gap-3">

                {{-- Ka-Ani Logo: rice sprout mascot --}}
                <div class="relative w-10 h-10 shrink-0">
                    <div class="w-10 h-10 rounded-2xl bg-white/15 border border-white/25 flex items-center justify-center shadow-inner">
                        <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7">
                            {{-- Soil base --}}
                            <ellipse cx="18" cy="30" rx="10" ry="3" fill="#a16207" fill-opacity="0.5"/>
                            {{-- Main stem --}}
                            <path d="M18 29 Q17 22 18 14" stroke="#86efac" stroke-width="1.8" stroke-linecap="round"/>
                            {{-- Left leaf --}}
                            <path d="M17.5 20 Q11 17 10 11 Q15 13 17.5 20Z" fill="#4ade80"/>
                            {{-- Right leaf --}}
                            <path d="M18.5 17 Q25 14 26 8 Q21 11 18.5 17Z" fill="#22c55e"/>
                            {{-- Rice grains top-left --}}
                            <ellipse cx="14" cy="9" rx="2.2" ry="3.2" fill="#fde68a" transform="rotate(-20 14 9)"/>
                            {{-- Rice grains top-center --}}
                            <ellipse cx="18" cy="7" rx="2.2" ry="3.2" fill="#fcd34d" transform="rotate(0 18 7)"/>
                            {{-- Rice grains top-right --}}
                            <ellipse cx="22" cy="9" rx="2.2" ry="3.2" fill="#fde68a" transform="rotate(20 22 9)"/>
                            {{-- Rice sheen lines --}}
                            <path d="M13.5 7.5 Q14 8.5 13.8 10" stroke="#fef9c3" stroke-width="0.7" stroke-linecap="round" opacity="0.8"/>
                            <path d="M18 5.5 Q18.2 6.5 18 8.5" stroke="#fef9c3" stroke-width="0.7" stroke-linecap="round" opacity="0.8"/>
                            <path d="M22.5 7.5 Q22 8.5 22.2 10" stroke="#fef9c3" stroke-width="0.7" stroke-linecap="round" opacity="0.8"/>
                        </svg>
                    </div>
                    {{-- Online dot --}}
                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 border-2 border-white rounded-full shadow"></span>
                </div>

                <div>
                    <p class="text-white font-bold text-sm leading-tight tracking-wide">Ka-Ani 🌾</p>
                    <p class="text-emerald-200 text-[10px] mt-0.5 leading-none">AI Assistant · Farmers Support</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5">
                {{-- Clear chat button --}}
                <button
                    @click="clearChat()"
                    x-show="messages.length > 0"
                    title="Clear conversation"
                    class="w-7 h-7 rounded-full bg-white/15 hover:bg-white/30 flex items-center justify-center transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                </button>
                {{-- Close button --}}
                <button @click="open = false"
                        class="w-7 h-7 rounded-full bg-white/15 hover:bg-white/30 flex items-center justify-center transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- ===== PINNED WELCOME MESSAGE (shown only when no messages) ===== --}}
        <div x-show="messages.length === 0 && !loading" class="px-4 pt-3 pb-2 shrink-0">
            <div class="flex items-start gap-2">
                <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 mt-0.5 border border-green-200 bg-green-50">
                    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                        <path d="M18 29 Q17 22 18 14" stroke="#86efac" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M17.5 20 Q11 17 10 11 Q15 13 17.5 20Z" fill="#4ade80"/>
                        <path d="M18.5 17 Q25 14 26 8 Q21 11 18.5 17Z" fill="#22c55e"/>
                        <ellipse cx="14" cy="9" rx="2.2" ry="3.2" fill="#fde68a" transform="rotate(-20 14 9)"/>
                        <ellipse cx="18" cy="7" rx="2.2" ry="3.2" fill="#fcd34d"/>
                        <ellipse cx="22" cy="9" rx="2.2" ry="3.2" fill="#fde68a" transform="rotate(20 22 9)"/>
                    </svg>
                </div>
                <div class="bg-gradient-to-br from-slate-50 to-green-50 border border-green-100 rounded-2xl rounded-tl-sm px-3 py-2.5 shadow-sm">
                    <p class="text-slate-700 text-xs leading-relaxed">Kumusta! Ako si <strong class="text-green-700">Ka-Ani</strong> 🌾<br>Paano kita matutulungan ngayon? Magtanong tungkol sa presyo ng ani, SMS alerts, o paggamit ng Pricely app!</p>
                </div>
            </div>
        </div>

        {{-- ===== MESSAGES (only shown when conversation started) ===== --}}
        <div
            x-show="messages.length > 0 || loading"
            class="flex-1 overflow-y-auto px-4 py-3 space-y-3 scroll-smooth"
            id="chatbot-messages"
            x-ref="messages"
        >
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex items-start gap-2'">
                    {{-- Bot avatar --}}
                    <div x-show="msg.role === 'model'"
                         class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 mt-0.5 border border-green-200 bg-green-50">
                        <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                            <path d="M18 29 Q17 22 18 14" stroke="#86efac" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M17.5 20 Q11 17 10 11 Q15 13 17.5 20Z" fill="#4ade80"/>
                            <path d="M18.5 17 Q25 14 26 8 Q21 11 18.5 17Z" fill="#22c55e"/>
                            <ellipse cx="14" cy="9" rx="2.2" ry="3.2" fill="#fde68a" transform="rotate(-20 14 9)"/>
                            <ellipse cx="18" cy="7" rx="2.2" ry="3.2" fill="#fcd34d"/>
                            <ellipse cx="22" cy="9" rx="2.2" ry="3.2" fill="#fde68a" transform="rotate(20 22 9)"/>
                        </svg>
                    </div>
                    {{-- Bubble --}}
                    <div
                        :class="msg.role === 'user'
                            ? 'bg-gradient-to-br from-green-600 to-emerald-700 text-white rounded-2xl rounded-tr-sm px-3 py-2 max-w-[85%] shadow-sm'
                            : 'bg-gradient-to-br from-slate-50 to-green-50 border border-green-100 text-slate-700 rounded-2xl rounded-tl-sm px-3 py-2 max-w-[85%] shadow-sm'"
                    >
                        <p class="text-xs leading-relaxed whitespace-pre-wrap" x-text="msg.text"></p>
                    </div>
                </div>
            </template>

            {{-- Typing indicator --}}
            <div x-show="loading" class="flex items-start gap-2">
                <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 border border-green-200 bg-green-50">
                    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                        <path d="M18 29 Q17 22 18 14" stroke="#86efac" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M17.5 20 Q11 17 10 11 Q15 13 17.5 20Z" fill="#4ade80"/>
                        <path d="M18.5 17 Q25 14 26 8 Q21 11 18.5 17Z" fill="#22c55e"/>
                        <ellipse cx="14" cy="9" rx="2.2" ry="3.2" fill="#fde68a" transform="rotate(-20 14 9)"/>
                        <ellipse cx="18" cy="7" rx="2.2" ry="3.2" fill="#fcd34d"/>
                        <ellipse cx="22" cy="9" rx="2.2" ry="3.2" fill="#fde68a" transform="rotate(20 22 9)"/>
                    </svg>
                </div>
                <div class="bg-gradient-to-br from-slate-50 to-green-50 border border-green-100 rounded-2xl rounded-tl-sm px-3 py-2.5 flex items-center gap-1 shadow-sm">
                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                </div>
            </div>
        </div>

        {{-- ===== SUGGESTED QUESTIONS (scrollable, only when no messages) ===== --}}
        <div x-show="messages.length === 0 && !loading" class="px-3 pb-2 shrink-0 space-y-2 overflow-y-auto" style="max-height: 240px;">
            <template x-for="group in suggestionGroups" :key="group.label">
                <div>
                    <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-1 px-1" x-text="group.label"></p>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="q in group.items" :key="q">
                            <button
                                @click="sendSuggestion(q)"
                                class="text-[10px] bg-green-50 hover:bg-green-100 active:scale-95 text-green-800 border border-green-200 rounded-full px-2.5 py-1 transition-all cursor-pointer leading-tight"
                                x-text="q"
                            ></button>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        {{-- ===== INPUT ===== --}}
        <div class="border-t border-slate-100 px-3 py-3 flex items-end gap-2 shrink-0 bg-white">
            <textarea
                x-model="input"
                @keydown.enter.prevent="!$event.shiftKey && sendMessage()"
                placeholder="Magtanong kay Ka-Ani..."
                rows="1"
                :disabled="loading"
                class="flex-1 resize-none bg-slate-50 border border-slate-200 rounded-2xl px-3 py-2 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition-all disabled:opacity-60"
                style="max-height: 80px; overflow-y: auto;"
                @input="autoResize($event.target)"
                id="chatbot-input"
            ></textarea>
            <button
                @click="sendMessage()"
                :disabled="loading || !input.trim()"
                class="w-8 h-8 disabled:opacity-50 disabled:cursor-not-allowed rounded-full flex items-center justify-center transition-all shrink-0 shadow-sm hover:shadow-md active:scale-95"
                style="background: linear-gradient(135deg, #166534, #047857);"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
            </button>
        </div>
    </div>

    {{-- ===== TOGGLE BUTTON ===== --}}
    <div class="relative group flex flex-col items-end gap-2">

        {{-- Hover tooltip --}}
        <div class="absolute bottom-full right-0 mb-3 pointer-events-none">
            <div class="opacity-0 group-hover:opacity-100 translate-y-1 group-hover:translate-y-0 transition-all duration-200 bg-gray-900 text-white text-xs font-medium px-3 py-1.5 rounded-xl whitespace-nowrap shadow-lg">
                Ask questions from Ka-Ani 🌾
                <div class="absolute top-full right-5 border-4 border-transparent border-t-gray-900"></div>
            </div>
        </div>

        <button
            @click="open = !open"
            class="relative w-16 h-16 rounded-full shadow-xl hover:shadow-2xl flex items-center justify-center transition-all duration-300 hover:scale-110 active:scale-95 overflow-hidden"
            style="background: linear-gradient(145deg, #14532d 0%, #166534 35%, #15803d 65%, #047857 100%);"
            id="chatbot-toggle-btn"
        >
            {{-- Unread badge --}}
            <span x-show="!open && unread > 0"
                  class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center z-10 shadow"
                  x-text="unread"></span>

            {{-- Ring pulse when closed --}}
            <span x-show="!open" class="absolute inset-0 rounded-full animate-ping opacity-20" style="background:#15803d;"></span>

            {{-- Ka-Ani Logo when closed --}}
            <div x-show="!open" class="flex flex-col items-center justify-center gap-0.5 z-10">
                <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8">
                    <path d="M18 33 Q17 24 18 13" stroke="#bbf7d0" stroke-width="2" stroke-linecap="round"/>
                    <path d="M17.2 21 Q9 18 8 10 Q14 12 17.2 21Z" fill="#4ade80"/>
                    <path d="M18.8 17 Q27 13 28 5 Q22 9 18.8 17Z" fill="#22c55e"/>
                    <ellipse cx="13" cy="8" rx="2.5" ry="3.8" fill="#fde68a" transform="rotate(-22 13 8)"/>
                    <ellipse cx="18" cy="5.5" rx="2.5" ry="3.8" fill="#fcd34d"/>
                    <ellipse cx="23" cy="8" rx="2.5" ry="3.8" fill="#fde68a" transform="rotate(22 23 8)"/>
                    <path d="M12.5 6 Q13 7.5 12.8 9.5" stroke="#fef9c3" stroke-width="0.8" stroke-linecap="round" opacity="0.9"/>
                    <path d="M18 3.5 Q18.3 5 18 7.5" stroke="#fef9c3" stroke-width="0.8" stroke-linecap="round" opacity="0.9"/>
                    <path d="M23.5 6 Q23 7.5 23.2 9.5" stroke="#fef9c3" stroke-width="0.8" stroke-linecap="round" opacity="0.9"/>
                </svg>
            </div>

            {{-- X icon when open --}}
            <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<script>
function pricelyChatbot() {
    return {
        open: false,
        loading: false,
        input: '',
        messages: [],
        unread: 0,
        suggestions: [],
        suggestionGroups: [
            {
                label: '🗺️ Map & Prices',
                items: [
                    'Saan makikita ang presyo ng ani?',
                    'Paano gamitin ang Shop Map?',
                    'Ano ang presyo ng Palay ngayon?',
                    'Sino ang mga buyers sa malapit?',
                ]
            },
            {
                label: '📱 SMS Alerts',
                items: [
                    'Paano gumagana ang SMS alerts?',
                    'Paano mag-subscribe sa isang shop?',
                    'Libre ba ang SMS alerts?',
                    'Bakit hindi ako nakakatanggap ng SMS?',
                ]
            },
            {
                label: '📊 Price Forecasting',
                items: [
                    'Ano ang Price Forecasting?',
                    'Kailan magandang ibenta ang ani?',
                    'Paano basahin ang trend chart?',
                ]
            },
            {
                label: '⚙️ Account & Setup',
                items: [
                    'Paano mag-verify ng phone?',
                    'Paano mag-register?',
                    'Paano i-update ang profile?',
                    'Ano ang DA Ceiling Prices?',
                ]
            },
        ],

        init() {
            this.$watch('open', (val) => {
                if (val) {
                    this.unread = 0;
                    this.$nextTick(() => this.scrollToBottom());
                }
            });
        },

        autoResize(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 80) + 'px';
        },

        scrollToBottom() {
            const el = this.$refs.messages;
            if (el) el.scrollTop = el.scrollHeight;
        },

        clearChat() {
            this.messages = [];
            this.unread = 0;
        },

        sendSuggestion(text) {
            this.input = text;
            this.sendMessage();
        },

        async sendMessage() {
            const text = this.input.trim();
            if (!text || this.loading) return;

            this.input = '';
            this.$nextTick(() => {
                const ta = document.getElementById('chatbot-input');
                if (ta) ta.style.height = 'auto';
            });

            this.messages.push({ role: 'user', text });
            this.loading = true;
            this.$nextTick(() => this.scrollToBottom());

            const history = this.messages.slice(0, -1).map(m => ({
                role: m.role,
                text: m.text,
            }));

            try {
                const response = await fetch('{{ route('chatbot.chat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ message: text, history }),
                });

                const data = await response.json();

                if (data.reply) {
                    this.messages.push({ role: 'model', text: data.reply });
                    if (!this.open) this.unread++;
                } else {
                    this.messages.push({ role: 'model', text: data.error || 'Sorry, something went wrong.' });
                }
            } catch (e) {
                this.messages.push({ role: 'model', text: 'Hindi ako makakonekta sa server. Subukan ulit.' });
            } finally {
                this.loading = false;
                this.$nextTick(() => this.scrollToBottom());
            }
        }
    };
}
</script>
@endauth
