<div class="ctnr">

	<div class="account item">
		
		<div class="breads">
			<a href="/" onclick="Page.get(this.href); return false;">Home</a> / 
			<a href="/category/{store-category-id}" onclick="Page.get(this.href); return false;">{store-category}</a>
		</div>
		
		<div class="dClear">
			<div class="pSide">
				<div class="itemPhotos">
					[img]
						<img src="/uploads/images/inventory/{id}/preview_{img}" onclick="showPhoto.show(this.src, 'iph');" id="iph">
					[not-img]
						<span class="fa fa-picture-o"></span>
					[/img]
				</div>
				[img]
				<div class="itemPhotosSet">
					<div class="itemGroup">
						<div class="igGroup">
							<div class="igGroupCtnr">
								{images}
							</div>
							<div class="igArrows">
								<span class="fa fa-chevron-left disable"></span>
								<span class="fa fa-chevron-right"></span>
							</div>
						</div>
					</div>
				</div>
				[/img]
			</div>
			<div class="iSide">
				<div class="iPrice">
					<span>${price}</span>
					<span class="fa fa-shopping-cart" onclick="cart.add({id}, '{name}', '{price}');"></span>
				</div>
				
				<div class="paTitle">{name}</div>
				
				<div class="itTitle">[stock]Technical characteristics[not-stock]Service include[/stock]</div>
				<ul class="det">
					[model]<li><b>Model:</b> {model}</li>[/model]
					[os_name]<li><b>OS:</b> {os_name} {ver_os}</li>[/os_name]
					{options}
				</ul>
				
				<div class="itTitle">Detail info</div>
				<div class="iText">
					{descr}
				</div>
			</div>
		</div>
	</div>	
</div>		
<script>
	$(function() {
		$('.igGroup').carousel(); 
	})
</script>