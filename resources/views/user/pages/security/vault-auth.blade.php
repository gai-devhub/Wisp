@extends('user.base-user')

@section('user-section', 'vault')

@section('content')
<div class="content-section active" id="vault">
    <div class="card mx-auto mt-5 vault-auth-card">
        <div class="card-header text-center">
            <h3><i class="fas fa-lock text-warning me-2"></i> The Vault</h3>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <p class="text-center text-muted mb-4">Enter your Vault PIN to access locked conversations.</p>
            <form method="POST" action="{{ route('user.vault.auth') }}">
                @csrf
                <div class="form-group mb-4">
                    <input type="password" class="form-control text-center fs-4" name="pin" placeholder="••••" required autofocus autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-unlock me-1"></i> Unlock</button>
            </form>
        </div>
    </div>
</div>
@endsection
