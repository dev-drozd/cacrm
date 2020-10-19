<div class="dClear cash">
	<!-- <div class="pnl fw oCash" id="object">
		<div class="pnlTitle">{lang=SelectObject}</div>
		<input type="hidden" name="object">
		<ul class="hdn"></ul>
		<input type="hidden" name="credit">
	</div> -->

	<div class="pnl lPnl oCash" id="object">
		<div class="pnlTitle">{lang=SelectObject}</div>
		<input type="hidden" name="object">
		<ul class="hdn"></ul>
		<input type="hidden" name="credit">
	</div>
	
	<div class="pnl rPnl" id="cashBtn">
		<div class="pnlTitle">{lang=Cash}</div>
		<div class="oCash">
			<button disabled class="btn btnOCash" onclick="cash.openCash(5000, 'open');">Open cash</button>
		</div>
	</div>
	
	<div class="dClear"></div>
	
	<div class="pnl fw oCash" id="cashHistory">
		<div class="pnlTitle">
			{lang=Statistic}
			<div class="filters ap cl">
				<span class="hnt hntTop" data-title="{lang=Filters}" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
				<div class="filterCtnr cl">
					<div class="fTitle cl">{lang=Filters}</div>
					<div class="iGroup fw dGroup cl">
						<label>{lang=Date} <span class="fa fa-eraser cl" onclick="clearDate();"></span></label>
						<input class="cl" type="text" onclick="$(this).next().show().parent().addClass('act');" name="date_filter">
						<div id="calendar" data-multiple="1"></div>
					</div>
					<!--<div class="iGroup fw cl">
						<label>{lang=Status}</label>
						<select name="status">
							<option value="0">{lang=NotSelected}</option>
							<option value="accept">{lang=Accepted}</option>
							<option value="dicline">{lang=Diclined}</option>
						</select>
					</div>-->
					<div class="iGroup fw cl">
						<label>{lang=Action}</label>
						<select name="action">
							<option value="0">{lang=NotSelected}</option>
							<option value="open">{lang=open}</option>
							<option value="close">{lang=close}</option>
						</select>
					</div>
					<div class="iGroup fw cl" id="staff">
						<label>{lang=Staff}</label>
						<input name="staff" type="hidden">
						<ul class="hdn"></ul>
					</div>
					<div class="cashGroup cl">
						<button type="button" class="btn btnSubmit ac cl" onclick="cash.selectObject();">{lang=OK}</button>
						<button type="button" class="btn btnSubmit dc cl" onclick="$(this).parents('.filterCtnr').hide();">{lang=Cancel}</button>
					</div>
				</div>
			</div>
		</div>
		<table class="tbl responsive">
			<thead class="tHead">
				<tr class="tr">
					<th class="th w150">{lang=Store}</th>
					<th class="th">{lang=Type}</th>
					<th class="th wAmount">{lang=SystemAmount}</th>
					<th class="th wAmount">{lang=UserAmount}</th>
					<th class="th wAmount">{lang=DrawerAmount}</th>
<!-- 					<div class="th">System Amount(credit)</div>
					<div class="th">User Amount(credit)</div> -->
					[owner]<th class="th" id="drops">{lang=Drop}</th>[/owner]
					<th class="th">{lang=Action}</th>
					<th class="th">{lang=Date}</th>
					<th class="th wStaff">{lang=Staff}</th>
					<th class="th" style="width: 70px"></th>
				</tr>
			</thead>
			<tbody class="userList tBody">
				{history}
			</tbody>
		</table>
		<button class="btn btnLoad{more}" onclick="doload(this);">{lang=LoadMore}</button>
	</div>
</div>

<script>
	$(function() {
		$('#calendar').calendar(function() {
			$('input[name="date_filter"]').val($('#calendar > input[name="date"]').val() + ' / ' + $('#calendar > input[name="fDate"]').val());
			$('.dGroup > input + div').hide().parent().removeClass('act');
		});
		
		[object]
			$('input[name="object"]').data({
				{object_id}: {
					name: '{object}'
				}
			});
		[/object]
		
		$.post('/invoices/objects', {nIds: Object.keys($('input[name="object"]').data()).join(',')}, function (r) {
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
							nIds: Object.keys($('input[name="object"]').data() || {}).join(','),
							query: $('#object > .sfWrap input').val() || ''
						},
						all: (r.count <= 20) ? true : false,
						select: $('input[name="object"]').data(),
						s: true,
						link: 'objects/edit'
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
					$('#object').append($('<div/>', {
						class: 'storeOne red',
						html: lang[168]
					}));
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
		$('.calendar > input[name="date"]').val('');
		$('.calendar > input[name="fDate"]').val('');
		$('input[name="date_filter"]').val('');
		$('.filterCtnr').hide();
	}
</script>