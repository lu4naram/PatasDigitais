@extends('layout')

@section('conteudo')
<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="register-card">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
            <i class="bi bi-person-plus-fill"></i> Criar Conta
        </h3>

        <form action="{{ route('register.processar') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Nome completo</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" placeholder="Digite seu nome" 
                       value="{{ old('name') }}" required autofocus>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" placeholder="Digite seu email" 
                       value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            

            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       id="password" name="password" placeholder="Mínimo 6 caracteres" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmar senha</label>
                <input type="password" class="form-control" 
                       id="password_confirmation" name="password_confirmation" 
                       placeholder="Digite a senha novamente" required>
            </div>

            <button type="submit" class="btn btn-custom w-100">
                <i class="bi bi-check-circle"></i> Cadastrar
            </button>
        </form>

        <p class="mt-3 text-center">
            Já tem uma conta?<br>
            <a href="{{ route('login') }}">Faça login aqui</a>
        </p>

    </div>
</div>
@endsection