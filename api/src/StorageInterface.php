<?php

interface StorageInterface
{
    public function index(string $entity): array;

    public function store(string $entity, array $data): bool;

    public function destroy(string $entity): bool;
}