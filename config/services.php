<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'twilio' => [
        /**
         * Taken from the environment
         */
        'accountSid' => env('TWILIO_ACCOUNT_SID'),
        'authToken' => env('TWILIO_AUTH_TOKEN'),
        'number' => env('TWILIO_NUMBER'),
        'missedCallsEmail' => env('MISSED_CALLS_EMAIL_ADDRESS'),

        /**
         * These are created with the workspace:create command for Artisan
         */
        'workspaceSid' => env('WORKSPACE_SID'),
        'workflowSid' => env('WORKFLOW_SID'),
        'postWorkActivitySid' => env('POST_WORK_ACTIVITY_SID'),
        'phoneToWorker' => env('PHONE_TO_WORKER'),

        /**
         * TaskRouter
         */
        'missedCallEvents' => ["workflow.timeout", "task.canceled"],
        'leaveMessage' => "Sorry, All agents are busy. Please leave a message. We will call you as soon as possible",
        'offlineMessage' => 'Your status has changed to Offline. Reply with "On" to get back Online'
    ]
];
