<div class="hero">
    <span class="hero-version">v1.0.0-alpha</span>
    <h1>Blumiga</h1>
    <p>Microframework MVC para PHP com arquitetura procedural, modular e de alta performance.</p>
    <a href="<?php echo route('exemplo.banco') ?>" class="btn btn-primary">Ver exemplos</a>
    <a href="https://github.com/bluiceoficial/blumiga" target="_blank" class="btn btn-outline">GitHub</a>
</div>

<div class="section">
    <h2>📖 Documentação</h2>
    <p>Visão geral dos conceitos e ferramentas do Blumiga.</p>

    <div class="grid">
        <div class="card">
            <div class="card-icon">🚦</div>
            <h3>Rotas</h3>
            <p>Defina URLs e conecte a controllers de forma simples e intuitiva.</p>
            <div class="card-links">
                <a href="#rotas">Ver detalhes →</a>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">🎯</div>
            <h3>Controllers</h3>
            <p>Funções organizadas em namespaces que processam requisições.</p>
            <div class="card-links">
                <a href="#controllers">Ver detalhes →</a>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">🗄️</div>
            <h3>Models</h3>
            <p>Camada de dados com mysqli procedural, sempre fechando conexões.</p>
            <div class="card-links">
                <a href="<?php echo route('exemplo.banco') ?>">Ver exemplo →</a>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">🎨</div>
            <h3>Views</h3>
            <p>Separação da apresentação com suporte a layouts e escape XSS.</p>
            <div class="card-links">
                <a href="#views">Ver detalhes →</a>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">🛠️</div>
            <h3>Helpers</h3>
            <p>Funções globais para tarefas comuns: sessão, URL, formulários, formatação.</p>
            <div class="card-links">
                <a href="<?php echo route('exemplo.helpers') ?>">Ver exemplos →</a>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">🔒</div>
            <h3>Middlewares</h3>
            <p>Execute lógica antes ou depois dos controllers — autenticação, logs, etc.</p>
            <div class="card-links">
                <a href="<?php echo route('admin.home') ?>">Ver exemplo →</a>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">📦</div>
            <h3>Migrations</h3>
            <p>Versionamento de banco de dados com funções up/down usando mysqli.</p>
            <div class="card-links">
                <a href="#migrations">Ver detalhes →</a>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">🌱</div>
            <h3>Seeders</h3>
            <p>Popule o banco com dados iniciais de forma estruturada e reversível.</p>
            <div class="card-links">
                <a href="#seeders">Ver detalhes →</a>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">💻</div>
            <h3>CLI</h3>
            <p>Gere controllers, models, views, migrations e seeders pelo terminal.</p>
            <div class="card-links">
                <a href="#cli">Ver detalhes →</a>
            </div>
        </div>
    </div>
</div>

<div class="section" id="rotas">
    <h2>🚦 Rotas</h2>
    <p>Definidas em <code>config/routes.php</code>.</p>

    <div class="code-block">
        <code>
<span class="comment">// Rota GET simples</span>
<span class="function">routeGET</span>(<span class="string">'/'</span>, <span class="string">'home@index'</span>, <span class="string">'home'</span>);

<span class="comment">// Rota com parâmetros e regex</span>
<span class="function">routeGET</span>(<span class="string">'/usuario/{id:[0-9]+}'</span>, <span class="string">'usuario@perfil'</span>, <span class="string">'usuario.perfil'</span>);

<span class="comment">// Grupo com prefixo e namespace</span>
<span class="function">routeGROUP</span>(<span class="string">'/admin'</span>, <span class="string">'Admin'</span>, <span class="keyword">function</span>() {
    <span class="function">routeGET</span>(<span class="string">'/'</span>, <span class="string">'dashboard@index'</span>, <span class="string">'admin.home'</span>);
}, [<span class="string">'log'</span>]);
        </code>
    </div>
    <p class="code-caption">
        Use <code>route('home')</code> para gerar URLs a partir do nome da rota.
        Parâmetros: <code>route('usuario.perfil', ['id' => 42])</code>.
    </p>
</div>

