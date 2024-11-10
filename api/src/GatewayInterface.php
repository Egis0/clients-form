<?php

interface GatewayInterface
{
    public function getAll(): array;

    public function create(array $data): bool;

    public function deleteAll(): bool;
}