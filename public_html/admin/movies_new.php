<?php
if (isset($_POST['submit']) && $_POST['submit'] == 'Create') {
    $title = $_POST['title'];
    $orig = $_POST['orig_title'];
    $image = $_POST['image'];
    $imdb = $_POST['imdb'];
    $year = $_POST['year'];
    $date = $_POST['date'];
    $director = $_POST['director'];
    $length = $_POST['length'];
    $country = $_POST['country'];
    $genre = $_POST['genre'];
    $description = $_POST['description'];
    if (
        !isset($title) || empty($title)
        || !isset($orig) || empty($orig)
        || !isset($image) || empty($image)
        || !isset($imdb) || empty($imdb)
        || !isset($year) || empty($year)
        || !isset($date) || empty($date)
        || !isset($director) || empty($director)
        || !isset($length) || empty($length)
        || !isset($country) || empty($country)
        || !isset($genre) || empty($genre)
        || !isset($description) || empty($description)
    ) {
        echo '<p style="color: red;">Error: Missing data!</p>';
    } else {
        $query = "INSERT INTO "
            . "kfs_movies(`title`, `orig_title`, `genre`, `country`, `director`, `length`, `imdb`, `image`, `date`, `year`, `description`) "
            . "VALUES(:title, :orig, :genre, :country, :director, :length, :imdb, :image, :date, :year, :description);";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':orig', $orig);
        $stmt->bindValue(':genre', $genre);
        $stmt->bindValue(':country', $country);
        $stmt->bindValue(':director', $director);
        $stmt->bindValue(':length', $length);
        $stmt->bindValue(':imdb', $imdb);
        $stmt->bindValue(':image', $image);
        $stmt->bindValue(':date', $date);
        $stmt->bindValue(':year', $year);
        $stmt->bindValue(':description', $description);
        $stmt->execute();
        if ($stmt->rowCount() < 1)
            echo '<p class="text-danger">Failed to insert new movie entry!</p>';
        else
            echo '<p class="text-success">Created new movie entry (' . $title . ')!';
    }
}
?>
<h2>Create new movies using this form</h2>
<p><a href="movies.php">Go back</a></p>
<?php
    $form = new MovieForm();
    $form->printForm("Create");
?>
