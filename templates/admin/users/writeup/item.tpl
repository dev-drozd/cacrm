<div class="sUser" id="writeup_{id}">
	<a class="sName" href='javascript:user.openWriteup({id}, "{name}");'>
		{name}
	</a>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li>
				<a href='javascript:user.openWriteup({id}, "{name}");'>
					<span class="fa fa-pencil"></span> Edit write up
				</a>
			</li>
			<li>
				<a href="javascript:user.delWriteup({id})">
					<span class="fa fa-times"></span> Del write up
				</a>
			</li>
		</ul>
	</div>
</div>