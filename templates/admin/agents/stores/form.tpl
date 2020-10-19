{include="agents/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{title}: {name}</div>
	<form class="uForm" method="post" onsubmit="agents.addStore(this, event, {id});">
		<div class="iGroup">
			<label>Name</label>
			<input type="text" name="name" value="{name}">
		</div>
		<div class="iGroup">
			<label>Address</label>
			<input type="text" name="address" value="{address}">
		</div>
		<div class="iGroup">
			<label>Phone</label>
			<div class="phoneZone">
				<div class="hPhone">
					<div>Country code</div>
					<div>Area code</div>
					<div>7-dt number</div>
					<div>Extension</div>
					<div>SMS</div>
				</div>
				[edit]
                {phone}
				[not-edit]
					<div class="sPhone">
						<span class="fa fa-times rd hide" onclick=""></span>
						<select name="phoneCode">
							<option value="+1" selected>+1</option>
							<option value="+3">+3</option>
						</select>
						<span class="wr">(</span>
						<input type="number" name="code" onkeyup="phones.next(this, 3);" value="" max="999">
						<span class="wr">)</span>
						<input type="number" name="part1" onkeyup="phones.next(this, 7);" value="">
						<input type="number" name="part2" value="'.$n[3].'">
						<input type="checkbox" name="sms" checked onchange="phones.onePhone(this);">
					</div>
				[/edit]
                <span class="fa fa-plus plusNewPhone nPhone" onclick="phones.newPhone();"></span>
            </div>
		</div>
		<div class="iGroup">
			<label>Description</label>
			<textarea name="desc">{descr}</textarea>
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
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {send}</button>
		</div>
	</form>
</section>