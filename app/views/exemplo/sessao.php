<div class="section">
    <h2>🔐 Exemplo — Sessão</h2>
    <p>Manipulação de sessão com a função helper <code>session()</code>.</p>

    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-value"><?php echo $contador ?></div>
            <div class="stat-label">Visitas nesta página</div>
        </div>
    </div>

    <div class="alert alert-success">
        <strong>✔</strong> A cada refresh o contador incrementa. Os dados ficam em <code>$_SESSION</code>.
    </div>

    <div class="code-block">
        <code>
<span class="comment">// No controller</span>
<span class="variable">$contador</span> = (<span class="function">session</span>(<span class="string">'contador'</span>) ?? <span class="number">0</span>) + <span class="number">1</span>;
<span class="variable">$_SESSION</span>[<span class="string">'contador'</span>] = <span class="variable">$contador</span>;

<span class="comment">// Na view</span>
&lt;?= <span class="function">e</span>(<span class="variable">$contador</span>) ?&gt;  <span class="comment">// exibe o número de visitas</span>
        </code>
    </div>

    <div class="mt-3">
        <a href="<?php echo route('exemplo.sessao') ?>" class="btn btn-primary">Recarregar página</a>
        <a href="<?php echo route('home') ?>" class="btn btn-outline">← Voltar</a>
    </div>
</div>
