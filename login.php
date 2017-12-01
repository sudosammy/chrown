<?php
require_once('config.php');

// Bail out if a password hasn't been set
if (empty(PASSWORD)) {
  die('You must set a password in config.php');
}

// Handle submission
$err = false;
if (isset($_POST['login'])) {
  if ($_POST['password'] == PASSWORD) {
    $_SESSION['logged_in'] = true;

  } else {
    $err = true;
  }
}

if (EXTERNAL) {
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    gtfo('/');
  }
  
} else {
  // local - no auth needed
  gtfo('/');
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title><?php echo APP_NAME ?></title>

    <link href="vendor/bootstrap-4.0.0-beta.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">

    <script src="vendor/jquery-3.2.1/jquery.min.js"></script>
    <script src="vendor/bootstrap-4.0.0-beta.2/js/bootstrap.bundle.min.js"></script>

  </head>
  <body>

    <!-- Page Content -->
    <div class="container" style="margin-top: 100px;">
      <div class="row">
        <div class="col-lg-3">
          <h1><?php echo APP_NAME ?></h1>
        </div>

        <div class="col-lg-5">
          <section class="card card-outline-secondary">
            <div class="card-header">
              You must authenticate to continue
            </div>
            <div class="card-body">
              <form class="form-inline" method="post" action="?">
                <div class="row">
                  <div class="col-md-9">
                    <label for="password" class="sr-only">Password</label>
                    <input type="password" class="form-control <?php echo $err ? 'is-invalid' : '' ?>" id="password" name="password" placeholder="Password" autofocus required>
                    <div class="invalid-feedback">
                      Incorrect
                    </div>
                  </div>
                  <div class="col-md-2">
                    <button type="submit" class="btn btn-primary" name="login">Login</button>
                  </div>
                </div>
              </form>
            </div>
          </section>
        </div>
      </div>
    </div>
    <!-- /.container -->
  </body>
</html>
