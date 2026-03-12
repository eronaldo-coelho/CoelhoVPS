@extends('painel.layouts.app')

@section('title', 'Meu Perfil')

@push('styles')
<style>
    :root { --primary: #38bdf8; }
    
    .dash-card {
        background: rgba(12, 12, 12, 0.88);
        backdrop-filter: blur(14px);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    /* Input profissional e visível */
    .campo-input {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
        width: 100%;
        padding: 1rem 1.25rem;
        border-radius: 1rem;
        transition: all 0.3s ease;
        position: relative;
        z-index: 10;
    }

    .campo-input:focus {
        border-color: var(--primary) !important;
        background: rgba(56, 189, 248, 0.03) !important;
        box-shadow: 0 0 15px rgba(56, 189, 248, 0.1);
        outline: none;
    }

    .campo-input::placeholder {
        color: #3f3f46;
    }

    label {
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div x-data="perfilHandler()">
    
    <!-- HEADER -->
    <div class="mb-10" data-aos="fade-right">
        <h1 class="text-4xl font-black text-white italic uppercase tracking-tight">
            Meu <span class="text-sky-400">Perfil</span>
        </h1>
        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-500 mt-2">
            Configurações de conta e faturamento
        </p>
    </div>

    <div class="max-w-4xl">
        <!-- FEEDBACKS -->
        @if (session('success'))
            <div class="mb-8 rounded-2xl bg-sky-400/10 border border-sky-400/20 p-4" data-aos="fade-up">
                <p class="text-xs font-black text-sky-400 uppercase tracking-widest italic">{{ session('success') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-8 rounded-2xl bg-red-500/10 border border-red-500/20 p-4" data-aos="fade-up">
                <ul class="text-xs font-black text-red-500 uppercase tracking-tight list-disc list-inside">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('painel.perfil.update') }}" method="POST" class="space-y-8" @submit="isProcessing = true">
            @csrf
            @method('PUT')

            <!-- DADOS DE ACESSO -->
            <div class="dash-card rounded-[2.5rem] p-8 md:p-10 shadow-2xl shadow-sky-400/5" data-aos="fade-up">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-10 w-10 rounded-xl bg-sky-400/10 flex items-center justify-center text-sky-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h2 class="text-xl font-black text-white italic uppercase tracking-tight">Dados de <span class="text-sky-400">Acesso</span></h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                    <div class="md:col-span-6">
                        <label class="text-[9px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">E-mail Principal</label>
                        <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required class="campo-input">
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-[9px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Nova Senha</label>
                        <input type="password" name="password" placeholder="Mínimo 8 caracteres" class="campo-input">
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-[9px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Confirmar Senha</label>
                        <input type="password" name="password_confirmation" placeholder="Repita a nova senha" class="campo-input">
                    </div>
                </div>
            </div>

            <!-- DADOS PESSOAIS -->
            <div class="dash-card rounded-[2.5rem] p-8 md:p-10 shadow-2xl shadow-sky-400/5" data-aos="fade-up" data-aos-delay="100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-10 w-10 rounded-xl bg-sky-400/10 flex items-center justify-center text-sky-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h2 class="text-xl font-black text-white italic uppercase tracking-tight">Dados de <span class="text-sky-400">Faturamento</span></h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                    <div class="md:col-span-3">
                        <label class="text-[9px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Nome</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $cliente->first_name) }}" required class="campo-input">
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-[9px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Sobrenome</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $cliente->last_name) }}" required class="campo-input">
                    </div>

                    <div class="md:col-span-1">
                        <label class="text-[9px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1 text-center">DDD</label>
                        <input type="text" name="phone_area_code" maxlength="2" value="{{ old('phone_area_code', $cliente->phone_area_code) }}" required class="campo-input text-center" @input="onlyNumbers">
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-[9px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">WhatsApp</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $cliente->phone_number) }}" required class="campo-input" @input="maskPhone">
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-[9px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">CPF</label>
                        <input type="text" name="identification_number" value="{{ old('identification_number', $cliente->identification_number) }}" required class="campo-input" @input="maskCPF">
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-[9px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">CEP</label>
                        <input type="text" name="address_zip_code" value="{{ old('address_zip_code', $cliente->address_zip_code) }}" required class="campo-input" @input="maskCEP">
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-[9px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Endereço</label>
                        <input type="text" name="address_street_name" value="{{ old('address_street_name', $cliente->address_street_name) }}" required class="campo-input">
                    </div>
                    
                    <div class="md:col-span-1">
                        <label class="text-[9px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1 text-center">Nº</label>
                        <input type="text" name="address_street_number" value="{{ old('address_street_number', $cliente->address_street_number) }}" required class="campo-input text-center" @input="onlyNumbers">
                    </div>
                </div>

                <div class="mt-12 flex justify-end">
                    <button type="submit" :disabled="isProcessing" class="w-full md:w-auto px-12 py-5 rounded-2xl bg-sky-400 text-black font-black uppercase tracking-[0.2em] text-[10px] transition transform hover:scale-[1.02] hover:bg-white active:scale-95 shadow-xl shadow-sky-400/20 disabled:opacity-50">
                        <span x-show="!isProcessing">Salvar Alterações</span>
                        <span x-show="isProcessing">Processando...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function perfilHandler() {
    return {
        isProcessing: false,

        onlyNumbers(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        },

        maskPhone(e) {
            let v = e.target.value.replace(/\D/g, '');
            v = v.replace(/^(\d{5})(\d)/g, '$1-$2');
            e.target.value = v.substring(0, 10);
        },

        maskCPF(e) {
            let v = e.target.value.replace(/\D/g, '');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = v.substring(0, 14);
        },

        maskCEP(e) {
            let v = e.target.value.replace(/\D/g, '');
            v = v.replace(/^(\d{5})(\d)/, '$1-$2');
            e.target.value = v.substring(0, 9);
        }
    }
}
</script>
@endsection