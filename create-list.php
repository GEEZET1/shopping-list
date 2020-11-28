<section class="create-list">
    <form class="add-list" method="post" onKeyUp="addList(this)" onMouseUp="addList(this)">
        <div class="list-name-field">
            <i class="fas fa-plus"></i>

            <input type="text" name="list-name" id="list-name" placeholder="List name" maxlength="50" autofocus>
        </div>

        <div class="list-owner-field">
            <i class="fas fa-id-badge"></i>

            <input type="email" name="list-owner" placeholder="Email address" disabled>

            <i class="fas fa-user-plus button-disabled" onClick="duplicateNode(this)"></i>
        </div>

        <div class="create-list-field div-disabled" onClick="createList(this.parentElement)">
            <p name="create-list">Create list</p>
        </div>
    </form>

    <div class="modal warning">
        <div class="modal-content">
            <i class="fas fa-exclamation fa-lg"></i>
            <p>List's max owners reached!</p>
        </div>
    </div>

    <div class="modal failure">
        <div class="modal-content">
            <i class="fas fa-exclamation fa-lg"></i>
            <p>Something went wrong! Try again in few seconds.</p>
        </div>
    </div>

    <div class="modal success">
        <div class="modal-content">
            <i class="fas fa-check-square fa-lg"></i>
            <p>Action performed successfully.</p>
        </div>
    </div>
</section>