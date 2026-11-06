<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../config/database.php";

$db = new Database();
$conn = $db->connect();

// Get books ranked by successful borrowing records
$sql = "SELECT
            books.id,
            books.title,
            books.author,
            COUNT(borrowings.id) AS total_borrows
        FROM books
        LEFT JOIN borrowings
            ON books.id = borrowings.book_id
            AND borrowings.status IN ('borrowed', 'returned')
        GROUP BY books.id, books.title, books.author
        ORDER BY total_borrows DESC, books.title ASC";

$stmt = $conn->prepare($sql);
$stmt->execute();

$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Most Borrowed Books | Otukpo E-Library</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .report-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 25px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            background: #fff;
        }

        .report-table th,
        .report-table td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .report-table th {
            background: #3B2418;
            color: #fff;
        }

        .report-table tr:hover {
            background: #F5E9D5;
        }

        .rank-number {
            font-weight: bold;
            color: #D4AF37;
        }

        .total-borrows {
            font-weight: bold;
            color: #3B2418;
        }

        .report-back {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 22px;
            background: #3B2418;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .empty-report {
            padding: 25px;
            background: #F5E9D5;
            border-radius: 8px;
            text-align: center;
        }

        @media (max-width: 600px) {
            .report-container {
                padding: 12px;
                overflow-x: auto;
            }

            .report-table th,
            .report-table td {
                padding: 10px;
                font-size: 13px;
            }
        }
    </style>

</head>

<body>

    <section class="catalogue-hero">

        <h1>Most Borrowed Books</h1>

        <p>
            Discover the most popular books in Otukpo E-Library.
        </p>

    </section>

    <section class="report-container">

        <h2>Book Borrowing Rankings</h2>

        <p>
            Books are ranked according to the number of successful borrowing records.
        </p>

        <?php if (!empty($books)): ?>

            <table class="report-table">

                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Times Borrowed</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($books as $index => $book): ?>

                        <tr>

                            <td class="rank-number">
                                <?php echo $index + 1; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($book['title']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($book['author']); ?>
                            </td>

                            <td class="total-borrows">
                                <?php echo (int) $book['total_borrows']; ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="empty-report">
                No books are available for this report yet.
            </div>

        <?php endif; ?>

        <a href="dashboard.php" class="report-back">
            Back to Admin Dashboard
        </a>

    </section>

</body>

</html>