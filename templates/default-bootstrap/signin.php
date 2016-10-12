<div class="container">
    <div class="row">
        <h1 class="text-xs-center">php-mpm</h1>
        <hr />
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <form class="form-signin" id="frm_signin" method="post" action="/api/user/login.php">
                <h2 class="form-signin-heading">Please sign in</h2>
                <div class="form-group" id="fg_email">
                    <label for="email" class="sr-only">Email address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Email address" required value="admin@localhost" autofocus>
                </div>
                <div class="form-group" id="fg_password">
                    <label for="inputPassword" class="sr-only">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password" value="password" required>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
            </form>
        </div>
        <div class="col-md-4"></div>
    </div>
</div>