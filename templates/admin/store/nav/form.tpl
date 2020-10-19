{include="store/menu-settings.tpl"}
<section class="mngContent">

	<div class="sTitle">
		<span class="fa fa-chevron-right"></span> New item
	</div>
	
	<form class="uForm" action="/store/nav/send" method="post" id="nav_form">
	
		<div class="iGroup">
			<label>Name</label>
			<input type="text" name="name" value="{name}" required>
		</div>
		
		<div class="iGroup">
			<label>Action type</label>
			<select name="act_type" onchange="onActType(this.value)" required>
				{types}
			</select>
		</div>
		
		<div class="iGroup actType[act-url] selected[/act-url]" id="block_url">
			<label>Url</label>
			<input type="text" name="url" value="{url}"[act-url] required[/act-url]>
		</div>
		
		<div class="iGroup actType[act-page] selected[/act-page]" id="block_page">
			<label>Page</label>
			<div name="page_id" json='/store/allPages' res="list" search="ajax0" class="sfWrap"[act-page] required[/act-page]></div>
		</div>
		
		<div class="iGroup actType[act-blog] selected[/act-blog]" id="block_blog">
			<label>Blog</label>
			<div name="blog_id" json='/store/allBlogs' res="list" search="ajax0" class="sfWrap"[act-blog] required[/act-blog]></div>
		</div>
		
		<div class="iGroup">
			<label>Parent</label>
			<div name="parent_id" json='/store/allNav' res="list" search="ajax0" class="sfWrap"></div>
		</div>
		
		<input type="hidden" name="id" value="{id}">
		
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit">	
				<span class="fa fa-save"></span> Submit
			</button>
		</div>
		
	</form>
</section>
<script>
[select]
$('div[name="{act-type}_id"]').data('value', '{nav-id}');
[/select]
$('div[name="parent_id"]').data('value', '{parent-id}');
function onActType(a){
	$('.actType.selected').removeClass('selected').find('select,input').removeAttr('required');
	$('#block_'+a).addClass('selected').find('select,input').attr('required', true);
}
$('div[json]').json_list();
$('#nav_form').ajaxSubmit({
	callback: function(r){
		if(r == 'OK') Page.get('/store/nav'+(
			$('div[name="parent_id"]').data('value') ? '/'+$('div[name="parent_id"]').data('value') : ''
		));
	}
});
</script>
<style>
.actType:not(.selected) {
	display: none;
}
</style>