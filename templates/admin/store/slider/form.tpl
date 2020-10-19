{include="store/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{title}</div>
	<form class="uForm" method="post" onsubmit="store.sendSlide(this, event, {id});">
		<div class="iGroup fw">
			<label>{lang=Title}</label>
			<input type="text" name="name" value="{name}">
		</div>
		<div class="iGroup fw">
			<label>{lang=Text}</label>
			<textarea name="content" id="editor">{content}</textarea>
		</div>
		<div class="iGroup">
			<label>{lang=LinkType}</label>
			<select name="link_type" onchange="linkType(this.value);">
				<option value="none">None</option>
				<option value="blog">Blog</option>
				<option value="category">Category</option>
				<option value="item">Item</option>
				<option value="custom">Custom</option>
			</select>
		</div>
		<div class="iGroup link_type link_blog" id="blog">
			<label>Blog</label>
			<input type="hidden" name="blog">
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup link_type link_category" id="category">
			<label>Category</label>
			<input type="hidden" name="category">
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup link_type link_item" id="item">
			<label>Item</label>
			<input type="hidden" name="item">
			<ul class="hdn"></ul>
		</div>
		<div class="iGroup link_type link_custom">
			<label>Custom</label>
			<input type="text" name="custom" value="{link-id}">
		</div>
		<div class="iGroup imgGroup wc">
			<label>Image</label>
			[image]
				<figure>
					<img src="/uploads/images/slider/{id}/thumb_{image}" onclick="showPhoto(this.src);">
					<span class="fa fa-times" onclick="$(this).parent().remove();"></span>
				</figure>
			[/image]
			<div class="dragndrop" data-type="image">
				<span class="fa fa-download"></span>
				Click or drag and drop file here
			</div>
		</div>
		<div class="iGroup">
			<label>{lang=imageAlign}</label>
			<select name="image_align">
				<option value="left">Left</option>
				<option value="right">Right</option>
				<option value="center">Center</option>
			</select>
		</div>
		<div class="iGroup imgGroup wc">
			<label>Background</label>
			[back]
				<figure>
					<img src="/uploads/images/slider/{id}/thumb_{back}" onclick="showPhoto(this.src);">
					<span class="fa fa-times" onclick="$(this).parent().remove();"></span>
				</figure>
			[/back]
			<div class="dragndrop" data-type="background">
				<span class="fa fa-download"></span>
				Click or drag and drop file here
			</div>
		</div>
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {send}</button>
		</div>
	</form>
</section>
<script>
	function linkType(v) {
		$('.link_type').hide();
		if ($('.link_' + v).length) $('.link_' + v).show();
	}
	$(function() {
		$('#editor').fEditor();
		$('select[name="link_type"]').val('{link-type}' || 'none').trigger('change');
		$('select[name="image_align"]').val('{align}' || 'left').trigger('change');
		
		[edit]
			[link-type]
				$('input[name="{link-type}"]').data({link-id} || {});
			[/link-type]
		[/edit]
		
		$.post('/store/allCategories', { nIds: Object.keys($('input[name="category"]').data()).join(',')}, function (r) {
			if (r){
				var items = '', lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#category > ul').html(items).sForm({
					action: '/store/allCategories',
					data: {
						lId: lId,
						query: $('#category > .sfWrap input').val()
					},
					all: false,
					select: $('input[name="category"]').data(),
					s: true
				}, $('input[name="category"]'));
			}
		}, 'json');
		
		$.post('/store/allBlogs', { nIds: Object.keys($('input[name="blog"]').data()).join(',')}, function (r) {
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
						query: $('#blog > .sfWrap input').val()
					},
					all: false,
					select: $('input[name="blog"]').data(),
					s: true
				}, $('input[name="blog"]'));
			}
		}, 'json');
		
		$.post('/inventory/all', {
			noCust: 1,
			store: 1,
			nIds: Object.keys($('input[name="item"]').data()).join(',')}, function (r) {
			if (r){
				var items = '',
				lId = 0;
				$.each(r.list, function(i, v) {
					items += '<li data-value="' + v.id + '" data-price="$' + parseFloat(v.price).toFixed(2) + '">' + v.name + '</li>';
					lId = v.id;
				});
				$('#item > ul').html(items).sForm({
					action: '/inventory/all',
					data: {
						lId: lId,
						nIds: Object.keys($('input[name="item"]').data() || {}).join(','),
						query: $('#item > .sfWrap input').val() || '',
						noCust: 1,
						store: 1
					},
					all: false,
					select: $('input[name="item"]').data(),
					s: true
				}, $('input[name="item"]'));
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
