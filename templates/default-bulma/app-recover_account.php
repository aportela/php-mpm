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
    </div>
</nav>

<div class="content">
    <div class="columns">
        <div class="column is-5"></div>
        <div class="column">
            <div class="card is-fullwidth">
                <div>
                    <div class="tabs is-boxed">
                        <ul>
                            <li><a href="index.php?page=signin"><span class="icon is-small"><i class="fa fa-sign-in"></i></span> sign in</a></li>
                            <li><a href="index.php?page=signup"><span class="icon is-small"><i class="fa fa-user-plus"></i></span> sign up</a></li>
                            <li class="is-active"><a href="index.php?page=recover_account"><span class="icon is-small"><i class="fa fa-lock"></i></span> recover account</a></li>
                        </ul>
                    </div>        
                </div>            
                <div class="card-content">
                    <form id="frm_recover_account" method="post" action="#">
                        <p class="control has-icon" id="c_email">
                            <input class="input" type="email" name="email" placeholder="Email" value="admin@localhost">
                            <i class="fa fa-envelope"></i>
                        </p>
                        <p class="control">
                            <button type="submit" class="button is-success">recover</button>
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <div class="column is-5"></div>
    </div>
</div>