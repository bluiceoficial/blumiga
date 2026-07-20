<div class="section">
    <h2>🗄️ Exemplo — Banco de Dados</h2>
    <p>Dados carregados via <code>model('usuarioModel')</code> com mysqli procedural.</p>

    <div class="alert alert-info">
        <strong>💡 Fluxo:</strong> Controller → <code>model('usuarioModel')</code> →
        <code>mysqli_connect</code> → <code>mysqli_query</code> →
        <code>mysqli_fetch_all</code> → <code>mysqli_free_result</code> →
        <code>mysqli_close</code> → View.
    </div>

    <?php if (empty($usuarios)): ?>
        <div class="alert alert-warning">
            Nenhum usuário encontrado. Execute <span class="command"><span class="prompt">$</span> php blumiga migrate && php blumiga db:seed</span>
            para criar a tabela e inserir dados de exemplo.
        </div>
    <?php else: ?>
        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-value"><?php echo $total ?></div>
                <div class="stat-label">Total de usuários</div>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Criado em</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?php echo e($u['id']) ?></td>
                            <td><?php echo e($u['nome']) ?></td>
                            <td><?php echo e($u['email']) ?></td>
                            <td><?php echo e($u['criado_em']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="mt-3">
        <a href="<?php echo route('home') ?>" class="btn btn-outline">← Voltar</a>
    </div>
</div>
