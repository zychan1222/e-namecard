<!DOCTYPE html>
<html>
<head>
    <title>Account Created</title>
</head>
<body>
    <p>Hello {{ $name }},</p>

    <p>Your account has been created successfully. You can now log in using the following link:</p>

    <p><a href="{{ $loginUrl }}">Login Here</a></p>

    <p>Thank you!</p>
</body>
</html>