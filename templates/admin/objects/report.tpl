{include="objects/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{lang=ObjectsReport}
		<span class="fa fa-download exportXls" id="download"></span>
	</div>
	<form class="uForm" method="post">
		<div class="iGroup sfGroup" id="object">
			<label>{lang=store}</label>
			<input type="hidden" name="object" />
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup">
			<label>{lang=Date}</label>
			<div class="iRight">
				<input type="text" name="date" onclick="$(this).next().show();" class="cl"/>
				<div id="calendar" class="hdn" data-multiple="1"></div>
			</div>
		</div>
		<div class="sGroup">
			<button class="btn btnSubmit" type="button" onclick="objects.report(this);"><span class="fa fa-save"></span> {lang=getReport}</button>
		</div>
	</form>
	<div id="report">
	</div>
</section>
<script>
	function showST(e) {
		if (e.value == 'status_issues') 
			$(e).parents('.iGroup').next().show();
		else 
			$(e).parents('.iGroup').next().hide();
	}
	$(function() {
		$('#calendar').calendar(function() {
			$('.iRight > input[name="date"]').val($('#calendar > input[name="date"]').val() + ' / ' + $('#calendar > input[name="fDate"]').val());
			$('.iRight > input + div').hide();
		});
		
		$('body').on('click', function(event) {
			if (!$(event.target).hasClass('cl'))
				$('.iRight > input + div').hide();
		});
		
		//objects.report(null, 1);
		
		if (location.search) {
			var search = location.search,
				sDate = $_GET('sDate'),
				eDate = $_GET('eDate'),
				oName = $_GET('name').replace(/%20/ig, ' '),
				o = {};
				
			if ($_GET('object') != 0) o[$_GET('object')] = {name: oName};
			$('#calendar > input[name="date"]').val(sDate);
			$('#calendar > input[name="fDate"]').val(eDate);
			$('.iRight > input[name="date"]').val(sDate + ' / ' + eDate);
			$('input[name="object"]').data(o);
			objects.report($('button.btnSubmit'));
		}
		
		$.post('/invoices/objects', {}, function (r) {
			if(r.list.length > 1) {
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
					all: false,
					select: $('input[name="object"]').data()
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
					alr.show({
						class: 'alrDanger',
						content: 'You have no access to stores or trying to enter not from the store',
						delay: 3
					});
					Page.get('/purchases');
				} 
		}, 'json');
		
		$(function() {
			$('#download').click(function() {
				location.href = location.origin + '/xls/object?date_start=' + $('#calendar > input[name="date"]').val() + 
					'&date_finish=' + $('#calendar > input[name="fDate"]').val() + 
					'&objects=' + Object.keys($('input[name="object"]').data()).join(',');
			});
		})
		
	});
</script>