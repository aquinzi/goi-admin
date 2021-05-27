


<?php require_once 'part-head.php';?>
<?php
/*
    session_start();
    echo isset($_SESSION['login']);
    if(isset($_SESSION['login'])) {
      header('LOCATION:index2.php'); 
      die();
    }
*/
    // generar contraseña hasehada, y comparar con eso
    // admin = $2y$10$IV3x874C6nluao/k5heHuuwHhj4dbdP6GHJzbbA.1ZRugQPg./Gmi
    // same as ldap = $2y$10$PR4/nDdLzzPaRYWjhC3nNOuMswvSksXwlF42Z5/E3ZcMUC4JgdYLO
    //die(echo password_hash("admin", PASSWORD_BCRYPT));

?>


<div class="container">
    <h3 class="text-center">Login</h3>

    <?php if ($template['was_loggedin'] == false && $template['step'] != 'login' ): ?>
        <div class='alert alert-danger'>
            Username and password do not match.
        </div>

    <?php endif; ?>

    <form action="/login" method="post">
      <p>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
      </p>
      <p>
        <label for="pwd">Password:</label>
        <input type="password" id="pwd" name="password" required>
      </p>
      <button type="submit" name="logmein">Login</button>
    </form>

</div>


  <?php require_once 'part-foot.php';?>