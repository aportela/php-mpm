<table id="attributes" class="table is-bordered is-narrow">
  <thead>
    <tr>
      <form class="frm_search_attributes" method="post" action="/api/attribute/search.php">
      <input type="hidden" name="page" class="i_page" value="1">
      <th colspan="4">
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
          <input class="input is-expanded is-disabled" type="text" placeholder="text filter (TODO)">
        </p>                
      </th>
      <th colspan="2">
        <nav class="pagination">
          <a class="button is-info btn_previous_page is-disabled">Previous</a>
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
      </th>
      </form>
    </tr>        
    <tr>
        <th>Operations</th>                
        <th>Name</th>
        <th>Description</th>
        <th>Type</th>
        <th>Created by</th>
        <th>Created <i class="fa fa-fw fa-sort-amount-asc" aria-hidden="true"></i></th>
  </thead>
  <tbody>
  </tbody>
</table>

<div class="modal" id="modal_update">
  <div class="modal-background"></div>
  <div class="modal-card">
    <form id="frm_update_attribute" method="post" action="/api/attribute/update.php">
      <input type="hidden" name="id" id="update_attribute_id" value="" />
      <header class="modal-card-head">
        <p class="modal-card-title">Update attribute</p>
        <button class="delete modal_close"></button>
      </header>
      <section class="modal-card-body">
        <p class="control has-icon" id="c_name">
            <input class="input" type="text" name="name" id="update_attribute_name" placeholder="Name" maxlength="32" required>
            <i class="fa fa-users"></i>
        </p>
        <p class="control has-icon" id="c_description">
            <input class="input" type="text" name="description" id="update_attribute_description" placeholder="Description" value="" maxlength="128">
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
    <form id="frm_delete_attribute" method="post" action="/api/attribute/delete.php">
      <input type="hidden" name="id" id="delete_attribute_id" value="" />
      <header class="modal-card-head">
        <p class="modal-card-title">Delete attribute</p>
        <button class="delete modal_close"></button>
      </header>
      <section class="modal-card-body">
        <article class="message is-warning">
          <div class="message-header">
            Warning
          </div>
          <div class="message-body">
            <p>Are you really sure you want to delete attribute: &laquo;<strong id="delete_attribute_name"></strong>&raquo;.</p>
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