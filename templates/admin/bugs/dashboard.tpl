{include="bugs/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
        <span class="fa fa-chevron-right"></span>{title}
    </div>
	<div>
		<div class="flex main-panel">
			<a href="/bugs/my" onclick="Page.get(this.href); return false;">
				<i class="fa fa-lightbulb-o" style="color:#90a0af"></i>
				Improvement
				<p>{improvement}</p>
			</a>

			<a href="/bugs/opened" onclick="Page.get(this.href); return false;">
				<i class="fa fa-bug" style="color: #36b1e6;"></i>
				Open bugs
				<p>{opened}</p>
			</a>
			<a href="/bugs/pending" onclick="Page.get(this.href); return false;">
				<i class="fa fa-bug" style="color: #f78e1e;"></i>
				Pending bugs
				<p>{pending}</p>
			</a>

			<a href="/bugs/rejected" onclick="Page.get(this.href); return false;">
				<i class="fa fa-bug" style="color: #d04646"></i>
				Rejected bugs
				<p>{rejected}</p>
			</a>

			<a href="/bugs/closed" onclick="Page.get(this.href); return false;">
				<i class="fa fa-bug" style="color: #77c159"></i>
				Fixed bugs
				<p>{closed}</p>
			</a>
		</div>
		<div>{users}</div>
	</div>
</section>
<style>
.main-panel > a > i {
    font-size: 3em;
}
.main-panel > a > p {
    font-size: 2em;
}
</style>