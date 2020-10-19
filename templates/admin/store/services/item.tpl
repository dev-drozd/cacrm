<div class="sUser" id="service_{id}"[confirm][not-confirm] style="background: #f7f6b9!important;"[/confirm]>
	<div class="uThumb">
		[image]
		<div>
			<img src="/uploads/images/services/{id}/thumb_{image}" onclick="showPhoto(this.src)">
			<span class="fa fa-search-plus" onclick="showPhoto(this.previousElementSibling.src)"></span>
		</div>
		[not-image]
		<span class="fa fa-picture-o"></span>
		[/image]
	</div>
	<a href="/store/services/edit/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			{name}
		</div>
		[status]<div class="uInfo" style="color: red;">{status}</div>[/status]
	</a>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>		
			[edit-services]<li><a href="/store/services/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> Edit service</a></li>[/edit-services]
			[del-services]<li><a href="javascript:store.delService({id})"><span class="fa fa-times"></span> Delete service</a></li>[/del-services]
		</ul>
	</div>
</div>