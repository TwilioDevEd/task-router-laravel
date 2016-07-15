<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskRouter for Laravel</title>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" integrity="sha256-MfvZlkHCEqatNoGiOXveE8FIwMzZg4W85qfrfIFBfYc= sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha256-k2/8zcNbxVIh5mnQ52A0r3a6jAgMGxFJFE2707UxGCk= sha512-ZV9KawG2Legkwp3nAlxLIVFudTauWuBpC10uEafMHYL0Sarrz5A7G79kXh5+5+woxQ5HM559XX2UZjMJ36Wplg==" crossorigin="anonymous">
    <link rel="stylesheet" href="css/task-router.css">
</head>
<body>
<div class="container">
    <section class="page-header">
        <h1>Missed Calls</h1>
    </section>
    <section class="body-content">
        <div class="panel panel-default full-height-container">
            <div class="panel-heading"><strong>Missed calls</strong> <span class="text-muted">Product/Number<span></div>
            @if (empty($missed_calls) || $missed_calls->isEmpty())
                <div class="panel-body">
                    <p>There are no missed calls at the moment.</p>
                    <p>Call to your Twilio Phone number:<p>
                    <ul>
                        <li><a href="tel:{{ $twilioNumber }}">{{ formatPhoneNumberToUSInternational($twilioNumber) }}</a></li>
                    </ul>
                </div>
            @else
                <!-- Table -->
                <table class="table">
                    <tbody>
                    @foreach ($missed_calls as $missed_call)
                        <tr>
                            <td>{{ $missed_call->selected_product }}</td>
                            <td>
                                <a href="tel:{{ $missed_call->phone_number }}">
                                    {{ $missed_call->international_phone_number }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </section>
</div>
<footer class="footer">
    <div class="container">
        <p class="text-muted">
            Made with <i class="fa fa-heart"></i> by your pals
            <a href="http://www.twilio.com">@twilio</a>
        </p>
    </div>
</footer>
</body>
</html>
