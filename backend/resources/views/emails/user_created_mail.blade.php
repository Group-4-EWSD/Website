<!DOCTYPE html>
<html>
<head>
    <title>Account Created Successful</title>
</head>
<body>
    <h2>Hello {{ $user_name }},</h2>
    <p>Your account has been created successfully.</p>
    <p>Your email: <strong>{{ $user_email }}</strong></p>
    <p>Your password: <strong>{{ $password }}</strong></p>
    <p>Please log in and change your password immediately.</p>
    <br>
    <p>Thank you!</p>
</body>
</html>
