<section class="section">
    <div class="container-fluid">
        <div class="columns">
            <div class="column is-2">
                <aside class="menu">
                <p class="menu-label">
                    General                    
                </p>
                <ul class="menu-list">
                    <li><a href="index.php" <?= ! isset($_GET["page"]) ? 'class="is-active"' : null ?>><span class="icon"><i class="fa fa-home"></i></span> Home</a></li>
                </ul>
                <?php
                if ($_TEMPLATE["session_user_is_admin"] == 1) {
                ?>
                <p class="menu-label">
                    Administration
                </p>
                <ul class="menu-list">
                    <li><a <?= isset($_GET["page"]) && $_GET["page"]== "users" ? 'class="is-active"': null ?> href="index.php?page=users"><span class="icon"><i class="fa fa-user"></i></span> Users</a></li>
                    <li><a <?= isset($_GET["page"]) && $_GET["page"]== "groups" ? 'class="is-active"': null ?>href="index.php?page=groups"><span class="icon"><i class="fa fa-users"></i></span> Groups</a></li>
                    <li><a <?= isset($_GET["page"]) && $_GET["page"]== "attributes" ? 'class="is-active"': null ?>href="index.php?page=attributes"><span class="icon"><i class="fa fa-object-group"></i></span> Attributes</a></li>
                </ul>
                <?php
                }
                ?>
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
