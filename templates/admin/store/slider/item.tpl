<div class="sUser" id="slide_{id}">
	<div class="uThumb">
		[image]<div><img src="/uploads/images/slider/{id}/thumb_{image}" onclick="showPhoto(this.src);"><span class="fa fa-search-plus" onclick="showPhoto(this.previousElementSibling.src);"></span></div>[not-image]<span class="fa fa-picture-o"></span>[/image]
	</div>
	<a href="/store/slider/edit/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			{title}
		</div>
	</a>
	<div class="uMore">
		[deny]
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			[edit]<li><a href="/store/slider/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> {lang=editSlide}</a></li>[/edit]
			[delete]<li><a href="javascript:store.delSlide({id});"><span class="fa fa-times"></span> {lang=delSlide}</a></li>[/delete]
		</ul>
		[/deny]
	</div>
</div>