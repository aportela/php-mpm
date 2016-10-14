<div class="content">
    <div class="columns">
        <div class="column is-5"></div>
        <div class="column">
            <div class="card is-fullwidth" id="auth_container">
                <div>
                    <div class="tabs is-boxed">
                        <ul>
                            <li class="is-active"><a data-target="frm_signin" href="#"><span class="icon is-small"><i class="fa fa-sign-in"></i></span> sign in</a></li>
                            <li><a data-target="frm_signup" href="#"><span class="icon is-small"><i class="fa fa-user-plus"></i></span> sign up</a></li>
                            <li><a data-target="frm_recover_account" href="#"><span class="icon is-small"><i class="fa fa-lock"></i></span> recover account</a></li>
                        </ul>
                    </div>        
                </div>            
                <div class="card-content">
                    <form class="tab-content" id="frm_signin" method="post" action="/api/user/login.php">
                        <p class="control has-icon" id="c_email">
                            <input class="input" type="email" name="email" placeholder="Email" value="admin@localhost">
                            <i class="fa fa-envelope"></i>
                        </p>
                        <p class="control has-icon" id="c_password">
                            <input class="input" type="password" name="password" placeholder="Password" value="password">
                            <i class="fa fa-lock"></i>
                        </p>
                        <p class="control">
                            <button type="submit" class="button is-success">sign in</button>
                        </p>
                    </form>
                    <form class="tab-content is-hidden" id="frm_signup" method="post" action="/api/user/signup.php">
                        <p class="control has-icon" id="c_email">
                            <input class="input" type="email" name="email" placeholder="Email" value="admin@localhost">
                            <i class="fa fa-envelope"></i>
                        </p>
                        <p class="control has-icon" id="c_name">
                            <input class="input" type="text" name="name" placeholder="Name" value="administrator">
                            <i class="fa fa-user"></i>
                        </p>
                        <p class="control has-icon" id="c_password">
                            <input class="input" type="password" name="password" placeholder="Password" value="password">
                            <i class="fa fa-lock"></i>
                        </p>
                        <p class="control">
                            <button type="submit" class="button is-success">sign up</button>
                        </p>
                    </form>
                    <form class="tab-content is-hidden" id="frm_recover_account" method="post" action="#">
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