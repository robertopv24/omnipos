<div class="auth-container">
    <div class="auth-card glass-effect">
        <div class="auth-header">
            <div class="logo-circle bg-warning-10 text-warning mb-1 text-3xl">
                <i class="fa fa-key"></i>
            </div>
            <h1 class="text-gradient border-none mb-025">Recuperar Acceso</h1>
            <p class="text-dim">Ingresa tu correo para recibir un enlace de recuperación.</p>
        </div>

        <form action="<?= url('/password-reset') ?>" method="POST" class="auth-form">
            <div class="form-group">
                <label for="email" class="form-label"><?= __('email') ?></label>
                <div class="input-icon-wrapper">
                    <i class="fa fa-envelope input-icon"></i>
                    <input type="email" name="email" id="email" class="form-control" placeholder="ejemplo@correo.com" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-1">
                Enviar Enlace <i class="fa fa-paper-plane ml-05"></i>
            </button>
        </form>

        <div class="auth-footer mt-2 text-center">
            <p class="text-dim">¿Recordaste tu contraseña? <a href="<?= url('/login') ?>" class="text-primary font-600">Volver al inicio</a></p>
        </div>
    </div>
</div>

<style>
.auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: radial-gradient(circle at top right, rgba(245, 158, 11, 0.1), transparent 40%),
                radial-gradient(circle at bottom left, rgba(30, 41, 59, 0.4), transparent 40%);
}
.auth-card {
    width: 100%;
    max-width: 450px;
    padding: 2.5rem;
    border-radius: 1.5rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}
.auth-header {
    text-align: center;
    margin-bottom: 2rem;
}
.logo-circle {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.auth-form .form-group {
    margin-bottom: 1.25rem;
}
.input-icon-wrapper {
    position: relative;
}
.input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255, 255, 255, 0.4);
}
.auth-form .form-control {
    padding-left: 2.75rem;
}
</style>
