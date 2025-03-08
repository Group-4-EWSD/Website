<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Our System</title>
</head>
<body>
    <h1>Hello, {{ $user->user_name }}!</h1>
    <p>Thank you for joining us.</p>
    <p>Your registered email: {{ $user->user_email }}</p>
</body>
</html>
