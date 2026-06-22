<?php
// Copyright (C) 2026 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Site: https://www.bluice.com.br

namespace Blumiga\cache;

interface cacheInterface {
    public function get(string $chave): mixed;
    public function save(string $chave, mixed $dados, int $ttl): bool;
}
