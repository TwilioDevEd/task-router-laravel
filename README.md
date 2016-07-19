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

1. Install the dependencies with [Composer](https://getcomposer.org/).

   ```bash
   $ composer install
   ```

1. Generate an `APP_KEY`.

   ```bash
   $ php artisan key:generate
   ```

1. Run the Setup script to configure your project.

   ```bash
   $ php Setup.php
   ```
   This will:
   * Create your [SQLite](https://www.sqlite.org/) database file.
   * Create your `.env` file for you to add your private credentials.

1. Run the migrations.
   ```bash
   $ php artisan migrate
   ```

1. Seed the database.

   ```bash
   $ php artisan db:seed
   ```

1. Make sure the tests succeed.

   ```bash
   $ ./vendor/bin/phpunit
   ```

1. Start the server.

   ```bash
   $ php artisan serve
   ```

1. Check it out at [http://localhost:8000](http://localhost:8000).

### Expose the Application to the Wider Internet

1. Expose your application to the wider internet using [ngrok](http://ngrok.com).
   You can click[here](#expose-the-application-to-the-wider-internet) for more
   details. This step is important because the application won't work as expected
   if you run it through localhost.

  ```bash
  $ ngrok http 3000
  ```

  Once ngrok is running, open up your browser and go to your ngrok URL. It will
  look something like this: `http://<sub-domain>.ngrok.io`

1. Configure Twilio to call your webhooks.

  You will also need to configure Twilio to call your application when calls are received
  on your _Twilio Number_. The **SMS & MMS Request URL** should look something like this:

  ```
  http://<sub-domain>.ngrok.io/directory/search
  ```

  ![Configure SMS](http://howtodocs.s3.amazonaws.com/twilio-number-config-all-med.gif)

### How To Demo?

1. Call your Twilio Phone Number. You will get a voice response:

  > For Programmable SMS, press one.
  For Voice, press any other key.

1. Reply with 1.
1. The specified phone for agent 1 will be called:  __agent1-phone__.
1. If __agent1-phone__ is not answered in 30 seconds then __agent2-phone__ will
  be called.
1. In case the second agent doesn't answer the call, it will be logged as a
  missed call. You can see all missed calls in the main page of the running
  server at [http://{sub-domain}.ngrok.io](//localhost:8000).
1. Repeat the process but enter any key different to __1__ to choose Voice.

 [twilio-phone-number]: https://www.twilio.com/console/phone-numbers/incoming

 ## Meta

 * No warranty expressed or implied. Software is as is. Diggity.
 * [MIT License](http://www.opensource.org/licenses/mit-license.html)
 * Lovingly crafted by Twilio Developer Education.