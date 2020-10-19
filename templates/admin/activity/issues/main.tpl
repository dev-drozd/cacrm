<aside class="sideNvg">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>Manage</div>
	<ul class="mng">
		<li><a href="/activity/issues" onclick="Page.get(this.href); return false;"><span class="fa fa-cart-plus" style="color: #A2CE4E;"></span>All jobs</a></li>
		[archive]<li><a href="/issues/archive" onclick="Page.get(this.href); return false;"><span class="fa fa-book"></span>Archive jobs</a></li>[/archive]
	</ul>
</aside>
<section class="mngContent">
	<div class="pnlTitle">
		Jobs
		[filters]
		<div class="filters">
			<span class="hnt hntTop" data-title="{lang=Filters}" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="fTitle">{lang=Filters}</div>
				<div class="iGroup fw dGroup">
					<label>{lang=Date} <span class="fa fa-eraser" onclick="clearDate();"></span></label>
					<input type="text" onclick="$(this).next().show().parent().addClass('act');" class="cl" name="date_activity" readonly>
					<div id="calendar" data-multiple="true"></div>
				</div>
				<div class="iGroup fw" id="object">
					<label>{lang=Store}</label>
					<select name="object" >
						{stores}
					</select>
				</div>
				<!--<div class="iGroup fw" id="staff">
					<label>{lang=Assigned}</label>
					<input type="hidden" name="staff">
					<ul class="hdn"></ul>
				</div>-->
				<div class="iGroup fw">
					<label>{lang=Status}</label>
					<select name="status" onchange="if (this.value > 0) $('[name=\'current_status\']').val(0).trigger('change');">
						{statuses}
					</select>
				</div>
				<div class="iGroup fw">
					<label>{lang=CurrentStatus}</label>
					<select name="current_status" onchange="if (this.value > 0) $('[name=\'status\']').val(0).trigger('change');">
						{statuses}
					</select>
				</div>
				<div class="iGroup fw">
					<label>{lang=Payment}</label>
					<select name="payment">
						<option value="">- Not selected -</option>
						<option value="unpaid">{lang=Unpaid}</option>
						<option value="paid">{lang=Paid}</option>
					</select>
				</div>
				<div class="iGroup">
					<label>{lang=Internal}</label>
					<input type="checkbox" name="instore">
				</div>
				<div class="iGroup">
					<label>{lang=PickedUp}</label>
					<input type="checkbox" name="pickedup">
				</div>
				<div class="iGroup">
					<label>{lang=ShowAll}</label>
					<input type="checkbox" name="all">
				</div>
				<div class="iGroup">
					<label>Archive</label>
					<input type="checkbox" name="title">
				</div>
				<div class="cashGroup">
					<button type="button" class="btn btnSubmit ac" onclick="Search2($('input[name=\'searchText\']').val());">{lang=OK}</button>
					<button type="button" class="btn btnSubmit dc" onclick="$(this).parents('.filterCtnr').hide();">{lang=Cancel}</button>
				</div>
			</div>
		</div>
		<span class="hnt hntTop exportXls" data-title="{lang=DownloadXLS}" id="download"><span class="fa fa-download"></span></span>
		[not-filters]
		<input name="object" hidden>
		<span id="calendar">
			<input name="date" hidden>
			<input name="fDate" hidden>
		</span>
		<input name="current_status" hidden>
		[/filters]
	</div>
	<div class="mngSearch">
		<input type="text" name="searchText" value="{query}" placeholder="{lang=issueSearch}" onkeypress="if(event.keyCode == 13) Search2(this.value);">
		<button class="btn btnSearch" onclick="Search2($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="tbl issuesTbl">
		<div class="tHead">
			<div class="tr">
				<div class="th">ID</div>
				<div class="th">{lang=Customer}</div>
				<div class="th">{lang=Date}</div>
				<div class="th">{lang=total}</div>
				<div class="th">{lang=DeviceType}</div>
				<div class="th">Paid</div>
				<div class="th">{lang=Assigned}</div>
				<div class="th">{lang=Location}</div>
				<div class="th">{lang=CurrentStatus}</div>
				<div class="th">{lang=Status}</div>
			</div>
		</div>
		<div class="tBody userList">
			{issues}
		</div>
	</div>
	{include="doload.tpl"}
</section>

<script>
	$(function() {
		$('div#calendar').calendar(function() {
			$('input[name="date_activity"]').val($('#calendar > input[name="date"]').val() + ' / ' + $('#calendar > input[name="fDate"]').val());
			$('.dGroup > input + div').hide().parent().removeClass('act');
		});
		
		if (location.search) {
			var search = location.search,
				sDate = ($_GET('sDate') || $_GET('date_start') || ''),
				eDate = ($_GET('eDate') || $_GET('date_finish') || ''),
				st = ($_GET('status') || ''),
				cst = ($_GET('current_status') || ''),
				payment = $_GET('payment') || '',
				oName = ($_GET('name') || '').replace(/%20/ig, ' '),
				sName = ($_GET('staff_name') || '').replace(/%20/ig, ' '),
				pickedup = parseInt(($_GET('pickedup') || 0)),
				all = parseInt(($_GET('all') || 0)),
				o = {},
				s = {};
				
			if ($_GET('object') != 0) o[$_GET('object')] = {name: oName};
			if ($_GET('staff') != 0) s[$_GET('staff')] = {name: sName};
			$('#calendar > input[name="date"]').val(sDate);
			$('#calendar > input[name="fDate"]').val(eDate);
			$('input[name="date_activity"]').val(sDate + ' / ' + eDate);
			$('input[name="object"]').data(o);
			$('input[name="staff"]').data(s);
			if(st) $('select[name="status"]').val(st).trigger('change');
			$('select[name="object"]').val($_GET('object')).trigger('change');
			$('[name="current_status"]').val(cst).trigger('change');
			$('select[name="payment"]').val(payment).trigger('change');
			if (pickedup) $('input[name="pickedup"]').val(pickedup).trigger('click');
			if (all) $('input[name="all"]').val(all).trigger('click');
			//Search2($('input[name="searchText"]').val());
		}
		
		/*$.post('/invoices/objects', {}, function (r) {
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
		}, 'json');*/
		
		/*$.post('/users/all', {staff: 1, nIds: Object.keys($('input[name="staff"]').data()).join(',')}, function (r) {
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
					select: $('input[name="staff"]').data(),
					s: true
				}, $('input[name="staff"]'));
			}
		}, 'json');*/
		
		$('#download').click(function() {
			location.href = location.origin + '/xls/issues?date_start=' + $('#calendar > input[name="date"]').val() + 
				'&date_finish=' + $('#calendar > input[name="fDate"]').val() + 
				'&event=' + $('select[name="searchSel"]').val() + 
				'&status=' + $('select[name="status"]').val() + 
				'&current_status=' + $('select[name="current_status"]').val() + 
				'&staff=' + Object.keys($('input[name="staff"]').data() || '').join(',') + 
				'&search' + $('input[name="searchText"]').val();
		});
	});
	
	function clearDate() {
		$('#calendar > input[name="date"]').val('');
		$('#calendar > input[name="fDate"]').val('');
		$('input[name="date_activity"]').val('');
		$('input[name="staff"]').removeData();
		$('select[name="status"]').val(0).trigger('change');
		$('.filterCtnr').hide();
	}
</script>