<div class="section" id="controllers">
    <h2>🎯 Controllers</h2>
    <p>Funções em namespaces, sem classes. O namespace segue o caminho do arquivo.</p>

    <div class="grid" style="grid-template-columns: 1fr 1fr;">
        <div class="code-block">
            <code>
<span class="comment">// app/controllers/home.php</span>
<span class="keyword">&lt;?php</span>

<span class="keyword">namespace</span> Blumiga\<span class="variable">controllers</span>\<span class="variable">home</span>;

<span class="keyword">function</span> <span class="function">index</span>(): <span class="keyword">void</span>
{
    <span class="function">view</span>(<span class="string">'home'</span>, [
        <span class="string">'titulo'</span> => <span class="string">'Página Inicial'</span>
    ], <span class="string">'layout'</span>);
}
            </code>
        </div>

        <div class="code-block">
            <code>
<span class="comment">// Rota: /exemplo/banco</span>
routeGET(<span class="string">'/exemplo/banco'</span>,
    <span class="string">'exemplo@banco'</span>,
    <span class="string">'exemplo.banco'</span>);

<span class="comment">// Router monta o namespace:</span>
<span class="comment">// \Blumiga\controllers\exemplo\banco</span>

<span class="comment">// Função chamada com os</span>
<span class="comment">// parâmetros da URL</span>
            </code>
        </div>
    </div>
</div>

<div class="section" id="views">
    <h2>🎨 Views</h2>
    <p>Arquivos PHP em <code>app/views/</code>. Renderize com a função global <code>view()</code>.</p>

    <div class="grid" style="grid-template-columns: 1fr 1fr;">
        <div class="code-block">
            <code>
<span class="comment">// Renderizar com layout</span>
<span class="function">view</span>(<span class="string">'home'</span>, <span class="variable">$data</span>, <span class="string">'layout'</span>);

<span class="comment">// Sem layout (view direta)</span>
<span class="function">view</span>(<span class="string">'exemplo/banco'</span>, <span class="variable">$data</span>);

<span class="comment">// Escapar para HTML (XSS)</span>
&lt;h1&gt;&lt;?= <span class="function">e</span>(<span class="variable">$titulo</span>) ?&gt;&lt;/h1&gt;

<span class="comment">// Assets com cache-busting</span>
&lt;link rel=<span class="string">"stylesheet"</span>
  href=<span class="string">"&lt;?= </span><span class="function">asset</span>(<span class="string">'assets/css/style.css'</span>)<span class="string"> ?&gt;"</span>&gt;
            </code>
        </div>

        <div class="code-block">
            <code>
<span class="comment">// app/views/layout.php</span>
<span class="comment">// $content é injetado automaticamente</span>
<span class="keyword">&lt;!DOCTYPE html&gt;</span>
&lt;html&gt;
&lt;head&gt;
    &lt;title&gt;&lt;?= <span class="function">e</span>(<span class="variable">$titulo</span>) ?&gt;&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;nav&gt;...&lt;/nav&gt;
    &lt;main&gt;
        &lt;?= <span class="variable">$content</span> ?? <span class="string">''</span> ?&gt;
    &lt;/main&gt;
&lt;/body&gt;
&lt;/html&gt;
            </code>
        </div>
    </div>
</div>

<div class="section" id="helpers">
    <h2>🛠️ Helpers</h2>
    <p>Funções globais disponíveis em qualquer lugar sem import. Definições em <code>core/functions.php</code>.</p>

    <div class="grid" style="grid-template-columns: 1fr 1fr;">

        <div class="card">
            <h3>🔒 Segurança</h3>
            <p><code>e()</code> — Escape XSS (htmlspecialchars). <code>eJS()</code> — Escape para JS (json_encode).</p>
            <div class="code-block">
                <code>
&lt;?= <span class="function">e</span>(<span class="variable">$titulo</span>) ?&gt;
&lt;script&gt;var msg = <span class="function">eJS</span>(<span class="variable">$msg</span>);&lt;/script&gt;
                </code>
            </div>
            <p class="mt-1"><code>encrypt()</code> / <code>decrypt()</code> — AES-256-CBC + HMAC-SHA256.</p>
            <div class="code-block">
                <code>
