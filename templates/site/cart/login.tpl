<div class="ctnr">
        <div class="page checkout">
            <h1>proceed to Checkout</h1>
            <div class="checkout-nav">
                <a href="#" class="active" onclick="return false;">Registration</a>
                <a href="#" onclick="return false;">Delivery info</a>
                <a href="#" onclick="return false;">Payment</a>
            </div>

            <div class="checkout-reg">
                <div class="flex">
                    <div>
                        <h2>Registered</h2>
                        <form onsubmit="cart_login(event)">
                            <div class="input-group">
                                <label>Email</label>
                                <input type="email" name="login_email" id="email" required>
                            </div>
                            <div class="input-group">
                                <label>Password</label>
                                <input type="password" name="login_password" id="password" required>
                            </div>
                            <button class="btn" type="submit">Login</button>
                        </form>
                    </div>
                    <div>
                        <h2>New customer</h2>
                        <form onsubmit="return false;" class="regForm">
                            <div class="input-group">
                                <label>First name</label>
                                <input type="text" name="name">
                            </div>
							<div class="input-group">
                                <label>Last name</label>
                                <input type="text" name="lastname">
                            </div>
                            <div class="input-group">
                                <label>Your phone</label>
                                <input type="tel" name="phone">
                            </div>
                            <div class="input-group">
                                <label>Your Email</label>
                                <input type="email" name="email">
                            </div>
                            <div class="input-group">
                                <label>Password</label>
                                <input type="password" name="password">
                            </div>
                            <div class="input-group">
                                <label>Confirm password</label>
                                <input type="password" name="password2">
                            </div>
                            <button type="button" class="btn" onclick="account.auth(this);">Continue</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>