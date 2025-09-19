<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */
// in config/cors.php

'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register'], // UPDATED

'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:5173')], // UPDATED

'supports_credentials' => true, // UPDATED

];