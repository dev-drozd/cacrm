<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>{title} | Your Company</title>
  [description]<meta name="description" content="{description}">[/description]
  [keywords]<meta name="keywords" content="{keywords}">[/keywords]{canonical}<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="{theme}/css/style.css" type="text/css">
</head>
<body>

<div class="container">
  <header class="header wrap">
    <div class="header-top">
      <div class="header-button">
        <a href="#" class="header-button-link">
          <span></span>
          <span></span>
          <span></span>
        </a>
      </div>
      <div class="header-logo">
        <a href="/" onclick="Page.get(this.href); return false">
			<img src="{theme}/img/header-logo.svg" alt="">
		</a>
      </div>
      <nav class="header-nav menu">
        <ul>
          <li class="menu-dropdown">
            <a href="#">Locations</a>
            <img src="{theme}/img/arrow-bottom.svg" alt="">
            <ul>
              <li><a href="#">Schenectady, NY</a></li>
              <li><a href="#">Schenectady, NY</a></li>
              <li><a href="#">Schenectady, NY</a></li>
              <li><a href="#">Schenectady, NY</a></li>
              <li><a href="#">Schenectady, NY</a></li>
            </ul>
          </li>
          <li class="menu-dropdown">
            <a href="#">Services</a>
            <img src="{theme}/img/arrow-bottom.svg" alt="">
            <ul>
              <li><a href="#">Schenectady, NY</a></li>
              <li><a href="#">Schenectady, NY</a></li>
              <li><a href="#">Schenectady, NY</a></li>
              <li><a href="#">Schenectady, NY</a></li>
              <li><a href="#">Schenectady, NY</a></li>
            </ul>
          </li>
          <li><a href="#">Store</a></li>
          <li><a href="#">Pricing</a></li>
          <li><a href="#">Blog</a></li>
          <li class="menu-dropdown">
            <a href="#">About us</a>
            <img src="{theme}/img/arrow-bottom.svg" alt="">
            <ul>
              <li><a href="#">Schenectady, NY</a></li>
              <li><a href="#">Schenectady, NY</a></li>
              <li><a href="#">Schenectady, NY</a></li>
            </ul>
          </li>
          <li><a href="#">Self Service</a></li>
        </ul>
      </nav>
      <div class="header-tel">
        <a href="tel:1-866-488-2806">1-866-488-2806</a>
        <div class="header-call">
          <button onclick="smoothScroll('contact', 80)">Qet quote</button>
        </div>
      </div>
      <div class="header-cart">
        <a href="#">Sign in</a>
        <a href="#"><img src="{theme}/img/cart.svg" alt="">
          <ul class="header-basket">
            <li class="dropdown">
              <span>Subtotal:</span>
              <span class="header-price">$0</span>
            </li>
          </ul>
        </a>

      </div>
    </div>
    <div class="header-bottom">
      <div class="header-search">
        <button>
			<img src="{theme}/img/search.svg">
		</button>
        <input type="search" placeholder="Enter your zip or adress" onkeyup="if(event.keyCode == 13) nearStore(this);">
      </div>

    </div>
  </header>
  
  <span id="page">{content}</span>

  <footer class="footer">
    <div class="wrap">
        <div class="wrap-left">
          <p class="footer-text">2020 © Your Company. All Rights Reserved</p>
          <p class="footer-text">ERMS OF SERVICE | Devpartner</p>
        </div>
        <div class="wrap-right">
          <a href="#"><img src="{theme}/img/twitter.svg" alt=""></a>
          <a href="#"><img src="{theme}/img/instagram.svg" alt=""></a>
          <a href="#"><img src="{theme}/img/facebook.svg" alt=""></a>
        </div>
    </div>

  </footer>
</div>


<a class="sckroll" onclick="smoothScroll()">
	<img src="{theme}/img/scroll-top.svg">
</a>

<div class="chat">
  <img class="chat-icon" src="{theme}/img/chat.svg">

  <div class="chat-block">
     <span class="chat-title">Welcome</span>
     <img class="chat-close" src="{theme}/img/chat-close.svg" alt="">
    <div class="chat-bottom">
      <div class="chat-hello">
        <div><img src="{theme}/img/women.svg" alt=""></div>
        <p class="chat-text">Hello, me name is Ekaterina, and I will answer any of your questions</p>
      </div>
      <form class="chat-form">
        <input type="text" placeholder="Enter your name" required>
        <input type="email" placeholder="Enter your email" required>
        <input type="phone" placeholder="Enter your phone" required>
        <button type="submit">Start dialog</button>
      </form>
    </div>

  <div class="chat-dialog">
    <div class="chat-user">
      Hello, me name is Ekaterina, and I will answer any of your questions
    </div>
    <div class="chat-user">
      Hello, I want to ask one question.
    </div>
    <div class="chat-user">
      Your Company: Thank you for your patience, a technician will respond in approximately 60 seconds.
    </div>
    <div class="chat-send">
      <input type="text" placeholder="Write to us">
      <img src="{theme}/img/strelka.svg" alt="">
    </div>

  </div>

  </div>

</div>
<script defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAtalQktYG8vsla1PXKMpdUOyvmYK7a7YY&callback=initMap"></script>
<script src="//code.jquery.com/jquery-3.4.1.min.js"></script>
<script>
let lang_code = '{lang}',
	locations = [{obj-loc}],
	near = null;
</script>
<script src="{theme}/js/main.js"></script>
<script src="{theme}/js/custom.js"></script>
</body>
</html>