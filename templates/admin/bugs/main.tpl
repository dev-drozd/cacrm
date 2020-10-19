{include="bugs/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
        <span class="fa fa-chevron-right"></span>{title}
        <a href="#" class="btn addBtn" onclick="bugs.add(); return false;">{lang=addBug}</a>
        <a href="#" class="btn addBtn imp" onclick="bugs.add(0, 1); return false;">{lang=Add} {lang=improvement}</a>
    </div>
	<div class="mngSearch">
		<input type="text" placeholder="{lang=bugSearch}" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList bug">
		{bugs}
	</div>
	{include="doload.tpl"}
</section>
<script>
	function delete_image(e){
		var drag = $('.dragndrop')[0], count = drag.count || 1;
		$(e.parentNode).remove();
		drag.delete.push($(e.parentNode).find('img').attr('src'));
		drag.count = count-1;
	}
</script>