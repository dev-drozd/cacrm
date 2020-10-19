<div class="sUser" id="issue_{id}">
	<a href="/issues/edit/{id}" onclick="Page.get(this.href); return false;" class="uInfo">
		<div class="uInfo">
			#{id} {name}
			<p>{descr}</p>
		</div>
	</a>
	<div class="pInfo">
		<span>{customer}</span>
	</div>
	<div class="pInfo">
		<span>{inventory}</span>
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