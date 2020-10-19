<div class="pnl fw lPnl">
	<div class="pnlTitle">
		{lang=All} {lang=cashStat}
		<div class="filters">
			<span class="hnt hntTop" data-title="{lang=Filters}" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="fTitle">{lang=Filters}</div>
				<div class="iGroup fw dGroup">
					<label>{lang=Date} <span class="fa fa-eraser" onclick="clearDate();"></span></label>
					<input type="text" class="cl" onclick="$(this).next().show().parent().addClass('act');" name="date_activity">
					<div id="calendar" data-multiple="true"></div>
				</div>
				<div class="iGroup fw cl" id="object">
					<label>{lang=Store}</label>
					<input type="hidden" name="object">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw cl">
					<label>{lang=Status}</label>
					<select name="status">
						<option value="0">{lang=notSelected}</option>
						<option value="accept">Accepted</option>
						<option value="dicline">Diclined</option>
					</select>
				</div>
				<div class="iGroup fw cl">
					<label>{lang=Action}</label>
					<select name="action">
						<option value="0">{lang=notSelected}</option>
						<option value="open">{lang=Open}</option>
						<option value="close">{lang=Close}</option>
					</select>
				</div>
				<div class="iGroup fw cl" id="staff">
					<label>{lang=Staff}</label>
					<input name="staff" type="hidden">
					<ul class="hdn"></ul>
				</div>
				<div class="cashGroup cl">
					<button type="button" class="btn btnSubmit ac cl" onclick="Search($('input[name=\'searchTex\']').val());">{lang=OK}</button>
					<button type="button" class="btn btnSubmit dc cl" onclick="$(this).parents('.filterCtnr').hide();">{lang=Cancel}</button>
				</div>
			</div>
		</div>
		<!--<span class="fa fa-download exportXls" id="download"></span>-->
	</div>
	<div class="mngSearch hdn">
		<input type="text" name="searchText" placeholder="{lang=activitySearch}" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="tbl">
		<div class="tHead">
			<div class="tr">
				<div class="th w150">{lang=Store}</div>
					<div class="th">{lang=Type}</div>
					<div class="th wAmount">{lang=SystemAmount}</div>
					<div class="th wAmount">{lang=UserAmount}</div>
					<div class="th wAmount">{lang=DrawerAmount}</div>
					<div class="th">{lang=Action}</div>
					<div class="th">{lang=Date}</div>
					<div class="th wStaff">{lang=Staff}</div>
					<div class="th" style="width: 30px"></div>
			</div>
		</div>
		<div class="tBody userList">
			{cash}
		</div>
	</div>
	{include="doload.tpl"}
</div>

<script>
	$(function() {
		$('#calendar').calendar(function() {
			$('input[name="date_activity"]').val($('#calendar > input[name="date"]').val() + ' / ' + $('#calendar > input[name="fDate"]').val());
			$('.dGroup > input + div').hide().parent().removeClass('act');
			//$('.filterCtnr').hide();
		});
		
		if (location.search) {
			var search = location.search,
				sDate = $_GET('sDate'),
				eDate = $_GET('eDate'),
				type = $_GET('type'),
				status = $_GET('status'),
				action = $_GET('action'),
				oName = $_GET('name').replace(/%20/ig, ' '),
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
			$('select[name="type"]').val(type).trigger('change');
			$('select[name="status"]').val(status).trigger('change');
			$('select[name="action"]').val(action).trigger('change');
			Search($('input[name="searchText"]').val());
		}
		
		$('#download').click(function() {
			location.href = location.origin + '/xls/activity?date_start=' + $('#calendar > input[name="date"]').val() + 
				'&date_finish=' + $('#calendar > input[name="fDate"]').val() + 
				'&event=' + $('select[name="searchSel"]').val() + 
				'&search' + $('input[name="searchText"]').val();
		});
		
		$.post('/invoices/objects', {}, function (r) {
			if (r){
				var items = '', lId = 0;
				if (r.list.length > 1) {
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
					}, $('input[name="object"]'), cash.selectObject);
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
					cash.selectObject();
				} else {
					alr.show({
						class: 'alrDanger',
						content: lang[168],
						delay: 3
					});
					$('#cashHistory').hide();
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
	});
	
	function clearDate() {
		$('#calendar > input[name="date"]').val('');
		$('#calendar > input[name="fDate"]').val('');
		$('input[name="date_activity"]').val('');
		$('input[name="staff"]').removeData();
		$('select[name="type"]').val(0).trigger('change');
		$('select[name="action"]').val(0).trigger('change');
		$('select[name="status"]').val(0).trigger('change');
		$('.filterCtnr').hide();
	}
	
</script>