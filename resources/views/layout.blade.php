<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PatasDigitais</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <header class="topbar">
        <div class="offcanvas offcanvas-end" tabindex="-1" id="menuLateral">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>

            <div class="offcanvas-body d-flex flex-column">
                @auth
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-person-circle fs-2 me-2"></i>
                        <div>
                            <strong>{{ Auth::user()->name }}</strong>
                        </div>
                    </div>
                    <hr>
                @endauth

                <!-- LINKS -->
                <ul class="list-unstyled flex-grow-1">
                    <li class="mb-3">
                        <a href="{{ route('animais.index') }}" class="text-decoration-none">
                            <i class="bi bi-house me-2"></i>
                            Início
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ route('meus-pedidos') }}"
                            class="text-decoration-none d-flex justify-content-between align-items-center">
                            <span>
                                <i class="bi bi-envelope-heart me-2"></i>
                                Meus Pedidos
                            </span>
                            @auth
                                @php
                                    $pedidosPendentes = Auth::user()->pedidosPendentesCount();
                                @endphp
                                @if($pedidosPendentes > 0)
                                    <span class="badge bg-danger rounded-pill">{{ $pedidosPendentes }}</span>
                                @endif
                            @endauth
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="/anuncios" class="text-decoration-none">
                            <i class="bi bi-megaphone me-2"></i>
                            Meus anúncios
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="/salvos" class="text-decoration-none">
                            <i class="bi bi-bookmark me-2"></i>
                            Anúncios salvos
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="/conversas" class="text-decoration-none">
                            <i class="bi bi-chat-dots me-2"></i>
                            Conversas
                        </a>
                    </li>
                </ul>

                <!-- BOTÃO LOGOUT -->
                @auth
                    <hr>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-box-arrow-right"></i>
                            Sair
                        </button>
                    </form>
                @endauth

                <!-- BOTÃO LOGIN -->
                @guest
                    <hr>
                    <a href="{{ route('login') }}"
                        class="btn btn-custom w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Entrar
                    </a>
                @endguest
            </div>
        </div>

        <div class="topbar-accent"></div>

        <div class="topbar-content">
            <div class="logo">
                <img src="{{ asset('storage/logo.png') }}" alt="Patas Digitais">
            </div>

            <div class="d-flex align-items-center gap-3">


                <div class="menu-icon position-relative">
                    <i class="bi bi-list fs-1" data-bs-toggle="offcanvas" data-bs-target="#menuLateral"></i>
                    @auth
                        @php
                            $user = Auth::user();
                            $pedidosPendentes = method_exists($user, 'pedidosPendentesCount') ? $user->pedidosPendentesCount() : 0;
                        @endphp
                        @if($pedidosPendentes > 0)
                            <span
                                class="badge bg-danger rounded-pill">{{ $pedidosPendentes > 99 ? '99+' : $pedidosPendentes }}</span>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <div class="container py-3">
        @yield('conteudo')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
