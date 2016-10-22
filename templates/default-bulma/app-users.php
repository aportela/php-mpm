<table id="users" class="table is-bordered is-narrow">
  <thead>
    <tr>
      <th colspan="6">
        <form id="frm_admin_search" method="post" action="/api/user/search.php">
          <input type="hidden" name="page" class="i_page" value="1">
          <div class="columns">
            <div class="column">
              <a class="button is-primary modal-button btn_add_user" data-target="#modal_add"><span class="icon"><i class="fa fa-plus" aria-hidden="true"></i></span><span>add user</span></a>              
            </div>
            <div class="column">
              <p class="control has-addons">
                <span class="select">
                  <select id="s_results_page" name="resultsPage">
                    <option value="8">8 results/page</option>
                    <option value="16" selected>16 results/page</option>
                    <option value="32">32 results/page</option>
                    <option value="64">64 results/page</option>
                    <option value="0">no pagination</option>
                  </select>
                </span>
                <input class="input is-expanded" id="fast_search_filter" type="text" name="text" placeholder="text filter">
                <a id="btn_clear_text" class="button is-primary modal-button is-disabled"><span class="icon"><i class="fa fa-times" aria-hidden="true"></i></span><span>clear</span></a>
              </p>                          
            </div>
            <div class="column">
              <nav class="pagination">
                <a class="button is-info btn_previous_page is-disabled">Previous page</a>
                <a class="button is-info btn_next_page is-disabled">Next page</a>
                <ul>
                  <li>
                    <a class="button is-primary pager_actual_page is-disabled">1</a>
                  </li>
                  <li>
                    <span>...</span>
                  </li>
                  <li>
                    <a class="button pager_total_pages is-disabled">1</a>
                  </li>
                </ul>
              </nav>                  
            </div>
            <div class="column">
              <p class="control has-addons has-addons-right">
                <span class="select">
                  <select id="export_table_data_format">
                    <option value="">select format</option>
                    <option value="json">json</option>
                    <option value="xml">xml</option>
                    <option value="csv">csv</option>
                  </select>
                </span>
                <a id="btn_export_table_data" class="button is-primary is-disabled"><span class="icon"><i class="fa fa-table" aria-hidden="true"></i></span><span>Export data</span></a>
              </p>          
            </div>            
          </div>      
        </form>
      </th>
    </tr>   
    <tr>
        <th class="ignore_on_export">Operations</th>
        <th>Type</th>
        <th>Name</th>
        <th>Email</th>
        <th>Created by</th>
        <th>Created <i class="fa fa-fw fa-sort-amount-asc" aria-hidden="true"></i></th>
      </tr>
  </thead>
  <tbody>
  </tbody>
</table>

<div class="modal" id="modal_add">
  <div class="modal-background"></div>
  <div class="modal-card">
    <form id="frm_add_user" method="post" action="/api/user/add.php">
      <header class="modal-card-head">
        <p class="modal-card-title">Add user</p>
        <button class="delete modal_close"></button>
      </header>
      <section class="modal-card-body">
        <p class="control has-icon" id="ca_email">
            <input class="input" type="email" name="email" id="add_user_email" placeholder="Email" maxlength="254" required>
            <i class="fa fa-envelope"></i>
        </p>
        <p class="control has-icon" id="ca_name">
            <input class="input" type="text" name="name" id="add_user_name" placeholder="Name" maxlength="32" required>
            <i class="fa fa-user"></i>
        </p>
        <p class="control has-icon" id="ca_password">
            <input class="input" type="password" name="password" id="add_user_password" placeholder="Password" value="">
            <i class="fa fa-lock"></i>
        </p>    
        <p class="control has-addons" id="ca_type">
          <a class="button is-disabled">User type</a>          
          <span class="select full_width">
            <select name="type" id="add_user_type">
              <option value="0" selected>normal</option>
              <option value="1">administrator</option>
            </select>
          </span>
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
        <button type="submit" class="button is-primary">Add</button>
        <a class="button modal_close">Cancel</a>
      </footer>
    </form>
  </div>
</div>

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
        <p class="control has-icon" id="cu_email">
            <input class="input" type="email" name="email" id="update_user_email" placeholder="Email" maxlength="254" required>
            <i class="fa fa-envelope"></i>
        </p>
        <p class="control has-icon" id="cu_name">
            <input class="input" type="text" name="name" id="update_user_name" placeholder="Name" maxlength="32" required>
            <i class="fa fa-user"></i>
        </p>
        <p class="control has-icon" id="cu_password">
            <input class="input" type="password" name="password" id="update_user_type" placeholder="Password" value="">
            <i class="fa fa-lock"></i>
        </p>
        <p class="control has-addons" id="ca_type">
          <a class="button is-disabled">User type</a>          
          <span class="select full_width">
            <select name="type" id="update_user_type">
              <option value="0" selected>normal</option>
              <option value="1">administrator</option>
            </select>
          </span>
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