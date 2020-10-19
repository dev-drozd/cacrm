<div class="blog dClear">
	<div class="date">
		<div class="day">{day}</div>
		{month} {year}
	</div>
	<div class="nmContent">
		<a href="/blog/[pathname]{pathname}[not-pathname]{id}[/pathname]" onclick="Page.get(this.href);return false;" class="mbTitle">{name}</a>
		<p>{content} <a href="/blog/[pathname]{pathname}[not-pathname]{id}[/pathname]" onclick="Page.get(this.href);return false;">Read more</a></p>
	</div>
	<ul class="ss">
		<li><a href="javascript:share('https://www.facebook.com/sharer/sharer.php?u=https://yoursite.com/blog/[pathname]{pathname}[not-pathname]{id}[/pathname]');" target="_blank"><span class="fa fa-facebook"></span></a></li>
		<li><a href="javascript:share('https://twitter.com/home?status=https://yoursite.com/blog/[pathname]{pathname}[not-pathname]{id}[/pathname]');" target="_blank"><span class="fa fa-twitter"></span></a></li>
		<li><a href="javascript:share('https://plus.google.com/share?url=https://yoursite.com/blog/[pathname]{pathname}[not-pathname]{id}[/pathname]');" target="_blank"><span class="fa fa-google-plus"></span></a></li>
	</ul>
</div>