<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve database credentials from the form submission
    $host = "localhost";
    $dbname = htmlspecialchars($_POST['dbname']);
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    try {
        // Establish a database connection
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // SQL query to get all categories and their respective movies
        $sql = "SELECT category, title FROM movies ORDER BY category ASC, title ASC";
        $stmt = $pdo->query($sql);

        // Prepare to track global uniqueness
        $globalMovies = []; // Global list of all movies to avoid duplicates
        $categoryData = []; // Category-wise data

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $category = htmlspecialchars($row['category']);
            $title = htmlspecialchars($row['title']);

            // Add the movie to the category only if it's not already in the global list
            if (!in_array($title, $globalMovies)) {
                $globalMovies[] = $title;
                if (!isset($categoryData[$category])) {
                    $categoryData[$category] = [];
                }
                $categoryData[$category][] = $title;
            }
        }

        // Write the data to the text file
        $filename = "categories_movies.txt";
        $file = fopen($filename, "w");

        // Start HTML output
        echo "<!DOCTYPE html>";
        echo "<html lang='en'>";
        echo "<head><meta charset='UTF-8'><title>Movies by Category</title></head>";
        echo "<body>";
        echo "<h1>Movies by Category</h1>";
        echo "<table border='1'>";
        echo "<tr><th>Category</th><th>Count of Movies</th><th>Movies</th></tr>";

        // Process and write each category
        foreach ($categoryData as $category => $movies) {
            $movieCount = count($movies);

            // Join movie titles with a comma and a space for better formatting
            $movieList = implode(', ', $movies);

            // Write to the text file
            fwrite($file, "$category:$movieCount:$movieList;\n");

            // Output to HTML table
            echo "<tr>";
            echo "<td>$category</td>";
            echo "<td>$movieCount</td>";
            echo "<td>$movieList</td>";
            echo "</tr>";
        }

        // Close the file and HTML output
        fclose($file);
        echo "</table>";
        echo "<p>Data has been successfully written to <a href='$filename'>$filename</a>.</p>";
        echo "</body>";
        echo "</html>";

    } catch (PDOException $e) {
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    // Display the form to input database credentials
    echo "<!DOCTYPE html>";
    echo "<html lang='en'>";
    echo "<head><meta charset='UTF-8'><title>Enter Database Credentials</title></head>";
    echo "<body>";
    echo "<h1>Enter Database Credentials</h1>";
    echo "<form method='POST'>";
    echo "<label for='dbname'>Database Name:</label><br>";
    echo "<input type='text' id='dbname' name='dbname' required><br><br>";
    echo "<label for='username'>Username:</label><br>";
    echo "<input type='text' id='username' name='username' required><br><br>";
    echo "<label for='password'>Password:</label><br>";
    echo "<input type='password' id='password' name='password' required><br><br>";
    echo "<button type='submit'>Submit</button>";
    echo "</form>";
    echo "</body>";
    echo "</html>";
}
?>
