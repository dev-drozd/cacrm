<div class="sUser" id="status_{id}">
	<span class="fa fa-bars" draggable="true" ondragover="options.dragover(event)" ondragstart="options.dragstart(event)" ondragend="options.dragend(event, camera.sendPriority);" onmousedown="$(this).parent().addClass('drag');" onmouseup="$(this).parent().removeClass('drag');"></span>
	<a class="sName" href='javascript:camera.openStatus({id}, "{name}");'>
		{name}
	</a>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li>
				<a href='javascript:camera.openStatus({id}, "{name}");'>
					<span class="fa fa-pencil"></span> Edit Status
				</a>
			</li>
			<li>
				<a href="javascript:camera.delStatus({id})">
					<span class="fa fa-times"></span> Del Status}
				</a>
			</li>
		</ul>
	</div>
</div>