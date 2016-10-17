<script src="http://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
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
<script src="/templates/default-bulma/static/scripts/non_auth.js"></script>
<?php
    if (isset($_GET["page"])) {
        switch($_GET["page"]) {
            case "users":
                ?>
<script src="/templates/default-bulma/static/scripts/app-users.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.js"></script>
                <?php
            break;
            case "groups":
                ?>
<script src="/templates/default-bulma/static/scripts/app-groups.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.js"></script>
                <?php
            break;
            case "attributes":
                ?>
<script src="/templates/default-bulma/static/scripts/app-attributes.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.js"></script>
                <?php
            break;
            case "templates":
                ?>
<script src="/templates/default-bulma/static/scripts/app-templates.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.js"></script>
                <?php
            break;
            case "errors":
                ?>
<script src="/templates/default-bulma/static/scripts/app-errors.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.js"></script>
                <?php
            break;
        }
    }
?>
<script src="/templates/default-bulma/static/scripts/script.js"></script>
<script src="/templates/default-bulma/static/scripts/locale-en.js"></script>
