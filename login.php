<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Login | Otukpo E-Library</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <nav class="site-nav">
        <a href="index.php" class="brand">OTUKPO E-LIBRARY</a>

        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="books.php">Books</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
            <a href="register.php">Register</a>
        </div>
    </nav>

    <section class="catalogue-hero">
        <h1>Welcome Back</h1>
        <p>Login to access your Otukpo E-Library account.</p>
    </section>

    <section class="section">

        <div class="details-card">

            <h2>Member Login</h2>

            <form action="processes/login_process.php" method="POST">

                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    placeholder="Enter your email address"
                    required
                >

                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    placeholder="Enter your password"
                    required
                >

                <button type="submit" class="btn-gold">
                    Login
                </button>

            </form>

            <p style="margin-top: 25px;">
                Don't have an account?
                <a href="register.php">Register here</a>.
            </p>

        </div>

    </section>

    <footer class="site-footer">
        <h3>Otukpo E-Library</h3>
        <p>Knowledge, Learning and Discovery.</p>
        <p>&copy; <?php echo date('Y'); ?> Otukpo E-Library. All rights reserved.</p>
    </footer>

</body>
</html>