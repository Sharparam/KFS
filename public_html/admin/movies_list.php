<h2>Movie List</h2>
<p>Edit a movie or toggle its visible state by using the links provided.</p>
<?php
	function printEntry($id, $title, $show) {
        ?>
		<tr>
            <td><?php echo $id; ?></td>
            <td><?php echo $title; ?></td>
            <td>
                <form class="pull-right" action="movies.php" method="post">
                    <input type="hidden" name="id" value="<?php echo $id; ?>" />
                    <input class="btn btn-sm btn-warning" type="submit" name="submit" value="Edit" />
                    <input class="btn btn-sm btn-default" type="submit" name="submit" value="<?php echo $show == 0 ? 'Show' : 'Hide'; ?>" />
                    <input class="btn btn-sm btn-danger" type="submit" name="submit" value="Delete" />
                </form>
		    </td>
        </tr>
	    <?php
    }

	function toggleShow($db, $id) {
		$query = "SELECT `show` FROM kfs_movies WHERE id=:id;";
		$stmt = $db->prepare($query);
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		if ($stmt->rowCount() < 1) {
			echo '<p class="text-danger">Invalid ID</p>';
			return;
		}
		$result = $stmt->fetch(PDO::FETCH_OBJ);
		$query = "UPDATE kfs_movies SET `show`=" . ($result->show == 0 ? '1' : '0') . " WHERE id=:id;";
		$stmt = $db->prepare($query);
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		if ($stmt->rowCount() < 1) {
			echo '<p class="text-danger">Unknown error while updating show state</p>';
			return;
		}
		echo '<p class="text-success">Successfully updated show state!</p>';
	}

	function deleteMovie($db, $id) {
		$query = "DELETE FROM kfs_movies WHERE id=:id;";
		$stmt = $db->prepare($query);
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		if ($stmt->rowCount() < 1) {
			echo '<p class="text-danger">Invalid ID</p>';
			return;
		}

		echo '<p class="text-success">Successfully deleted!</p>';
	}

	if (isset($_POST['submit']) && isset($_POST['id'])) {
		if ($_POST['submit'] === 'Delete')
			deleteMovie($db, $_POST['id']);
		else
			toggleShow($db, $_POST['id']);
	}

	$query = "SELECT id, title, `show` FROM kfs_movies ORDER BY id DESC;";
	$stmt = $db->query($query);
?>
<form action="./movies.php" method="post"><input class="btn btn-success" type="submit" name="submit" value="Create new entry" /></form>
<table class="table">
    <thead>
        <tr>
            <td style="width: 50px;">ID</td>
            <td>Title</td>
        </tr>
    </thead>
    <tbody>
        <?php if ($stmt->rowCount() < 1): ?>
        <tr><td>N/A</td><td>No entries!</td></tr>
        <?php
        else:
            while($row = $stmt->fetch(PDO::FETCH_OBJ))
                printEntry($row->id, $row->title, $row->show);
        endif;
        ?>
    </tbody>
</table>
