<div class="ctnr">

		<div id="page">
			<div class="account">
				<div class="paTitle">Personal account</div>
				<div class="dClear">
					<div class="pSide">
						<div class="userPhoto">
							<!-- <span class="fa fa-user-secret"></span> -->
							<img src="{theme}/img/thumb_user.png">
							<span class="fa fa-search-plus" onclick="showPhoto.show(this.previousSibling.previousSibling.src);"></span>
						</div>
						<ul class="share dClear">
							<li><a href="#"><span class="fa fa-facebook"></span></a></li>
							<li><a href="#"><span class="fa fa-twitter"></span></a></li>
							<li><a href="#"><span class="fa fa-google-plus"></span></a></li>
						</ul>
						<div class="shareText">
							Share our link on social networks and you will get discounts in the store.
						</div>
					</div>
					<div class="iSide">
						<div class="uName">
							User name
							<span class="fa fa-pencil"></span>
						</div>
						
						<ul class="uInfo">
							<li>Email: test@site.com</li>
							<li>Address: test address</li>
							<li>Phone: 0887766554</li>
						</ul>

						<ul class="roundInfo dClear">
							<li>
								<span>200</span>
								points
							</li>
							<li>
								<span>50</span>
								devices
							</li>
							<li>
								<span>10</span>
								orders
							</li>
						</ul>

						<div class="adTitle">Devices <span class="fa fa-plus"></span></div>
						<div class="tbl tblDev">
							<div class="tr">
								<div class="th w10">
									ID
								</div>
								<div class="th">
									Type
								</div>
								<div class="th">
									Category
								</div>
								<div class="th">
									Model
								</div>
								<div class="th">
									OS
								</div>
								<div class="th w100">
									Event
								</div>
							</div>
							<div class="tr dev" id="dev_4" onclick="account.toggleList(this, event);">
								<div class="td w10">
									<span class="fa fa-chevron-right isOpen"></span> <strong>#4</strong>
								</div>
								<div class="td">
									Laptop
								</div>
								<div class="td">
									NOKIA
								</div>
								<div class="td">
									ER11
								</div>
								<div class="td">
									Windows
								</div>
								<div class="td w100">
									<a href="javascript:account.editDevice();" class="hnt hntTop" data-title="Edit device"><span class="fa fa-pencil"></span></a>
									<a href="javascript:account.addIssue();" class="hnt hntTop" data-title="Add issue"><span class="fa fa-plus"></span></a>
									<a href="javascript:account.delDevice();" class="hnt hntTop" data-title="Delete device"><span class="fa fa-times"></span></a>
								</div>
							</div>
							<div class="tr issues" style="display: none;">
								<div class="issue head">
									<div class="iId">
										id
									</div>
									<div class="isDate">
										Date
									</div>
									<div class="is">
										Description
									</div>
									<div class="iAuthor">
										Staff
									</div>
								</div>
								<div class="issue">
									<div class="iId">
										<a href="/admin/issues/view/10">10</a>
									</div>
									<div class="isDate">
										2020-05-31 04:16:41
									</div>
									<div class="is">
										Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. 
									</div>
									<div class="iAuthor">
										<a href="/admin/users/view/17" target="_blank">Alexandr Dev</a>
									</div>
								</div>
							</div>
						</div>

						<div class="adTitle">Orders</div>
						<div class="tbl tblDev">
							<div class="tr">
								<div class="th w10">
									ID
								</div>
								<div class="th">
									Date
								</div>
								<div class="th">
									Status
								</div>
								<div class="th">
									Price
								</div>
							</div>
							<div class="tr dev">
								<div class="td w10">
									<strong>#4</strong>
								</div>
								<div class="td">
									2020-05-31 04:16:41
								</div>
								<div class="td">
									confirmed
								</div>
								<div class="td">
									300$
								</div>
							</div>
						</div>
						
						<div class="adTitle">Invoices</div>
						<div class="tbl tblDev">
							<div class="tr">
								<div class="th w10">
									ID
								</div>
								<div class="th">
									Date
								</div>					
								<div class="th">
									Amount
								</div>
								<div class="th">
									Paid
								</div>
								<div class="th">
									Due
								</div>
								<div class="th">
									Status
								</div>
							</div>
							<div class="tr">
								<div class="td w10">
									20
								</div>
								<div class="td">
									2020-06-13 12:56:14
								</div>					
								<div class="td">
									380.6
								</div>
								<div class="td">
									380.6
								</div>
								<div class="td">
									0
								</div>
								<div class="td">
									<span class="stPaid">Paid</span>
								</div>
							</div>
							<div class="tr">
								<div class="td w10">
									21
								</div>
								<div class="td">
									2020-06-13 13:16:59
								</div>					
								<div class="td">
									380.6
								</div>
								<div class="td">
									0
								</div>
								<div class="td">
									380.6
								</div>
								<div class="td">
									<span class="stUnpaid">Unpaid</span>
								</div>
							</div>
						</div>

<!-- 						<div class="adTitle">Edit user</div>
						<div class="tabs">
							<div class="tab" data-title="Change password" id="password">
								<div class="iGroup">
									<label>Password</label>
									<input type="password" name="password" placeholder="Password">
								</div>
								<div class="iGroup">
									<label>Repeat Password</label>
									<input type="password" name="password2" placeholder="Password">
								</div>
								<div class="sGroup">
									<button class="btn btnSubmit">Save</button>
								</div>
							</div>
							<div class="tab" data-title="Change email" id="email">
								<div class="iGroup">
									<label>Email</label>
									<input type="email" name="email" placeholder="Email">
								</div>
								<div class="iGroup">
									<label>Repeat email</label>
									<input type="email" name="email2" placeholder="Email">
								</div>
								<div class="sGroup">
									<button class="btn btnSubmit">Save</button>
								</div>
							</div>
						</div> -->
					</div>
				</div>
			</div>
		</div>
		
		<script>
			$(function() {
				$('.tabs').tabs();
			});
		</script>