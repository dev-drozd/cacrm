<div class="pSide">
	<div class="userPhoto[photo][not-photo] brdr[/photo]">
		[photo]
			<img src="/uploads/images/users/{id}/preview_{image}">
			<span class="fa fa-search-plus" onclick="showPhoto.show(this.previousSibling.previousSibling.src);"></span>
		[not-photo]
		<span class="fa fa-user-secret"></span>
		[/photo]
	</div>
	<ul class="share dClear">
		<li>
			<a href="javascript:share('https://www.facebook.com/sharer/sharer.php?u='+location.origin+'/?ref={id}');" target="_blank">
				<span class="fa fa-facebook"></span>
			</a>
		</li>
		<li>
			<a href="javascript:share('https://twitter.com/home?status='+location.origin+'/?ref={id}');" target="_blank">
				<span class="fa fa-twitter"></span>
			</a>
		</li>
		<li>
			<a href="javascript:share('https://plus.google.com/share?url='+location.origin+'/?ref={id}');" target="_blank">
				<span class="fa fa-google-plus"></span>
			</a>
		</li>
	</ul>
	<div class="shareText">
		Share our link on social networks and you will get discounts in the store.
	</div>
</div>