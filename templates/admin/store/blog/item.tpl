<div class="sUser" id="post_{id}"[confirm][not-confirm] style="background: #f7f6b9!important;"[/confirm]>
	<div class="uThumb">
		[image]
		<div>
			<img src="/uploads/images/blogs/{id}/thumb_{image}" onclick="showPhoto(this.src)">
			<span class="fa fa-search-plus" onclick="showPhoto(this.previousElementSibling.src)"></span>
		</div>
		[not-image]
		<span class="fa fa-picture-o"></span>
		[/image]
	</div>
	<a href="/store/blog/edit/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			{name}
		</div>
		[symbols]<div class="uInfo" style="color: red;">Edited | {symbols} symbols</div>[/symbols]
		[status]<div class="uInfo" style="color: red;">{status}</div>[/status]
	</a>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>		
			[edit-blog]<li><a href="/store/blog/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> {lang=editPost}</a></li>[/edit-blog]
			[del-blog]<li><a href="javascript:store.delBlog({id})"><span class="fa fa-times"></span> {lang=delPost}</a></li>[/del-blog]
		</ul>
	</div>
</div>