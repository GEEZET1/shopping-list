<section class="login-section">
    <form class="login-form" action="./inc/login.inc.php" method="post" onkeyup="validateLogin(this)">
        <div class="email-address-field">
            <i class="fas fa-user"></i>
            <input type="text" name="email-address" id="email-address" placeholder="Email address" autofocus>
        </div>

        <div class="password-field">
            <i class="fas fa-lock"></i>

            <input type="password" name="password" placeholder="Password" disabled>

            <i class="fas fa-eye" onClick="showPassword(this)"></i>
        </div>

        <div class="login-field invalid">
            <input type="submit" name="login-button" value="Login" disabled>
        </div>
    </form>
</section>