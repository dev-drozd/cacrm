{include="store/menu.tpl"}
<section class="mngContent">
	<div class="sTitle"><span class="fa fa-chevron-right"></span>All Service
	<div class="filters">
		<span class="hnt hntTop" data-title="Filters" onclick="$(this).next().toggle();"><span class="fa fa-filter cl"></span></span>
		<div class="filterCtnr">
			<div class="fTitle">Filters</div>
			<div class="iGroup">
				<label>Long title</label>
				<input type="checkbox" name="long_title">
			</div>
			<div class="iGroup">
				<label>Long description</label>
				<input type="checkbox" name="long_description">
			</div>
			<div class="iGroup">
				<label>Empty title</label>
				<input type="checkbox" name="title">
			</div>
			<div class="iGroup">
				<label style="min-width: 105px;">Empty description</label>
				<input type="checkbox" name="description">
			</div>
			<div class="iGroup">
				<label>Empty keywords</label>
				<input type="checkbox" name="keywords">
			</div>
			<div class="iGroup">
				<label>Empty canonical</label>
				<input type="checkbox" name="canonical">
			</div>
			<div class="iGroup">
				<label>Without image</label>
				<input type="checkbox" name="image">
			</div>
			<div class="cashGroup">
				<button type="button" class="btn btnSubmit ac" onclick="Search2($('input[name=\'search\']').val(), '');">OK</button>
				<button type="button" class="btn btnSubmit dc" onclick="$(this).parents('.filterCtnr').hide();">Cancel</button>
			</div>
		</div>
	</div>
	[add-services]<a href="/store/services/add" class="btn addBtn" onclick="Page.get(this.href); return false;">Add service</a>[/add-services]
	</div>
	<div class="mngSearch">
		<input type="text" name="search" placeholder="Search service" onkeypress="if(event.keyCode == 13) Search2(this.value);">
		<button class="btn btnSearch" onclick="Search2($(this).prev().val());">{lang=search} <span class="total">{lang=total}: <span id="res_count">{res_count}</span><span></button>
	</div>
	<div class="userList">
		{services}
	</div>
	{include="doload.tpl"}
</section>
<script>
if (location.search) {
	var search = location.search;
	$('input[name="long_title"]').attr('checked', $_GET('long_title') > 0 ? true : false);
	$('input[name="long_description"]').attr('checked', $_GET('long_description') > 0 ? true : false);
	$('input[name="title"]').attr('checked', $_GET('title') > 0 ? true : false);
	$('input[name="description"]').attr('checked', $_GET('description') > 0 ? true : false);
	$('input[name="keywords"]').attr('checked', $_GET('keywords') > 0 ? true : false);
	$('input[name="canonical"]').attr('checked', $_GET('canonical') > 0 ? true : false);
	$('input[name="image"]').attr('checked', $_GET('image') > 0 ? true : false);
}
</script>