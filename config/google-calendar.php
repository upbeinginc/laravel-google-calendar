<?php

return [

    'default_auth_profile' => env('GOOGLE_CALENDAR_AUTH_PROFILE', 'oauth'),

    'auth_profiles' => [

        /*
        //  * Authenticate using a service account.
        //  */
        // 'service_account' => [
        //     /*
        //      * Path to the json file containing the credentials.
        //      */
        //     'credentials_json' => storage_path('app/google-calendar/service-account-credentials.json'),
        // ],

        /*
         * Authenticate with actual google user account.
         */
        'oauth' => [
            /*
             * Path to the json file containing the oauth2 credentials.
             */
            'credentials_json' => env('GOOGLE_CALENDAR_OAUTH_CREDENTIALS_JSON', '{
                "web": {
                    "client_id": "test-id",
                    "project_id": "test-project",
                    "auth_uri": "https://accounts.google.com/o/oauth2/auth",
                    "token_uri": "https://oauth2.googleapis.com/token",
                    "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
                    "client_secret": "test-secret",
                    "redirect_uris": [
                        "http://localhost/auth/google/response",
                        "https://localhost/auth/google/response",
                        "http://localhost/login/google/response",
                    ],
                    "javascript_origins": [
                        "http://localhost",
                        "https://localhost",
                    ]
                }
            }'),
        ],
    ],

    /*
     *  The id of the Google Calendar that will be used by default.
     */
    'calendar_id' => env('GOOGLE_CALENDAR_ID', 'personal'),

     /*
     *  The email address of the user account to impersonate.
     */
    'user_to_impersonate' => env('GOOGLE_CALENDAR_IMPERSONATE'),
];
