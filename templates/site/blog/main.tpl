<div class="ctnr">

	<nav class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
		<span typeof="v:Breadcrumb">
			<a property="v:title" rel="v:url" href="/" onclick="Page.get(this.href); return false;">Main</a> >
		</span>
		[catname]
		<span typeof="v:Breadcrumb">
			<a property="v:title" rel="v:url" href="/blog" onclick="Page.get(this.href); return false;">[/catname]Blog[catname]</a>
		</span>
		[/catname]
		[catname] > <span typeof="v:Breadcrumb">
			<b property="v:title">{catname}</b>
		</span>[/catname]
	</nav>
	
	<div class="page">
		<h1>[catname]{catname}[not-catname]Blog posts[/catname]</h1>
		<div class="blog flex">
			[categories]
			<div class="blog-content">
				{posts}
			</div>
			[/categories]
			<div class="blog-side">
				<div class="blog-widget">
				[categories]
					<div class="bw-title">Categories</div>
					<ul>
						{categories}
					</ul>
				[/categories]
				</div>
			</div>
		</div>
	</div>
</div>