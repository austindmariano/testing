<?php
include "connection.php";
if(isset($_POST["loginbtn"]))
{
     if(empty($_POST["username"]) || empty($_POST["password"]))
     {
          $message = '<label>All fields are required</label>';
     }
     else
     {
          $query = "SELECT * FROM users WHERE username = :username AND password = :password";
          $statement = $connect->prepare($query);
          $statement->execute(
               array(
                    'username'     =>     $_POST["username"],
                    'password'     =>     $_POST["password"]
               )
          );
          $count = $statement->rowCount();
          if($count > 0)
          {
               $_SESSION["username"] = $_POST["username"];
               $_SESSION["password"] = $_POST["password"];
               $message = '<script>
                              swal({
                              title: "Successfully Login!",
                              text: "Click OK to proceed Homepage",
                              type: "success"
                              },
                            function(){
                                window.location="index.php"
                            });
                            </script>';
// window.location.href = "index.php"
               // header("location:index.php");
          }
          else
          {
               $message = '<strong><center>Invalid Username Or Password!<center></strong>';
          }
   }
}
  // elseif (isset($_POST['logoutBtn'])) {
  //   echo '<script type="text/javascript">alert("Successfully Logout");</script>';
  //   // code...
  // }

?>



<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="../build/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.js" charset="utf-8"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.css.map">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.js" charset="utf-8"></script>

    <title>Login</title>
  </head>
  <body id="LoginBody">
    <div class="container logo_title">
      <img src="images/comteq_logo.png" class="responsive" id="LoginComteqLogo" alt="COMTEQ LOGO">
      <h1>REGISTRATION SYSTEM</h1>
      </div>
    <div class="hr"></div>
    <form method="post" action="Login.php" id="LoginFormGroup">

      <div class="container" id="LoginContainer">
        <div class="">

          <?php echo $message; ?>
        </div>
        <center><h1 style="margin:10px;">LOGIN</h1></center>
        <div class="form-group" id="LoginFormGroup" >
          <label for="Username">Username:</label>
          <input type="text" class="form-control" id="Username" name="username" placeholder="username" required>
        </div>
        <div class="form-group" id="LoginFormGroup">
          <label for="Password">Password:</label>
          <input type="password" class="form-control" id="Password" name="password" placeholder="password" required>
        </div>
        <div class="form-group login" id="LoginFormGroup">
          <button type="submit" id="LoginBtn" name="loginbtn" class="btn btn-primary">Login</button>
        </div>
      </div>

</form>



    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="../build/js/app.js" charset="utf-8"></script>
  </body>
</html>
