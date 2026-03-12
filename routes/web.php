<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServidorController;
use App\Http\Controllers\AutenticacaoController;
use App\Http\Controllers\DefinirSenhaController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\ProvisioningController;
use App\Http\Controllers\Painel\DashboardController;
use App\Http\Controllers\Painel\FaturaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CartaoController;
use App\Http\Controllers\Painel\CartaoController as PainelCartaoController;
use App\Http\Controllers\Painel\PerfilController;
use App\Http\Controllers\Painel\SuporteController;
use App\Http\Controllers\BrandingController;
use App\Http\Controllers\Painel\TerminalController;
use App\Http\Controllers\Painel\TicketController;
use App\Http\Controllers\RecuperarSenhaController;

// Rotas de Recuperação de Senha
Route::get('/esqueceu-senha', [RecuperarSenhaController::class, 'mostrarFormularioEsqueceu'])->name('senha.esqueceu.mostrar');
Route::post('/esqueceu-senha', [RecuperarSenhaController::class, 'enviarLinkRecuperacao'])->name('senha.esqueceu.enviar');
Route::get('/redefinir-senha/{token}', [RecuperarSenhaController::class, 'mostrarFormularioRedefinir'])->name('senha.redefinir.mostrar');
Route::post('/redefinir-senha', [RecuperarSenhaController::class, 'processarRedefinir'])->name('senha.redefinir.processar');

Route::post('/configurar/servidor', [BrandingController::class, 'changeBrand']);
Route::view('/termos-de-uso', 'termos.termos-de-uso')->name('termos.mostrar');
Route::view('/politica-de-privacidade', 'termos.politica-de-privacidade')->name('privacidade.mostrar');
Route::view('/suporte', 'termos.suporte')->name('suporte.mostrar');
Route::post('/provision/contract/{contrato}', [ProvisioningController::class, 'provisionInstance'])->name('api.provision.instance');
Route::get('/', [ServidorController::class, 'index']);
Route::get('/servidor/{servidor}', [ServidorController::class, 'show'])->name('servidor.show');
Route::post('/webhook', [WebhookController::class, 'handleMercadoPago'])->name('webhook.mercadopago');

Route::middleware('guest')->group(function () {
    Route::get('/registrar', [AutenticacaoController::class, 'mostrarFormularioRegistro'])->name('registrar.mostrar');
    Route::post('/registrar', [AutenticacaoController::class, 'processarRegistro'])->name('registrar.processar');
    Route::get('/login', [AutenticacaoController::class, 'mostrarFormularioEntrar'])->name('login');
    Route::get('/entrar', [AutenticacaoController::class, 'mostrarFormularioEntrar'])->name('entrar.mostrar');
    Route::post('/entrar', [AutenticacaoController::class, 'processarEntrar'])->name('entrar.processar');
    Route::get('/autenticacao/google', [AutenticacaoController::class, 'redirecionarParaGoogle'])->name('autenticacao.google');
    Route::get('/autenticacao/google/callback', [AutenticacaoController::class, 'lidarComCallbackDoGoogle']);
});
 
Route::get('/sobre-nos', function () {
    return view('sobrenos');
})->name('sobrenos.mostrar');

Route::middleware('auth')->group(function () {
    Route::get('/cliente/completar-cadastro', [ClienteController::class, 'create'])->name('cliente.create');
    Route::post('/cliente/salvar', [ClienteController::class, 'store'])->name('cliente.store');
    Route::get('/cartao/adicionar', [CartaoController::class, 'create'])->name('cartao.create');
    Route::post('/cartao/salvar', [CartaoController::class, 'store'])->name('cartao.store');
    Route::post('/sair', [AutenticacaoController::class, 'sair'])->name('sair');
    Route::get('/sair', function() {
    return redirect('/');
});
    Route::post('/logout', [AutenticacaoController::class, 'sair'])->name('logout');
    Route::get('/definir-senha', [DefinirSenhaController::class, 'mostrarFormulario'])->name('senha.definir.mostrar');
    Route::post('/definir-senha', [DefinirSenhaController::class, 'processarFormulario'])->name('senha.definir.processar');
    Route::match(['get', 'post'], '/checkout/iniciar', [CheckoutController::class, 'iniciar'])->name('checkout.iniciar');
    Route::post('/checkout/processar', [CheckoutController::class, 'processar'])->name('checkout.processar');
    Route::get('/pagamento/{contrato}', [PagamentoController::class, 'exibir'])->name('pagamento.exibir');
});

Route::middleware(['auth'])->prefix('painel')->name('painel.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/faturas', [FaturaController::class, 'index'])->name('faturas.index');
    Route::post('/faturas/pagar-cartao', [FaturaController::class, 'processarPagamentoCartao'])->name('faturas.pagar.cartao');
    
    Route::get('/cartoes', [PainelCartaoController::class, 'index'])->name('cartoes.index');
    Route::delete('/cartoes/{cardId}', [PainelCartaoController::class, 'destroy'])->name('cartoes.destroy');
    
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

    Route::get('/suporte', [SuporteController::class, 'index'])->name('suporte.index');
    Route::post('/suporte/chat', [SuporteController::class, 'chat'])->name('suporte.chat');

    Route::get('/contrato/{contrato}/terminal', [TerminalController::class, 'show'])->name('terminal.show');
    
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    
    Route::post('/contratos/{contrato}/actions/{action}', [DashboardController::class, 'gerenciarInstancia'])
        ->where('action', 'start|stop|restart|shutdown')
        ->name('contrato.action');

    Route::post('/contratos/{contrato}/reinstalar', [DashboardController::class, 'reinstalarInstancia'])->name('contrato.reinstalar');
    Route::post('/contratos/{contrato}/reset-password', [DashboardController::class, 'resetPassword'])->name('contrato.reset_password');

    Route::get('/contratos/{contrato}/snapshots', [DashboardController::class, 'gerenciarSnapshots'])->name('contrato.snapshots.gerenciar');
    Route::post('/contratos/{contrato}/snapshots', [DashboardController::class, 'createSnapshot'])->name('contrato.snapshots.create');
    Route::post('/contratos/{contrato}/snapshots/{snapshotId}/revert', [DashboardController::class, 'revertSnapshot'])->name('contrato.snapshots.revert');
    Route::delete('/contratos/{contrato}/snapshots/{snapshotId}', [DashboardController::class, 'deleteSnapshot'])->name('contrato.snapshots.delete');
});
