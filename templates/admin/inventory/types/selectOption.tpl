<div class="dClear">
    <span class="fa fa-bars opt" draggable="true" ondragover="options.dragover(event)" ondragstart="options.dragstart(event)" ondragend="options.dragend(event)" onmousedown="$(this).parent().addClass('drag');" onmouseup="$(this).parent().removeClass('drag');">
	</span>
    <div class="sSide w100">
        <input name="opt" placeholder="Option..." value="{option}" data-id="{hid}">
    </div>
    <span class="fa fa-times" onclick="$(this).parent().remove();"></span>
</div>