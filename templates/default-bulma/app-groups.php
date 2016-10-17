<table id="groups" class="table is-bordered is-narrow">
  <thead>   
    <tr>
      <form id="frm_admin_search" method="post" action="/api/group/search.php">
      <input type="hidden" name="page" class="i_page" value="1">
      <th colspan="6">
        <div class="columns">
          <div class="column">
            <a class="button is-primary modal-button btn_add_group" data-target="#modal_add"><span class="icon"><i class="fa fa-plus" aria-hidden="true"></i></span><span>add group</span></a>              
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
                </select>
              </span>
              <a id="btn_export_table_data" class="button is-primary is-disabled"><span class="icon"><i class="fa fa-table" aria-hidden="true"></i></span><span>Export data</span></a>
            </p>          
          </div>
        </div>      
      </th>
      </form>
    </tr>   
    <tr>
        <th class="ignore_on_export">Operations</th>        
        <th>Name</th>
        <th>Description</th>
        <th>Total users</th>
        <th>Created by</th>
        <th>Created <i class="fa fa-fw fa-sort-amount-asc" aria-hidden="true"></i></th>
  </thead>
  <tbody>
  </tbody>
</table>

<div class="modal" id="modal_add">
  <div class="modal-background"></div>
  <div class="modal-card">
    <form id="frm_add_group" method="post" action="/api/group/add.php">
      <header class="modal-card-head">
        <p class="modal-card-title">Add group</p>
        <button class="delete modal_close"></button>
      </header>
      <section class="modal-card-body">
        <div class="tabs">
          <ul>
            <li class="is-active"><a data-target="add_group_tab_metadata" href="#">Metadata</a></li>
            <li><a data-target="add_group_tab_users" href="#">Users</a></li>
          </ul>
        </div>
        <div class="tab-content" id="add_group_tab_metadata">            
          <p class="control has-icon" id="ca_name">
              <input class="input" type="text" name="name" id="add_group_name" placeholder="Name" maxlength="32" required>
              <i class="fa fa-users"></i>
          </p>
          <p class="control has-icon" id="ca_description">
              <input class="input" type="text" name="description" id="add_group_description" placeholder="Description" maxlength="128">
              <i class="fa fa-comments-o" aria-hidden="true"></i>
          </p>
        </div>
        <div class="tab-content is-hidden" id="add_group_tab_users">
          <p class="control has-addons">
            <span class="select">
              <select id="add_group_user_list">
                <option value="">select user</option>
              </select>
            </span>
            <a id="btn_add_group_user" class="button is-primary is-disabled">Add</a>
          </p>
           <table id="add_group_userlist" class="table is-bordered is-narrow">
            <thead>
              <tr>
                <th>Operation</th>
                <th>Name</th>
                <th>Email</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>                  
        </div>
        
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
    <form id="frm_update_group" method="post" action="/api/group/update.php">
      <input type="hidden" name="id" id="update_group_id" value="" />
      <header class="modal-card-head">
        <p class="modal-card-title">Update group</p>
        <button class="delete modal_close"></button>
      </header>
      <section class="modal-card-body">
        <p class="control has-icon" id="c_name">
            <input class="input" type="text" name="name" id="update_group_name" placeholder="Name" maxlength="32" required>
            <i class="fa fa-users"></i>
        </p>
        <p class="control has-icon" id="c_description">
            <input class="input" type="text" name="description" id="update_group_description" placeholder="Description" maxlength="128">
            <i class="fa fa-comments-o" aria-hidden="true"></i>
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
    <form id="frm_delete_group" method="post" action="/api/group/delete.php">
      <input type="hidden" name="id" id="delete_group_id" value="" />
      <header class="modal-card-head">
        <p class="modal-card-title">Delete group</p>
        <button class="delete modal_close"></button>
      </header>
      <section class="modal-card-body">
        <article class="message is-warning">
          <div class="message-header">
            Warning
          </div>
          <div class="message-body">
            <p>Are you really sure you want to delete group: &laquo;<strong id="delete_group_name"></strong>&raquo;.</p>
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