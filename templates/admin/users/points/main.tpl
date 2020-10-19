{include="users/menu.tpl"}
<div class="mngContent lPnl">
	<div class="pnlTitle">
		Points
		<div class="filters">
			<span class="hnt hntTop" data-title="Filter" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="iGroup fw dGroup cl">
					<label>Date <span class="fa fa-eraser cl" onclick="clearDate();"></span></label>
					<input class="cl" type="text" onclick="$(this).next().show().parent().addClass('act');" name="date_value">
					<div id="calendar" data-multiple="1"></div>
				</div>
				<div class="iGroup fw" id="object">
					<label>Store</label>
					<input type="hidden" name="object">
					<ul class="hdn"></ul>
				</div>
				<div class="cashGroup">
					<button type="button" class="btn btnSubmit ac" onclick="Search($('input[name=\'searchText\']').val());">OK</button>
					<button type="button" class="btn btnSubmit dc" onclick="$(this).parents('.filterCtnr').hide();">Cancel</button>
				</div>
			</div>
		</div>
	</div>
	<!--[details]
	<div class="topMenu inside">
		<a href="/users/points/{uid}" onclick="Page.get(this.href); return false;">Points per hour</a>
	</div>
	[not-details]
	[can_details]
	<div class="topMenu inside">
		<a href="/users/point_details/{uid}" onclick="Page.get(this.href); return false;">Point details</a>
	</div>
	[/can_details]
	[/details]-->
	<div class="totalTime totalMoney">
		Total points: <span>{total}</span>
	</div>
	<div class="tbl">
		<div class="tHead">
			<div class="tr">
				<div class="th [details]w200[/details]">Date</div>
				<div class="th [details]w200[/details]">Points</div>
				[details]<div class="th w200">Object</div>
				<div class="th w200">Action</div>[/details]
			</div>
		</div>
	</div>
	<div class="userList tbl">
		<div class="tBody">
			{timers}
		</div>
	</div>
	{include="doload.tpl"}
</div>

<script>
	$(function() {
		$('#calendar').calendar(function() {
			$('input[name="date_value"]').val($('#calendar > input[name="date"]').val() + ' / ' + $('#calendar > input[name="fDate"]').val());
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
		
		if (location.search) {
			var search = location.search,
				sDate = $_GET('sDate') || '',
				eDate = $_GET('eDate') || '',
				oName = ($_GET('name') || '').replace(/%20/ig, ' '),
				o = {};
				
			if ($_GET('object') != 0) {
				o[$_GET('object')] = {name: oName}
				$('input[name="object"]').data(o);
			}
			$('#calendar > input[name="date"]').val(sDate);
			$('#calendar > input[name="fDate"]').val(eDate);
			//Search($('input[name="searchText"]').val());
		}
		
		$('#download').click(function() {
			location.href = location.origin + '/xls/salary?date_start=' + $('#calendar > input[name="date"]').val() + 
				'&date_end=' + $('#calendar > input[name="fDate"]').val() + 
				'&staff=' + Object.keys($('input[name="staff"]').data()).join(',') + 
				'&object=' + Object.keys($('input[name="object"]').data()).join(',') + 
				'&query' + $('input[name="searchText"]').val();
		});
	});
	
	function clearDate() {
		$('#calendar > input[name="date"]').val('');
		$('#calendar > input[name="fDate"]').val('');
		$('input[name="date_value"]').val('');
	}
</script>