<div class="section">
    <h2>🛠️ Exemplo — Helpers</h2>
    <p>Funções globais disponíveis sem necessidade de import.</p>

    <div class="grid">
        <div class="card">
            <h3>🔒 <code>e()</code></h3>
            <p>Escape XSS: <code>htmlspecialchars()</code> com encoding UTF-8.</p>
            <div class="code-block">
                <code>&lt;?= <span class="function">e</span>(<span class="variable">$variavel</span>) ?&gt;</code>
            </div>
        </div>

        <div class="card">
            <h3>🔗 <code>route()</code></h3>
            <p>Gera URL a partir do nome da rota.</p>
            <div class="code-block">
                <code>
<span class="function">route</span>(<span class="string">'home'</span>)
<span class="comment">// → /</span>

<span class="function">route</span>(<span class="string">'exemplo.banco'</span>)
<span class="comment">// → /exemplo/banco</span>
                </code>
            </div>
        </div>

        <div class="card">
            <h3>📦 <code>asset()</code></h3>
            <p>URL de asset com cache-busting via <code>filemtime()</code>.</p>
            <div class="code-block">
                <code>
<span class="function">asset</span>(<span class="string">'assets/css/style.css'</span>)
<span class="comment">// → /assets/css/style.css?v=1234567890</span>
                </code>
            </div>
        </div>

        <div class="card">
            <h3>📝 <code>str_limit()</code></h3>
            <p>Limita string em N caracteres com sufixo.</p>
            <div class="code-block">
                <code>
<span class="function">str_limit</span>(<span class="string">'Texto longo...'</span>, <span class="number">10</span>)
<span class="comment">// → 'Texto long...'</span>
                </code>
            </div>
        </div>

        <div class="card">
            <h3>💰 <code>formatCurrency()</code></h3>
            <p>Formata valor monetário com <code>NumberFormatter</code>.</p>
            <div class="code-block">
                <code>
<span class="function">formatCurrency</span>(<span class="number">1234.56</span>)
<span class="comment">// → R$ 1.234,56</span>
                </code>
            </div>
        </div>

        <div class="card">
            <h3>🔐 <code>encrypt()</code> / <code>decrypt()</code></h3>
            <p>AES-256-CBC + HMAC-SHA256 (encrypt-then-MAC).</p>
            <div class="code-block">
                <code>
<span class="variable">$token</span> = <span class="function">encrypt</span>(<span class="string">'dado sensível'</span>, <span class="string">'chave-secreta'</span>);
<span class="variable">$original</span> = <span class="function">decrypt</span>(<span class="variable">$token</span>, <span class="string">'chave-secreta'</span>);
                </code>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="<?php echo route('home') ?>" class="btn btn-outline">← Voltar</a>
    </div>
</div>
