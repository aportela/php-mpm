<table id="errors" class="table is-bordered is-narrow">
  <thead>   
    <tr>
      <th colspan="6">
        <form id="frm_admin_search" method="post" action="/api/error/search.php">
          <input type="hidden" name="page" class="i_page" value="1">        
          <div class="columns">
            <div class="column">
              <div class="control is-grouped">
                <p class="control">
                  <span class="select">
                    <select id="s_filter_by_date" name="date" class="is-disabled">
                      <option value="">select date range</option>
                    </select>
                  </span>            
                </p>
                <p class="control">
                  <span class="select">
                    <select id="s_filter_by_user_id" name="userId" class="is-disabled">
                      <option value="">select user</option>
                    </select>                
                  </span>
                </p>
              </div>
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
                <input class="input is-expanded is-disabled" id="fast_search_filter" type="text" name="text" placeholder="text filter">
                <a id="btn_clear_text" class="button is-primary modal-button is-disabled"><span class="icon"><i class="fa fa-times" aria-hidden="true"></i></span><span>clear</span></a>
              </p>                          
            </div>
            <div class="column">
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
        <th>Created <i class="fa fa-fw fa-sort-amount-asc" aria-hidden="true"></i></th>
        <th>User</th>
        <th>Remote address</th>
        <th>Operating system</th>
        <th>Browser</th>
        <th>Details</th>
    </tr>        
  </thead>
  <tbody>
  </tbody>
</table>
