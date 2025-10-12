@extends('layouts.app')
@section('title','Iniciar sesión')

@section('content')
    <style>
        :root{
            --morado:#6C63FF; --amarillo:#FFD460; --beige:#FAF3DD; --gris:#2E2E2E;
            --bg:var(--beige); --text:var(--gris); --card:#fff; --muted:#555;
            --fs-0: clamp(1rem, .9rem + .4vw, 1.125rem);
            --fs-1: clamp(1.25rem, 1.05rem + .9vw, 1.6rem);
            --pad: clamp(1rem, .8rem + 1.2vw, 2rem);
            --radius: 1.25rem;
        }
        body{ background:var(--bg); color:var(--text); }
        .card-soft{ background:var(--card); border:1px solid #eee; border-radius: var(--radius); }
        .btn-primary{ background:var(--morado); border:none; font-weight:700; }
        .btn-primary:hover{ background:#584efc; }
        .accent{ color: var(--morado); }
        .input-group-text{ background: var(--card); border-right:0; color: var(--muted); }
        .input-group .form-control{ border-left:0; }
    </style>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8 col-xl-6">
                <div class="card-soft shadow-sm p-3 p-md-4">
                    <div class="mb-3 text-center">
                        <h1 class="h3 fw-bold mb-1">Bienvenido de nuevo</h1>
                        <p class="m-0" style="color:var(--muted)">Accede para continuar con tu balance financiero</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" novalidate>
                        @csrf

                        {{-- EMAIL --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                <input id="email" type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                       placeholder="tucorreo@ejemplo.com">
                            </div>
                            @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- PASSWORD --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                <input id="password" type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password" required autocomplete="current-password"
                                       placeholder="Tu contraseña segura">
                                <button class="btn btn-outline-secondary" type="button" id="togglePass" aria-label="Mostrar u ocultar contraseña">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- REMEMBER --}}
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Recordarme</label>
                        </div>

                        {{-- BOTONES --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Iniciar sesión
                            </button>
                            <a class="btn btn-outline-dark" href="{{ url('/register') }}">
                                <i class="fa-solid fa-user-plus me-1"></i> Crear cuenta
                            </a>
                        </div>

                        {{-- OLVIDO DE CONTRASEÑA --}}
                        @if (Route::has('password.request'))
                            <div class="text-center mt-3">
                                <a class="accent text-decoration-none" href="{{ route('password.request') }}">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            </div>
                        @endif
                    </form>
                </div>

                <p class="text-center mt-3" style="color:var(--muted)">
                    © {{ date('Y') }} Spendly — Seguridad y equilibrio
                </p>
            </div>
        </div>
    </div>

    {{-- JS mínimo: toggle de contraseña --}}
    <script>
        const toggle = document.getElementById('togglePass');
        const pass = document.getElementById('password');
        if (toggle && pass) {
            toggle.addEventListener('click', ()=>{
                const icon = toggle.querySelector('i');
                const show = pass.type === 'password';
                pass.type = show ? 'text' : 'password';
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        }
    </script>
@endsection
