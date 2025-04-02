<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Account Created</title>
</head>
<body>
    <p>Welcome, {{ $user_name }}!</p>
    <p>Your account has been successfully created with the following details:</p>
    <p><strong>Email:</strong> {{ $user_email }}</p>
    <p><strong>Temporary Password:</strong> {{ $new_password }}</p>
    <p>For security reasons, we strongly recommend changing your password after logging in.</p>
    <p>If you did not request this account, please contact support immediately.</p>
</body>
</html>
