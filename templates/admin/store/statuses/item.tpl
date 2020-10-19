<div class="sUser" id="status_{id}">
	<span class="fa fa-bars" draggable="true" ondragover="options.dragover(event)" ondragstart="options.dragstart(event)" ondragend="options.dragend(event, store.sendPriority);" onmousedown="$(this).parent().addClass('drag');" onmouseup="$(this).parent().removeClass('drag');"></span>
	<a class="sName" href='javascript:store.openStatus({id}, "{name}", {form}, "{color}", "{alt_color}");'>
		{name}
	</a>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li>
				<a href='javascript:store.openStatus({id}, "{name}", {form}, "{color}", "{alt_color}");'>
					<span class="fa fa-pencil"></span> {lang=editStatus}
				</a>
			</li>
			[default][not-default]
			<li>
				<a href="javascript:store.delStatus({id})">
					<span class="fa fa-times"></span> {lang=delStatus}
				</a>
			</li>
			[/default]
		</ul>
	</div>
</div>