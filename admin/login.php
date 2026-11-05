<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Otukpo E-Library</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <nav class="site-nav">
        <a href="../index.php" class="brand">OTUKPO E-LIBRARY</a>

        <div class="nav-links">
            <a href="../index.php">Home</a>
            <a href="../books.php">Books</a>
        </div>
    </nav>

    <section class="catalogue-hero">
        <h1>Administrator Login</h1>
        <p>Access the library management system.</p>
    </section>

    <section class="section">

        <div class="details-card">

            <h2>Admin Login</h2>

            <form action="../processes/admin_login_process.php" method="POST">

                <label for="email">Admin Email</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    placeholder="Enter admin email"
                    required
                >

                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    placeholder="Enter admin password"
                    required
                >

                <button type="submit" class="btn-gold">
                    Login to Dashboard
                </button>

            </form>

            <p style="margin-top: 25px;">
                <a href="../index.php">Back to Homepage</a>
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