<div class="slider" id="slider">
		<div class="slItems">
			{slider}
		</div>
	</div>

	<div class="ctnr">

		{main}

		<div class="textArea">
			<div>
				{main-page}
			</div>
		</div>

		<div class="miniBlog dClear">
			<div class="mbTitle">
				<span>Articles</span>
			</div>
			<div class="mBlogs dClear">
				<div class="slItems">
					{blog}
				</div>
			</div>
		</div>
		
		<div class="miniServices dClear">
			<div class="msTitle">
				<span>Services</span>
			</div>
			<div class="mServices dClear">
				{services}
			</div>
		</div>
	</div>
	
	<!-- <div class="chat hide" style="right: auto; left: 0;">
		<div class="chatTitle">
			Live chat
			<span class="fa fa-times"></span>
			<span class="fa fa-window-minimize"></span>
		</div>
		<div class="welcome-chat" style="display: none;">
			<p>Please enter your message and click Start to begin the chat.</p>
			<input type="email" name="email" placeholder="Enter yout email">
			<div class="flRight">
				<button type="button" class="btn btnChat">Start</button>
			</div>
		</div>
		
		<div class="email-chat">
			<p>Let us know if you need any help! We respond super fast :)</p>
			<div class="chatForm">
				<span class="fa fa-at"></span>
				<input type="email" name="email" placeholder="Enter your email...">
			</div>
			<div class="chatForm">
				<span class="fa fa-envelope"></span>
				<textarea name="message" placeholder="Enter your message..."></textarea>
			</div>
			<div class="flRight">
				<button type="button" class="btn btnChat">Start</button>
			</div>
		</div>
		
		<div class="dialog-chat" style="display: block;">
			<div class="dialog">
				<div class="dialog-message">
					<div class="dm-title">
						<span class="name">Name</span>
						<i class="name">8:04am</i>
					</div>
					<div class="dm-content">
						Ever wake up after leaving your iPhone
					</div>
				</div>
				
				<div class="dialog-message me">
					<div class="dm-title">
						<span class="name">Me</span>
						<i class="name">8:04am</i>
					</div>
					<div class="dm-content">
						Ever wake up after leaving your iPhone
					</div>
				</div>
				
				<div class="dialog-message">
					<div class="dm-title">
						<span class="name">Name</span>
						<i class="name">8:04am</i>
					</div>
					<div class="dm-content">
						Ever wake up after leaving your iPhone
					</div>
				</div>
				
				<div class="dialog-message me">
					<div class="dm-title">
						<span class="name">Me</span>
						<i class="name">8:04am</i>
					</div>
					<div class="dm-content">
						Ever wake up after leaving your iPhone
					</div>
				</div>
			</div>
			<div class="dialog-form">
				<textarea name="chat-message" placeholder="Enter your message..."></textarea>
				<span class=" fa fa-chevron-right"></span>
			</div>
		</div>
		<div class="feedback-chat" style="display: none;">
			<p>Please rate our services.</p>
			<div class="rating">
				<span class="fa fa-star rStart mini" ratting="1"></span>
				<span class="fa fa-star rStart mini" ratting="2"></span>
				<span class="fa fa-star rStart mini" ratting="3"></span>
				<span class="fa fa-star rStart mini" ratting="4"></span>
				<span class="fa fa-star rStart mini" ratting="5"></span>
			</div>
		</div>
	</div> -->
	
	<script>
		$(function() {
			$('.igGroup').carousel(); 

			$('#slider').rbtSlider({
				'height': '500px', 
				'dots': true,
				'arrows': true
			});

			$('.mBlogs').rbtSlider({
				'height': $(window).width() > 991 ? '350px' : '700px', 
				'dots': true
			});
		});
	</script>