# rest-api-wordpress

## Utilizar o JWT
Defina essas duas linhas no seu wp-config.php

[Link para adquiri um token](https://api.wordpress.org/secret-key/1.1/salt)

```php
define('JWT_AUTH_SECRET_KEY', 'your_token');
define('JWT_AUTH_CORS_ENABLE', true);
```