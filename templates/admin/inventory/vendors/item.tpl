<div class="sUser" id="vendor_{id}">
	<a class="sName" href='javascript:inventory.openVendor({id}, "{name}");'>
		{name}
	</a>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li>
				<a href='javascript:inventory.openVendor({id}, "{name}");'>
					<span class="fa fa-pencil"></span> {lang=editVendor}
				</a>
			</li>
			<li>
				<a href="javascript:inventory.delVendor({id})">
					<span class="fa fa-times"></span> {lang=delVendor}
				</a>
			</li>
		</ul>
	</div>
</div>