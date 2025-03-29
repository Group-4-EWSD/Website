<!DOCTYPE html>
<html>
<head>
    <title>Password Reset Successful</title>
</head>
<body>
    <h2>Hello {{ $user_name }},</h2>
    <p>Your password has been reset successfully.</p>
    <p>Your new password: <strong>{{ $new_password }}</strong></p>
    <p>Please log in and change your password immediately.</p>
    <br>
    <p>Thank you!</p>
</body>
</html>
