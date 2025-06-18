<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Configuration for FCM
    |--------------------------------------------------------------------------
    */
    
    'credentials' => [
        'file' => storage_path('app/firebase/firebase-credentials.json'),
    ],

    'project_id' => env('FIREBASE_PROJECT_ID', 'tata-print'),

    'messaging' => [
        'sender_id' => env('FIREBASE_SENDER_ID', '813275722990'),
    ],
]; 