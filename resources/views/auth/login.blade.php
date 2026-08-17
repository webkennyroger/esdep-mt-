<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | ESDEP-MT</title>
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center">
    <div class="w-full max-w-md rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
        <div class="mb-6 text-center">
            <img src="{{ asset('assets/images/logo-esdep.png') }}" alt="ESDEP-MT" class="mx-auto mb-3 h-16 w-auto">
        </div>

        @if($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none"
                    placeholder="seu.email@esdep.mt.gov.br">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Senha</label>
                <input type="password" name="password" required
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none"
                    placeholder="Sua senha">
            </div>

            <button type="submit" class="w-full rounded-lg bg-green-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-800">
                Entrar
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-sm text-slate-500 hover:text-slate-700">Voltar para o início</a>
        </div>
    </div>
</body>
</html>
