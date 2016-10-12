<!DOCTYPE html>
<html lang="en">
  <?php include "html-head.php"; ?>
  <body>
  <i id="ajax_icon" class="fa fa-cog fa-spin fa-1x fa-fw"></i>
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