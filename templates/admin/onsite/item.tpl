<tr id="onsite_{id}" class="sUser">
	<td>{id}</td>
	<td style="line-height: 45px;">
		<a href="/users/view/{staff-id}" target="_blank" class="nc">
			[image-staff]<img src="/uploads/images/users/{staff-id}/thumb_{image-staff}" class="miniRound">[not-image-staff]<span class="fa fa-user-secret miniRound"></span>[/image-staff]
			{staff-name}
		</a>
	</td>
	<td style="line-height: 45px;">
		<a href="/users/view/{customer-id}" target="_blank" class="nc">
			[image-customer]<img src="/uploads/images/users/{customer-id}/thumb_{image-customer}" class="miniRound">[not-image-customer]<span class="fa fa-user-secret miniRound"></span>[/image-customer]
			{customer-name}
		</a>
	</td>
	<td>{service-name}</td>
	<td><b>${service-price}</b></td>
	<td>{service-date}</td>
	<td>{create-date}</td>
	<td>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li><a href="/onsite/view/{id}" onclick="Page.get(this.href); return false;"><span class="fa fa-eye"></span> View this onsite</a></li>
			<li><a href="/invoices/view/{invoice-id}" onclick="Page.get(this.href); return false;"><span class="fa fa-eye"></span> View Invoice</a></li>
		</ul>
	</div>
	</td>
</tr>