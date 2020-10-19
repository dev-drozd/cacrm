<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>Franchise #{id}
	</div>
	<div class="userInfo">
		<div class="uTitle dClear">
			<figure>
				[image]<div><img src="/uploads/images/franchises/{id}/thumb_{image}" onclick="showPhoto(this.src);"><span class="fa fa-search-plus" onclick="showPhoto(this.previousSibling.src);"></span></div>[not-image]<span class="fa fa-user-secret"></span>[/image]
			</figure>
			<div class="uName">
				<div>
					<b>{name}</b>
					<br>
					<p><b>Owner: </b><a href="/users/view/{owner-id}" onclick="Page.get(this.href); return false;">{owner-name}</a></p>
					<br>
					<p><b>Phone: </b>{phones}</p>
					<p><b>Email: </b> <a href="mailto:{email}">{email}</a></p>
					<p><b>Website: </b> <a href="{website}" target="_blank">{website}</a></p>
					<p><b>Reg date: </b>{date}</p>
					<p><b>Address:</b> {address}</p>
				</div>
				<div class="address">
					
					
				</div>
			</div>
		</div>
	</div>
	<div class="newUserItems dClear">
		<div onclick="Page.get('/users/add/1?franchise_id={id}');">
			<span class="fa fa-user"></span>
			Add owner
			<span class="fa fa-plus"></span>
		</div>
		
		<div onclick="Page.get('/objects/add?franchise_id={id}');">
			<span class="fa fa-shopping-cart"></span>
			Add store
			<span class="fa fa-plus"></span>
		</div>
		
	</div>

</section>