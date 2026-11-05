<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Otukpo E-Library</title>

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
            <a href="login.php" class="login-btn">Login</a>
        </div>
    </nav>

    <section class="catalogue-hero">
        <h1>Contact Us</h1>
        <p>We would love to hear from you.</p>
    </section>

    <section class="section">

        <div class="details-card">

            <h2>Get In Touch</h2>

            <p>
                Have questions, suggestions or enquiries?
                Send us a message using the form below.
            </p>

            <form action="processes/contact_process.php" method="POST">

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
                    placeholder="Enter your email"
                    required
                >

                <label for="phone">Phone Number</label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    class="form-control"
                    placeholder="Enter your phone number"
                >

                <label for="message">Your Message</label>
                <textarea
                    id="message"
                    name="message"
                    class="form-control"
                    rows="6"
                    placeholder="Write your message here..."
                    required
                ></textarea>

                <button type="submit" class="btn-gold">
                    Send Message
                </button>

            </form>

        </div>

    </section>

    <footer class="site-footer">
        <h3>Otukpo E-Library</h3>
        <p>Knowledge, Learning and Discovery.</p>
        <p>&copy; <?php echo date('Y'); ?> Otukpo E-Library. All rights reserved.</p>
    </footer>

</body>
</html>