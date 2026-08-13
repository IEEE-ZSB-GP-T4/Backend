<?php

return [
    'api_key' => env('GROK_API_KEY'),
    'api_url' => env('GROK_API_URL', 'https://api.x.ai/v1/chat/completions'),
    'model'   => env('GROK_MODEL', 'grok-4'),
];