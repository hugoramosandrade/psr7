# Implementation of http-message PSR-7

### Examples

```php
$serverRequest = new ServerRequest('POST', 'http://localhost:8000');

$body = $serverRequest->getParsedBody();
echo $body['name']; // display name field send from request 
```

### Running tests
```bash
composer test
```