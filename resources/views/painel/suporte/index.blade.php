@extends('painel.layouts.app')

@section('title', 'Suporte Inteligente')

@push('styles')
<style>
    :root { --primary: #38bdf8; }
    
    .dash-card {
        background: rgba(12, 12, 12, 0.88);
        backdrop-filter: blur(14px);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .chat-container {
        height: calc(100vh - 16rem);
    }

    .bubble-user {
        background: rgba(56, 189, 248, 0.15);
        border: 1px solid rgba(56, 189, 248, 0.2);
        color: #e0f2fe;
    }

    .bubble-bot {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #d1d5db;
    }

    .campo-input {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
        transition: all 0.3s ease;
    }

    .campo-input:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 15px rgba(56, 189, 248, 0.1);
        outline: none;
    }

    /* Estilização da Scrollbar do Chat */
    .chat-scroll::-webkit-scrollbar { width: 4px; }
    .chat-scroll::-webkit-scrollbar-track { background: transparent; }
    .chat-scroll::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
</style>
@endpush

@section('content')
<div x-data="chatSuporte()" class="flex flex-col space-y-6">
    
    <!-- HEADER -->
    <div data-aos="fade-right">
        <h1 class="text-3xl md:text-4xl font-black text-white italic uppercase tracking-tight">
            Suporte <span class="text-sky-400">Inteligente</span>
        </h1>
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-500 mt-2">
            Assistência técnica 24/7 alimentada por IA
        </p>
    </div>

    <!-- CHAT BOX -->
    <div class="dash-card chat-container flex flex-col rounded-[2.5rem] overflow-hidden shadow-2xl" data-aos="fade-up">
        
        <!-- MENSAGENS -->
        <div x-ref="chatbox" class="flex-1 p-6 md:p-10 space-y-8 overflow-y-auto chat-scroll">
            <template x-for="(message, index) in messages" :key="index">
                <div class="flex items-start gap-4 w-full" :class="message.role === 'user' ? 'justify-end' : 'justify-start'">
                    
                    <!-- Avatar Bot -->
                    <div x-show="message.role === 'model'" class="flex-shrink-0 w-10 h-10 rounded-xl bg-sky-400/10 flex items-center justify-center border border-sky-400/20 text-sky-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    
                    <div class="max-w-[85%] md:max-w-[70%]" :class="message.role === 'user' ? 'text-right' : 'text-left'">
                        <p class="text-[9px] font-black uppercase tracking-widest mb-2 opacity-40" x-text="message.role === 'user' ? 'Você' : 'CoelhoBot'"></p>
                        <div class="inline-block rounded-2xl p-4 text-sm leading-relaxed" :class="message.role === 'user' ? 'bubble-user rounded-tr-none' : 'bubble-bot rounded-tl-none'">
                           <div class="whitespace-pre-wrap" x-html="formatMessage(message.text)"></div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Loading Indicator -->
            <div x-show="isLoading" class="flex items-start gap-4 justify-start">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-sky-400/10 flex items-center justify-center border border-sky-400/20 text-sky-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                 <div class="text-left">
                    <p class="text-[9px] font-black uppercase tracking-widest mb-2 opacity-40">CoelhoBot</p>
                    <div class="inline-block rounded-2xl bubble-bot rounded-tl-none p-4">
                        <div class="flex items-center gap-1.5">
                            <div class="h-1.5 w-1.5 bg-sky-400 rounded-full animate-bounce [animation-delay:-0.3s]"></div>
                            <div class="h-1.5 w-1.5 bg-sky-400 rounded-full animate-bounce [animation-delay:-0.15s]"></div>
                            <div class="h-1.5 w-1.5 bg-sky-400 rounded-full animate-bounce"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- INPUT AREA -->
        <div class="p-6 bg-white/[0.02] border-t border-white/5">
            <form @submit.prevent="sendMessage()" class="relative flex items-center">
                <input type="text" x-model="newMessage" :disabled="isLoading" 
                       class="campo-input w-full pl-6 pr-16 py-5 rounded-2xl text-sm placeholder-zinc-600 focus:outline-none"
                       placeholder="Descreva seu problema ou dúvida técnica...">
                
                <button type="submit" :disabled="isLoading"
                        class="absolute right-3 p-3 rounded-xl bg-sky-400 text-black transition-all transform hover:scale-105 active:scale-90 disabled:opacity-50 shadow-lg shadow-sky-400/20">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path d="M3.478 2.404a.75.75 0 00-.926.941l2.432 7.905H13.5a.75.75 0 0 1 0 1.5H4.984l-2.432 7.905a.75.75 0 0 0 .926.94 60.519 60.519 0 0 0 18.445-8.986.75.75 0 0 0 0-1.218A60.517 60.517 0 0 0 3.478 2.404Z" />
                    </svg>
                </button>
            </form>
            <p class="mt-4 text-[9px] text-center text-zinc-600 font-bold uppercase tracking-widest">
                Para suporte humano direto, utilize nossos canais de E-mail ou WhatsApp.
            </p>
        </div>
    </div>
</div>

<script>
    function chatSuporte() {
        return {
            messages: [{ role: 'model', text: 'Olá! Sou o **CoelhoBot**, estou aqui para te auxiliar com qualquer dúvida técnica ou financeira. No que posso ser útil?' }],
            newMessage: '',
            isLoading: false,

            init() {
                this.$nextTick(() => this.scrollToBottom());
            },

            formatMessage(text) {
                if (!text) return '';
                // Formata negrito
                return text.replace(/\*\*(.*?)\*\*/g, '<strong class="text-white font-black">$1</strong>');
            },

            scrollToBottom() {
                const el = this.$refs.chatbox;
                el.scrollTop = el.scrollHeight;
            },

            async sendMessage() {
                if (this.newMessage.trim() === '' || this.isLoading) return;

                this.messages.push({ role: 'user', text: this.newMessage });
                const userText = this.newMessage;
                this.newMessage = '';
                this.isLoading = true;
                
                this.$nextTick(() => this.scrollToBottom());
                
                try {
                    const response = await fetch('{{ route("painel.suporte.chat") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            history: this.messages 
                        })
                    });

                    if (!response.ok) throw new Error('Serviço indisponível.');
                    
                    const data = await response.json();
                    this.messages.push({ role: 'model', text: data.reply });

                } catch (error) {
                    this.messages.push({ role: 'model', text: 'Ocorreu uma instabilidade na conexão. Por favor, tente novamente em instantes.' });
                } finally {
                    this.isLoading = false;
                    this.$nextTick(() => this.scrollToBottom());
                }
            }
        }
    }
</script>
@endsection