{include="objects/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{title}: {name}</div>
	<form class="uForm" method="post" onsubmit="objects.add(this, event, {id});">
		<div class="tabs">
			<div class="tab" id="main_info" data-title="Main info">
				<div class="iGroup">
					<label>{lang=Name}</label>
					<input type="text" name="name" value="{name}">
				</div>
				<div class="iGroup">
					<label>{lang=IPAddress}</label>
					<input type="text" name="ip" value="{ip}">
				</div>
				<div class="iGroup">
					<label>Postal code:</label>
					<input type="text" name="zipcode" value="{zipcode}">
				</div>
				<div class="iGroup">
					<label>{lang=Address}</label>
					<input type="text" name="address" value="{address}">
				</div>
				<div class="iGroup">
					<label>{lang=Phone}</label>
					<input type="text" name="phone" value="{phone}">
				</div>
				<div class="iGroup">
					<label>{lang=Email}</label>
					<input type="email" name="email" value="{email}">
				</div>
				<div class="iGroup">
					<label>{lang=Desc}</label>
					<textarea name="desc">{descr}</textarea>
				</div>
				<div class="iGroup">
					<label>{lang=MapLocation}</label>
					<input type="text" name="map" value="{map}">
				</div>
				<div class="iGroup">
					<label>{lang=timezone}</label>
					<select name="timezone">
						{timezones}
					</select>
				</div>
				<div class="iGroup">
					<label>Work time</label>
					<div class="timeObject">
						<div>
							<input type="time" name="work_time_start" value="{work-time-start}">
						</div>
						<div>
							<input type="time" name="work_time_end" value="{work-time-end}">
						</div>
					</div>
				</div>
				<div class="iGroup">
					<label>{lang=Punch_out}</label>
					<input type="time" name="punch_out" value="{punch-out}">
				</div>
				<div class="iGroup">
					<label>{lang=Tax}, %</label>
					<input type="number" name="tax" value="{tax}" step="0.001">
				</div>
				<div class="iGroup">
					<label>{lang=SalaryTax}, %</label>
					<input type="number" name="salary_tax" value="{salary-tax}" step="0.001">
				</div>
				<div class="iGroup">
					<label>{lang=StorePoints}</label>
					<input type="number" name="points" value="{points}" step="0.001">
				</div>
				<div class="iGroup">
					<label>{lang=PointsEqual}</label>
					<input type="number" name="points_equal" value="{points-equal}" step="0.001">
				</div>
				<div class="iGroup">
					<label>{lang=OnsitePay}</label>
					<input type="number" name="onsite_payment" value="{onsite-payment}" step="0.001">
				</div>
				<div class="iGroup">
					<label>{lang=WeekHours}</label>
					<input type="number" name="week_hours" value="{week-hours}" step="0.001">
				</div>
				<div class="iGroup">
					<label>{lang=Rent}</label>
					<input type="checkbox" name="rent" onchange="$('#rent').toggle();"{rent}>
				</div>
				<div id="rent"[rent] style="display: block;"[/rent]>
					<div class="iGroup">
						<label>{lang=Price}</label>
						<input type="number" name="rent_cost" value="{rent-cost}" step="0.001">
					</div>
					<!-- <div class="iGroup">
						<label>{lang=Total}</label>
						<div id="rentTotal"></div>
					</div> -->
				</div>
				[edit]
				<div class="iGroup">
					<label>{lang=selManager}</label>
					<input type="hidden" name="managers">
					<ul class="uiGroup" id="managers"></ul>
					<button class="btn btnSubmit" onclick="objects.sel({id}, 3, '{lang=selManager}', 'managers'); return false;">{lang=select}</button>
				</div>
				<div class="iGroup">
					<label>{lang=selStaff}</label>
					<input type="hidden" name="staff">
					<ul class="uiGroup" id="staff"></ul>
					<button class="btn btnSubmit" onclick="objects.sel({id}, 4, '{lang=selStaff}', 'staff'); return false;">{lang=select}</button>
				</div>
				[/edit]
				<div class="iGroup imgGroup">
					<label>Photo</label>
					[ava]<figure><img src="/uploads/images/stores/{id}/thumb_{ava}" onclick="showPhoto(this.src);"><span class="fa fa-times" onclick="$(this).parent().remove();"></span></figure>[/ava]
					<div class="dragndrop">
						<span class="fa fa-download"></span>
						Click or drag and drop file here
					</div>
				</div>
			</div>
			<div class="tab" id="expanses" data-title="Expanses">
				{options}
				<div class="iGroup addOpt">
					<button class="btn btnSubmit ao" type="button" onclick="objects.addExpanses(this); return false;"><span class="fa fa-plus"></span> Add option</button>
				</div>
			</div>
			<div class="tab" id="ebay" data-title="{lang=APIEbayKeys}">
				<div class="iGroup">
					<label>{lang=devID}</label>
					<input type="text" name="ebay_devID" value="{ebay-devID}">
				</div>
				<div class="iGroup">
					<label>{lang=appID}</label>
					<input type="text" name="ebay_appID" value="{ebay-appID}">
				</div>
				<div class="iGroup">
					<label>{lang=certID}</label>
					<input type="text" name="ebay_certID" value="{ebay-certID}">
				</div>
				<div class="iGroup">
					<label>{lang=Token}</label>
					<textarea type="text" name="ebay_token">{ebay-token}</textarea>
				</div>
			</div>
			<div class="tab" id="craglist" data-title="Craigslist">
				<div class="iGroup">
					<label>Email:</label>
					<input type="text" name="craigslist_email" value="{craigslist-email}">
				</div>
				<div class="iGroup">
					<label>Password:</label>
					<input type="text" name="craigslist_password" value="{craigslist-password}">
				</div>
			</div>
			<div class="tab" id="purchase_price" data-title="Purchase price">
				{purchase-price}
				 <div class="iGroup addOpt">
					<button class="btn btnSubmit ao" onclick="objects.addFormula(this); return false;"><span class="fa fa-plus"></span> Add Option</button>
				</div>
			</div>
			<div class="tab" id="social" data-title="Social">
				<div class="iGroup">
					<label>Twitter:</label>
					<input type="text" name="twitter" value="{twitter}">
				</div>
				<div class="iGroup">
					<label>Facebook:</label>
					<input type="text" name="facebook" value="{facebook}">
				</div>
				<div class="iGroup">
					<label>Google plus:</label>
					<input type="text" name="google_plus" value="{google-plus}">
				</div>
				<div class="iGroup">
					<label>YouTube:</label>
					<input type="text" name="youtube" value="{youtube}">
				</div>
			</div>
		</div>
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {send}</button>
		</div>
	</form>
</section>
<script>
$(function() {
	objects.rent();
	$('input[name="managers"]').data({managers});
	$('input[name="staff"]').data({staff});
	userAppend($('#managers'), {managers});
	userAppend($('#staff'), {staff});
	$('.dragndrop').upload({
		check: function(e){
			var self = this;
			if(!e.error){
				var img = new Image();
				img.src = URL.createObjectURL(e.file);
				var thumb = $('.dragndrop').prev();
				if(thumb[0].tagName == 'FIGURE') thumb.remove();
				$(this).before($('<figure/>', {
					html: $('<img/>', {
						src: URL.createObjectURL(e.file)
					})
				}).append($('<span/>', {
					class: 'fa fa-times'
				}).click(function(){
					self.files = {};
					$(this).parent().remove();
				})));
			} else if(e.error == 'type'){
				alr.show({
				   class: 'alrDanger',
				   content: 'You can load only jpeg, jpg, png, gif images',
				   delay: 2
				});
			} else if(e.error == 'size'){
				alr.show({
				   class: 'alrDanger',
				   content: 'Upload size for images is more than allowable',
				   delay: 2
				});
			}
		}
	});
});
</script>

