<div class="login-wrapper">
    <div class="login-card">
        <div class="brand login-brand">
            <div class="brand-logo">EA</div>
            <div>
                <strong>E-Agenda</strong>
                <span>Sistem Administrasi Surat</span>
            </div>
        </div>

        <h1>Masuk Aplikasi</h1>
        <p class="muted">Gunakan akun demo staff atau pimpinan yang tersedia di README.</p>

        <form wire:submit="login" class="form-stack">
            <div>
                <label>Email</label>
                <input type="email" wire:model="email" placeholder="staff@kantor.com" autofocus>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div>
                <label>Password</label>
                <input type="password" wire:model="password" placeholder="password">
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <label class="checkbox-row">
                <input type="checkbox" wire:model="remember">
                <span>Ingat saya</span>
            </label>

            <button type="submit" class="button button-primary button-block" wire:loading.attr="disabled">
                <span wire:loading.remove>Login</span>
                <span wire:loading>Memproses...</span>
            </button>
        </form>
    </div>
</div>
