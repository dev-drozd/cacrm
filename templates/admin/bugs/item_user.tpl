<div class="sUser bug">
	<div class="bugTitle">
        <div>
			<a href="/bugs/{id}" onclick="Page.get(this.href); return false;">
				{name} {lastname}
			</a>  <span class="st_opened">{count}</span>
		</div>
        <div class="date">
            Bugs aded {count} <span>|</span> <a href="/bugs/users/{id}" target="_blank">Link to the bug</a>
        </div>
		[image]<img src="/uploads/images/users/{id}/thumb_{image}" onclick="showPhoto(this.src);" style="border-radius: 50%;">[not-image]<span class="fa fa-user-secret" style="width: 94px;height: 94px;font-size: 51px;background: #fff;border-radius: 50%;padding: 22px 0px 0px 26px;"></span>[/image]
    </div>
</div>