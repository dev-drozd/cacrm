<div class="sUser" id="page_{id}"[confirm][not-confirm] style="background: #f7f6b9!important;"[/confirm]>
	<div class="uThumb">
		[image]
		<div>
			<img src="/uploads/images/pages/{id}/thumb_{image}" onclick="showPhoto(this.src)">
			<span class="fa fa-search-plus" onclick="showPhoto(this.previousElementSibling.src)"></span>
		</div>
		[not-image]
		<span class="fa fa-picture-o"></span>
		[/image]
	</div>
	<a href="/store/pages/edit/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			{name}
		</div>
		[symbols]<div class="uInfo" style="color: red;">Edited | {symbols} symbols</div>[/symbols]
		[status]<div class="uInfo" style="color: red;">{status}</div>[/status]
		[nav]
		<div class="uInfo">
			>&nbsp;&nbsp;&nbsp;&nbsp;<b onclick="window.open('/store/nav/edit/{nav-id}');">{nav-name}</b>
		</div>
		[/nav]
	</a>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>		
			<li><a href="https://yoursite.com/{uri}" target="_blank"><span class="fa fa-eye"></span> View page</a></li>
			<li><a href="https://yoursite.com/{pathname}" target="_blank"><span class="fa fa-eye"></span> View on the site</a></li>
			[edit-page]<li><a href="/store/pages/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> {lang=editPage}</a></li>[/edit-page]
			[del-page]<li><a href="javascript:store.delPage({id})"><span class="fa fa-times"></span> {lang=delPage}</a></li>[/del-page]
		</ul>
	</div>
</div>