<?php
session_start();
$loggedin = $_SESSION['loggedin'];
include("MovieForm.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="robots" content="noindex, nofollow"/>
    <title>KFS Movie Admin</title>
    <link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="/css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container">
    <h1 id="page-header">Kristinehamns Filmstudio</h1>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                        aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">KFS</a>
            </div>
            <div id="sff-nav-logo" class="navbar-brand navbar-right">
                <a href="http://sff-filmstudios.org"><img src="/images/logos/sff_78x41.png"></a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li><a href="/" title="Start">Start</a></li>
                    <li><a href="/?p=links" title="Länkar">Länkar</a></li>
                    <li><a href="/?p=about" title="Information om vilka som jobbar i filmstudion">Om oss</a></li>
                    <li><a href="/?p=movies"
                           title="Filmer som visas under <?php echo $SEASON_TEXT; ?>">Filmer <?php echo $YEAR_TEXT; ?></a>
                    </li>
                    <?php if ($loggedin): ?>
                        <li><a href="/admin">Admin</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <h1 class="center_text">Movie Admin Page</h1>
    <?php
    require_once('conn.php');
    if (!isset($loggedin) || empty($loggedin) || !$loggedin)
        require_once('movies_login.php');
    elseif (isset($_GET['logout'])) {
        $_SESSION['loggedin'] = false;
        session_destroy();
        echo '<p>You have been logged out.</p>';
        echo '<p><a href="movies.php">Go back</a></p>';
    } else {
		if (isset($_POST['submit']) && $_POST['submit'] === 'Update data') {
			$year = $_POST['year'];
			$season = $_POST['season'];
			
			$error = false;
			
			try {
				$stmt = $db->prepare("UPDATE `kfs_data` SET `value`=:value WHERE `key`='year';");
				$stmt->bindValue(':value', $year);		
				$stmt->execute();
			} catch (PDOException $e) {
				echo '<p class="text-danger">Failed to update site data (year): ' . $e->getMessage() . '</p>';
				$error = true;
			}
			
			try {
				$stmt = $db->prepare("UPDATE `kfs_data` SET `value`=:value WHERE `key`='season';");
				$stmt->bindValue(':value', $season);
				$stmt->execute();
			} catch (PDOException $e) {
				echo '<p class="text-danger">Failed to update site data (season): ' . $e->getMessage() . '</p>';
				$error = true;
			}
			
			if (!$error)
				echo '<p class="text-success">Successfully updated site data!</p>';
		}
		
		$YEAR_TEXT = $db->query("SELECT `value` FROM `kfs_data` WHERE `key`='year';")->fetch(PDO::FETCH_OBJ)->value;
		$SEASON_TEXT = $db->query("SELECT `value` FROM `kfs_data` WHERE `key`='season';")->fetch(PDO::FETCH_OBJ)->value;
		
        if (!isset($_POST['submit']) || $_POST['submit'] == 'Update data') {
			?>
			<h2>Site data</h2>
			<form action="movies.php" method="post" class="form-inline">
				<div class="form-group">
					<label for="year">Year</label>
					<input type="text" id="year" name="year" class="form-control" value="<?php echo $YEAR_TEXT; ?>" placeholder="Year">
				</div>
				<div class="form-group">
					<label for="season">Season</label>
					<input type="text" id="season" name="season" class="form-control" value="<?php echo $SEASON_TEXT; ?>" placeholder="Season">
				</div>
				<input type="submit" name="submit" id="submit" value="Update data" class="btn btn-default">
			</form>
			<?php
			require_once('movies_list.php');
        } else {
            switch ($_POST['submit']) {
                case 'Create':
                case 'Create new entry':
                    require_once('movies_new.php');
                    break;
                case 'Edit':
                case 'Update':
                    require_once('movies_edit.php');
                    break;
                default:
                    require_once('movies_list.php');
            }
        }
    }

    if ($_SESSION['loggedin']):
        ?>
        <hr>
        <p><a class="btn btn-link" href="movies.php?logout">Logout</a></p>
    <?php endif; ?>
</div>
</body>
</html>
