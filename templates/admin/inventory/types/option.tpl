<div class="iGroup optGroup">
	<span class="fa fa-bars" draggable="true" ondragover="options.dragover(event)" ondragstart="options.dragstart(event)" ondragend="options.dragend(event)" onmousedown="$(this).parent().addClass('drag');" onmouseup="$(this).parent().removeClass('drag');">
	</span>
    <div class="sSide">
        <label>Label</label>
        <input name="oName" value="{name}" data-id="{id}">
    </div>
    <div class="sSide">
        <label>Type</label>
        <select name="type">{options}</select>
    </div>
	<div class="sSide rSide">
		<label>Require</label>
		<input type="checkbox" name="req"[req] checked[/req]>
	</div>
	<span class="fa fa-times" onclick="$(this).parent().remove();"></span>
	[select]
    <div class="selArea">
        <div class="dClear"></div>
        <label>Multiple</label>
        <input type="checkbox" name="sMul" style="display: none;"[multiple] checked[/multiple]>
        <div class="dClear"></div>
        <div class="selOpts">
            <label class="lTitle">Select options</label>
			{select-options}
        </div>
        <div class="aRight">
            <button class="btn atnAo" onclick="options.addSel(this); return false;">Add option</button>
        </div>
    </div>
	[/select]
</div>