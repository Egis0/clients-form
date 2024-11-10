<?php

class FileStorage implements StorageInterface
{
    public function __construct(private string $file)
    {
    }

    private function read_file(): array
    {
        return json_decode(file_get_contents($this->file), true) ?? [];
    }

    public function index(string $entity): array
    {
        $existing_data = $this->read_file();

        return $existing_data[$entity] ?? [];
    }

    public function store(string $entity, array $data): bool
    {
        $existing_data = $this->read_file();
        if (empty($existing_data[$entity])) {
            $existing_data[$entity] = [];
        }
        array_unshift($existing_data[$entity], $data);

        return (bool) file_put_contents($this->file, json_encode($existing_data));
    }

    public function destroy(string $entity): bool
    {
        $existing_data = $this->read_file();
        if (empty($existing_data[$entity])) {
            return true;
        }
        unset($existing_data[$entity]);

        return (bool) file_put_contents($this->file, json_encode($existing_data));
    }
}









