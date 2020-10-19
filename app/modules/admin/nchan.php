<?php
	send_push(17, [
		'type' => 'new_task',
		'id' => 1,
		'note' => 'test'
	]);
    /*send_push('599a878979340', [
						'type' => 'chat_message',
						'message' => '<div class="dialog-message" data-id="2">
							<div class="dialog-img">
							</div>
							<div class="dialog-combi">
								<div class="dm-title">
									<span class="name">test</span>
									<i class="name">123</i>
								</div>
								<div class="dm-content">test mes admin
								</div>
							</div>
						</div>'
					]);*/
	die;
?>