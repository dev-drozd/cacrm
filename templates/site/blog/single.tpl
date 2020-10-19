<div class="ctnr">

	<nav class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
		<span typeof="v:Breadcrumb">
			<a property="v:title" rel="v:url"  href="/" onclick="Page.get(this.href); return false;">Main</a> >
		</span>
		<span typeof="v:Breadcrumb">
			<a property="v:title" rel="v:url"  href="/blog" onclick="Page.get(this.href); return false;">Blog</a> >
		</span>
		[catname]{catname} > [/catname]
		<span typeof="v:Breadcrumb">
			<b property="v:title">{name}</b>
		</span>
	</nav>
	
	<div class="page">
		[edit]
		<div align="right">
			<a align="right" href="https://erp.yoursite.com/store/blog/edit/{id}">Edit this post</a>
		</div>
		[/edit]
		<h1>{name}</h1>
		<div class="blog flex">
			<div class="blog-content">
				{content}
			</div>
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