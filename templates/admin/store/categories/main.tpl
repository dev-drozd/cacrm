{include="store/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>{lang=allCategories} <a href="#" class="btn addBtn" onclick="store.addCategory(); return false;">{lang=addCategory}</a></div>
	<div class="mngSearch">
		<input type="text" placeholder="Categories search" onkeypress="if(event.keyCode == 13) Search(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{invCategories}
	</div>
	{include="doload.tpl"}
</section>
<script>
	$(function() {
		$('.userList > ol').sortable({
			onDrop: function ($item, container, _super, event) {
				$item.removeClass(container.group.options.draggedClass).removeAttr("style")
				$("body").removeClass(container.group.options.bodyClass)
				store.sortCategory();
			}
		});
	})
</script>