<?php

class ClientController
{
    public function __construct(private GatewayInterface $gateway)
    {
    }
    
    public function processRequest(string $method): void
    {
        switch ($method) {
            case 'GET':
                echo json_encode(['data' => $this->gateway->getAll()]);
                break;
                
            case 'POST':
                $data = (array) json_decode(file_get_contents('php://input'), true);
                
                $errors = $this->validate($data);
                
                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode(['errors' => $errors]);
                    break;
                }
                
                if ($this->gateway->create($data)) {
                    http_response_code(201);
                    echo json_encode(['message' => 'Klientas sėkmingai išsaugotas']);
                } else {
                    http_response_code(422);
                }
                break;

            case 'DELETE':
                if ($this->gateway->deleteAll()) {
                    echo json_encode(['message' => 'Klientai sėkmingai ištrinti']);
                } else {
                    http_response_code(404);
                }
                break;
            
            default:
                http_response_code(405);
                header('Allow: GET, POST, DELETE');
        }
    }
    
    private function validate(array $data): array
    {
        $errors = [];
        $required = ['firstname', 'lastname', 'birthday'];
        $translations = [
            'firstname' => 'Vardas',
            'lastname' => 'Pavardė',
            'birthday' => 'Gimimo data',
        ];

        foreach ($required as $field_name) {
            if (empty($data[$field_name])) {
                $errors[] = "Laukas '$translations[$field_name]' privalo būti užpildytas";
            }
        }

        if (!empty($data['birthday'])) {
            $format = 'Y-m-d';
            $dateTime = DateTime::createFromFormat($format, $data['birthday']);
            if (!$dateTime) {
                $errors[] = 'Gimimo dienos formatas yra neteisingas';
            } elseif ($dateTime->format($format) != $data['birthday']) {
                $errors[] = 'Gimimo dienos data nėra egzistuojanti';
            }
        }
        
        return $errors;
    }
}









