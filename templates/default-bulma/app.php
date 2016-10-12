<nav class="nav">
  <div class="nav-left">
    <a class="nav-item is-brand" href="#">
      php-mpm
    </a>
  </div>
  <div class="nav-center">
    <a class="nav-item" href="https://github.com/aportela/php-mpm" title="github project page">
      <span class="icon">
        <i class="fa fa-github"></i>
      </span>
    </a>  
  </div>
  <div class="nav-right nav-menu">
  <a class="nav-item" href="#">
      Signed as <?= isset($_TEMPLATE["name"]) ? $_TEMPLATE["name"]: "undefined"; ?>
    </a>
    <a class="nav-item" id="signout" href="/api/user/signout.php">
      <i class="fa fa-sign-out" aria-hidden="true"></i>
 Sign Out
    </a>
  </div>
</nav>
<section class="section">
    <div class="container-fluid">
        <div class="columns">
            <div class="column is-2">
                <aside class="menu">
                <p class="menu-label">
                    General                    
                </p>
                <ul class="menu-list">
                    <li><a href="index.php" <?= ! isset($_GET["page"]) ? 'class="is-active"' : null ?>>Home</a></li>
                </ul>
                <p class="menu-label">
                    Administration
                </p>
                <ul class="menu-list">
                    <li><a <?= isset($_GET["page"]) && $_GET["page"]== "users" ? 'class="is-active"': null ?> href="index.php?page=users">Users</a></li>
                    <li><a <?= isset($_GET["page"]) && $_GET["page"]== "groups" ? 'class="is-active"': null ?>href="index.php?page=groups">Groups</a></li>
                    <li><a <?= isset($_GET["page"]) && $_GET["page"]== "attributes" ? 'class="is-active"': null ?>href="index.php?page=attributes">Attributes</a></li>
                </ul>
                </aside>            
            </div>
            <div class="column">
                <?php
                    if (isset($_GET["page"])) {
                        switch($_GET["page"]) {
                            case "users":
                                include "app-users.php";
                            break;
                            case "groups":
                                include "app-groups.php";
                            break;
                            case "attributes":
                                include "app-attributes.php";
                            break;
                            default:
                                ?>
                                <h1>TA-DA!</h1>
                                <?php                            
                            break;
                        }

                    } else {
                        ?>
                        <h1>TA-DA!</h1>
                        <?php
                    }
                ?>
            </div>
        </div>
    </div>
</section>

