<table id="users" class="table is-bordered is-narrow">
  <thead>   
      <tr>
        <th colspan="2">
          <a class="button is-info is-small"><span class="icon"><i class="fa fa-fw fa-arrow-left" aria-hidden="true"></i></span><span>Previous Page</span></a>
          <a class="button is-info is-small"><span>Next Page</span><span class="icon"><i class="fa fa-fw fa-arrow-right" aria-hidden="true"></i></span></a>
        </th>
        <th colspan="4">
          <p class="control has-addons">
            <input class="input is-small" type="text" placeholder="text filter">
            <a class="button is-info is-small">
              <span>Search</span><span class="icon"><i class="fa fa-fw fa-search" aria-hidden="true"></i></span>
            </a>
          </p>        
        </th>
      </tr>
    <tr>
        <th>Operations</th>
        <th>Type</th>
        <th>Name</th>
        <th>Email</th>
        <th>Created by</th>
        <th>Created <i class="fa fa-fw fa-sort-amount-asc" aria-hidden="true"></i>
</th>
      </tr>
  </thead>
  <tbody>
  </tbody>
</table>


<div class="modal" id="modal_update">
  <div class="modal-background"></div>
  <div class="modal-card">
    <form id="frm_update_user" method="post" action="/api/user/update.php">
      <input type="hidden" name="id" id="update_user_id" value="" />
      <header class="modal-card-head">
        <p class="modal-card-title">Update user</p>
        <button class="delete modal_close"></button>
      </header>
      <section class="modal-card-body">
        <p class="control has-icon" id="c_email">
            <input class="input" type="email" name="email" id="update_user_email" placeholder="Email" value="admin@localhost">
            <i class="fa fa-envelope"></i>
        </p>
        <p class="control has-icon" id="c_name">
            <input class="input" type="text" name="name" id="update_user_name" placeholder="Name" value="administrator">
            <i class="fa fa-user"></i>
        </p>
        <p class="control has-icon" id="c_password">
            <input class="input" type="password" name="password" placeholder="Password" value="">
            <i class="fa fa-lock"></i>
        </p>    
        <article class="message is-danger is-hidden modal_error">
          <div class="message-header">
            Error
          </div>
          <div class="message-body">
          </div>
        </article>          
      </section>
      <footer class="modal-card-foot">
        <button type="submit" class="button is-primary">Update</button>
        <a class="button modal_close">Cancel</a>
      </footer>
    </form>
  </div>
</div>

<div class="modal" id="modal_delete">
  <div class="modal-background"></div>
  <div class="modal-card">
    <form id="frm_delete_user" method="post" action="/api/user/delete.php">
      <input type="hidden" name="id" id="delete_user_id" value="" />
      <header class="modal-card-head">
        <p class="modal-card-title">Delete user</p>
        <button class="delete modal_close"></button>
      </header>
      <section class="modal-card-body">
        <article class="message is-warning">
          <div class="message-header">
            Warning
          </div>
          <div class="message-body">
            <p>Are you really sure you want to delete user: &laquo;<strong id="delete_user_name"></strong>&raquo;.</p>
            <p>This operation cannot be undone. Would you like to proceed ?</p>
          </div>
        </article>      
        <article class="message is-danger is-hidden modal_error">
          <div class="message-header">
            Error
          </div>
          <div class="message-body">
          </div>
        </article>          
      </section>
      <footer class="modal-card-foot">
        <button type="submit" class="button is-primary">Delete</button>
        <a class="button modal_close">Cancel</a>
      </footer>
    </form>
  </div>
</div>