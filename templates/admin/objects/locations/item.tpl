<div class="sUser" id="location_{id}">
	<a href="#" class="uInfo">
		<div class="uInfo">
			{name}
		</div>
	</a>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			[edit]<li><a href='javascript:objects.openLocation({id}, "{name}", {object-id}, {count})'><span class="fa fa-pencil"></span> {lang=editLocation}</a></li>[/edit]
			[add]<li><a href="javascript:objects.delLocation({id})"><span class="fa fa-times"></span> {lang=delLocation}</a></li>[/add]
		</ul>
	</div>
</div>