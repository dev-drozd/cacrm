<div class="sUser" id="nav_{id}">
	<span class="fa fa-bars" draggable="true" ondragover="options.dragover(event)" ondragstart="options.dragstart(event)" ondragend="options.dragend(event, store.sendNavPriority);" onmousedown="$(this).parent().addClass('drag');" onmouseup="$(this).parent().removeClass('drag');"></span>
	<a href="/store/nav/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			{name} ({action-type})
		</div>
	</a>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li><a href="/store/nav/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-eye"></span> Edit item</a></li>
			<li><a href="javascript:store.delNav({id})"><span class="fa fa-times"></span> Del nav</a></li>
		</ul>
	</div>
</div>