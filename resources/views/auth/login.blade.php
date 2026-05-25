@extends('layout')

@section('conteudo')
    <div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="login-card">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h3 class="text-center mb-4">
                <i class="bi bi-person-circle"></i> Acesso ao Sistema
            </h3>

            <form action="{{ route('login.processar') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                        placeholder="Digite seu email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Digite sua senha"
                        required>
                    @error('senha')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                

                <button type="submit" class="btn btn-custom w-100">
                    <i class="bi bi-box-arrow-in-right"></i> Entrar
                </button>
            </form>

            <p class="mt-3 text-center">
                Ainda não tem uma conta?<br>
                <a href="{{ route('register') }}">Cadastre-se aqui</a>
            </p>

        </div>
    </div>
@endsection