<span class="variable">$token</span> = <span class="function">encrypt</span>(<span class="string">'dado'</span>, <span class="string">'chave'</span>);
<span class="variable">$orig</span> = <span class="function">decrypt</span>(<span class="variable">$token</span>, <span class="string">'chave'</span>);
                </code>
            </div>
        </div>

        <div class="card">
            <h3>🔗 URL & Rotas</h3>
            <p><code>route()</code> — Gera URL pelo nome da rota.</p>
            <div class="code-block">
                <code>
<span class="function">route</span>(<span class="string">'home'</span>)
<span class="function">route</span>(<span class="string">'usuario.perfil'</span>, [<span class="string">'id'</span> => <span class="number">42</span>])
                </code>
            </div>
            <p class="mt-1"><code>asset()</code> — URL de asset com cache-busting.</p>
            <div class="code-block">
                <code>
<span class="function">asset</span>(<span class="string">'assets/css/style.css'</span>)
                </code>
            </div>
            <p class="mt-1"><code>getURL(n)</code>, <code>getFirstURL()</code>, <code>getLastURL()</code>, <code>getPenultimateURL()</code>, <code>requestURI()</code> — Manipulam segmentos da URL atual.</p>
        </div>

        <div class="card">
            <h3>📩 Requisição</h3>
            <p><code>inputGET()</code> / <code>inputPOST()</code> — Valor sanitizado de <code>$_GET</code> / <code>$_POST</code>.</p>
            <div class="code-block">
                <code>
<span class="variable">$nome</span> = <span class="function">inputGET</span>(<span class="string">'nome'</span>);
<span class="variable">$email</span> = <span class="function">inputPOST</span>(<span class="string">'email'</span>);
                </code>
            </div>
            <p class="mt-1"><code>requestPOST()</code> / <code>requestGET()</code> — Bool do método HTTP.</p>
            <p class="mt-1"><code>getClientIP()</code> — IP do visitante (suporta Cloudflare). <code>clientLanguage()</code> — Idioma do navegador.</p>
            <p class="mt-1"><code>servername()</code> — Nome do servidor com/sem protocolo. <code>documentroot()</code> — Caminho raiz do projeto.</p>
        </div>

        <div class="card">
            <h3>🔐 Sessão & Redirect</h3>
            <p><code>session()</code> — Acesso à <code>$_SESSION</code>.</p>
            <div class="code-block">
                <code>
<span class="function">session</span>(<span class="string">'logado'</span>)   <span class="comment">// valor</span>
<span class="function">session</span>()               <span class="comment">// $_SESSION inteiro</span>
                </code>
            </div>
            <p class="mt-1"><code>redirect()</code> — Redirect HTTP.</p>
            <div class="code-block">
                <code>
<span class="function">redirect</span>(<span class="string">'/login'</span>)
<span class="function">redirect</span>(<span class="string">'/busca'</span>, [<span class="string">'q'</span> => <span class="string">'foo'</span>])
                </code>
            </div>
            <p class="mt-1"><code>redirectJS()</code> — Redirect via JavaScript. <code>windowAlert()</code> — Alert JS.</p>
        </div>

        <div class="card">
            <h3>📝 String</h3>
            <p><code>str_limit()</code> — Limita string com sufixo.</p>
            <div class="code-block">
                <code>
<span class="function">str_limit</span>(<span class="string">'Texto longo'</span>, <span class="number">10</span>)
<span class="comment">// → 'Texto long...'</span>
                </code>
            </div>
            <p class="mt-1"><code>str_after()</code> / <code>str_before()</code> — Parte após/antes de um termo. <code>containsAny()</code> — Verifica se contém termos.</p>
            <p class="mt-1"><code>generateSlug()</code> — Slug amigável. <code>removeAccents()</code> / <code>removeSpecialChars()</code> — Limpeza.</p>
            <p class="mt-1"><code>generatePassword()</code> / <code>generatePrefix()</code> — Geradores seguros.</p>
        </div>

        <div class="card">
            <h3>📅 Data & Moeda</h3>
            <p><code>changeDate()</code> — Converte formato de data.</p>
            <div class="code-block">
                <code>
