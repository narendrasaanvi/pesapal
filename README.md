**Step 1**

PESAPAL_KEY=qkio1BGGYAXTu2JOfm7XSXNruoZsrqEW

PESAPAL_SECRET=osGQ364R49cXKeOYSpaOnT++rHs=


**Step 2**
config/services.php

    'pesapal' => [
      'key' => env('PESAPAL_KEY'),
      'secret' => env('PESAPAL_SECRET'),
    ],

 
