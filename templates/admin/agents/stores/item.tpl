<div class="sUser[deleted] deleted[/deleted]" id="store_{id}">
	<!--<div class="uThumb">
		<span class="fa fa-user-secret"></span>
	</div>-->
	<a href="/agents/stores/edit/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			{name}
			<p>{descr}</p>
		</div>
	</a>
	<div class="pInfo">
		{phone}<br>
		<i>{address}</i>
	</div>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li><a href="/agents/stores/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> Edit store</a></li>
			<li><a href="javascript:agents.delStore({id})"><span class="fa fa-times"></span> Del store</a></li>
		</ul>
	</div>
</div>