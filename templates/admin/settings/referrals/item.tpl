<div class="sUser" id="refferal_{id}">
	<div class="uInfo">
		<div class="uInfo">
			{name}
		</div>
	</div>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li><a href="#" onclick="Settings.openRefferal({id}, '{name}'); return false;"><span class="fa fa-pencil"></span> {lang=EditRefferal}</a></li>
			<li><a href="javascript:Settings.delRefferal({id})"><span class="fa fa-times"></span> {lang=DeleteRefferal}</a></li>
		</ul>
	</div>
</div>