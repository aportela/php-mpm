<!DOCTYPE html>
<html lang="en">
  <?php include "html-head.php"; ?>
  <body>
    <?php
      if ($_TEMPLATE["is_logged"] == false) {
          include "signin.php";
      } else {
          include "app.php";
      }
    ?>
    <?php include "html-scripts.php"; ?>
  </body>
</html>