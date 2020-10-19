{include="analytics/menu.tpl"}
<div class="mngContent">
	<div class="pnlTitle">
		Users profit
		<div class="filters">
			<span class="hnt hntTop" data-title="{lang=Filters}" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="fTitle">{lang=Filters}</div>
				<div class="iGroup fw dGroup">
					<label>{lang=Date} <span class="fa fa-eraser" onclick="clearDate();"></span></label>
					<input type="text" onclick="$(this).next().show().parent().addClass('act');" class="cl" name="date_activity">
					<div id="calendar" data-multiple="true"></div>
				</div>
				<div class="iGroup fw dGroup">
					<label>{lang=Type}</label>
					<select name="type">
						<option value="0">All</option>
						<option value="stock">Stock</option>
						<option value="service">Service</option>
						<option value="purchase">Purchase</option>
						<option value="tradein">Tradein</option>
						<option value="salary">Salary</option>
					</select>
				</div>
				<div class="iGroup sfGroup fw" id="object">
					<label>{lang=Store}</label>
					<input type="hidden" name="object" />
					<ul class="hdn"></ul>
				</div>
				<div class="cashGroup">
					<button type="button" class="btn btnSubmit ac" onclick="Search($('input[name=\'searchText\']').val());">{lang=OK}</button>
					<button type="button" class="btn btnSubmit dc" onclick="$(this).parents('.filterCtnr').hide();">{lang=Cancel}</button>
				</div>
			</div>
		</div>
		<!-- <span class="hnt hntTop exportXls" data-title="{lang=DownloadXLS}" id="download"><span class="fa fa-download"></span></span> -->
	</div>
	<div class="tbl">
		<div class="tHead">
			<div class="tr">
				<div class="th">Store</div>
				<div class="th">Date</div>
				<div class="th">Type</div>
				<div class="th">Issue</div>
				<div class="th">Amount</div>
			</div>
		</div>
		<div class="tBody userList">
			{profit}
		</div>
	</div>
	{include="doload.tpl"}
</div>
<script>
	$(function() {
		$('#calendar').calendar(function() {
			$('input[name="date_activity"]').val($('#calendar > input[name="date"]').val() + ' / ' + $('#calendar > input[name="fDate"]').val());
			$('.dGroup > input + div').hide().parent().removeClass('act');
		});
		
		$.post('/invoices/objects', {}, function (r) {
			if(r.list.length > 1) {
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '" data-tax="' + v.tax + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#object > ul').html(items).sForm({
					action: '/invoices/objects',
					data: {
						lId: lId,
						query: $('#object > .sfWrap input').val() || ''
					},
					all: false,
					select: $('input[name="object"]').data()
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
					alr.show({
						class: 'alrDanger',
						content: lang[168],
						delay: 3
					});
					Page.get('/purchases');
				} 
		}, 'json');
	});
</script>