<span class="function">changeDate</span>(<span class="string">'20/07/2026'</span>, <span class="string">'d/m/Y'</span>, <span class="string">'Y-m-d'</span>)
<span class="comment">// → '2026-07-20'</span>
                </code>
            </div>
            <p class="mt-1"><code>dayOfWeek()</code> — Nome do dia da semana (ex: <code>segunda-feira</code>). <code>monthName()</code> — Nome do mês.</p>
            <p class="mt-1"><code>formatCurrency()</code> — Formata valor monetário.</p>
            <div class="code-block">
                <code>
<span class="function">formatCurrency</span>(<span class="number">1234.56</span>)
<span class="comment">// → 'R$ 1.234,56'</span>
                </code>
            </div>
            <p class="mt-1"><code>padNumber()</code> — Zero à esquerda (2 dígitos).</p>
        </div>

        <div class="card">
            <h3>📂 Arquivo</h3>
            <p><code>readFileContent()</code> — Lê conteúdo de arquivo.</p>
            <p><code>writeFileContent()</code> — Escreve ou anexa dados.</p>
            <div class="code-block">
                <code>
<span class="variable">$html</span> = <span class="function">readFileContent</span>(<span class="string">'cache/page.html'</span>);
<span class="function">writeFileContent</span>(<span class="string">'log.txt'</span>, <span class="string">"erro ocorreu\n"</span>);
                </code>
            </div>
            <p class="mt-1"><code>deleteFile()</code> — Remove arquivo. <code>createDir()</code> — Cria diretório recursivo. <code>deleteDir()</code> — Remove diretório recursivamente.</p>
        </div>

        <div class="card">
            <h3>🐛 Debug</h3>
            <p><code>pre()</code> — Exibe variável formatada com <code>&lt;pre&gt;</code>.</p>
            <div class="code-block">
                <code>
<span class="function">pre</span>(<span class="variable">$array</span>);
<span class="comment">// <pre>array(…) </pre></span>
                </code>
            </div>
        </div>

    </div>
</div>

<div class="section" id="models">
    <h2>🗄️ Models</h2>
    <p>Sempre usando mysqli procedural com <code>close()</code> e <code>free_result()</code>.</p>

    <div class="code-block">
        <code>
<span class="comment">// app/models/usuarioModel.php</span>
<span class="keyword">&lt;?php</span>

<span class="keyword">namespace</span> Blumiga\<span class="variable">models</span>\<span class="variable">usuarioModel</span>;

<span class="keyword">function</span> <span class="function">listar</span>(): <span class="keyword">array</span>
{
    <span class="keyword">global</span> <span class="variable">$dbConfig</span>;

    <span class="variable">$conn</span> = <span class="function">mysqli_connect</span>(
        <span class="variable">$dbConfig</span>[<span class="string">'default'</span>][<span class="string">'server'</span>],
        <span class="variable">$dbConfig</span>[<span class="string">'default'</span>][<span class="string">'username'</span>],
        <span class="variable">$dbConfig</span>[<span class="string">'default'</span>][<span class="string">'password'</span>],
        <span class="variable">$dbConfig</span>[<span class="string">'default'</span>][<span class="string">'database'</span>]
    );

    <span class="variable">$result</span> = <span class="function">mysqli_query</span>(<span class="variable">$conn</span>, <span class="string">"SELECT * FROM usuarios"</span>);
    <span class="variable">$dados</span> = <span class="function">mysqli_fetch_all</span>(<span class="variable">$result</span>, MYSQLI_ASSOC);
    <span class="function">mysqli_free_result</span>(<span class="variable">$result</span>);
    <span class="function">mysqli_close</span>(<span class="variable">$conn</span>);

    <span class="keyword">return</span> <span class="variable">$dados</span>;
}
        </code>
    </div>
    <p class="code-caption">
        Carregue com: <code>model('usuarioModel')</code>.<br>
        Sempre libere resultados com <code>mysqli_free_result()</code> e feche com <code>mysqli_close()</code>.
    </p>
</div>

<div class="section" id="migrations">
    <h2>📦 Migrations</h2>
    <p>Arquivos em <code>app/database/migrations/</code> que retornam <code>['up' => fn, 'down' => fn]</code>.</p>

    <div class="code-block">
        <code>
<span class="comment">// Executar: php blumiga migrate</span>
<span class="comment">// Rollback:  php blumiga migrate:rollback</span>

