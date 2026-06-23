# Blumiga

[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.4-777bb4.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-GPL--2.0--only-blue.svg)](LICENSE)

> [!NOTE]
> **Status do Projeto:** Em desenvolvimento ativo. Atualmente implementando recursos.

O **Blumiga** é um framework PHP ultra-leve, veloz e seguro, estruturado sob o paradigma **MVC (Model-View-Controller)** adaptado para funções modernas isoladas por Namespaces. Desenvolvido para entregar máxima performance e controle absoluto sobre o fluxo da aplicação, o ecossistema elimina o overhead de ferramentas inchadas ao integrar um sistema de rotas dinâmicas, um **Query Builder nativo especialista (MySQLi)** e um **Mecanismo de Cache em Disco** de alta velocidade.

---

## 🛠️ Requisitos do Sistema

Certifique-se de que seu ambiente atende aos requisitos mínimos para garantir a estabilidade do núcleo:

* **PHP:** Versão 8.0 ou superior (com suporte a *Union Types*, tipos estritos e funções CSPRNG).
* **Servidor Web:** Apache (`mod_rewrite` ativo).
* **Banco de Dados:** MySQL 5.7+ ou MariaDB 10.3+.
* **Composer:** Para gerenciamento do autoload estrutural do framework.

### Extensões PHP Obrigatórias (`php.ini`)
* `mysqli`: Conector oficial e motor por trás de todas as operações e *Prepared Statements* seguros.
* `intl`: Mecanismo de internacionalização nativo para moedas, datas e formatação localizada.
* `iconv`: Sanitização de caracteres e geração de Slugs limpos para URLs amigáveis.
* `openssl`: Motor criptográfico para proteção, hashes e tratamento de dados sensíveis.

---

## 📂 Estrutura Arquitetural do Ecossistema

A organização de pastas do Blumiga isola as responsabilidades de forma limpa, protegendo o núcleo e expondo apenas o estritamente necessário:

```text
meu-projeto/
├── app/
│   ├── controllers/    # Controladores da Aplicação (Namespaces Dinâmicos)
│   ├── models/         # Camada de Regras de Negócio e Persistência
│   └── views/          # Camada de Visualização Isolada (HTML / Render)
├── config/
│   ├── config.php      # Constantes de Ambiente, Iniciação de Erros e $dbConfig
│   └── routes.php      # Definições centrais de Rotas e Grupos de Acesso
├── core/
│   ├── database/       # Core Data Layer (database, select, insert, update, delete, table)
│   ├── functions.php   # Helpers Globais de Sanitização, IP, Datas, Idioma e Segurança
│   ├── index.php       # Orquestrador Geral e Ciclo de Vida do Framework
│   └── route.php       # Motor interno de Matching de URLs e Closures
├── public/
│   └── index.php       # Ponto de entrada universal único (Single Entry Point)
├── storage/
│   ├── cache/          # Cache estático gerado em tempo de execução
│   └── logs/           # Registro centralizado de exceções SQL e do Sistema
└── vendor/             # Autoload estrutural e dependências do Composer

```

---

## 🚀 Instalação e Configuração Inicial

### 1. Inicializar o Autoload

Dentro do diretório raiz do projeto, rode o comando:

```bash
composer install
```

### 🗄️ 3. Configuração de Conexão (`config/config.php`)

O construtor do banco lê o array global `$dbConfig` e opcionalmente valida o modo `$blumegaSandbox` para depuração visual:

```php
global $dbConfig, $blumegaSandbox;

$blumegaSandbox = true; // Exibe erros estruturados em tela se em desenvolvimento

$dbConfig = [
    'default' => [
        'server'   => '127.0.0.1',
        'username' => 'root',
        'password' => 'sua_senha',
        'database' => 'blumiga_db',
        'port'     => 3306
    ]
];

```

---

## 📑 Guia de Utilização dos Módulos Principais

### 🛣️ Motor de Rotas (`config/routes.php`)

O Blumiga suporta mapeamento direto, parâmetros dinâmicos baseados em expressões regulares (`{slug}`) e isolamento de escopo via sub-pastas físicas:

```php
// Rota Clássica
routeGET('/contato', 'contatoController@pageContato', 'site.contato');

// Rota Dinâmica Capturada com Parâmetros
routeGET('/produto/{id}/{slug}', 'produtoController@detalhe', 'produto.ver');

// Agrupamento de Escopo e Sub-Namespace Físico (app/controllers/admin/)
routeGroup('admin', function() {
    routeGET('/admin/dashboard', 'dashboardController@index', 'admin.dashboard');
    routePOST('/admin/salvar', 'dashboardController@store');
});

// Manipulador de Fallback para Páginas Não Encontradas
route404(function() {
    view('erros/404');
});

```

### 🎮 Padrão de Controlador (`app/controllers/`)

Os controladores trabalham isolados por namespaces, comunicando-se com modelos e injetando variáveis de escopo nas views de maneira protegida:

```php
<?php
namespace Blumiga\controllers\site\homeController;

function pageHome() {
    // Carrega o model e recupera o namespace gerado dinamicamente
    $modelNs = model('homeModel');
    $dados = ($modelNs . 'getData')();

    // Renderiza a View injetando dados protegidos
    view('home', [
        'titulo' => 'Framework Blumiga',
        'lista'  => $dados
    ]);
}

```

### 🗄️ Query Builder Fluido (`Blumiga\database`)

A camada de dados separa as responsabilidades do CRUD em classes especialistas (`select`, `insert`, `update`, `delete`, `table`), otimizando recursos e mantendo a interface fluida.

