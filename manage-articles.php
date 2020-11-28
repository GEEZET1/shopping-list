<section class="manage-articles">
    <form class="add-article">
        <div class="input-field">
            <i class="fas fa-cart-plus"></i>

            <input type="text" name="article-name" placeholder="Article" autofocus>

            <?php 
                require_once 'inc/functions.inc.php';
                display_articles_category() 
            ?>
            <i class="fas fa-chevron-down"></i>

            <?php display_articles_unit() ?>
            <i class="fas fa-chevron-down"></i>
        </div>

        <div class="submit-field">
            <p name="add-article" onClick="addArticle(this.parentElement)">Add article</p>
        </div>
    </form>

    <form class="delete-article">
        <div class="input-field">
            <i class="far fa-minus-square"></i>

            <?php display_articles() ?>
            <i class="fas fa-chevron-down"></i>
        </div>

        <div class="submit-field">
            <p name="delete-article" onClick="deleteArticle(this.parentElement)">Delete article</p>
        </div>
    </form>

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