<span class="keyword">return</span> [
    <span class="string">'up'</span> => <span class="keyword">function</span> () {
        <span class="keyword">global</span> <span class="variable">$dbConfig</span>;
        <span class="variable">$conn</span> = <span class="function">mysqli_connect</span>(...);
        <span class="function">mysqli_query</span>(<span class="variable">$conn</span>, <span class="string">"CREATE TABLE ..."</span>);
        <span class="function">mysqli_close</span>(<span class="variable">$conn</span>);
    },

    <span class="string">'down'</span> => <span class="keyword">function</span> () {
        <span class="keyword">global</span> <span class="variable">$dbConfig</span>;
        <span class="variable">$conn</span> = <span class="function">mysqli_connect</span>(...);
        <span class="function">mysqli_query</span>(<span class="variable">$conn</span>, <span class="string">"DROP TABLE ..."</span>);
        <span class="function">mysqli_close</span>(<span class="variable">$conn</span>);
    },
];
        </code>
    </div>
</div>

<div class="section" id="seeders">
    <h2>🌱 Seeders</h2>
    <p>Arquivos em <code>app/database/seeders/</code> que retornam <code>['run' => fn, 'down' => fn]</code>.</p>

    <div class="code-block">
        <code>
<span class="comment">// Executar: php blumiga db:seed</span>
<span class="comment">// Específico: php blumiga db:seed usuario</span>
<span class="comment">// Rollback: php blumiga db:seed:rollback usuario</span>

<span class="keyword">return</span> [
    <span class="string">'run'</span> => <span class="keyword">function</span> () {
        <span class="keyword">global</span> <span class="variable">$dbConfig</span>;
        <span class="variable">$conn</span> = <span class="function">mysqli_connect</span>(...);
        <span class="variable">$stmt</span> = <span class="function">mysqli_prepare</span>(<span class="variable">$conn</span>, <span class="string">"INSERT INTO usuarios ..."</span>);
        <span class="function">mysqli_stmt_execute</span>(<span class="variable">$stmt</span>);
        <span class="function">mysqli_stmt_close</span>(<span class="variable">$stmt</span>);
        <span class="function">mysqli_close</span>(<span class="variable">$conn</span>);
    },
    ...
];
        </code>
    </div>
</div>

<div class="section" id="cli">
    <h2>💻 CLI</h2>
    <p>Comandos disponíveis via <code>php blumiga</code>.</p>

    <div class="grid" style="grid-template-columns: 1fr 1fr;">
        <div>
            <div class="command"><span class="prompt">$</span> php blumiga serve 8080</div>
            <p class="text-muted mt-1" style="font-size: .875rem;">Inicia servidor de desenvolvimento</p>
        </div>
        <div>
            <div class="command"><span class="prompt">$</span> php blumiga make:controller home</div>
            <p class="text-muted mt-1" style="font-size: .875rem;">Cria um controller</p>
        </div>
        <div>
            <div class="command"><span class="prompt">$</span> php blumiga make:model usuarioModel</div>
            <p class="text-muted mt-1" style="font-size: .875rem;">Cria um model</p>
        </div>
        <div>
            <div class="command"><span class="prompt">$</span> php blumiga make:view home</div>
            <p class="text-muted mt-1" style="font-size: .875rem;">Cria uma view</p>
        </div>
        <div>
            <div class="command"><span class="prompt">$</span> php blumiga make:migration criar_tabela</div>
            <p class="text-muted mt-1" style="font-size: .875rem;">Cria uma migration</p>
        </div>
        <div>
            <div class="command"><span class="prompt">$</span> php blumiga make:seeder usuario</div>
            <p class="text-muted mt-1" style="font-size: .875rem;">Cria um seeder</p>
        </div>
        <div>
            <div class="command"><span class="prompt">$</span> php blumiga make:middleware log</div>
            <p class="text-muted mt-1" style="font-size: .875rem;">Cria um middleware</p>
        </div>
        <div>
            <div class="command"><span class="prompt">$</span> php blumiga migrate</div>
            <p class="text-muted mt-1" style="font-size: .875rem;">Executa migrations</p>
        </div>
    </div>
</div>
