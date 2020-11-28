<?php 
require_once './inc/functions.inc.php'; 
?>

<section class="my-lists">
    <div class="lists">
        <?php display_user_lists($_SESSION['email_address']); ?>
    </div>

    <div class="list-detail"></div>

    <div class="modal addArticleToList">
        <div class="modal-content">
            <i class="fas fa-times fa-3x" onClick="hideModal('addArticleToList')"></i>
            <form method="GET">
                <div class="input-field">
                    <i class="fas fa-cart-plus"></i>

                    <div class="group-field">
                        <?php display_articles_category_select() ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
   
                    <div class="group-field">
                    </div>

                    <div class="group-field">
                    </div>

                    <div class="group-field">
                        <input type="number" value="1" min="1" step="1" max="10" placeholder="quanity">
                    </div>
                </div>
                <div class="submit-field" onClick="addArticleToList(this)">
                    <p name="add-article">Add article</p>
                </div>
            </form>
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

    <div class="modal additionalListOwner">
        <div class="modal-content">
            <i class="fas fa-times fa-3x" onClick="hideModal('additionalListOwner')"></i>

            <form>
                <p><i class="fas fa-exclamation"></i>Owner must have account in our service first.</p>

                <div class="additional-owner-field">
                    <i class="fas fa-at"></i><input type="email" placeholder="Subowner's email address">
                </div>

                <div class="submit-field" onClick="addListOwner(this)">
                    <p name="add-article">Add subowner</p>
                </div>
            </form>
        </div>
    </div>
</section>