{include="store/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>Blog categories [add-blog]<a href="#" class="btn addBtn" onclick="store.addBlogCategory(); return false;">{lang=addCategory}</a>[/add-blog]</div>
	<div class="mngSearch">
		<input type="text" placeholder="Categories search" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{blog-categories}
	</div>
	{include="doload.tpl"}
</section>
<script>
	$(function() {
		$('.userList > ol').sortable({
			onDrop: function ($item, container, _super, event) {
				$item.removeClass(container.group.options.draggedClass).removeAttr("style")
				$("body").removeClass(container.group.options.bodyClass)
				store.sortBlogCategory();
			}
		});
	})
</script>