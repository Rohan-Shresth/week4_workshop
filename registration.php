<?php
$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {

        $file = "users.json";

        if (file_exists($file)) {
            $data = file_get_contents($file);
            $users = json_decode($data, true);
            if (!is_array($users)) {
                $users = [];
            }
        } else {
            $users = [];
        }

        foreach ($users as $user) {
            if ($user["email"] === $email) {
                $error = "Email already registered.";
                break;
            }
        }

        if ($error === "") {
            $users[] = [
                "username" => $username,
                "email" => $email,
                "password" => password_hash($password, PASSWORD_DEFAULT)
            ];

            $jsonData = json_encode($users, JSON_PRETTY_PRINT);

            if (file_put_contents($file, $jsonData)) {
                $message = "Registration successful!";
            } else {
                $error = "Failed to save user.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <style>
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>

<h2>User Registration</h2>

<form method="post">
    <input type="text" name="username" placeholder="Username"><br><br>
    <input type="email" name="email" placeholder="Email"><br><br>
    <input type="password" name="password" placeholder="Password"><br><br>
    <input type="password" name="confirm_password" placeholder="Confirm Password"><br><br>
    <button type="submit">Register</button>
</form>

<?php if ($error): ?>
    <p class="error"><?php echo $error; ?></p>
<?php endif; ?>

<?php if ($message): ?>
    <p class="success"><?php echo $message; ?></p>
<?php endif; ?>

</body>
</html>
