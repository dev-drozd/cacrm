<aside class="sideNvg">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>Add customer
	</div>
	<ul class="mng">
		<li class="active"><a href="#" onclick="return false;"><span class="fa fa-check" style="color: #97c144;"></span>Adding Customer</a></li>
		<li class="active arr"><a href="#" onclick="return false;"><span class="fa fa-times" style="color: #d0d0d4;"></span>Adding Appointment</a></li>
	</ul>
</aside>
<section class="mngContent tr">
	<div class="sTitle spBottom"><span class="fa fa-chevron-right"></span>Step 2</div>
	<div class="bWhite">
		<form class="uForm" method="post" onsubmit="user.addApp(this, event, {user_id});">
			<div class="iGroup usr">
				<label>Date</label>
				<div class="iRight">
					<input type="text" name="sdate" class="cl" onclick="$(this).next().show();">
					<div id="calendar" class="hdn cl"></div>
				</div>
			</div>
			<div class="iGroup">
				<label>Time</label>
				<input type="time" name="time">
			</div>
			<div class="iGroup sfGroup" id="object">
				<label>Store</label>
				<input type="hidden" name="object" />
				<ul class="hdn"></ul>
			</div>
			<div class="sGroup">
				<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> Add</button>
			</div>
		</form>
	</div>
</section>
<script defer>
$(function() {
	$('#calendar').calendar(function() {
		var birth_date = $('#calendar > input[name="date"]').val().split('-');
		$('input[name="sdate"]').val($('#calendar > input[name="date"]').val());
		$('.iRight > input + div').hide();
	});
	
	$('body').on('click', function(event) {
		if (!$(event.target).hasClass('cl'))
			$('.iRight > input + div').hide();
	});
	
	$.post('/invoices/objects', {all: 1}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});

				$('#object > ul').html(items).sForm({
					action: '/invoices/objects',
					data: {
						lId: lId,
						query: $('#object > .sfWrap input').val() || '',
						all: 1
					},
					all: false,
					select: $('input[name="object"]').data(),
					s: true,
					link: 'objects/edit'
				}, $('input[name="object"]'));
			}
		}, 'json');
});
</script>