<!DOCTYPE html>
<html lang="en">
  <?php include "html-head.php"; ?>
  <body>
    <i id="ajax_icon" class="fa fa-cog fa-spin fa-2x fa-fw"></i>
    <nav class="nav" id="top_bar">
        <div class="nav-left">        
        </div>
        <div class="nav-center">
            <span class="nav-item">php-mpm</span>
            <a class="nav-item" href="https://github.com/aportela/php-mpm" title="github project page">
                <span class="icon"><i class="fa fa-github"></i></span>
            </a>
            </div>
        </div>
        <div class="nav-right">
            <?php
            if ($_TEMPLATE["session_user_is_logged"] == true) {
            ?>
            <a class="nav-item" href="#" data-id="<?= $_TEMPLATE["session_user_id"] ?>" data-admin="<?= $_TEMPLATE["session_user_is_admin"] ?>"><span class="icon"><i class="fa fa-user" aria-hidden="true"></i></span> Signed as <?= $_TEMPLATE["session_user_name"] ?></a>
            <a class="nav-item" id="signout" href="/api/user/signout.php"><span class="icon"><i class="fa fa-sign-out" aria-hidden="true"></i></span> Sign Out</a>
            <?php
            }
            ?>
        </div>
    </nav>  
    <?php
      if ($_TEMPLATE["session_user_is_logged"] == false) {
        include "app-not_logged.php";
      } else {
        include "app-logged.php";
      }
    ?>
    <?php include "html-scripts.php"; ?>
  </body>
</html>