<div class="sUser" id="customer_{id}">
	<a href="/inventory/step/?user={id}&type={type-id}&brand={brand-id}&model={model-id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			{first-name} {last-name}
		</div>
	</a>
	<div class="pInfo">
		<p><a href="tel:{phone}">{phone}</a></p>
		<p><a href="mailto:{email}">{email}</a></p>
	</div>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li><a href="#" onclick="Page.get(this.href); return false;" ><span class="fa fa-eye"></span> Next steep</a></li>		
		</ul>
	</div>
</div>