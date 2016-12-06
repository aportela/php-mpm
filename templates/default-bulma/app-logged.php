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
                <!--
                <p class="menu-label">
                    Search
                </p>
                <ul class="menu-list">
                    <li><a href="#"><span class="icon"><i class="fa fa-search"></i></span> Global search</a></li>
                    <li><a href="#"><span class="icon"><i class="fa fa-file-o"></i></span> Search Bills</a></li>
                </ul>
                <p class="menu-label">
                    Add new element
                </p>                
                <ul class="menu-list" id="template_list">
                <?php
                    /*
                    $data = \PHP_MPM\Template::search(1, 0, "");
                    foreach($data->results as $template) {
                        echo '<li><a href="index.php?createId=' . $template->id . '"><span class="icon"><i class="fa fa-file-o"></i></span> ' . $template->name . '</a></li>';
                    }
                    */
                ?>                
                </ul>
                -->
                <p class="menu-label">
                    Administration
                </p>
                <ul class="menu-list">
                    <li><a <?= isset($_GET["page"]) && $_GET["page"]== "users" ? 'class="is-active"': null ?> href="index.php?page=users"><span class="icon"><i class="fa fa-user"></i></span> Users</a></li>
                    <li><a <?= isset($_GET["page"]) && $_GET["page"]== "groups" ? 'class="is-active"': null ?>href="index.php?page=groups"><span class="icon"><i class="fa fa-users"></i></span> Groups</a></li>
                    <li><a <?= isset($_GET["page"]) && $_GET["page"]== "attributes" ? 'class="is-active"': null ?>href="index.php?page=attributes"><span class="icon"><i class="fa fa-object-group"></i></span> Attributes</a></li>
                    <li><a <?= isset($_GET["page"]) && $_GET["page"]== "templates" ? 'class="is-active"': null ?>href="index.php?page=templates"><span class="icon"><i class="fa fa-file-o"></i></span> Templates</a></li>
                    <li><a <?= isset($_GET["page"]) && $_GET["page"]== "search_templates" ? 'class="is-active"': null ?>href="index.php?page=search_templates"><span class="icon"><i class="fa fa-search"></i></span> Search templates</a></li>
                </ul>
                <p class="menu-label">
                    Tools
                </p>
                <ul class="menu-list">
                    <li><a <?= isset($_GET["page"]) && $_GET["page"]== "errors" ? 'class="is-active"': null ?> href="index.php?page=errors"><span class="icon"><i class="fa fa-exclamation-triangle"></i></span> Errors</a></li>
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
                            case "templates":
                                include "app-templates.php";
                            break;
                            case "search_templates":
                                include "app-search_templates.php";
                            break;
                            case "errors":
                                include "app-errors.php";
                            break;
                            case "create_element":
                                include "app-create_element.php";
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
