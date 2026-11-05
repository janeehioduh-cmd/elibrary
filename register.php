<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Member Registration | Otukpo E-Library</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- NAVIGATION -->
    <nav class="site-nav">

        <a href="index.php" class="brand">
            OTUKPO E-LIBRARY
        </a>

        <div class="nav-links">

            <a href="index.php">Home</a>
            <a href="books.php">Books</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>

            <a href="login.php" class="login-btn">Login</a>

        </div>

    </nav>


    <!-- PAGE HEADER -->
    <section class="catalogue-hero">

        <h1>Create Your Account</h1>

        <p>Join Otukpo E-Library and start exploring knowledge.</p>

    </section>


    <!-- REGISTRATION FORM -->
    <section class="section">

        <div class="details-card">

            <h2>Member Registration</h2>

            <form
                action="processes/register_process.php"
                method="POST"
                enctype="multipart/form-data"
            >

                <label for="name">Full Name</label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control"
                    placeholder="Enter your full name"
                    required
                >


                <label for="email">Email Address</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    placeholder="Enter your email address"
                    required
                >


                <label for="phone">Phone Number</label>

                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    class="form-control"
                    placeholder="Enter your phone number"
                    required
                >


                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    placeholder="Create a password"
                    required
                >

                <label for="confirm_password">Confirm Password</label>

<input
    type="password"
    id="confirm_password"
    name="confirm_password"
    class="form-control"
    placeholder="Confirm your password"
    required
>


                <!-- PROFILE PICTURE -->
                <label for="profile_image">
                    Profile Picture (Optional)
                </label>

                <input
                    type="file"
                    id="profile_image"
                    name="profile_image"
                    class="form-control"
                    accept="image/jpeg,image/png,image/webp"
                >

                <p style="font-size: 13px; color: #705E52;">
                    Upload a clear headshot. Accepted formats: JPG,
                    PNG and WEBP.
                </p>


                <button type="submit" class="btn-gold">
                    Create Account
                </button>

            </form>


            <p style="margin-top: 25px;">

                Already have an account?

                <a href="login.php">Login here</a>.

            </p>

        </div>

    </section>


    <!-- FOOTER -->
    <footer class="site-footer">

        <h3>Otukpo E-Library</h3>

        <p>Knowledge, Learning and Discovery.</p>

        <p>
            &copy; <?php echo date('Y'); ?>
            Otukpo E-Library. All rights reserved.
        </p>

    </footer>

</body>

</html>