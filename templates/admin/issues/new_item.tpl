<div class="sUser" id="issue_{id}">
	<a href="/issues/view/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			#{id}
			<p>{descr}</p>
		</div>
	</a>
	<div class="pInfo">
		<span><a href="/users/view/{customer-id}" onclick="Page.get(this.href); return false;">{customer-name} {customer-lastname}</a></span>
	</div>
	<div class="pInfo">
		<span><a href="/inventory/view/{inventory-id}" onclick="Page.get(this.href); return false;">{inventory}</a></span>
		<p><font color="grey">{date}</font></p>
	</div>
	<div class="uMore">
		<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
		<ul>
			<li><a href="/issues/view/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-eye"></span> {lang=viewIssue}</a></li>		
			<li><a href="/issues/edit/{id}" onclick="Page.get(this.href); return false;" ><span class="fa fa-pencil"></span> {lang=editIssue}</a></li>
			<li><a href="javascript:issues.del({id})"><span class="fa fa-times"></span> {lang=delIssue}</a></li>
		</ul>
	</div>
</div>