<?php

namespace App;


class TwilioAppSettings
{
    protected $account_sid;

    protected $auth_token;

    public function __construct()
    {
        $this->account_sid = getenv("TWILIO_ACCOUNT_SID")
        or die("TWILIO_ACCOUNT_SID is not set in the environment");
        $this->auth_token = getenv("TWILIO_AUTH_TOKEN")
        or die("TWILIO_AUTH_TOKEN is not set in the environment");
    }

    function getAccountSid()
    {
        return $this->account_sid;
    }

    function getAuthToken()
    {
        return $this->auth_token;
    }

}