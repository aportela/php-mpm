<!DOCTYPE html>
<html lang="en">
  <?php include "html-head.php"; ?>
  <body>
    <h1>Hello <?= isset($_TEMPLATE["name"]) ? $_TEMPLATE["name"]: "undefined"; ?></h1>
    <?php include "html-scripts.php"; ?>
  </body>
</html>