{include="objects/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{title}
		<div class="uMore">
			<span class="fa fa-ellipsis-v" onclick="$(this).next().toggle(0);"></span>
			<ul>
				<li><a href="/objects/edit/{id}" onclick="Page.get(this.href); return false;"><span class="fa fa-pencil"></span> Edit store</a></li>
			</ul>
		</div>
	</div>
	<div class="userInfo">
		<div class="uDetails">
			<div class="tbl tblDev">
				<div class="tr">
					<div class="th w10">
						ID
					</div>
					<div class="th">
						Type
					</div>
					<div class="th">
						Category
					</div>
					<div class="th">
						Model
					</div>
					<div class="th">
						OS
					</div>
					<div class="th">
						Location
					</div>
					<div class="th w100">
						Options
					</div>
				</div>
				{devices}
			</div>
		</div>
	</div>
</section>