<div class="hero">
    <h1>🔐 Login</h1>
    <p>Faça login para acessar a área administrativa.</p>
</div>

<div class="section">
    <div class="grid" style="grid-template-columns: 1fr; max-width: 400px; margin: 0 auto;">
        <div class="card">
            <form method="POST" action="<?php echo route('login.logar') ?>">
                <div style="margin-bottom: 16px;">
                    <label for="usuario" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: .875rem;">Usuário</label>
                    <input type="text" name="usuario" id="usuario" required
                           style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: .875rem; background: var(--bg); color: var(--text);">
                </div>
                <div style="margin-bottom: 20px;">
                    <label for="senha" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: .875rem;">Senha</label>
                    <input type="password" name="senha" id="senha" required
                           style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: .875rem; background: var(--bg); color: var(--text);">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Entrar</button>
            </form>

            <div style="margin-top: 20px; font-size: .8125rem; color: var(--text-muted); text-align: center;">
                <strong>Credenciais de teste:</strong><br>
                admin / 123456 &nbsp;|&nbsp; user / 123456
            </div>
        </div>
    </div>
</div>
