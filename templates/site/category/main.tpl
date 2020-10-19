<div class="ctnr">

	<nav class="breadcrumbs">
		<a href="/" onclick="Page.get(this.href); return false;">Main</a> >
		[catname]<a href="/category" onclick="Page.get(this.href); return false;">[/catname]Store[catname]</a>[/catname]
		[catname] > <b>{category-name}</b>[/catname]
	</nav>
	
	<div class="page">
		<h1>{category-name}</h1>
		<div class="category flex">
			<div class="products-categories">
				<ul>
				{categories}
				</ul>
			</div>
			<div class="products flex">
				{inventory}
			</div>
		</div>
	</div>
</div>