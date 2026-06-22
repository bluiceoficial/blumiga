<?php
// Copyright (C) 2026 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Site: https://www.bluice.com.br

namespace Blumiga\cache;

class cache {
    private cacheInterface $driver;
    private ?string $pageCacheKey = null;
    private int $pageCacheTtl = 60;

    // O Cache aceita qualquer driver que siga a CacheInterface.
    public function __construct(CacheInterface $driver) {
        $this->driver = $driver;
    }

    // Cache de Dados
    public function save(string $chave, mixed $dados, int $ttl = 300): bool {
        return $this->driver->save($chave, $dados, $ttl);
    }

    public function get(string $chave): mixed {
        return $this->driver->get($chave);
    }

    // Cache de Página Inteira
    public function pageStart(string $id_pagina, int $ttl = 60): void {
        $this->pageCacheKey = 'page_' . md5($id_pagina);
        $this->pageCacheTtl = $ttl;

        $html = $this->driver->get($this->pageCacheKey);

        if ($html !== null) {
            echo $html . "\n";
            exit;
        }

        // Ativa o buffer passando o método interno como callback
        ob_start([$this, 'bufferCallback']);
    }

    // Callback interno do ob_start para capturar e salvar o output
    private function bufferCallback(string $buffer): string {
        if (!empty($buffer) && $this->pageCacheKey !== null) {
            $this->driver->save($this->pageCacheKey, $buffer, $this->pageCacheTtl);
        }
        return $buffer;
    }
}
