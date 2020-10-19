<div class="pnl fw lPnl">
	<div class="pnlTitle">
		{lang=Activity}
		<div class="filters">
			<span class="fa fa-filter" onclick="$(this).next().toggle();"></span>
			<div class="filterCtnr">
				<div class="iGroup fw dGroup cl">
					<label class="cl">Date <span class="fa fa-eraser" onclick="clearDate();"></span></label>
					<input class="cl" type="text" onclick="$(this).next().show().parent().addClass('act');" name="date_activity">
					<div id="calendar" data-multiple="true"></div>
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
		<span class="fa fa-download exportXls" id="download"></span>
	</div>
	<div class="mngSearch">
		<input type="text" name="searchText" placeholder="{lang=timerSearch}" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="tbl">
		<div class="tHead">
			<div class="tr">
				<div class="th wp20">Staff</div>
				<div class="th w125">Date</div>
				<div class="th w125">Punch in</div>
				<div class="th w125">Break start</div>
				<div class="th w125">Break end</div>
				<div class="th w125">Punch out</div>
				<div class="th w125">Working time</div>
			</div>
		</div>
	</div>
	<div class="tbl">
		<div class="userList tBody">
			{timers}
		</div>
	</div>
	{include="doload.tpl"}
	<div class="totalTime">
		Total time: <span>{total_time}</span>
	</div>
</div>

<script>
	$(function() {
		$('#calendar').calendar(function() {
			$('input[name="date_activity"]').val($('#calendar > input[name="date"]').val() + ' / ' + $('#calendar > input[name="fDate"]').val());
			$('.dGroup > input + div').hide().parent().removeClass('act');
		});
		
		$.post('/invoices/objects', {}, function (r) {
			if (r){
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
			}
		}, 'json');
		
		if (location.search) {
			var search = location.search,
				sDate = $_GET('sDate') || '',
				eDate = $_GET('eDate') || '',
				oName = ($_GET('name') || '').replace(/%20/ig, ' '),
				o = {};
				
			if ($_GET('object')) o[$_GET('object')] = {name: oName}
			$('#calendar > input[name="date"]').val(sDate);
			$('#calendar > input[name="fDate"]').val(eDate);
			$('input[name="object"]').data(o);
			Search($('input[name="searchText"]').val());
		}
		
		$('#download').click(function() {
			location.href = location.origin + '/xls/timer?date_start=' + $('#calendar > input[name="date"]').val() + 
				'&date_finish=' + $('#calendar > input[name="fDate"]').val() + 
				'&event=' + $('select[name="searchSel"]').val() + 
				'&search=' + $('input[name="searchText"]').val() +
				'&user_time=1' +
				'&user=' + {uid};
		});
	});
	
	function clearDate() {
		$('#calendar > input[name="date"]').val('');
		$('#calendar > input[name="fDate"]').val('');
		$('input[name="date_activity"]').val('');
		$('.filterCtnr').hide();
	}
</script>