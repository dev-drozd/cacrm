<div class="pnl fw lPnl">
	<div class="pnlTitle">
		Appointments
		<div class="filters">
			<span class="hnt hntTop" data-title="{lang=Filters}" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="fTitle">{lang=Filters}</div>
				<div class="iGroup fw dGroup cl">
					<label class="l">{lang=Date} <span class="fa fa-eraser" onclick="clearDate();"></span></label>
					<input type="text" onclick="$(this).next().show().parent().addClass('act');" class="cl" name="date_activity">
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
					<label>Status</label>
					<select name="status">
						<option value="0">Not selected</option>
						<option value="accept">Accepted</option>
						<option value="dicline">Diclined</option>
					</select>
				</div>
				<div class="cashGroup cl">
					<button type="button" class="btn btnSubmit ac cl" onclick="Search($('input[name=\'searchTex\']').val());">{lang=OK}</button>
					<button type="button" class="btn btnSubmit dc cl" onclick="$(this).parents('.filterCtnr').hide();">{lang=Cancel}</button>
				</div>
			</div>
		</div>
	</div>
	<div class="tbl">
		<div class="tHead">
			<div class="tr">
				<div class="th">ID</div>
				<div class="th">Customer</div>
				<div class="th">Date</div>
				<div class="th">Store</div>
				<div class="th">Confirmed staff</div>
			</div>
		</div>
		<div class="tBody userList">
			{appointments}
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
		
		if (location.search) {
			var search = location.search,
				sDate = $_GET('sDate'),
				eDate = $_GET('eDate'),
				oName = $_GET('name').replace(/%20/ig, ' '),
				sName = $_GET('staff_name').replace(/%20/ig, ' '),
				status = $_GET('status'),
				o = {},
				s = {};
				
			if ($_GET('object') != 0) o[$_GET('object')] = {name: oName};
			if ($_GET('staff') != 0) s[$_GET('staff')] = {name: sName};
			$('#calendar > input[name="date"]').val(sDate);
			$('#calendar > input[name="fDate"]').val(eDate);
			$('input[name="date_activity"]').val(sDate + ' / ' + eDate);
			$('input[name="object"]').data(o);
			$('input[name="staff"]').data(s);
			$('select[name="status"]').val(status).trigger('change');
		}
		
		$('#download').click(function() {
			location.href = location.origin + '/xls/activity?date_start=' + $('#calendar > input[name="date"]').val() + 
				'&date_finish=' + $('#calendar > input[name="fDate"]').val() + 
				'&event=' + $('select[name="searchSel"]').val() + 
				'&staff=' + Object.keys($('input[name="staff"]').data()).join(',') + 
				'&search' + $('input[name="searchText"]').val();
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
	});
	
	function clearDate() {
		$('input[name="date_activity"]').val('');
		$('#calendar > input[name="date"]').val('');
		$('#calendar > input[name="fDate"]').val('');
		$('input[name="staff"]').removeData();
	}
</script>