<div class="ctnr">

	<div class="bBlog">
		
		<div class="breads"><a href="/" onclick="Page.get(this.href);return false;">Home</a> / <a href="/blog/" onclick="Page.get(this.href);return false;">Blog</a></div>
		
		<div class="dClear">
			<div class="blogs-list">
				<div class="cBlog">
					<div class="bTitle">
						{name}
						<div class="bDate">{dayname}, {month} {day}, {year} {time}</div>
					</div>
					<div class="bText">
						{content}
					</div>
					<ul class="ss">
						<li><a href="javascript:share('https://www.facebook.com/sharer/sharer.php?u=https://yoursite.com/blog/[pathname]{pathname}[not-pathname]{id}[/pathname]');"><span class="fa fa-facebook"></span></a></li>
						<li><a href="javascript:share('https://twitter.com/home?status=https://yoursite.com/blog/[pathname]{pathname}[not-pathname]{id}[/pathname]');"><span class="fa fa-twitter"></span></a></li>
						<li><a href="javascript:share('https://plus.google.com/share?url=https://yoursite.com/blog/[pathname]{pathname}[not-pathname]{id}[/pathname]');"><span class="fa fa-google-plus"></span></a></li>
					</ul>
				</div>
			</div>
			<div class="blogs-panel">
				<div class="prTitle">Categories</div>
				<ul class="blogs-ul">
					{categories}
				</ul>
				[posts]
				<div class="last-posts">
					<div class="prTitle">Last posts</div>
					<ul class="posts-ul">
						{posts}
					</ul>
				</div>
				[/posts]
			</div>
		</div>
	</div>
</div>