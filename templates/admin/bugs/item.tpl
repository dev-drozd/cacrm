<div class="sUser bug" id="bug_{id}">
	<div class="bugTitle" style="background: [comment-new]#32bf924f[not-comment-new]#fff[/comment-new] !important;cursor:pointer"[item][comment-new][not-comment-new] onmousedown="$('#bug_content_{id}').slideToggle();"[/comment-new][/item]>
        <div>
			<a href="/bugs/{id}" onclick="Page.get(this.href); return false;">
				<b>#{id}</b> <strong>{title}</strong> &#8594;
			</a> [owner]<span class="fa fa-pencil bEdit" onclick="bugs.add({id});"></span>[/owner] <span class="st_{status_id}"[dev] onclick="bugs.edit({id});" style="cursor: pointer;"[/dev]>{status}</span></div>
        <div class="date">
            <a href="/users/view/{user-id}" onclick="Page.get(this.href); return false;">{user-name} {user-lastname}</a> <span>|</span> {date} <span>|</span> <a href="{url}" target="_blank">Link to the bug</a>
        </div>
    </div>
    <div class="bugContent"[item] style="display: none;"[/item] id="bug_content_{id}">
		<a href="/im/{user-id}?text=Bug;{id}" onclick="Page.get(this.href); return false;" class="mesBtn" style="float: right;">
			<span class="fa fa-exclamation-circle" aria-hidden="true"></span>
		</a>
        {content}
        [comment]
			<div class="bugComment cm_{status}">
				<a href="/users/view/{dev-id}">{dev-name} {dev-lastname}</a>: 
				<span>{comment}</span>
				<div class="thumbnails">{comimages}</div>
			</div>
		[/comment]
    </div>
</div>