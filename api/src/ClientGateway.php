<?php

class ClientGateway implements GatewayInterface
{
    public function __construct(private StorageInterface $storage)
    {
    }

    private function getInitials(string $names): string
    {
        $initials = '';
        $firstnames = preg_split("/[\s,_-]+/", strip_tags($names));
        foreach ($firstnames as $firstname) {
            $initials .= mb_substr($firstname, 0, 1);
        }

        return strtoupper($initials);
    }
    
    public function getAll(): array
    {
        $clients = $this->storage->index('clients');
        $data = [];

        foreach ($clients as $client) {
            $data[] = [
                'initials' => $this->getInitials($client['firstname']) . $this->getInitials($client['lastname']),
                'year' => (new DateTime($client['birthday']))->format('Y'),
            ];
        }

        return $data;
    }
    
    public function create(array $data): bool
    {
        return $this->storage->store('clients', [
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'birthday' => $data['birthday'],
        ]);
    }
    
    public function deleteAll(): bool
    {
        return $this->storage->destroy('clients');
    }
}











