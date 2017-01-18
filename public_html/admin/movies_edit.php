<?php
if (isset($_POST['submit']) && isset($_POST['id'])) {

    if (empty($_POST['id']))
        echo '<p style="color: red;">Error: No ID specified.</p>';
    elseif ($_POST['submit'] === 'Update') {
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
            echo '<p class="text-danger">Error: Missing data!</p>';
        } else {
            $query = "SELECT id FROM kfs_movies WHERE id=:id;";
            $stmt = $db->prepare($query);
            $stmt->bindValue(':id', $_POST['id']);
            $stmt->execute();
            if ($stmt->rowCount() < 1)
                echo '<p class="text-danger">Invalid ID!</p>';
            else {
                $query = "UPDATE kfs_movies "
                    . "SET `title`=:title, `orig_title`=:orig, `genre`=:genre, `country`=:country, `director`=:director, `length`=:length, `imdb`=:imdb, `image`=:image, `date`=:date, `year`=:year, `description`=:description "
                    . "WHERE id=:id;";
                $stmt = $db->prepare($query);
                $stmt->bindValue(':id', $_POST['id']);
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
                if ($stmt->rowCount() < 1) {
                    echo '<p class="text-danger">Failed to update movie entry!</p>';
                    echo '<p><a href="movies.php">Go back</a></p>';
                } else {
                    ?>
                    <p class="text-success"><?php echo $title; ?> has been updated!</p>
                    <p><a class="btn btn-link" href="./movies.php">Go back</a></p>
                <?php
                }
            }
        }
    } else {
        $query = "SELECT `title`, `orig_title`, `genre`, `country`, `director`, `length`, `imdb`, `image`, `date`, `year`, `description` "
            . "FROM kfs_movies "
            . "WHERE id=:id;";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', $_POST['id']);
        $stmt->execute();
        if ($stmt->rowCount() < 1)
            echo '<p class="text-danger">Failed to insert new movie entry!</p>';
        else {
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            $title = $result->title;
            $orig = $result->orig_title;
            $image = $result->image;
            $imdb = $result->imdb;
            $year = $result->year;
            $date = $result->date;
            $director = $result->director;
            $length = $result->length;
            $country = $result->country;
            $genre = $result->genre;
            $description = $result->description;
            ?>
            <h2>Edit details for <?php echo $title; ?></h2>
            <p><a href="./movies.php">Go back</a></p>
            <?php
            $form = new MovieForm($title, $orig, $image, $imdb, $year, $date, $director, $length, $country, $genre, $description);
            $form->printForm("Update", $_POST['id']);
        }
    }
} else
    echo '<p style="color: red;">Error: Invalid request</p>';
?>
