<div class="ctnr loginPage">
    <div class="noLogin"> You are not logged in. Please, login.</div>
    <div class="loginForm">
        <div class="lGroup"><span class="fa fa-user"></span><input type="text" placeholder="Login" name="login" id="email"></div>
        <div class="lGroup"><span class="fa fa-lock"></span><input type="password" placeholder="Password" name="password" id="password"></div>
        <div class="lGroup"><button class="btn btnLogin" type="button" onclick="login();">Login</button></div>
        <div class="orReg"><span>or</span><button class="btn btnReg" onclick="account.registration();">Registration</button></div>
    </div>
    <div class="preloader blue">
        <span class="fa fa-cog fa-spin"></span>    
    </div>
</div>