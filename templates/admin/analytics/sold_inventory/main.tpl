{include="analytics/menu.tpl"}
<div class="mngContent">
	<div class="pnlTitle">
		Sales plot
		<div class="filters">
			<span class="hnt hntTop" data-title="{lang=Filters}" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="fTitle">{lang=Filters}</div>
				<div class="iGroup fw dGroup">
					<label>{lang=Date} <span class="fa fa-eraser" onclick="clearDate();"></span></label>
					<input type="text" onclick="$(this).next().show().parent().addClass('act');" class="cl" name="date_plot">
					<div id="calendar_plot" data-multiple="true"></div>
				</div>
				<div class="iGroup fw" id="object_stat">
					<label>{lang=Store}</label>
					<input type="hidden" name="object_stat">
					<ul class="hdn"></ul>
				</div>
				<div class="cashGroup">
					<button type="button" class="btn btnSubmit ac" onclick="analytics.tradein_plot(this);">{lang=OK}</button>
					<button type="button" class="btn btnSubmit dc" onclick="$(this).parents('.filterCtnr').hide();">{lang=Cancel}</button>
				</div>
			</div>
		</div>
	</div>
	<div id="stat"></div>
	<div class="legend">
		<div class="legend-line">
			<span style="background: #36b1e6"></span> Sold
		</div>
		<div class="legend-line">
			<span style="background: #ff0000"></span> Purchase
		</div>
		<div class="legend-line">
			<span style="background: #b7bd06"></span> Profit
		</div>
	</div>
</div>

<div class="pnl fw lPnl">
	<div class="pnlTitle">
		{lang=SoldInventory}
		<div class="filters">
			<span class="hnt hntTop" data-title="{lang=Filters}" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="fTitle">{lang=Filters}</div>
				<div class="iGroup fw dGroup">
					<label>{lang=Date} <span class="fa fa-eraser" onclick="clearDate();"></span></label>
					<input type="text" onclick="$(this).next().show().parent().addClass('act');" class="cl" name="date_activity">
					<div id="calendar" data-multiple="true"></div>
				</div>
				<div class="iGroup fw" id="object">
					<label>{lang=Store}</label>
					<input type="hidden" name="object">
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
	<div class="totalTime totalMoney scroll">
		{lang=TotalSold}: $<span id="total">{total}</span>
	</div>
	<div class="tbl">
		<div class="tHead">
			<div class="tr">
				<div class="th">ID</div>
				<div class="th">{lang=Name}</div>
				<div class="th">{lang=Price}</div>
			</div>
		</div>
		<div class="tBody userList">
			{inventory}
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
		
		$('#calendar_plot').calendar(function() {
			$('input[name="date_plot"]').val($('#calendar_plot > input[name="date"]').val() + ' / ' + $('#calendar_plot > input[name="fDate"]').val());
			$('.dGroup > input + div').hide().parent().removeClass('act');
		});
		
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
							query: $('#object > .sfWrap input').val() || ''
						},
						all: (r.count <= 20) ? true : false,
						select: $('input[name="object"]').data(),
						s: true
					}, $('input[name="object"]'));
					
					$('#object_stat > ul').html(items).sForm({
						action: '/invoices/objects',
						data: {
							lId: lId,
							query: $('#object_stat > .sfWrap input').val() || ''
						},
						all: (r.count <= 20) ? true : false,
						select: $('input[name="object_stat"]').data(),
						s: true
					}, $('input[name="object_stat"]'));
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
					
					$('#object_stat').append($('<div/>', {
						class: 'storeOne',
						html: r.list[0].name
					}));
				} else {
					$('#object').hide();
					$('#object_stat').hide();
				}
			}
		}, 'json');
		
		var tmTop = $('.totalMoney.scroll').offset().top,
			to = undefined;
		$(window).scroll(function() {
			to = setTimeout(function() {
				if ($('body').scrollTop() > tmTop + 100)
					$('.totalMoney.scroll').addClass('fixed');
				else
					$('.totalMoney.scroll').removeClass('fixed');
				clearTimeout(to);
			}, 100)
		})
		
		analytics.inventory_plot();
	});
</script>