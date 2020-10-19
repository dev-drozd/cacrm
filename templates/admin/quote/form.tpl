<section class="pnl fw">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{title}
	</div>
	<form class="uForm" method="post" onsubmit="quote.createApp(this, event, {id});">
		<input type="hidden" name="id" value="{id}">
		[customer]
		<div class="customer">
			Customer:  <a href="/users/view/{customer-id}"> {name} {lastname}</a>
		</div>
		<input type="hidden" name="customer_id" value="{customer-id}">
		[not-customer]
		<div class="iGroup">
			<label>Name</label>
			<input type="text" name="name" value="{name}">
		</div>
		<div class="iGroup">
			<label>Lastname</label>
			<input type="text" name="lastname" value="{lastname}">
		</div>
		<div class="iGroup">
			<label>Email</label>
			<input type="text" name="email" value="{email}">
		</div>
		<div class="iGroup">
			<label>Phone</label>
			<input type="text" name="phone" value="{phone}">
		</div>
		[/customer]
		<div class="iGroup">
			<label>Issue</label>
			<textarea type="text" name="issue">{issue}</textarea>
		</div>
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-check"></span> Create appointment</button>
		</div>
	</form>
</section>