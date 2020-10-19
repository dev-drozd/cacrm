{include="inventory/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>All {title}
		[create]<a href="/inventory/transfer/add" class="btn addBtn" onclick="Page.get(this.href); return false;">New transfer</a>[/create]
		<!-- <div class="filters">
			<span class="hnt hntTop" data-title="Filter" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="fTitle">Filters</div>
				<div class="iGroup fw">
					<label>Status</label>
					<select name="status">
						<option value="0">All</option>
						<option value="confirmed">Confirmed</option>
						<option value="notconfirmed">Not confirmed</option>
					</select>
				</div>
				<div class="iGroup fw" id="object">
					<label>Store</label>
					<input type="hidden" name="object">
					<ul class="hdn"></ul>
				</div>
				<div class="cashGroup">
					<button type="button" class="btn btnSubmit ac" onclick="Search($('input[name=\'search\']').val(), '{type}');">OK</button>
					<button type="button" class="btn btnSubmit dc" onclick="$(this).parents('.filterCtnr').hide();">Cancel</button>
				</div>
			</div>
		</div> -->
	</div>
	<div class="mngSearch">
		<input type="text" placeholder="Inventory search" onkeypress="if(event.keyCode == 13) Search(this.value, '{type}');" oninput="checkBarcode(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val(), '{type}');">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{transfers}
	</div>
	{include="doload.tpl"}
</section>
<script>
$(function() {
	$.post('/invoices/objects', {}, function (r) {
		if (r){
			if (r.list.length > 1) {
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '" data-tax="' + v.tax + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#object > ul').html(items).sForm({
					action: '/invoices/objects',
					data: {
						lId: lId,
						query: $('#object> .sfWrap input').val() || ''
					},
					all: (r.count <= 20) ? true : false,
					select: $('input[name="object"]').data(),
					s: true
				}, $('input[name="object"]'));
			} else if (r.list.length == 1) {
				var cash_object = {};
				cash_object[r.list[0].id] = {
					name: r.list[0].name
				}
				$('input[name="object"]').data(cash_object);
				$('#object').append($('<div/>', {
					class: 'storeOne',
					html: r.list[0].name
				}));
			} else {
				$('#object').hide();
			}
		}
	}, 'json');
})
</script>