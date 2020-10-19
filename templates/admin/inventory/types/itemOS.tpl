<div class="sUser" id="os_{id}">
	<a class="sName" href='javascript:inventory.openOS({id}, "{name}");'>
		{name}
	</a>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li>
				<a href='javascript:inventory.openOS({id}, "{name}");'>
					<span class="fa fa-pencil"></span> {lang=editOS}
				</a>
			</li>
			<li>
				<a href="javascript:inventory.delOS({id})">
					<span class="fa fa-times"></span> {lang=delOS}
				</a>
			</li>
		</ul>
	</div>
</div>