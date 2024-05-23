# Implementação do http-message PSR-7

### Exemplos

```php
$serverRequest = new ServerRequest('POST', 'http://localhost:8000');

$body = $serverRequest->getParsedBody();
echo $body['name']; // display name field send from request 
```

### Executando testes
```bash
composer test
```