<?php
  use KFS\User;

  if (User::isLoggedIn()) {
    header('Location: /');
    exit();
  }

  if (isset($_POST['register'])) {
    $user = new User();
    $user->setUsername($_POST['username']);
    $user->setPassword($_POST['password']);

    $result = password_verify($_POST['password'], $hash);

    if ($user->validate() && $user->save()) {
      User::login($_POST['username'], $_POST['password']);
      header('Location: /');
      exit();
    }
  }
?>
<h1>Register</h1>
<?php if (KFS\Alerts::hasAlerts()) KFS\Alerts::printAll(); ?>
<form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" class="form-horizontal">
  <div class="form-group">
    <label for="username" class="col-sm-2 control-label">Username</label>
    <div class="col-sm-10">
      <input type="text" name="username" class="form-control" placeholder="Username">
    </div>
  </div>
  <div class="form-group">
    <label for="password" class="col-sm-2 control-label">Password</label>
    <div class="col-sm-10">
      <input type="password" name="password" class="form-control" placeholder="Password">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      <button type="submit" name="register" class="btn btn-primary">Register</button>
    </div>
  </div>
</form>
