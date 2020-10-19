<div class="sUser" id="archive_{id}"[new] style="background: #aff4b9;"[/new]>
	<div class="uThumb">
		<div>
			<img src="/uploads/images/work_archive/{id}/thumb_{image}" onclick="showPhoto(this.src);">
		</div>
	</div>
	<a href="/issues/publish/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			{title}
			<p>{issue}</p>
		</div>
	</a>
	<div class="pInfo">
		<span><a href="/users/view/{author-id}" onclick="Page.get(this.href); return false;">{author-name}</a></span>
	</div>
	<div class="pInfo">
		<span>{device}</span>
		<p><font color="grey">{date}</font></p>
	</div>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li><a href="/issues/publish/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> Edit</a></li>
			<li><a href="javascript:this_del({id})"><span class="fa fa-times"></span> Delete</a></li>
		</ul>
	</div>
</div>