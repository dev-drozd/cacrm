<aside class="sideNvg">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>Manage</div>
	<ul class="mng">
		<li>
			<a href="/issues/view/{issue-id}" onclick="Page.get(this.href); return false;">
				<span class="fa fa-chevron-left"></span>Back to this job
			</a>
		</li>
	</ul>
</aside>
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{header}
		[author]
		<a href="https://yoursite.com/archive/{id}" target="_blank">view on the site</a>
		[/author]
		[new]
		<a href="javascript:this_approve({id});" class="btn addBtn" target="_blank">Approve</a>
		[/new]
	</div>
	<form class="uForm" method="post" onsubmit="this_submit(event)">
		[author]
		<div class="iGroup">
			<label>Author:</label>
			<a href="/users/view/{author-id}" onclick="Page.get(this.href); return false;">{author-name}</a>
		</div>
		[/author]
		<div class="iGroup">
			<label>Device:</label>
			<input type="text" name="device" value="{device}">
		</div>
		<div class="iGroup">
			<label>Issue:</label>
			<textarea name="description">{descr}</textarea>
		</div>
		[services]
		<div class="iGroup">
			<label>Services and inventory:</label>
			{services}
		</div>
		[/services]
		<div class="iGroup fw">
			<label>Content</label>
			<textarea name="content" id="editor">{content}</textarea>
		</div>
		<div class="iGroup">
			<label>Title:</label>
			<input type="text" name="title" value="{title}">
		</div>
		<div class="iGroup">
			<label>Keywords:</label>
			<input type="text" name="keywords" value="{keywords}">
		</div>
		<div class="iGroup imgGroup wc">
			<label>Image</label>
			[image]
				<figure>
					<img src="/uploads/images/work_archive/{id}/thumb_{image}" onclick="showPhoto(this.src);">
					<span class="fa fa-times" onclick="$(this).parent().remove();"></span>
				</figure>
			[/image]
			<div class="dragndrop">
				<span class="fa fa-download"></span>
				Click or drag and drop file here
			</div>
		</div>
		[edit]
		<div class="sGroup dClear">
			<button class="btn btnSubmit" type="submit">
				<span class="fa fa-save"></span> Complete
			</button>
		</div>
		[/edit]
		<input type="hidden" name="id" value="{id}">
		<input type="hidden" name="issue_id" value="{issue-id}">
	</form>
</section>
<script>
function this_approve(a){
	$.post('/issues/approve_publish', {id: a}, function(){
		Page.get("/issues/archive");
	});
}
function this_submit(a){
	a.preventDefault();
	var f = new FormData(a.target),
		u = Object.values($('.dragndrop')[0].files)[0];
		if(u) f.append("image", u);
		$.ajax({
		  url: "/issues/send_req_pub",
		  type: "POST",
		  data: f,
		  processData: false,
		  contentType: false
		}).done(function(r){
			if(r == 'OK') Page.get([author]"/issues/archive"[not-author]"/issues/view/{issue-id}"[/author]);
			else alr.show({
			   class: 'alrDanger',
			   content: r,
			   delay: 2
			});
		});
	return false;
}
$(function(){
	$('#editor').fEditor({
		plugins: 'resize,style,aligment,indent,insert,list,font,history'
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