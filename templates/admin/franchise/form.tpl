<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{title}
	</div>
	<form class="uForm" method="post" id="franchise" action="/franchise/send">
		<div class="iGroup com plusNew">
			<label>Owner franchise</label>
			<div name="owner_id" json='/users/all' res="list" search="ajax10" class="sfWrap"></div>
			<span class="fa fa-plus" onclick="user.newUsr(1)" style="line-height: 38px;"></span>
		</div>
		<div class="iGroup">
			<label>Name</label>
			<input type="text" name="name" value="{name}" placeholder="Name" maxlength="100" required>
		</div>
		<div class="iGroup plusNew flex">
			<label>Phone</label>
			<div class="phones">
				{phones}
				<span class="fa fa-plus" onclick="Phones.add(this.parentNode);"></span>
			</div>
		</div>
		<div class="iGroup">
			<label>Address</label>
			<textarea name="address" placeholder="Address" maxlength="255" required>{address}</textarea>
		</div>
		<div class="iGroup">
			<label>Email</label>
			<input type="email" name="email" value="{email}" maxlength="50" placeholder="Email" required>
		</div>
		<div class="iGroup">
			<label>Website</label>
			<input type="url" name="website" value="{website}" maxlength="255" placeholder="Website">
		</div>
		<div class="iGroup">
			<label>IP</label>
			<input type="text" name="ip" value="{ip}" maxlength="50" placeholder="IP">
		</div>
		<div class="iGroup imgGroup">
			<label>Image</label>
			[image]
				<figure>
					<img src="/uploads/images/franchises/{id}/thumb_{image}" onclick="showPhoto(this.src);">
					<span class="fa fa-times" onclick="$(this).parent().remove();"></span>
				</figure>
			[/image]
			<div class="dragndrop" name="image">
				<span class="fa fa-download"></span>
				Click or drag and drop file here
			</div>
		</div>
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {send}</button>
		</div>
		<input type="hidden" name="id" value="{id}">
	</form>
</section>
<script>
$('div[json]').json_list();
if({owner-id} > 0){
	$('[name="owner_id"]').data('value', {owner-id});
	$('[name="owner_id"] input').val('{owner-name}');
}
$('#franchise').ajaxSubmit({
	callback: function(r){
		r = JSON.parse(r);
		if (r.err == 'err_email') {
			alr.show({
				class: 'alrDanger',
				content: lang[165],
				delay: 2
			});
		} else {
			if(r.id){
				alr.show({
					class: 'alrSuccess',
					content: location.pathname.indexOf('add') != -1 ? 'New franchise successfully created' : 'Franchise successfully updated',
					delay: 3
				});
				Page.get('/franchise/view/'+r.id);
			}
		}
	},
	check: function(){
		var t = $(this);
		//if(!t.find('div[name=inventory]').data().value.length && !t.find('div[name=service]').data().value.length){
		//	alr.show({
		//		class: 'alrDanger',
		//		content: 'You must select an inventory or service',
		//		delay: 2
		//	});
		//	return false;
		//}
	}
});
	$('.dragndrop').upload({
		check: function(e){
			var self = this;
			if(!e.error){
				var img = new Image();
				img.src = URL.createObjectURL(e.file);
				var thumb = $('.dragndrop').prev();
				if(thumb[0].tagName == 'FIGURE') thumb.remove();
				$(this).before($('<figure/>', {
					html: $('<img/>', {
						src: URL.createObjectURL(e.file),
						onclick: 'showPhoto(this.src)'
					})
				}).append($('<span/>', {
					class: 'fa fa-times'
				}).click(function(){
					self.files = {};
					$(this).parent().remove();
				})));
			} else if(e.error == 'type'){
				alr.show({
				   class: 'alrDanger',
				   content: 'You can load only jpeg, jpg, png, gif images',
				   delay: 2
				});
			} else if(e.error == 'size'){
				alr.show({
				   class: 'alrDanger',
				   content: 'Upload size for images is more than allowable',
				   delay: 2
				});
			}
		}
	});
</script>