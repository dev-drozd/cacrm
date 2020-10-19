<div class="breadcrumps wrap">
	<ul>
		<li>
			<a href="/" onclick="Page.get(this.href); return false;">Main</a>
		</li>
		<li>
			<a href="/blog" onclick="Page.get(this.href); return false;">Blog</a>
		</li>
		<li>
			<a href="#">Category name</a>
		</li>
		<li class="active">
			<a href="#">{name}</a>
		</li>
	</ul>
</div>

<div class="request wrap">
	<button class="request-title" onclick="showFilter()">Choice category</button>
</div>


<section class="category">
  <h1 class="category-title category-title-single">{name}</h1>

  <div class="category-block block wrap">
    [categories]
    <div class="category-block-left">
      <img class="category-close" src="{theme}/img/close.svg" alt="">
      <div>
        <h3 class="block-title">Categories:</h3>
        <ul>{categories}</ul>
      </div>
    </div>
	[/categories]
	<div class="category-block-right">
		<div class="single">
<!-- 			<div class="single-img">
				<img src="{theme}/img/single-img.png" alt="{name}">
			</div> -->
			<div class="single-block">{content}</div>
			<div class="single-meta">
				<div class="single-meta-block">
					<div class="box-metka">
						<img src="{theme}/img/metka.svg" alt=""><a href="#">Business IT tips</a><a href="#">IT Support </a>
					</div>
					<p class="box-date">{date}</p>
				</div>
				<div class="single-social">
					<span>Share:</span>
					<a href="#">
						<img src="{theme}/img/telegramm.svg" alt="">
					</a>
					<a href="#">
						<img src="{theme}/img/twitter-1.svg" alt="">
					</a>
					<a href="#">
						<img src="{theme}/img/facebook1.svg" alt="">
					</a>
					<a href="#">
						<img src="{theme}/img/whatsapp-1.svg" alt="">
					</a>
				</div>
			</div>
		</div>
	</div>
  </div>
</section>