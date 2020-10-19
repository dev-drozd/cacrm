{include="settings/menu.tpl"}
<section class="mngContent">
	<div class="sTitle">
		<span class="fa fa-chevron-right"></span>{lang=Settings} <span class="fa fa-chevron-right ltl"></span> <font id="stitle">{lang=General}</font>
		<button class="btn btnAddGr" onclick="Settings.addGroup();">{lang=AddGroup}</button>
	</div>
	<form class="uForm" method="post" onsubmit="Settings.save(this, event);">
		<div id="general" class="tab">
			<div class="iGroup">
				<label>Default language</label>
				<select name="lang">
					<option value="en">English</option>
					<option value="ru">Russian</option>
					<option value="uk">Ukraine</option>
				</select>
			</div>
			<div class="iGroup">
				<label>{lang=admin_uri}</label>
				<input type="text" name="admin_uri" value="{admin-uri}">
			</div>
			<div class="iGroup">
				<label>Date and time format</label>
				<input type="text" name="format_date" value="{format-date}">
			</div>
			<div class="iGroup">
				<label>{lang=quick_sell}</label>
				<input type="text" name="quick_sell" value="{quick-sell}">
			</div>
			<div class="iGroup">
				<label>Min price for purchase confirmation</label>
				<input type="text" name="min_purchase" value="{min-purchase}">
			</div>
			<div class="iGroup">
				<label>Minutes removed from payroll hours per forfeit </label>
				<input type="number" step="0.001" min="0" name="min_forfeit" value="{min-forfeit}">
			</div>
			<div class="iGroup">
				<label>Min job total to receive job points</label>
				<input type="number" step="0.001" min="0" name="issue_min_total" value="{issue-min-total}">
			</div>
			<div class="iGroup">
				<label>{lang=offline}</label>
				<input type="checkbox" name="offline" {offline}>
			</div>
			<div class="iGroup">
				<label>Site is down message</label>
				<textarea type="text" name="offline_msg">{offline-msg}</textarea>
			</div>
			<div class="iGroup">
				<label>{lang=max_life_time}</label>
				<input type="number" name="maxlifetime" value="{max-life-time}" step="0.01" min="0">
			</div>
			<div class="iGroup">
				<label>Tablet feedback app</label>
				<a href="/uploads/feedback-yoursite.apk" class="btn-download">
					<button class="btn btnSubmit" type="button">
						<span class="fa fa-download"></span>
						Download
					</button>
				</a>
			</div>
			<div class="sTitle">Dashboard</div>
			<div class="iGroup dGroup cl">
				<label>Surveillance display period</label>
				<input class="cl" type="text" name="camera_period" value="{camera-period}" onclick="$(this).next().show().parent().addClass('act');" readonly>
				<div id="PeriodCamera" class="calendar-el" data-multiple="1"></div>
			</div>
			<div class="sTitle">SEO for static pages site</div>
			
			
			
			
			<div class="tabs">
				<div class="tab" id="seo_main_page" data-title="Main page (default)">
					<div class="iGroup">
						<label>{lang=title}</label>
						<input type="text" name="title_en" value="{title}">
					</div>
					<div class="iGroup">
						<label>{lang=keywords}</label>
						<input type="text" name="keywords_en" value="{keywords}">
					</div>
					<div class="iGroup">
						<label>{lang=description}</label>
						<textarea type="text" name="description_en">{description}</textarea>
					</div>
				</div>
				<div class="tab" id="seo_services_page" data-title="Services page">
					<div class="iGroup">
						<label>{lang=title}</label>
						<input type="text" name="title_services_en" value="{title-services}">
					</div>
					<div class="iGroup">
						<label>{lang=keywords}</label>
						<input type="text" name="keywords_services_en" value="{keywords-services}">
					</div>
					<div class="iGroup">
						<label>{lang=description}</label>
						<textarea type="text" name="description_services_en">{description-services}</textarea>
					</div>
				</div>
				<div class="tab" id="seo_self_services_page" data-title="Self services page">
					<div class="iGroup">
						<label>{lang=title}</label>
						<input type="text" name="title_self_services_en" value="{title-self-services}">
					</div>
					<div class="iGroup">
						<label>{lang=keywords}</label>
						<input type="text" name="keywords_self_services_en" value="{keywords-self-services}">
					</div>
					<div class="iGroup">
						<label>{lang=description}</label>
						<textarea type="text" name="description_self_services_en">{description-self-services}</textarea>
					</div>
				</div>
				<div class="tab" id="seo_blog_page" data-title="Blog page">
					<div class="iGroup">
						<label>{lang=title}</label>
						<input type="text" name="title_blog_en" value="{title-blog}">
					</div>
					<div class="iGroup">
						<label>{lang=keywords}</label>
						<input type="text" name="keywords_blog_en" value="{keywords-blog}">
					</div>
					<div class="iGroup">
						<label>{lang=description}</label>
						<textarea type="text" name="description_blog_en">{description-blog}</textarea>
					</div>
				</div>
				<div class="tab" id="seo_store_page" data-title="Store page">
					<div class="iGroup">
						<label>{lang=title}</label>
						<input type="text" name="title_store_en" value="{title-store}">
					</div>
					<div class="iGroup">
						<label>{lang=keywords}</label>
						<input type="text" name="keywords_store_en" value="{keywords-store}">
					</div>
					<div class="iGroup">
						<label>{lang=description}</label>
						<textarea type="text" name="description_store_en">{description-store}</textarea>
					</div>
				</div>
			</div>
			
			
			<div class="sTitle">SEO sitemap</div>
			<div class="iGroup">
				<label>Sitemap</label>
				<textarea style="font-family: monospace;margin-bottom: -7px;background: #384a52;color: #efe8d0;">{sitemap}</textarea>
				Last update: {sitemap-lastdate}
				| <a href="https://crm.yoursite.com/cron/sitemap.php">update now</a>
			</div>
			
			<div class="sTitle">Cache</div>
			<div class="iGroup">
				<label>{lang=cache_type}</label>
				<select name="cache_sel">
					<option value="1">{lang=file_cache}</option>
					<option value="2">Memcache</option>
					<option value="3">MemcacheD</option>
				</select>
			</div>
			<div class="iGroup">
				<label>{lang=mc_host}</label>
				<input type="text" name="cache_host" value="{cache-host}">
			</div>
			<div class="iGroup">
				<label>{lang=mc_key}</label>
				<input type="text" name="cache_key" value="{cache-key}">
			</div>
			<div class="sTitle">Tablet authorization</div>
			<div class="iGroup">
				<label>User</label>
				<input type="text" name="tablet_user" value="{tablet-user}">
			</div>
			<div class="iGroup">
				<label>Password</label>
				<input type="text" name="tablet_password" value="{tablet-password}">
			</div>
			<div class="sTitle">E-Commerce</div>
			<div class="iGroup">
				<label>Order form</label>
				<select name="order_form">
					<option value="0">Not selected</option>
					{order-form}
				</select>
			</div>
			<div class="sTitle">Drawer alert</div>
			<div class="iGroup dGroup cl">
				<label>Less then</label>
				<input class="cl" type="text" name="min_lack" value="{min-lack}">
			</div>
			<div class="iGroup dGroup cl">
				<label>More then</label>
				<input class="cl" type="text" name="max_lack" value="{max-lack}">
			</div>
		</div>
		<div id="Privileges" class="tab">
		</div>
		<div id="Groups" class="tab">
		</div>
		<div class="sGroup">
			<button class="btn btnSubmit" type="submit"><span class="fa fa-save"></span> {lang=save}</button>
		</div>
	</form>
</section>
<script>
$(document).ready(function(){
	calendar.init({el: ['#PeriodCamera']});
	var hash = location.hash.split('#');
	if(hash[1])
		Settings.tab(hash[1], hash[2]);
	if (hash[1] == 'Groups')
		$('.sGroup').hide()
	else 
		$('.sGroup').show()
	$('select[name="cache_sel"] option[value="{cache-sel}"]').attr('selected', 'selected');
	$('select[name="timezone"] option[value="{timezone}"]').attr('selected', 'selected');
	$('select[name="lang"] option[value="{lang}"]').attr('selected', 'selected');
});
</script>