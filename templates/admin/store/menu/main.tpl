{include="store/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>Menu <!--[add]<a href="/store/slider/add" class="btn addBtn" onclick="Page.get(this.href); return false;">{lang=addSlide}</a>[/add]--></div>
	<div class="mngSearch">
		<input type="text" placeholder="{lang=slideSearch}" onkeypress="if(event.keyCode == 13) Search(this.value);" oninput="checkBarcode(this.value);">
		<button class="btn btnSearch" onclick="Search($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="menuList">
        <div class="item dragel" id="mitem_1" draggable="true" ondragover="dragdrop.dragover(event)" ondragstart="dragdrop.dragstart(event)" ondragend="dragdrop.dragend(event);" onmousedown="$(this).addClass('drag');" onclick="Page.get('#');">
            item1
            <div class="child dragel"></div>
        </div>
        <div class="item dragel" id="mitem_2" draggable="true" ondragover="dragdrop.dragover(event)" ondragstart="dragdrop.dragstart(event)" ondragend="dragdrop.dragend(event);" onmousedown="$(this).addClass('drag');" onclick="Page.get('#');">
            item2
            <div class="child dragel"></div>
        </div>
        <div class="item dragel" id="mitem_3" draggable="true" ondragover="dragdrop.dragover(event)" ondragstart="dragdrop.dragstart(event)" ondragend="dragdrop.dragend(event);" onmousedown="$(this).addClass('drag');" onclick="Page.get('#');">
            item3
            <div class="child dragel"></div>
        </div>
        <div class="item dragel" id="mitem_4" draggable="true" ondragover="dragdrop.dragover(event)" ondragstart="dragdrop.dragstart(event)" ondragend="dragdrop.dragend(event);" onmousedown="$(this).addClass('drag');" onclick="Page.get('#');">
            item4
            <div class="child dragel"></div>
        </div>
	</div>
	{include="doload.tpl"}
</section>