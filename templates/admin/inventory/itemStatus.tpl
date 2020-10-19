<div class="sUser" id="status_{id}">
	<span class="fa fa-bars" draggable="true" ondragover="options.dragover(event)" ondragstart="options.dragstart(event)" ondragend="options.dragend(event, inventory.sendPriority);" onmousedown="$(this).parent().addClass('drag');" onmouseup="$(this).parent().removeClass('drag');"></span>
	<a class="sName" href="/inventory/types/status/{id}" onclick="Page.get(this.href); return false;">
		{name}
	</a>
	<div class="uCb">
		<input type="checkbox" name="noPriority"{priority} onchange="inventory.sendPriority(this.parentNode.parentNode)">
	</div>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li>
				<a href='javascript:inventory.openStatus({id}, {point-group}, "{name}", {forfeit})'>
					<span class="fa fa-pencil"></span> {lang=editStatus}
				</a>
			</li>
			<li>
				<a href='/inventory/types/status/{id}' onclick="Page.get(this.href); return false;">
					<span class="fa fa-map-marker"></span> {lang=editLocations}
				</a>
			</li>
			<li>
				<a href="javascript:inventory.delStatus({id})">
					<span class="fa fa-times"></span> {lang=delStatus}
				</a>
			</li>
		</ul>
	</div>
</div>