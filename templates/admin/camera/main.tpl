{include="camera/menu.tpl"}
<div class="pnl fw lPnl">
	<div class="pnlTitle">
		{lang=Activity}
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
				[updated]
				[not-updated]
				<div class="iGroup">
					<label>{lang=Updated}</label>
					<input type="checkbox" name="updated">
				</div>
				[/updated]
				<div class="cashGroup cl">
					<button type="button" class="btn btnSubmit ac cl" onclick="Search($('input[name=\'searchTex\']').val());">{lang=OK}</button>
					<button type="button" class="btn btnSubmit dc cl" onclick="$(this).parents('.filterCtnr').hide();">{lang=Cancel}</button>
				</div>
			</div>
		</div>
		<span class="hnt hntTop exportXls" data-title="{lang=DownloadXLS}" id="download"><span class="fa fa-download"></span></span>
	</div>
	<div class="mngSearch selW">
		<select onchange="Search($(this).next().val());" name="searchSel">
			<option value="">{lang=EmptyFilter}</option>
			{events}
		</select>
		<input type="text" name="searchText" placeholder="{lang=activitySearch}" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">Search <span class="total">Total: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="tbl">
		<div class="tHead">
			<div class="tr">
				<div class="th wp20">{lang=Staff}</div>
				<div class="th">{lang=Event}</div>
				<div class="th">{lang=Status}</div>
				<div class="th">{lang=Store}</div>
				<div class="th wp20">{lang=Date}</div>
			</div>
		</div>
		<div class="tBody userList">
			{activity}
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
		
		$('#download').click(function() {
			location.href = location.origin + '/xls/camera?date_start=' + $('#calendar > input[name="date"]').val() + 
				'&date_finish=' + $('#calendar > input[name="fDate"]').val() + 
				'&event=' + $('select[name="searchSel"]').val() + 
				'&staff=' + Object.keys($('input[name="staff"]').data()).join(',') + 
				'&object=' + Object.keys($('input[name="object"]').data()).join(',') + 
				'&search' + $('input[name="searchText"]').val();
		});
		
		$.post('/invoices/objects', {all: 1}, function (r) {
			if (r){
			console.log(r);
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
							query: $('#object > .sfWrap input').val() || '',
							all: 1
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