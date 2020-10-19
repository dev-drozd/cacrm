{include="feedbacks/menu.tpl"}
<div class="pnl fw lPnl">
	<div class="pnlTitle">
		Feedbacks
		<div class="filters">
			<span class="hnt hntTop" data-title="Filter" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="fTitle">Filters</div>
				<div class="iGroup fw dGroup cl">
					<label class="l">Date <span class="fa fa-eraser" onclick="clearDate();"></span></label>
					<input type="text" onclick="$(this).next().show().parent().addClass('act');" class="cl" name="date_activity">
					<div id="calendar" data-multiple="true"></div>
				</div>
				<div class="iGroup fw" id="staff">
					<label>Staff</label>
					<input type="hidden" name="staff">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw" id="create">
					<label>Created Staff</label>
					<input type="hidden" name="create">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw">
					<label>Type</label>
					<select name="type">
						<option value="-1">All</option>
						<option value="0">Custom</option>
						<option value="1">SMS</option>
						<option value="2">Email</option>
						<option value="3">Tablet</option>
					</select>
				</div>
				<div class="iGroup fw" id="object">
					<label>Store</label>
					<input type="hidden" name="object">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw">
					<label>Rating</label>
					<select name="rating">
						<option value="0">All</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
					</select>
				</div>
				<div class="cashGroup cl">
					<button type="button" class="btn btnSubmit ac cl" onclick="Search2($('input[name=\'searchTex\']').val());">OK</button>
					<button type="button" class="btn btnSubmit dc cl" onclick="$(this).parents('.filterCtnr').hide();">Cancel</button>
				</div>
			</div>
		</div>
		<!-- <span class="hnt hntTop exportXls" data-title="Download XLS report" id="download"><span class="fa fa-download"></span></span> -->
	</div>
	<div class="mngSearch">
		<input type="text" name="searchText" value="{query}" placeholder="Search..." onkeypress="if(event.keyCode == 13) Search2(this.value);">
		<button class="btn btnSearch" onclick="Search2($(this).prev().val());">Search <span class="total">Total: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="tbl" style="width: 100%;">
		<div class="tHead">
			<div class="tr">
				<div class="th">Issue</div>
				<div class="th">Date</div>
				<div class="th">Staff</div>
				<div class="th">Customer</div>
				<div class="th">Phone</div>
				<div class="th">Rating</div>
				<div class="th">Comment</div>
			</div>
		</div>
		<div class="tBody userList">
			{feedbacks}
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
		
		$.post('/users/all', {staff: 1}, function (r) {
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
				
				$('#create > ul').html(items).sForm({
					action: '/users/all',
					data: {
						staff: 1,
						lId: lId,
						nIds: Object.keys($('input[name="create"]').data() || {}).join(','),
						query: $('#create > .sfWrap input').val() || ''
					},
					all: false,
					select: $('input[name="create"]').data(),
					s: true
				}, $('input[name="create"]'));
			}
		}, 'json');
		
		$.post('/invoices/objects', {all: 1,}, function (r) {
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
							all: 1,
							query: $('#object > .sfWrap input').val() || ''
						},
						all: (r.count <= 20) ? true : false,
						select: $('input[name="object"]').data(),
						s: true
					}, $('input[name="object"]'));
				}
			}
		}, 'json');
		
		if (location.search) {
			var rating = $_GET('rating'),
				sDate = $_GET('date_start'),
				eDate = $_GET('date_finish'),
				oName = ($_GET('oName') || '').replace(/%20/ig, ' '),
				sName = ($_GET('sname') || '').replace(/%20/ig, ' '),
				o = {};
				s = {};
				
			if ($_GET('object') != 0) o[$_GET('object')] = {name: oName};
			if ($_GET('staff') != 0) s[$_GET('staff')] = {name: sName};
			$('select[name="rating"]').val(rating).trigger('change');
			$('input[name="object"]').data(o);
			$('input[name="staff"]').data(s);
			$('#calendar > input[name="date"]').val(sDate);
			$('#calendar > input[name="fDate"]').val(eDate);
		}
	});
</script>