<div class="sUser" id="status_{id}">
	<span class="fa [nnew]fa-bars[not-nnew]noSort[/nnew]" [nnew]draggable="true" ondragover="options.dragover(event)" ondragstart="options.dragstart(event)" ondragend="options.dragend(event, inventory.sendWarrantyPriority);" onmousedown="$(this).parent().addClass('drag');" onmouseup="$(this).parent().removeClass('drag');"[/nnew]></span>
	<a class="sName" href="/inventory/warranty_statuses/{id}" onclick="Page.get(this.href); return false;">
		{name}
	</a>
	<div class="uCb">
		[nnew]<input type="checkbox" name="noPriority"{priority} onchange="inventory.sendWarrantyPriority(this.parentNode.parentNode)">[/nnew]
	</div>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			[nnew]<li>
				<a href='javascript:inventory.openStatus({id}, {point-group}, "{name}", {forfeit}, {sms}, {smsForm}, 1)'>
					<span class="fa fa-pencil"></span> {lang=editStatus}
				</a>
			</li>[/nnew]
			<li>
				<a href='/inventory/warranty_statuses/{id}' onclick="Page.get(this.href); return false;">
					<span class="fa fa-map-marker"></span> {lang=editLocations}
				</a>
			</li>
			<li>
				[nnew]<a href="javascript:inventory.delStatus({id}, 1)">
					<span class="fa fa-times"></span> {lang=delStatus}
				</a>
			</li>[/nnew]
		</ul>
	</div>
</div>