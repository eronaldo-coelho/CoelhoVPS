<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; background-color: #050505; color: #ffffff; padding: 40px; }
        .container { max-width: 600px; margin: 0 auto; background: #0c0c0c; border: 1px solid #38bdf8; border-radius: 20px; padding: 40px; text-align: center; }
        .logo { height: 150px; margin-bottom: 10px; }
        h1 { font-style: italic; font-weight: 900; letter-spacing: -1px; text-transform: uppercase; color: #ffffff; }
        p { color: #a1a1aa; line-height: 1.6; }
        .btn { display: inline-block; padding: 15px 30px; background-color: #38bdf8; color: #000000; text-decoration: none; border-radius: 12px; font-weight: 900; text-transform: uppercase; font-size: 12px; margin-top: 20px; }
        .footer { margin-top: 40px; font-size: 10px; color: #52525b; text-transform: uppercase; letter-spacing: 2px; }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('coelhovps.png') }}" class="logo" alt="CoelhoVPS">
        <h1>Recuperação de Senha</h1>
        <p>Olá, <strong>{{ $user->name }}</strong>.</p>
        <p>Recebemos uma solicitação para redefinir a senha da sua conta. Se não foi você, basta ignorar este e-mail.</p>
        <a href="{{ $link }}" class="btn">Redefinir minha senha</a>
        <p style="margin-top: 30px; font-size: 12px; color: #f43f5e;">Atenção: Este link expira em 10 minutos.</p>
        <div class="footer">
            © {{ date('Y') }} CoelhoVPS — Empresa Brasileira de Infra Global
        </div>
    </div>
</body>
</html>