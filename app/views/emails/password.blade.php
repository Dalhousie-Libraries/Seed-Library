<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <p>Hello, {{$name}}! Thank you for joining the Seed Lending Library!</p>

        <p>Click on the link below to activate your account (or copy it and paste it on your browser's address bar).</p>
        <p>
            <a href="{{URL::to('activate')}}/{{$activationLink}}">
                {{URL::to('activate')}}/{{$activationLink}}
            </a>
        </p>
    </body>
</html>