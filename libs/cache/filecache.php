<?php
// Copyright (C) 2026 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Site: https://www.bluice.com.br

namespace Blumiga\cache;

class filecache implements cacheInterface {
    private string $cacheDir;

    public function __construct(?string $cacheDir = null) {
        if ($cacheDir === null) {
            $cacheDir = dirname(__FILE__, 3) . '/storage/cache/';
        }
        $this->cacheDir = rtrim($cacheDir, '/') . '/';
    }

    private function getCachePath(string $chave): string {
        return $this->cacheDir . md5($chave) . '.cache';
    }

    public function save(string $chave, mixed $dados, int $ttl): bool {
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }

        $arquivo = $this->getCachePath($chave);
        $conteudo = [
            'expira_em' => time() + $ttl,
            'dados'     => $dados
        ];

        return file_put_contents($arquivo, serialize($conteudo)) !== false;
    }

    public function get(string $chave): mixed {
        $arquivo = $this->getCachePath($chave);

        if (!file_exists($arquivo)) {
            return null;
        }

        $conteudo = unserialize(file_get_contents($arquivo));

        // Validação do Ciclo de Vida (TTL)
        if (time() > $conteudo['expira_em']) {
            @unlink($arquivo);
            return null;
        }

        return $conteudo['dados'];
    }
}