#### 🔍 Consultas Avançadas e Prepared Statements (`select`)

```php
use Blumiga\database\select;

$db = new select();

// Exemplo 1: Query fluida com Joins e Condicionais complexas
$usuarios = $db->table('usuarios')
               ->column('id')
               ->column('nome', 'nome_completo')
               ->innerJoin('perfis', 'perfis.usuario_id = usuarios.id')
               ->where('status', 'ativo')
               ->whereIn('nivel_id', [1, 2])
               ->orderby('nome', true)
               ->limit(0, 15)
               ->select()
               ->fetchAll();

// Exemplo 2: Prepared Statement nativo anti-SQLInjection
$resultado = $db->table('usuarios')
                ->where('email', '?')
                ->and()
                ->where('senha', '?')
                ->prepared('contato@localhost', 's')
                ->prepared($senhaHash, 's')
                ->select()
                ->fetchAssoc();

```

#### 📥 Inserção de Dados (`insert`)

```php
use Blumiga\database\insert;

$db = new insert();
$query = $db->table('produtos')
            ->add('nome', 'Mouse Sem Fio')
            ->add('preco', 129.90)
            ->add('estoque', 50)
            ->insert();

$novoId = $query->idinsert();

```

#### 🆙 Atualização de Dados (`update`)

```php
use Blumiga\database\update;

$db = new update();
$db->table('usuarios')
   ->add('ultimo_login', date('Y-m-d H:i:s'))
   ->where('id', 42)
   ->update();

```

#### ❌ Exclusão de Dados (`delete`)

```php
use Blumiga\database\delete;

$db = new delete();
$db->table('sessoes')
   ->where('expira_em', '<', date('Y-m-d H:i:s'))
   ->semIgual() // Remove o operador '=' para aceitar filtros customizados no where
   ->delete();

```

#### 🛠️ Abstração e Manipulação de Schema (`table`)

Criação e modificação de estruturas de tabelas dinamicamente diretamente pelo código:

```php
use Blumiga\database\table;

$schema = new table();
$schema->table('clientes')
       ->engine('InnoDB')
       ->int()->primaryKey()->autoIncrement()->add('id')
       ->varchar(150)->add('razao_social')
       ->varchar(14)->null()->add('cnpj')
       ->decimal(10, 2)->defaultValueZero()->add('limite_credito')
       ->datetime()->add('criado_em')
       ->create();

```

### ⚡ Sistema de Cache Estático Integrado

Otimize gargalos e evite requisições repetitivas salvando dados pesados ou consultas complexas do banco em arquivos temporários em disco:

```php
$cacheKey = 'produtos_destaque_home';

// Verifica a existência e a validade do cache (Ex: 3600 segundos = 1 hora)
if (cacheExists($cacheKey) && !cacheExpired($cacheKey, 3600)) {
    $produtos = getCache($cacheKey);
} else {
    // Se expirado ou inexistente, busca da fonte
    $db = new select();
    $produtos = $db->table('produtos')->where('destaque', 1)->select()->fetchAll();

    // Grava no cache físico para as próximas requisições
    setCache($cacheKey, $produtos);
}
```

### 🛡️ Helpers Globais de Utilidade e Segurança

O Blumiga traz funções prontas de escopo global projetadas para agilizar e blindar o desenvolvimento:

* **Proteção XSS (`e()`)**: Escapa strings dinâmicas antes da renderização na View.
```php
<h1><?php echo e($titulo); ?></h1>
```
* **Inputs Tratados (`inputPOST()` / `inputGET()`)**: Lê buffers de requisição aplicando filtros nativos, contornando o uso inseguro de superglobais brutas.

```php
$email = inputPOST('email', FILTER_VALIDATE_EMAIL);
```

* **Geração de URL Reversa (`route()`)**: Constrói caminhos absolutos baseando-se nos apelidos configurados no arquivo de rotas.
```php
<a href="<?php echo route('produto.ver', ['id' => 5, 'slug' => 'mouse']); ?>">Ver Produto</a>
```

* **Internacionalização Dinâmica**: Funções auxiliares calibradas para responder conforme a localidade atual do usuário:
```php
echo dayOfWeek('now'); // Exemplo: "Segunda-feira"
echo formatCurrency(2490.50); // Exemplo: R$ 2.490,50
```
---

## 🛡️ Camada de Mitigação de Vulnerabilidades Nativa

1. **Injeção de SQL (SQLi):** Mitigada através do uso sistemático de `mysqli_stmt_bind_param` em consultas que utilizam o método `->prepared()`. Além disso, dados textuais passados em métodos literais como `whereIn` ou `whereBetween` passam obrigatoriamente por `mysqli_real_escape_string`.
2. **Cross-Site Scripting (XSS):** Neutralizado por meio da função global de escape automático `e($string)`.
3. **Injeção de Path/Directory Traversal:** Parâmetros que referenciam caminhos físicos de arquivos de views e models passam por um tratamento rigoroso de substituição de strings (`str_replace`), inviabilizando saltos maliciosos de diretório.

---

## 👤 Autor

**Murilo Gomes Julio**

🔗 [https://www.bluice.com.br](https://www.bluice.com.br)

📺 [https://youtube.com/@bluiceoficial](https://youtube.com/@bluiceoficial)

---

## License

The Blumiga is provided under:

[SPDX-License-Identifier: GPL-2.0-only](https://codeberg.org/bluice/blumiga/src/branch/main/LICENSE)

Beign under the terms of the GNU General Public License version 2 only.

All contributions to the Blumiga are subject to this license.
