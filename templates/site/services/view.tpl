<div class="ctnr">

	<nav class="breadcrumbs">
		<a href="/" onclick="Page.get(this.href); return false;">Main</a> >
		<a href="/services" onclick="Page.get(this.href); return false;">Services</a> >
		<b>{header}</b>
	</nav>
	
	<div class="page">
		[edit]
		<div align="right">
			<a align="right" href="https://crm.yoursite.com/store/services/edit/{id}">Edit this service</a>
		</div>
		[/edit]
		<h1>{header}</h1>
		<div class="blog">
			<div class="blog-content">
				[image]
				<img src="/uploads/images/services/{id}/{image}" alt="{header}" align="left" hspace="25">
				[/image]
				{content}
			</div>
		</div>
	</div>
</div>