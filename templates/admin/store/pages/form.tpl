{include="store/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{title}
		[edit]<a href="https://yoursite.com/{uri}" target="_blank">view on the site</a>[/edit]
		[confirm]
			<a href="javascript:store.serviceDecline({id}, 'pages');" class="btn addBtn st_rejected" style="margin-left: 12px;">Decline</a>
			<a href="javascript:store.pageApprove({id});" class="btn addBtn">Approve</a>
		[/confirm]
	</div>
	<form class="uForm" method="post" onsubmit="store.sendPage(this, event, {id});">
		<div class="iGroup fw">
			<label>{lang=Title}</label>
			<input type="text" name="name" value="{name}" oninput="titleChange(this.value)">
		</div>
		<div class="iGroup imgGroup wc">
			<label>Poster</label>
			[image]
				<figure>
					<img src="/uploads/images/pages/{id}/thumb_{image}" onclick="showPhoto(this.src);">
					<span class="fa fa-times" onclick="$(this).parent().remove();"></span>
				</figure>
			[/image]
			<div class="dragndrop" data-type="image">
				<span class="fa fa-download"></span>
				Click or drag and drop file here
			</div>
		</div>
		<div class="iGroup fw">
			<label>{lang=Pathname}</label>
			<input type="text" name="pathname" value="{pathname}" oninput="this.value = this.value.replace(/https?:\/\/yoursite.com\//i, '')">
		</div>
		<div class="iGroup fw">
			<label>{lang=Text}</label>
			<textarea name="content" id="editor">{content}</textarea>
		</div>
		<div class="iGroup">
			<label>Main page</label>
			<input type="checkbox" name="main_page" [main_page]checked[/main_page]>
		</div>
		<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=SEO}</div>
		<div class="iGroup">
			<label>{lang=Title}</label>
			<input type="text" name="title" value="{stitle}" maxlength="60">
		</div>
		<div class="iGroup">
			<label>{lang=Description}</label>
			<textarea name="description" maxlength="255">{description}</textarea>
		</div>
		<div class="iGroup">
			<label>{lang=Keywords}</label>
			<input type="text" name="keywords" value="{keywords}">
		</div>
		<div class="iGroup">
			<label>Canonical pathname</label>
			<input type="text" name="canonical" value="{canonical}" oninput="this.value = this.value.replace(/https?:\/\/yoursite.com\//i, '')">
		</div>
		<div class="sGroup">
			<div class="flLeft load hdn"><span class="fa fa-spin fa-circle-o-notch"></span> <span id="sended"></span> of <span id="all"></span> was sending</div>
			<button class="btn btnPurchase" type="button" onclick="store.moveToService({id})">Move to service</button>
			<button class="btn btnSubmit" type="submit" value="preview"><span class="fa fa-eye"></span> preview</button>
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {send}</button>
		</div>
	</form>
</section>
<script>
	var pagePathname = '{pathname}';
	var titleChange = function(a){
		$('input[name="pathname"]').val(a ? a.trim().toLocaleLowerCase().replace(/[\s,&]/gi, '-') : '');
		$('input[name="title"]').val(a || '');
	};
	$(function() {
		$('#editor').fEditor();
	});
	$('.dragndrop').upload({
		check: function(e){
			var self = this;
			if(!e.error){
				var img = new Image();
				img.src = URL.createObjectURL(e.file);
				var thumb = $(this).prev();
				if(thumb[0].tagName == 'FIGURE') thumb.remove();
				$(this).before($('<figure/>', {
					html: $('<img/>', {
						src: URL.createObjectURL(e.file)
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
