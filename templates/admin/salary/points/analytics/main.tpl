{include="analytics/menu.tpl"}
<div class="pnl fw lPnl">
	<div class="pnlTitle">
		{lang=Points}
		<div class="filters">
			<span class="hnt hntTop" data-title="Filter" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="iGroup fw" id="object">
					<label>Store</label>
					<input type="hidden" name="object">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw dGroup cl">
					<label>Date <span class="fa fa-eraser" onclick="clearDate();"></span></label>
					<input class="cl" type="text" onclick="$(this).next().show().parent().addClass('act');" name="date_value">
					<div id="calendar" data-multiple="1"></div>
				</div>
				<div class="iGroup fw" id="staff">
					<label>Staff</label>
					<input type="hidden" name="staff">
					<ul class="hdn"></ul>
				</div>
				<div class="cashGroup">
					<button type="button" class="btn btnSubmit ac" onclick="Search($('input[name=\'searchText\']').val());">OK</button>
					<button type="button" class="btn btnSubmit dc" onclick="$(this).parents('.filterCtnr').hide();">Cancel</button>
				</div>
			</div>
		</div>
		<span class="hnt hntTop exportXls" data-title="Points" onclick="Page.get('/salary/points/')"><span class="fa fa-arrow-circle-left"></span></span>
		<!--<span class="hnt hntTop exportXls" data-title="Download XLS report" id="download"><span class="fa fa-download"></span></span>-->
	</div>
	<div class="mngSearch">
		<input type="text" name="searchText" placeholder="{lang=staffSearch}" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="tbl">
		<div class="tHead">
			<div class="tr">
				<div class="th wp50">Staff</div>
				<div class="th w125">Working time</div>
				<div class="th w125">Points per hour</div>
				<div class="th w125">Staff Points</div>
				<div class="th w125">Store Points</div>
				<div class="th w125">Total Points</div>
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
		
		$.post('/users/all', {staff: 1, nIds: Object.keys($('input[name="staff"]').data()).join(',')}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#staff > ul').html(items).sForm({
					action: '/users/all',
					data: {
						staff: 1,
						lId: lId,
						nIds: Object.keys($('input[name="staff"]').data() || {}).join(','),
						query: $('#staff > .sfWrap input').val() || ''
					},
					all: false,
					select: $('input[name="staff"]').data()
				}, $('input[name="staff"]'));
			}
		}, 'json');
		
		if (location.search) {
			var search = location.search,
				sDate = $_GET('sDate') || '',
				eDate = $_GET('eDate') || '',
				oName = ($_GET('name') || '').replace(/%20/ig, ' '),
				sName = $_GET('staff_name').replace(/%20/ig, ' '),
				o = {},
				s = {};
				
			if ($_GET('object') != 0) {
				o[$_GET('object')] = {name: oName}
				$('input[name="object"]').data(o);
			}
			if ($_GET('staff') != 0) {
				s[$_GET('staff')] = {name: sName}
				$('input[name="staff"]').data(s);
			}
			$('#calendar > input[name="date"]').val(sDate);
			$('#calendar > input[name="fDate"]').val(eDate);
			Search($('input[name="searchText"]').val());
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