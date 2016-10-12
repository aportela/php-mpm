<section class="section">
    <div class="container-fluid">
        <div class="columns">
            <div class="column is-2">
                <aside class="menu">
                <p class="menu-label">
                    General
                </p>
                <ul class="menu-list">
                    <li><a href="index.php">Home</a></li>
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
                                <h1>Hello <?= isset($_TEMPLATE["name"]) ? $_TEMPLATE["name"]: "undefined"; ?>, <a id="signout" href="/api/user/signout.php">click here to sign out</a></h1>
                                <?php                            
                            break;
                        }

                    } else {
                        ?>
                        <h1>Hello <?= isset($_TEMPLATE["name"]) ? $_TEMPLATE["name"]: "undefined"; ?>, <a id="signout" href="/api/user/signout.php">click here to sign out</a></h1>
                        <?php
                    }
                ?>
            </div>
        </div>
    </div>
</section>

