<div class="content">
    <h1 class="has-text-centered">php-mpm</h1>
    <hr />
    <h2 class="has-text-centered">Please sign in</h2>
    <div class="columns">
        <div class="column is-5"></div>
        <div class="column">
            <div class="box">
                <form class="form-signin" id="frm_signin" method="post" action="/api/user/login.php">
                    <p class="control has-icon" id="c_email">
                        <input class="input" type="email" name="email" placeholder="Email" value="admin@localhost">
                        <i class="fa fa-envelope"></i>
                    </p>
                    <p class="control has-icon" id="c_password">
                        <input class="input" type="password" name="password" placeholder="Password" value="password">
                        <i class="fa fa-lock"></i>
                    </p>
                    <p class="control">
                        <button type="submit" class="button is-success">Login</button>
                    </p>
                </form>
            </div>
        </div>
        <div class="column is-5"></div>
    </div>
</div>