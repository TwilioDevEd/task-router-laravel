<a href="https://www.twilio.com">
  <img src="https://static0.twilio.com/marketing/bundles/marketing/img/logos/wordmark-red.svg" alt="Twilio" width="250" />
</a>

# TaskRouter on Laravel

[![Build Status](https://travis-ci.org/TwilioDevEd/task-router-laravel.svg?branch=master)](https://travis-ci.org/TwilioDevEd/task-router-laravel)

Use Twilio to provide your user with multiple options through phone calls, so
they can be assisted by an agent specialized in the chosen topic. This is
basically a call center created with the Task Router API of Twilio. This example
uses a SQLite database to log phone calls which were not assisted.

[Read the full tutorial here](//www.twilio.com/docs/tutorials/walkthrough/task-router/php/laravel)

## Local Development

This project is build using [Laravel](http://laravel.com/) web framework;

1. First clone this repository and `cd` into it.

   ```bash
   $ git clone git@github.com:TwilioDevEd/task-router-laravel.git
   $ cd task-router-laravel
   ```

1. Run the Setup script to configure your project.

   ```bash
   $ php Setup.php
   ```
   This will:
   * Create your [SQLite](https://www.sqlite.org/) database file.
   * Create your `.env` file for you to add your private credentials.

1. Edit your `.env` file to match your configuration.

1. Install the dependencies with [Composer](https://getcomposer.org/).

   ```bash
   $ composer install
   ```

1. Generate an `APP_KEY`.

   ```bash
   $ php artisan key:generate
   ```

1. Run the migrations.

   ```bash
   $ php artisan migrate
   ```

1. Expose your application to the wider internet using [ngrok](http://ngrok.com).

   This step is important because the application won't work as expected
   if you run it through localhost.

   ```bash
   $ ngrok http 8000
   ```

   Once ngrok is running, open up your browser and go to your ngrok URL. It will
   look something like this: `http://<sub-domain>.ngrok.io`

1. Configure a Task Router workflow.

   This application ships with an Artisan command to create and configure the workflow
   necessary for this application to work. You need to execute this command before running
   this app.

   ```
   $ php artisan workspace:create http://<sub-domain>.ngrok.io <bob_phone> <alice_phone>
   ```

   The command will modify your `.env` file with some additional environment variables.

1. Configure Twilio to call your webhooks

   You will also need to configure Twilio to call your application when calls or SMSs are received on your `TWILIO_NUMBER`. Your urls should look something like this:

   ```
   voice: http://<sub-domain>.ngrok.io/call/incoming (POST)

   sms:   http://<sub-domain>.ngrok.io/message/incoming (POST)
   ```

   ![Configure webhooks](http://howtodocs.s3.amazonaws.com/twilio-number-config-all-med.gif)

1. Make sure the tests succeed.

   ```bash
   $ ./vendor/bin/phpunit
   ```

1. Start the server.

   ```bash
   $ php artisan serve
   ```

1. Check it out at [http://localhost:8000](http://localhost:8000).

### How To Demo?

1. Call your Twilio Phone Number. You will get a voice response:

  > For Programmable SMS, press one.
  For Voice, press any other key.

1. Select and option and the phone assigned to the product you selected (Bob or Alice's)
   will start ringing. You can answer the call and have a conversation.

1. Alternatively, if you don't answer the call for 15 seconds, the call should be
   redirected to the next worker. If the call isn't answered by the second worker,
   you should be redirected to voice mail and leave a message. The transcription
   of that message should be sent to the email you specified in your environment variables.

1. Each time a worker misses a call, their activity is changed to offline. You should
   receive an SMS notification to the number that missed the call. You can reply
   with `On` or `Off` to this SMS in order to change a worker's status.

1. If both workers' activity changes to `Offline` and you call your Twilio Number again,
   you should be redirected to voice mail after a few seconds as the workflow timeouts
   when there are no available workers. Change your workers status with the `On`
   SMS command to be able to receive calls again.

1. Navigate to `https://<ngrok_subdomain>.ngrok.io` to see a list of the missed calls.

## Meta

* No warranty expressed or implied. Software is as is. Diggity.
* [MIT License](http://www.opensource.org/licenses/mit-license.html)
* Lovingly crafted by Twilio Developer Education.
