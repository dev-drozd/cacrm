<div class="sUser bug" id="bug_{id}">
	<div class="bugTitle">
        <div>
			<a href="/bugs/{id}" onclick="Page.get(this.href); return false;">
				<b>#{id}</b> <strong>{title}</strong> &#8594;
			</a> [owner]<span class="fa fa-pencil bEdit" onclick="bugs.add({id});"></span>[/owner] <span class="st_{status_id}"[dev] onclick="bugs.edit({id});" style="cursor: pointer;"[/dev]>{status}</span></div>
        <div class="date">
            <a href="/users/view/{user-id}" onclick="Page.get(this.href); return false;">{user-name} {user-lastname}</a> <span>|</span> {date} <span>|</span> <a href="{url}" target="_blank">Link to the bug</a>
        </div>
    </div>
    <div class="bugContent">
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