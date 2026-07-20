<div class="section">
    <h2>🔒 Admin — Dashboard</h2>
    <p>Bem-vindo, <strong><?php echo e($user['nome'] ?? 'Visitante') ?></strong>!</p>

    <div class="alert alert-success">
        <strong>✔</strong> Você está autenticado. Esta página é protegida pelos middlewares <code>auth</code> e <code>log</code>.
    </div>

    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-value">🔐</div>
            <div class="stat-label">Auth ativo</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">📝</div>
            <div class="stat-label">Log automático</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">👤</div>
            <div class="stat-label"><?php echo e($user['usuario'] ?? '—') ?></div>
        </div>
    </div>

    <div class="mt-3">
        <a href="<?php echo route('logout') ?>" class="btn btn-primary">Sair</a>
        <a href="<?php echo route('home') ?>" class="btn btn-outline">← Início</a>
    </div>
</div>
