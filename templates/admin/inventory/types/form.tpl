{include="inventory/types/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=addGroup}</div>
	<form class="uForm" method="post" onsubmit="inventory.addGroup(this, event, {id});">
		<div class="iGroup">
			<label>{lang=Name}</label>
			<input type="text" name="name" value="{name}">
		</div>
		<!-- <div class="iGroup" id="brand">
			<label>{lang=Brand}</label>
			<input type="hidden" name="brand">
			<ul class="hdn"></ul>
			<select name="type">
				<option value="service" checked>Service</option>
				<option value="inventory">Inventory</option>
			</select>
		</div> -->
		{options}
		<div class="iGroup addOpt">
			<button class="btn btnSubmit ao" onclick="options.add(this); return false;"><span class="fa fa-plus"></span> {lang=addOption}</button>
		</div>
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {send}</button>
		</div>
	</form>
</section>
<script>
//$('select[name="type"] > option[value="{type}"]').prop('selected', true).trigger('change');
$('input[name="brand"]').data({brand-id});
$.post('/inventory/allCategories', {nIds: Object.keys($('input[name="brand"]').data()).join(',')}, function (r) {
	if (r){
		var items = '', lId = 0;
		$.each(r.list, function(i, v) {
			items += '<li data-value="' + v.id + '">' + v.name + '</li>';
			lId = v.id;
		});
		$('#brand > ul').html(items).sForm({
			action: '/inventory/allCategories',
			data: {
				lId: lId,
				nIds: Object.keys($('input[name="brand"]').data() || {}).join(','),
				query: $('#brand > .sfWrap input').val() || ''
			},
			all: (r.count <= 20) ? true : false,
			select: $('input[name="brand"]').data(),
			s: true
		}, $('input[name="brand"]'));
	}
}, 'json');
</script>