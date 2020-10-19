<style>
.userList {
    display: table-footer-group;
    width: auto;
}
</style>
<section class="mngContent ffullw">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{lang=Drops}
		<div class="filters">
			<span class="fa fa-filter" onclick="$(this).next().toggle();"></span>
			<div class="filterCtnr">
				<div class="fTitle">{lang=Filters}</div>
				<div class="iGroup fw dGroup cl">
					<label class="cl">{lang=Date}</label>
					<input class="cl" type="text" onclick="$(this).next().show().parent().addClass('act');" name="date">
					<div id="calendar" data-multiple="1"></div>
				</div>
				<div class="iGroup fw cl" id="object">
					<label>{lang=Store}</label>
					<input type="hidden" name="object">
					<ul class="hdn"></ul>
				</div>
				<div class="cashGroup">
					<button type="button" class="btn btnSubmit ac" onclick="Search($('input[name=\'search\']').val());">{lang=OK}</button>
					<button type="button" class="btn btnSubmit dc" onclick="$(this).parents('.filterCtnr').hide();">{lang=Cancel}</button>
				</div>
			</div>
		</div>
		<span class="hnt hntTop exportXls" data-title="{lang=Drops}" onclick="Page.get('/cash/drops/')"><span class="fa fa-arrow-circle-left"></span></span>
	</div>
	<div class="mngSearch">
		<input type="text" placeholder="Inventory search" onkeypress="if(event.keyCode == 13) Search(this.value);" oninput="checkBarcode(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">Search <span class="total">Total: <span id="res_count">{res_count}</span><span></button>
	</div>
	<table class="responsive">
		<thead>
			<tr>
				<th>{lang=DropDate}</th>
				<th>{lang=DropStaff}</th>
				<th>{lang=Store}</th>
				<th>{lang=ConfirmDate}</th>
				<th>{lang=ConfirmStaff}</th>
				<th>{lang=OutCash}</th>
			</tr>
		</thead>
		<tbody class="userList">
			{drops}
		</tbody>
	</table>
	{include="doload.tpl"}
</section>
<script>
	$(function(){
		$('#calendar').calendar(function() {
			$('input[name="date"]').val($('#calendar > input[name="date"]').val() + ' / ' + $('#calendar > input[name="fDate"]').val());
			$('.dGroup > input + div').hide().parent().removeClass('act');
		});
		
		if (location.search) {
			var search = location.search,
				sDate = $_GET('sDate'),
				eDate = $_GET('eDate'),
				oName = $_GET('name').replace(/%20/ig, ' '),
				o = {};
				
			if ($_GET('object') != 0) o[$_GET('object')] = {name: oName};
			$('#calendar > input[name="date"]').val(sDate);
			$('#calendar > input[name="fDate"]').val(eDate);
			$('input[name="object"]').data(o);
			Search($('input[name="searchText"]').val());
		}
		
		$.post('/objects/all', {}, function (r) {
			if (r){
				if (r.list.length > 1) {
					var items = '', lId = 0;
					$.each(r.list, function(i, v) {
						items += '<li data-value="' + v.id + '" data-tax="' + v.tax + '">' + v.name + '</li>';
						lId = v.id;
					});
					
					$('#object > ul').html(items).sForm({
						action: '/objects/all',
						data: {
							lId: lId,
							query: $('#object> .sfWrap input').val() || ''
						},
						all: (r.count <= 20) ? true : false,
						select: $('input[name="object"]').data(),
						s: true
					}, $('input[name="object"]'));
				}
			}
		}, 'json');
	});
</script>