<table id="templates" class="table is-bordered is-narrow">
  <thead>   
    <tr>
      <form class="frm_search_templates" method="post" action="/api/template/search.php">
      <input type="hidden" name="page" class="i_page" value="1">
      <th colspan="5">
        <div class="columns">
          <div class="column is-2">
            <p class="control has-addons has-addons-right">
              <span class="select">
                <select id="export_table_data_format">
                  <option value="">select format</option>
                  <option value="json">json</option>
                  <option value="xml">xml</option>
                </select>
              </span>
              <a id="btn_export_table_data" class="button is-primary is-disabled">Export data</a>
            </p>          
          </div>
          <div class="column is-1">
            <a class="button is-primary modal-button btn_add_template" data-target="#modal_add">add template</a>              
          </div>          
          <div class="column is-5">
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
          </div>
          <div class="column is-4">
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
          </div>
        </div>      
      </th>
      </form>
    </tr>   
    <tr>
        <th class="ignore_on_export">Operations</th>        
        <th>Name</th>
        <th>Description</th>
        <th>Created by</th>
        <th>Created <i class="fa fa-fw fa-sort-amount-asc" aria-hidden="true"></i></th>
  </thead>
  <tbody>
  </tbody>
</table>

<div class="modal" id="modal_add">
  <div class="modal-background"></div>
  <div class="modal-card">
    <form id="frm_add_template" method="post" action="/api/template/add.php">
      <header class="modal-card-head">
        <p class="modal-card-title">Add template</p>
        <button class="delete modal_close"></button>
      </header>
      <section class="modal-card-body">
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
    <form id="frm_update_template" method="post" action="/api/template/update.php">
      <input type="hidden" name="id" id="update_template_id" value="" />
      <header class="modal-card-head">
        <p class="modal-card-title">Update template</p>
        <button class="delete modal_close"></button>
      </header>
      <section class="modal-card-body">
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
    <form id="frm_delete_template" method="post" action="/api/template/delete.php">
      <input type="hidden" name="id" id="delete_template_id" value="" />
      <header class="modal-card-head">
        <p class="modal-card-title">Delete template</p>
        <button class="delete modal_close"></button>
      </header>
      <section class="modal-card-body">
        <article class="message is-warning">
          <div class="message-header">
            Warning
          </div>
          <div class="message-body">
            <p>Are you really sure you want to delete template: &laquo;<strong id="delete_template_name"></strong>&raquo;.</p>
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