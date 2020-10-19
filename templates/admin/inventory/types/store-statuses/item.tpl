<div class="sUser" id="status_{id}">
	<span class="fa fa-bars" draggable="true" ondragover="options.dragover(event)" ondragstart="options.dragstart(event)" ondragend="options.dragend(event, inventory.sendStorePriority);" onmousedown="$(this).parent().addClass('drag');" onmouseup="$(this).parent().removeClass('drag');"></span>
	<a class="sName" href='javascript:inventory.openStoreStatus({id}, "{name}");'>
		{name}
	</a>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li>
				<a href='javascript:inventory.openStoreStatus({id}, "{name}");'>
					<span class="fa fa-pencil"></span> {lang=editStatus}
				</a>
			</li>
			<li>
				<a href="javascript:inventory.delStoreStatus({id})">
					<span class="fa fa-times"></span> {lang=delStatus}
				</a>
			</li>
		</ul>
	</div>
</div>