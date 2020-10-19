{include="feedbacks/menu.tpl"}
<div class="pnl fw lPnl">
	<div class="pnlTitle">
		Feedbacks of jobs
		<div class="filters">
			<span class="hnt hntTop" data-title="Filter" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
			<div class="filterCtnr">
				<div class="fTitle">Filters</div>
				<div class="iGroup fw dGroup cl">
					<label class="l">Date <span class="fa fa-eraser" onclick="clearDate();"></span></label>
					<input type="text" onclick="$(this).next().show().parent().addClass('act');" class="cl" name="date_activity">
					<div id="calendar" data-multiple="true"></div>
				</div>
				<div class="iGroup fw" id="object">
					<label>Store</label>
					<input type="hidden" name="object">
					<ul class="hdn"></ul>
				</div>
				<div class="iGroup fw">
					<label>Feedback</label>
					<select name="type">
						<option value="0">All feedbacks</option>
						<option value="1">No feedback</option>
						<option value="2">With feedback</option>
					</select>
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
	</div>
	<div class="mngSearch">
		<input type="text" name="searchText" value="{query}" placeholder="Search..." onkeypress="if(event.keyCode == 13) Search2(this.value);">
		<button class="btn btnSearch" onclick="Search2($(this).prev().val());">Search <span class="total">Total: <span id="res_count">{res-count}</span><span></button>
	</div>
	<table class="responsive">
		<thead>
			<tr>
				<th>Issue</th>
				<th>Customer</th>
				<th>Phone</th>
				<th>Rating</th>
				<th>Comment</th>
				<th>Job date</th>
				<th>Feedback date</th>
				<th align="center">Action</th>
			</tr>
		</thead>
		<tbody class="userList">
			{feedbacks}
		</tbody>
	</table>
	{include="doload.tpl"}
</div>
<style>
.userList {
    display: contents;
    width: 100%;
}
.miniRound {
	float: none;
}
.nFb > td {
	background: #fffad0 !important;
}
.wFb > td {
	background: #c7ffc7 !important;
}
.pFb > td {
	background: #ffdada !important;
}
.act-btn {
	padding: 0 10px !important;
}
.act-btn:first-child, .act-btn:first-child:hover {
	color: #d04646;
}
</style>
<script>
	$(function() {
		$('#calendar').calendar(function() {
			$('input[name="date_activity"]').val($('#calendar > input[name="date"]').val() + ' / ' + $('#calendar > input[name="fDate"]').val());
			$('.dGroup > input + div').hide().parent().removeClass('act');
		});
		$.post('/invoices/objects', {all: 1,}, function (r) {
			if (r){
				if (r.list.length > 1) {
					var items = '', lId = 0, object = $_GET('object') || 0;
					$.each(r.list, function(i, v) {
						if(v.id == object){
							var obj = {};
							obj[v.id] = {name: v.name};
							$('input[name="object"]').data(obj);
						}
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
		if (location.search){
			var rating = $_GET('rating'),
				type = $_GET('type'),
				sDate = $_GET('date_start'),
				eDate = $_GET('date_finish'),
				o = {};
				s = {};
			$('select[name="rating"]').val(rating).trigger('change');
			$('select[name="type"]').val(type).trigger('change');
			$('#calendar > input[name="date"]').val(sDate);
			$('#calendar > input[name="fDate"]').val(eDate);
			$('input[name="date_activity"]').val($('#calendar > input[name="date"]').val() + ' / ' + $('#calendar > input[name="fDate"]').val());
		}
	});
</script>