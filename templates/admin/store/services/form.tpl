{include="store/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{title}
		[edit]<a href="https://yoursite.com/{uri}" target="_blank">view on the site</a>[/edit]
		[can-decline]<a href="javascript:store.serviceDecline({id}, 'services');" class="btn addBtn st_rejected" target="_blank" style="margin-left: 12px;">Decline</a>[/can-decline]
		[confirm][not-confirm]<a href="javascript:store.serviceApprove({id});" class="btn addBtn" target="_blank">Approve</a>[/confirm]
	</div>
	<form class="uForm" method="post" onsubmit="store.sendService(this, event, {id});">
		<div class="iGroup sfGroup fw" id="category">
			<label>Category</label>
			<input type="hidden" name="category" />
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup fw">
			<label>{lang=Title}</label>
			<input type="text" name="name" value="{name}" oninput="titleChange(this.value)" style="height: 48px;">
		</div>
		<div class="iGroup fw">
			<label>{lang=Pathname}</label>
			<input type="text" name="pathname" value="{pathname}" oninput="this.value = this.value.replace(/https?:\/\/yoursite.com\//i, '')">
		</div>
		<div class="iGroup fw">
			<label>Content</label>
			<textarea name="content" id="editor">{content}</textarea>
		</div>
		<div class="sTitle"><span class="fa fa-chevron-right"></span>SEO</div>
		<div class="iGroup">
			<label>Title</label>
			<input type="text" name="title" value="{title}">
		</div>
		<div class="iGroup">
			<label>Description</label>
			<textarea name="description">{description}</textarea>
		</div>
		<div class="iGroup">
			<label>Keywords</label>
			<input type="text" name="keywords" value="{keywords}">
		</div>
		<div class="iGroup">
			<label>Canonical pathname</label>
			<input type="text" name="canonical" value="{canonical}" oninput="this.value = this.value.replace(/https?:\/\/yoursite.com\//i, '')">
		</div>
		<div class="iGroup fw curGroup">
			<label>Price</label>
			<div class="cur">
				<select name="currency" style="display: none;">
					{currency}
				</select>
			</div>
			<input type="number" name="price" step="0.001" min="0" value="{price}" style="height: 48px;">
		</div>
<!-- 		<div class="iGroup fw iIcon">
			<label>Icon</label>
			<input type="text" name="icon" value="{icon}" onclick="store.openServiceIcon();">
			<span class="[icon]fa fa-{icon}[/icon]"></span>
		</div> -->
		<div class="iGroup imgGroup wc">
			<label>Image</label>
			[image]
				<figure>
					<img src="/uploads/images/services/{id}/thumb_{image}" onclick="showPhoto(this.src);">
					<span class="fa fa-times" onclick="$(this).parent().remove();"></span>
				</figure>
			[/image]
			<div class="dragndrop" data-type="image">
				<span class="fa fa-download"></span>
				Click or drag and drop file here
			</div>
		</div>
		<div class="iGroup sfGroup fw" id="blog">
			<label>Blog</label>
			<input type="hidden" name="blog" />
			<ul class="hdn"></ul>
		</div>
		<div class="sGroup">
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
		
		$('input[name="category"]').data({category-id});
		$.post('/store/allServiceCategories', {nIds: Object.keys($('input[name="category"]').data()).join(',')}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});

				$('#category > ul').html(items).sForm({
					action: '/store/allServiceCategories',
					data: {
						lId: lId,
						query: $('#category > .sfWrap input').val() || ''
					},
					all: false,
					select: $('input[name="category"]').data(),
					s: true
				}, $('input[name="category"]'));
			}
		}, 'json');
		
		$('input[name="blog"]').data({blog-id});
		$.post('/store/allBlogs', {nIds: Object.keys($('input[name="blog"]').data()).join(',')}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});

				$('#blog > ul').html(items).sForm({
					action: '/store/allBlogs',
					data: {
						lId: lId,
						query: $('#blog > .sfWrap input').val() || ''
					},
					all: false,
					select: $('input[name="blog"]').data(),
					s: true
				}, $('input[name="blog"]'));
			}
		}, 'json');
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
	});
</script>
