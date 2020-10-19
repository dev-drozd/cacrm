<div class="tr dev" id= "dev_{id}" data-serial="{serial}" [has-issue]onclick="account.toggleList(this, event);"[/has-issue]>
	<div class="td w10">
		<span class="fa fa-chevron-right isOpen [has-issue][not-has-issue]hide[/has-issue]"></span> <strong>#{id}</strong>
	</div>
	<div class="td clType" data-id="{type-id}">
		{type}
	</div>
	<div class="td clCat" data-id="{category-id}">
		{category}
	</div>
	<div class="td clMo">
		{model}
	</div>
	<div class="td clOs" data-id="{os-id}" data-ver="{os-ver}">
		{os}
	</div>
	<div class="td">
		{status}
	</div>
	<div class="td w100">
		<a href="javascript:account.addIssue({id});" class="hnt hntTop" data-title="Add issue"><span class="fa fa-plus"></span></a>
		<a href="javascript:account.delDevice({id});" class="hnt hntTop" data-title="Delete device"><span class="fa fa-times"></span></a>
	</div>
</div>
[has-issue]
<div class="tr issues">
	<div class="issue head">
		<div class="iId">
			id
		</div>
		<div class="isDate">
			Date
		</div>
		<div class="is">
			Description
		</div>
	</div>
	{issues}
</div>
[/has-issue]