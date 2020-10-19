<div class="tr dev" id="issue_{id}">
	<div class="td w10">
		{id}
	</div>
	<div class="td isDate">
		{date}
	</div>
	<div class="td isDescr">
		{description}
	</div>
	<div class="td">
		<a href="/users/view/{staff-id}" target="_blank">{staff-name} {staff-lastname}</a>
	</div>
	<div class="td w100">
		<a href="/pdf/barcode/{id}" target="_blank" class="hnt hntTop" data-title="Barcode"><span class="fa fa-barcode"></span></a>
		<a href="/issues/view/{id}" onclick="Page.get(this.href); return false;" class="hnt hntTop" data-title="Edit issue"><span class="fa fa-pencil"></span></a><!--onclick="issues.addIssue({id}, '{staff-name} {staff-lastname}', '{category} {model}'); return false;"-->
		<a href="#" class="hnt hntTop" onclick="issues.del({id}); return false;" data-title="Delete issue"><span class="fa fa-times"></span></a>
	</div>
</div>