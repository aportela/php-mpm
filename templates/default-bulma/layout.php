<!DOCTYPE html>
<html lang="en">
  <?php include "html-head.php"; ?>
  <body>
    <i id="ajax_icon" class="fa fa-cog fa-spin fa-2x fa-fw"></i>
    <nav class="nav" id="top_bar">
        <div class="nav-left">
                <?php
                  if (ENVIRONMENT_DEV && DEBUG) {
                ?>
          <a class="nav-item modal-button" href="#" data-target="#modal_debug" title="click to open modal debug info">
              <span class="icon"><i class="fa fa-bug"></i></span>
          </a>
                
                <?php
                  }
                ?>
        </div>
        <div class="nav-center">
            <span class="nav-item">php-mpm</span>
            <a class="nav-item" href="https://github.com/aportela/php-mpm" title="github project page">
                <span class="icon"><i class="fa fa-github"></i></span>
            </a>
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

    <div class="modal" id="modal_general_error">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">General error</p>
          <button class="delete modal_close"></button>
        </header>
        <section class="modal-card-body">
          <article class="message is-danger">
            <div class="message-header">
            <i class="fa fa-ambulance fa-2x" aria-hidden="true"></i> Error
            </div>
            <div class="message-body">
              <p>php-mpm kernel panic - sorry for any inconvenience</p>
              <div class="notification" id="stack_trace">
              </div>
            </div>
          </article>      
        </section>
        <footer class="modal-card-foot">
          <a class="button modal_close">Close</a>
        </footer>
      </div>
    </div>

    <?php
      if (ENVIRONMENT_DEV && DEBUG) {
    ?>
    <div class="modal" id="modal_debug">
      <div class="modal-background"></div>
      <div class="modal-card modal-card_xl">
        <header class="modal-card-head">
          <p class="modal-card-title">Debug</p>
          <button class="delete modal_close"></button>
        </header>
        <section class="modal-card-body">      
          <div class="columns">
            <div class="column">
              <div class="card">
                <header class="card-header">$_SESSION</header>
                <div class="card-content">
                  <pre>
                    <?= print_r($_SESSION, true); ?>
                  </pre>
                  </div>
              </div>
            </div>
            <div class="column">
              <div class="card">
                <header class="card-header"><?= ($_SERVER["REQUEST_METHOD"] == "GET" ? '$_GET': '$_POST') ?></header>
                <div class="card-content">
                  <pre>
                    <?php
                      switch($_SERVER["REQUEST_METHOD"]) {
                        case "GET":
                          echo print_r($_GET, true);
                        break;
                        case "POST":
                          echo print_r($_POST, true);
                        break;
                      }
                    ?>
                  </pre>
                  </div>
              </div>
            </div>
          </div>
        </section>
        <footer class="modal-card-foot">
          <a class="button modal_close">Close</a>
        </footer>        
      </div>
    </div>
    <?php
      }
    ?>    
    <?php include "html-scripts.php"; ?>
  </body>
</html>