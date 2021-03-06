<script src="http://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
<script src="/templates/default-bulma/static/scripts/app-common.js"></script>
<script src="/templates/default-bulma/static/scripts/mpm.js"></script>
<?php
    if ($_TEMPLATE["session_user_is_logged"] == false) {
        ?>
<script src="/templates/default-bulma/static/scripts/app-section-signin.js"></script>
<script src="/templates/default-bulma/static/scripts/app-section-signup.js"></script>
<script src="/templates/default-bulma/static/scripts/app-section-recover_account.js"></script>
        <?php
    }
?>
<?php
    if ($_TEMPLATE["session_user_is_logged"] && isset($_GET["page"])) {
        switch($_GET["page"]) {
            case "users":
                ?>
<script src="/templates/default-bulma/static/scripts/app-section-users.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.js"></script>
                <?php
            break;
            case "groups":
                ?>
<script src="/templates/default-bulma/static/scripts/app-section-groups.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.js"></script>
                <?php
            break;
            case "attributes":
                ?>
<script src="/templates/default-bulma/static/scripts/app-section-attributes.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.js"></script>
                <?php
            break;
            case "templates":
                ?>
<script src="/templates/default-bulma/static/scripts/app-section-templates.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.js"></script>
                <?php
            break;
            case "search_templates":
                ?>
<script src="/templates/default-bulma/static/scripts/app-section-search_templates.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.js"></script>
                <?php
            break;
            case "errors":
                ?>
<script src="/templates/default-bulma/static/scripts/app-section-errors.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.js"></script>
                <?php
            break;
            case "create_element":
                ?>
<script src="/templates/default-bulma/static/scripts/app-section-create_element.js"></script>
                <?php
            break;
        }
    }
?>
<script src="/templates/default-bulma/static/scripts/locale-en.js"></script>
