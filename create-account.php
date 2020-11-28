<section class="create-account">
    <form class="add-account" action="./inc/create-account.inc.php" method="post" onkeyup="validateCreateAccountForm(this)">
        <div class="email-address-field">
            <i class="fas fa-user"></i>
            <input type="email" name="email-address" id="email-address" placeholder="Email address" autofocus>
        </div>

        <div class="password-field">
            <i class="fas fa-lock"></i>

            <input type="password" name="password" placeholder="Password" disabled>

            <i class="fas fa-eye" onClick="showPassword(this)"></i>
        </div>

        <div class="password-field">
            <i class="fas fa-lock"></i>

            <input type="password" name="password-repeat" placeholder="Password" disabled>

            <i class="fas fa-eye" onClick="showPassword(this)"></i>
        </div>

        <div class="create-account-field invalid">
            <input type="submit" name="create-account" value="Create account" disabled>
        </div>

        <div class="password-hint-field">
            <p><i class="fas fa-exclamation-triangle fa-lg"></i>Password must contain atleast 8 characters, including:</p>
                <ul>
                    <li>1 number (0-9)</li>
                    <li>1 uppercase letter</li>
                    <li>1 lowercase letter</li>
                    <li>1 non-alpha numeric symbol</li>
                </ul>
        </div>
    </form>
</section>