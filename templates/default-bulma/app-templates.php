<table id="templates" class="table is-bordered is-narrow">
  <thead>   
    <tr>
      <th colspan="5">
        <form id="frm_admin_search" method="post" action="/api/template/search.php">
          <input type="hidden" name="page" class="i_page" value="1">
          <div class="columns">
            <div class="column">
              <a class="button is-primary modal-button btn_add_template" data-target="#modal_add">add template</a>              
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
  <div class="modal-card modal-card_xl">
    <form id="frm_add_template" method="post" action="/api/template/add.php">
      <header class="modal-card-head">
        <p class="modal-card-title">Add template</p>
        <button class="delete modal_close"></button>
      </header>
      <section class="modal-card-body">
        <div class="tabs">
          <ul>
            <li class="is-active"><a data-target="add_template_tab_metadata" href="#">Metadata</a></li>
            <li><a data-target="add_template_tab_permissions" href="#">Permissions</a></li>
            <li><a data-target="add_template_tab_attributes" href="#">Attributes</a></li>
            <!--
            <li><a data-target="add_template_tab_form" href="#">Form</a></li>
            -->
          </ul>
        </div>
        <div class="tab-content" id="add_template_tab_metadata">            
          <p class="control has-icon" id="ca_name">
              <input class="input" type="text" name="name" id="add_group_name" placeholder="Name" maxlength="32" required>
              <i class="fa fa-users"></i>
          </p>
          <p class="control has-icon" id="ca_description">
              <input class="input" type="text" name="description" id="add_group_description" placeholder="Description" maxlength="128">
              <i class="fa fa-comments-o" aria-hidden="true"></i>
          </p>
        </div>
        <div class="tab-content is-hidden" id="add_template_tab_permissions">
          <p class="control has-addons">
            <span class="select full_width">
              <select class="template_group_list full_width">
                <option value="">select group</option>
              </select>
            </span>
            <a class="button is-primary is-disabled btn_add_template_permission">Add</a>
          </p>
           <table id="add_template_permissions" class="table is-bordered is-narrow">
            <thead>
              <tr>
                <th>Operation</th>
                <th>Group</th>
                <th>Create</th>
                <th>View</th>
                <th>Update</th>
                <th>Delete</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>                  
        </div>        
        <div class="tab-content is-hidden" id="add_template_tab_attributes">
          <p class="control has-addons">
            <span class="select full_width">
              <select class="template_attribute_list full_width">
                <option value="">select attribute</option>
              </select>
            </span>
            <a class="button is-primary is-disabled btn_add_template_attribute">Add</a>
          </p>
           <table id="add_template_attributes" class="table is-bordered is-narrow">
            <thead>
              <tr>
                <th>Operation</th>
                <th>Attribute</th>
                <th>Label</th>
                <th>Required</th>
                <th>Default value</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>                  
        </div>
        <div class="tab-content is-hidden" id="add_template_tab_form">
          <div class="columns">
            <div class="column is-half">
            <p class="control">Customize HTML 
              <a class="refresh_form button is-info is-small"><span class="icon"><i class="fa fa-refresh"></i></span><span>Refresh</span></a>
              </p>
              <textarea class="form_html" rows="16">
&#x3C;form&#x3E;
  &#x3C;label class=&#x22;label&#x22;&#x3E;Name&#x3C;/label&#x3E;
  &#x3C;p class=&#x22;control&#x22;&#x3E;
    &#x3C;input class=&#x22;input&#x22; type=&#x22;text&#x22; placeholder=&#x22;Text input&#x22;&#x3E;
  &#x3C;/p&#x3E;    
  &#x3C;label class=&#x22;label&#x22;&#x3E;Subject&#x3C;/label&#x3E;
  &#x3C;p class=&#x22;control&#x22;&#x3E;
    &#x3C;span class=&#x22;select&#x22;&#x3E;
      &#x3C;select&#x3E;
        &#x3C;option&#x3E;Select dropdown&#x3C;/option&#x3E;
        &#x3C;option&#x3E;With options&#x3C;/option&#x3E;
      &#x3C;/select&#x3E;
    &#x3C;/span&#x3E;
  &#x3C;/p&#x3E;                          
&#x3C;/form&#x3E;
              </textarea>
            </div>
            <div class="column is-half form_preview">
            </div>
          </div>
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
  <div class="modal-card modal-card_xl">
    <form id="frm_update_template" method="post" action="/api/template/update.php">
      <input type="hidden" name="id" id="update_template_id" value="" />
      <header class="modal-card-head">
        <p class="modal-card-title">Update template</p>
        <button class="delete modal_close"></button>
      </header>
      <section class="modal-card-body">
        <div class="tabs">
          <ul>
            <li class="is-active"><a data-target="update_template_tab_metadata" href="#">Metadata</a></li>
            <li><a data-target="update_template_tab_permissions" href="#">Permissions</a></li>
            <li><a data-target="update_template_tab_attributes" href="#">Attributes</a></li>
            <!--
            <li><a data-target="update_template_tab_form" href="#">Form</a></li>
            -->
          </ul>
        </div>      
        <div class="tab-content" id="update_template_tab_metadata">
          <p class="control has-icon" id="c_name">
              <input class="input" type="text" name="name" id="update_template_name" placeholder="Name" maxlength="32" required>
              <i class="fa fa-users"></i>
          </p>
          <p class="control has-icon" id="c_description">
              <input class="input" type="text" name="description" id="update_template_description" placeholder="Description" maxlength="128">
              <i class="fa fa-comments-o" aria-hidden="true"></i>
          </p>
          <article class="message is-danger is-hidden modal_error">
            <div class="message-header">
              Error
            </div>
            <div class="message-body">
            </div>
          </article>
        </div>
        <div class="tab-content is-hidden" id="update_template_tab_permissions">
          <p class="control has-addons">
            <span class="select full_width">
              <select class="template_group_list full_width">
                <option value="">select group</option>
              </select>
            </span>
            <a class="button is-primary is-disabled btn_add_template_permission">Add</a>
          </p>
           <table id="update_template_permissions" class="table is-bordered is-narrow">
            <thead>
              <tr>
                <th>Operation</th>
                <th>Group</th>
                <th>Create</th>
                <th>View</th>
                <th>Update</th>
                <th>Delete</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>                  
        </div>                          
        <div class="tab-content is-hidden" id="update_template_tab_attributes">
          <p class="control has-addons">
            <span class="select full_width">
              <select class="template_attribute_list full_width">
                <option value="">select attribute</option>
              </select>
            </span>
            <a class="button is-primary is-disabled btn_add_template_attribute">Add</a>
          </p>
           <table id="update_template_tab_attributes" class="table is-bordered is-narrow">
            <thead>
              <tr>
                <th>Operation</th>
                <th>Attribute</th>
                <th>Label</th>
                <th>Required</th>
                <th>Default value</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>                  
        </div>        
        <div class="tab-content is-hidden" id="update_template_tab_form">
        </div>        
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