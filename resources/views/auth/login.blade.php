@extends('layouts.login')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card border-0 shadow-lg p-4" style="width: 100%; max-width: 420px; border-radius: 12px;">
        <div class="text-center mb-4">
            <h3 class="fw-bold mb-1" style="color: #1fbf59;">Expense Management</h3>
            <p class="text-muted">Sign in to your account</p>
        </div>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
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

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="username" class="form-label fw-medium">Username</label>
                <input 
                    type="text" 
                    name="username" 
                    id="username" 
                    value="{{ old('username') }}" 
                    class="form-control @error('username') is-invalid @enderror" 
                    required 
                    autofocus
                    style="padding: 10px; border-radius: 8px;"
                >
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-medium">Password</label>
                <div class="input-group">
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        required
                        style="padding: 10px; border-radius: 8px 0 0 8px; border-right: none;"
                    >
                    <button 
                        type="button" 
                        class="btn password-toggle" 
                        id="togglePassword"
                        style="background-color: #f8f9fa; border: 1px solid #ced4da; border-left: none; border-radius: 0 8px 8px 0;"
                    >
                        <span id="eye-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#495057" viewBox="0 0 16 16">
                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8a13.133 13.133 0 0 1-1.66 2.043C11.879 11.332 10.12 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.133 13.133 0 0 1 1.172 8z"/>
                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM6.5 8a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0z"/>
                            </svg>
                        </span>
                        <span id="eye-slash-icon" style="display: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#495057" viewBox="0 0 16 16">
                                <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/>
                                <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                                <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>
                            </svg>
                        </span>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn w-100 py-2 fw-medium" style="background-color: #1fbf59; color: white; border-radius: 8px; border: none; transition: all 0.3s ease;">
                Sign In
            </button>
            
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .password-toggle:hover {
        background-color: #e9ecef !important;
    }
    .password-toggle:active {
        background-color: #dfe3e6 !important;
    }
    body {
        background-color: #f8f9fa;
    }
    .card {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .form-control:focus {
        border-color: #1fbf59;
        box-shadow: 0 0 0 0.25rem rgba(31, 191, 89, 0.25);
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        const eyeSlashIcon = document.getElementById('eye-slash-icon');

        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        
        if (isPassword) {
            eyeIcon.style.display = 'none';
            eyeSlashIcon.style.display = 'inline';
        } else {
            eyeIcon.style.display = 'inline';
            eyeSlashIcon.style.display = 'none';
        }
    });
</script>
@endpush