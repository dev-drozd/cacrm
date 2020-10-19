[my]
	<table border="0" width="100%">
		<tr>
			<td>
				[first]
				<div style="color: #bfbfbf;text-align: right;">
					<a href="#" style="color: #36b1e6;">{name} {lastname}</a>
				</div>
				[/first]
				<div style="background: #ebf6ff;padding: 5px 13px;border-radius: 3px;position: relative;color: #9a9fa7;word-break: break-all;">
					<span style="text-align: right;display: block;font-size: 11px;color: #9db5c7;line-height: 18px;">{date}</span>
					{message}
				</div>
			</td>
			<td width="50" style="position: relative;">
				[first]
				<a href="#" style="width: 50px;padding-top: 15px;">
					[ava]
						<img src="https://yoursite.com/uploads/images/users/{uid}/thumb_{image}" style="width: 35px;height: 35px;border-radius: 50%;margin-left: 10px;">
					[not-ava]
						<span style="width: 35px;height: 35px;text-align: center;line-height: 35px;border-radius: 50%;float: left;margin-left: 10px;color: #cfd4de;font-size: 16px;border: 1px solid #e1e6ef;background: #fff;">&#x270e;</span>
					[/ava]
				</a>
				[/first]
			</td>
	</table>
[not-my]
	<table border="0" width="100%">
		<tr>
			<td width="50" style="position: relative;">
				[first]
				<a href="#" style="width: 50px;padding-top: 15px;">
					<span style="width: 35px;height: 35px;text-align: center;line-height: 35px;border-radius: 50%;float: left;margin-right: 10px;color: #cfd4de;font-size: 16px;border: 1px solid #e1e6ef;background: #fff;">&#x270e;</span>
				</a>
				[/first]
			</td>
			<td>
				[first]
				<div style="color: #bfbfbf;">
					<a href="#" style="color: #36b1e6;">{name} {lastname}</a>
				</div>
				[/first]
				<div style="background: #f7f8fa;padding: 5px 13px;border-radius: 3px;position: relative;color: #9a9fa7;word-break: break-all;">
					<span style="text-align: right;display: block;font-size: 11px;color: #9db5c7;line-height: 18px;">{date}</span>
					{message}
				</div>
			</td>
	</table>
[/my]