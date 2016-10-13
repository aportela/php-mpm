<table id="users" class="table table-bordered table-striped table-hover table-sm small">
  <thead>   
      <tr>
        <th colspan="2">
          <button class="btn btn-info"><span class="icon"><i class="fa fa-fw fa-arrow-left" aria-hidden="true"></i></span><span>Previous Page</span></button>
          <button class="btn btn-info"><span>Next Page</span><span class="icon"><i class="fa fa-fw fa-arrow-right" aria-hidden="true"></i></span></button>
        </th>
        <th colspan="4">
          <p class="control has-addons">
            <input class="input" type="text" placeholder="text filter">
            <a class="button is-info">
              <span>Search</span><span class="icon"><i class="fa fa-fw fa-search" aria-hidden="true"></i></span>
            </a>
          </p>        
        </th>
      </tr>
    <tr>
        <th>Type</th>
        <th>Name</th>
        <th>Email</th>
        <th>Created by</th>
        <th>Created <i class="fa fa-fw fa-sort-amount-asc" aria-hidden="true"></i></th>
        <th>Operations</th>
      </tr>
  </thead>
  <tbody>
  </tbody>
</table>


<div class="modal" id="modal_update">
  <div class="modal-background"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title">Update user</p>
      <button class="delete"></button>
    </header>
    <section class="modal-card-body">
      <!-- Content ... -->
    </section>
    <footer class="modal-card-foot">
      <a class="button is-primary">Save changes</a>
      <a class="button">Cancel</a>
    </footer>
  </div>
</div>

<div class="modal" id="modal_delete">
  <form id="frm_delete_user" method="post" action="/api/user/delete.php">
  <input type="hidden" name="id" value="" />
  <div class="modal-background"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title">Delete user</p>
      <button class="delete"></button>
    </header>
    <section class="modal-card-body">
      <div class="notification is-warning">
        Warning: this operation can not be undone
      </div>
    </section>
    <footer class="modal-card-foot">
      <button class="button is-primary" type="submit">Delete</button>
      <a class="button btn_cancel">Cancel</a>
    </footer>
  </div>
</div>