<div class="pnl fw lPnl">
	<div class="pnlTitle">
		{lang=Activity}
		<div class="filters">
			<span class="hnt hntTop" data-title="{lang=Filters}" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="fTitle">{lang=Filters}</div>
				<div class="iGroup fw dGroup cl">
					<label class="cl">Date <span class="fa fa-eraser" onclick="clearDate();"></span></label>
					<input type="text" onclick="$(this).next().show().parent().addClass('act');" name="date_activity" class="cl">
					<div id="calendar" data-multiple="true"></div>
				</div>
				<div class="iGroup fw" id="object">
					<label>{lang=Store}</label>
					<input type="hidden" name="object">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw" id="staff">
					<label>{lang=Staff}</label>
					<input type="hidden" name="staff">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw">
					<label>Group by</label>
					<select name="group">
						<option value="0">None</option>
						<option value="1">Week</option>
						<option value="2">Two weeks</option>
					</select>
				</div>
				[time-money]
				<div class="iGroup">
					<label>Salary</label>
					<input type="checkbox" name="salary">
				</div>
				<div class="iGroup">
					<label>Tax</label>
					<input type="checkbox" name="tax">
				</div>
				[/time-money]
				<div class="iGroup">
					<label>Hours</label>
					<input type="checkbox" name="hours">
				</div>
				<div class="cashGroup">
					<button type="button" class="btn btnSubmit ac" onclick="Search($('input[name=\'searchText\']').val());">{lang=OK}</button>
					<button type="button" class="btn btnSubmit dc" onclick="$(this).parents('.filterCtnr').hide();">{lang=Cancel}</button>
				</div>
			</div>
		</div>
		<span class="hnt hntTop exportXls" data-title="{lang=DownloadXLS}" id="download"><span class="fa fa-download"></span></span>
	</div>
	<div class="mngSearch">
		<input type="text" name="searchText" placeholder="{lang=timerSearch}" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="tbl">
		<div class="tHead">
			<div class="tr">
				<div class="th" style="width: 250px">{lang=Staff}</div>
				<div class="th" style="width: 100px">{lang=Date}</div>
				<div class="th w125">{lang=startTime}</div>
				<div class="th w125">{lang=pauseTime}</div>
				<div class="th w125">{lang=pauseTimeEnd}</div>
				<div class="th w125">{lang=stopTime}</div>
				<div class="th w125" id="hours"[hours][not-hours] style="display: none"[/hours]>{lang=workingTime}</div>
				[time-money]<div class="th w125" style="width: 125px;[salary][not-salary] display: none;[/salary]" id="salary">{lang=Salary}</div>[/time-money]
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
			$('input[name="date_activity"]').val($('#calendar > input[name="date"]').val() + ' / ' + $('#calendar > input[name="fDate"]').val());
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
					select: $('input[name="staff"]').data(),
					s: true
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
			location.href = location.origin + '/xls/timer?date_start=' + $('#calendar > input[name="date"]').val() + 
				'&date_finish=' + $('#calendar > input[name="fDate"]').val() + 
				'&event=' + $('select[name="searchSel"]').val() + 
				'&staff=' + Object.keys($('input[name="staff"]').data()).join(',') + 
				'&search=' + $('input[name="searchText"]').val() +
				'&group=' + $('select[name="group"]').val() +
				'&salary=' + $('input[name="salary"]').val() +
				'&tax=' + $('input[name="tax"]').val() +
				'&hours=' + $('input[name="hours"]').val();
		});
	});
	
	function clearDate() {
		$('#calendar > input[name="date"]').val('');
		$('#calendar > input[name="fDate"]').val('');
		$('input[name="date_activity"]').val('');
		$('input[name="staff"]').removeData();
		$('.filterCtnr').hide();
	}
</script>