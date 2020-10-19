<div class="iGroup">
	<label>{lang=userGroup}</label>
	<select name="user_group" onchange="Settings.privileges(this.value);">
		{group-option}
	</select>
</div>
<div class="tabs">
	<div class="tab" id="pageAccess" data-title="Page Access">
		<div class="iGroup">
			<label>{lang=ShowUsers}</label>
			<input type="checkbox" name="privileges[undenfined][users]">
		</div>
		<div class="iGroup">
			<label>{lang=ShowIm}</label>
			<input type="checkbox" name="privileges[undenfined][im]">
		</div>
		<div class="iGroup">
			<label>{lang=ShowSettings}</label>
			<input type="checkbox" name="privileges[undenfined][settings]">
		</div>
		<div class="iGroup">
			<label>{lang=ShowStore}</label>
			<input type="checkbox" name="privileges[undenfined][store]">
		</div>
		<div class="iGroup">
			<label>{lang=ShowService}</label>
			<input type="checkbox" name="privileges[undenfined][service]">
		</div>
		<div class="iGroup">
			<label>{lang=ShowPurchase}</label>
			<input type="checkbox" name="privileges[undenfined][purchase]">
		</div>
		<div class="iGroup">
			<label>{lang=ShowCommerce}</label>
			<input type="checkbox" name="privileges[undenfined][commerce]">
		</div>
		<div class="iGroup">
			<label>{lang=ShowInvoces}</label>
			<input type="checkbox" name="privileges[undenfined][invoces]">
		</div>
		<div class="iGroup">
			<label>{lang=ShowCash}</label>
			<input type="checkbox" name="privileges[undenfined][cash]">
		</div>
		<div class="iGroup">
			<label>{lang=ShowOrganizer}</label>
			<input type="checkbox" name="privileges[undenfined][organizer]">
		</div>
		<div class="iGroup">
			<label>{lang=ShowSalary}</label>
			<input type="checkbox" name="privileges[undenfined][salary]">
		</div>
		<div class="iGroup">
			<label>Show Feedbacks</label>
			<input type="checkbox" name="privileges[undenfined][feedback]">
		</div>
		<div class="iGroup">
			<label>Show analytics</label>
			<input type="checkbox" name="privileges[undenfined][analytics]">
		</div>
		<div class="iGroup">
			<label>Camera</label>
			<input type="checkbox" name="privileges[undenfined][camera]">
		</div>
		<div class="iGroup">
			<label>Working time</label>
			<input type="checkbox" name="privileges[undenfined][working_time]">
		</div>
	</div>
	<div class="tab" id="groupPriv" data-title="Users">
		<div class="iGroup">
			<label>{lang=addUsers}</label>
			<select name="privileges[undenfined][add_users]" multiple>
				{group-option}
			</select>
		</div>
		<div class="iGroup">
			<label>{lang=editUsers}</label>
			<select name="privileges[undenfined][edit_users]" multiple>
				{group-option}
			</select>
		</div>
		<div class="iGroup">
			<label>{lang=deleteUsers}</label>
			<select name="privileges[undenfined][delete_users]" multiple>
				{group-option}
			</select>
		</div>
		<div class="iGroup">
			<label>{lang=deleteApproval}</label>
			<input type="checkbox" name="privileges[undenfined][delete_users_approval]">
		</div>
		<div class="iGroup">
			<label>Edit photo</label>
			<input type="checkbox" name="privileges[undenfined][edit_photo]">
		</div>
		<div class="iGroup">
			<label>{lang=Email}:</label>
			<select name="privileges[undenfined][email_users]">
				<option value="0">deny</option>
				<option value="1">view</option>
				<option value="2">view/edit</option>
			</select>
		</div>
		<div class="iGroup">
			<label>{lang=NameLastname}:</label>
			<select name="privileges[undenfined][name_users]">
				<option value="0">deny</option>
				<option value="1">view</option>
				<option value="2">view/edit</option>
			</select>
		</div>
		<div class="iGroup">
			<label>{lang=Phone}:</label>
			<select name="privileges[undenfined][phone_users]">
				<option value="0">deny</option>
				<option value="1">view</option>
				<option value="2">view/edit</option>
			</select>
		</div>
		<div class="iGroup">
			<label>{lang=Address}:</label>
			<select name="privileges[undenfined][address_users]">
				<option value="0">deny</option>
				<option value="1">view</option>
				<option value="2">view/edit</option>
			</select>
		</div>
		<div class="iGroup">
			<label>{lang=Group}:</label>
			<select name="privileges[undenfined][group_users]">
				<option value="0">deny</option>
				<option value="1">view</option>
				<option value="2">view/edit</option>
			</select>
		</div>
		<div class="iGroup">
			<label>{lang=Password}:</label>
			<select name="privileges[undenfined][password_users]">
				<option value="0">deny</option>
				<option value="1">edit</option>
			</select>
		</div>
		<div class="iGroup">
			<label>Make suspention</label>
			<input type="checkbox" name="privileges[undenfined][make_suspention]">
		</div>
		<div class="iGroup">
			<label>View point details</label>
			<input type="checkbox" name="privileges[undenfined][point_details]">
		</div>
	</div>
	<div class="tab" id="objectPriv" data-title="{lang=Objects}">
		<div class="iGroup">
			<label>{lang=AddObject}</label>
			<input type="checkbox" name="privileges[undenfined][add_object]">
		</div>
		<div class="iGroup">
			<label>{lang=EditObject}</label>
			<input type="checkbox" name="privileges[undenfined][edit_object]">
		</div>
		<div class="iGroup">
			<label>{lang=DeleteObject}</label>
			<input type="checkbox" name="privileges[undenfined][delete_object]">
		</div>
		<div class="iGroup">
			<label>{lang=AddLocation}</label>
			<input type="checkbox" name="privileges[undenfined][add_location]">
		</div>
		<div class="iGroup">
			<label>{lang=deleteApproval}</label>
			<input type="checkbox" name="privileges[undenfined][delete_objects_approval]">
		</div>
		<div class="iGroup">
			<label>{lang=CheckIp}</label>
			<input type="checkbox" name="privileges[undenfined][stores_check_ip]">
		</div>
		<div class="iGroup">
			<label>{lang=Name}:</label>
			<select name="privileges[undenfined][name_object]">
				<option value="0">{lang=deny}</option>
				<option value="1">{lang=view}</option>
				<option value="2">{lang=viewedit}</option>
			</select>
		</div>
		<div class="iGroup">
			<label>{lang=Address}:</label>
			<select name="privileges[undenfined][address_object]">
				<option value="0">{lang=deny}</option>
				<option value="1">{lang=view}</option>
				<option value="2">{lang=viewedit}</option>
			</select>
		</div>
		<div class="iGroup">
			<label>{lang=Phone}:</label>
			<select name="privileges[undenfined][phone_object]">
				<option value="0">{lang=deny}</option>
				<option value="1">{lang=view}</option>
				<option value="2">{lang=viewedit}</option>
			</select>
		</div>
		<div class="iGroup">
			<label>{lang=Email}:</label>
			<select name="privileges[undenfined][email_object]">
				<option value="0">{lang=deny}</option>
				<option value="1">{lang=view}</option>
				<option value="2">{lang=viewedit}</option>
			</select>
		</div>
		<div class="iGroup">
			<label>{lang=Description}:</label>
			<select name="privileges[undenfined][description_object]">
				<option value="0">{lang=deny}</option>
				<option value="1">{lang=view}</option>
				<option value="2">{lang=viewedit}</option>
			</select>
		</div>
	</div>
	<div class="tab" id="inventoryPriv" data-title="Inventory">
		<div class="iGroup">
			<label>Add inventory</label>
			<input type="checkbox" name="privileges[undenfined][add_inventory]">
		</div>
		
		<div class="iGroup">
			<label>Edit inventory</label>
			<input type="checkbox" name="privileges[undenfined][edit_inventory]">
		</div>
		<div class="iGroup">
			<label>Delete inventory</label>
			<input type="checkbox" name="privileges[undenfined][delete_inventory]">
		</div>
		<div class="iGroup">
			<label>{lang=CheckIp}</label>
			<input type="checkbox" name="privileges[undenfined][inventory_check_ip]">
		</div>
		<div class="iGroup">
			<label>{lang=DelIssue}</label>
			<input type="checkbox" name="privileges[undenfined][del_issue]">
		</div>
		<div class="iGroup">
			<label>Add upcharge service</label>
			<input type="checkbox" name="privileges[undenfined][add_upcharge]">
		</div>
		<div class="iGroup">
			<label>Edit upcharge service</label>
			<input type="checkbox" name="privileges[undenfined][edit_upcharge]">
		</div>
		<div class="iGroup">
			<label>Delete upcharge service</label>
			<input type="checkbox" name="privileges[undenfined][delete_upcharge]">
		</div>
		<div class="iGroup">
			<label>Create inventory transfer</label>
			<input type="checkbox" name="privileges[undenfined][create_inventory_transfer]">
		</div>
		<div class="iGroup">
			<label>Confirm inventory transfer</label>
			<input type="checkbox" name="privileges[undenfined][confirm_inventory_transfer]">
		</div>
		<div class="iGroup">
			<label>Confirm warranty</label>
			<input type="checkbox" name="privileges[undenfined][confirm_warranty]">
		</div>
		<div class="iGroup">
			<label>Show services by store</label>
			<select name="privileges[undenfined][service_by_store]">
				<option value="0">Show all services</option>
				<option value="1">Show by auth store</option>
			</select>
		</div>
	</div>
	<div class="tab" id="servicePriv" data-title="Services">
		<div class="iGroup">
			<label>Add service</label>
			<input type="checkbox" name="privileges[undenfined][add_iservice]">
		</div>
		
		<div class="iGroup">
			<label>Edit service</label>
			<input type="checkbox" name="privileges[undenfined][edit_iservice]">
		</div>
		<div class="iGroup">
			<label>Delete service</label>
			<input type="checkbox" name="privileges[undenfined][delete_iservice]">
		</div>
	</div>
	<div class="tab" id="purchasePriv" data-title="Purchase">
		<div class="iGroup">
			<label>{lang=AddPurchase}</label>
			<input type="checkbox" name="privileges[undenfined][add_purchase]">
		</div>
		<div class="iGroup">
			<label>{lang=EditPurchase}</label>
			<input type="checkbox" name="privileges[undenfined][edit_purchase]">
		</div>
		<div class="iGroup">
			<label>{lang=DeletePurchase}</label>
			<input type="checkbox" name="privileges[undenfined][delete_purchase]">
		</div>
		<div class="iGroup">
			<label>{lang=ConfirmPurchase}</label>
			<input type="checkbox" name="privileges[undenfined][confirm_purchase]">
		</div>
		<div class="iGroup">
			<label>{lang=CheckIp}</label>
			<input type="checkbox" name="privileges[undenfined][purchase_check_ip]">
		</div>
	</div>
	<div class="tab" id="cashPriv" data-title="Cash">
		<div class="iGroup">
			<label>{lang=ShowAmount}</label>
			<input type="checkbox" name="privileges[undenfined][show_amount]">
		</div>
		<div class="iGroup">
			<label>{lang=CheckIp}</label>
			<input type="checkbox" name="privileges[undenfined][cash_check_ip]">
		</div>
		<div class="iGroup">
			<label>Unlimited open/close</label>
			<input type="checkbox" name="privileges[undenfined][cash_unlimited_open]">
		</div>
	</div>
	<div class="tab" id="InvoicesPriv" data-title="Invoices">
		<div class="iGroup">
			<label>{lang=EditInvoices}</label>
			<input type="checkbox" name="privileges[undenfined][edit_invoices]">
		</div>
		<div class="iGroup">
			<label>{lang=EditPaidInvoices}</label>
			<input type="checkbox" name="privileges[undenfined][edit_paid_invoices]">
		</div>
		<div class="iGroup">
			<label>Confirm refund</label>
			<input type="checkbox" name="privileges[undenfined][confirm_refund]">
		</div>
		<div class="iGroup">
			<label>Del invoice</label>
			<input type="checkbox" name="privileges[undenfined][del_invoices]">
		</div>
		<div class="iGroup">
			<label>Check for IP</label>
			<input type="checkbox" name="privileges[undenfined][check_ip_invoice]">
		</div>
		<div class="iGroup">
			<label>{lang=ConfirmDiscount}</label>
			<input type="checkbox" name="privileges[undenfined][confirm_discount]">
		</div>
	</div>
	<div class="tab" id="onsitePriv" data-title="On Site">
		<div class="iGroup">
			<label>{lang=AddService}</label>
			<input type="checkbox" name="privileges[undenfined][add_service]">
		</div>
		<div class="iGroup">
			<label>{lang=EditService}</label>
			<input type="checkbox" name="privileges[undenfined][edit_service]">
		</div>
		<div class="iGroup">
			<label>{lang=DeleteService}</label>
			<input type="checkbox" name="privileges[undenfined][delete_service]">
		</div>
		<div class="iGroup">
			<label>{lang=ConfirmService}</label>
			<input type="checkbox" name="privileges[undenfined][confirm_service]">
		</div>
	</div>
	<div class="tab" id="requestPriv" data-title="Requests">
		<div class="iGroup">
			<label>{lang=EditRequest}</label>
			<input type="checkbox" name="privileges[undenfined][edit_request]">
		</div>
		<div class="iGroup">
			<label>{lang=DeleteRequest}</label>
			<input type="checkbox" name="privileges[undenfined][delete_request]">
		</div>
		<div class="iGroup">
			<label>{lang=ConfirmRequest}</label>
			<input type="checkbox" name="privileges[undenfined][confirm_request]">
		</div>
	</div>
	<div class="tab" id="ecommercePriv" data-title="E-commerce">
	
		<div class="sTitle">Stock</div>
		<div class="iGroup">
			<label>Add stock</label>
			<input type="checkbox" name="privileges[undenfined][add_stock]">
		</div>
		<div class="iGroup">
			<label>Delete stock</label>
			<input type="checkbox" name="privileges[undenfined][del_stock]">
		</div>
		<div class="iGroup">
			<label>Edit stock</label>
			<input type="checkbox" name="privileges[undenfined][edit_stock]">
		</div>
	
		<div class="sTitle">Slider</div>
		<div class="iGroup">
			<label>{lang=AddSlide}</label>
			<input type="checkbox" name="privileges[undenfined][add_slider]">
		</div>
		<div class="iGroup">
			<label>{lang=DeleteSlide}</label>
			<input type="checkbox" name="privileges[undenfined][del_slider]">
		</div>
		<div class="iGroup">
			<label>{lang=EditSlide}</label>
			<input type="checkbox" name="privileges[undenfined][edit_slider]">
		</div>
		
		<div class="sTitle">Pages</div>
		<div class="iGroup">
			<label>Add pages</label>
			<input type="checkbox" name="privileges[undenfined][add_pages]">
		</div>
		<div class="iGroup">
			<label>Delete pages</label>
			<input type="checkbox" name="privileges[undenfined][del_pages]">
		</div>
		<div class="iGroup">
			<label>Edit pages (with confirmation request)</label>
			<input type="checkbox" name="privileges[undenfined][edit_pages]">
		</div>
		<div class="iGroup">
			<label>Approve pages</label>
			<input type="checkbox" name="privileges[undenfined][approve_pages]">
		</div>
		
		<div class="sTitle">Blog</div>
		<div class="iGroup">
			<label>Add blog</label>
			<input type="checkbox" name="privileges[undenfined][add_blogs]">
		</div>
		<div class="iGroup">
			<label>Delete blog</label>
			<input type="checkbox" name="privileges[undenfined][del_blogs]">
		</div>
		<div class="iGroup">
			<label>Edit blog (with confirmation request)</label>
			<input type="checkbox" name="privileges[undenfined][edit_blogs]">
		</div>
		<div class="iGroup">
			<label>Approve blogs</label>
			<input type="checkbox" name="privileges[undenfined][approve_blogs]">
		</div>
		
		<div class="sTitle">Services</div>
		<div class="iGroup">
			<label>Add services</label>
			<input type="checkbox" name="privileges[undenfined][add_services]">
		</div>
		<div class="iGroup">
			<label>Delete services</label>
			<input type="checkbox" name="privileges[undenfined][del_services]">
		</div>
		<div class="iGroup">
			<label>Edit services (with confirmation request)</label>
			<input type="checkbox" name="privileges[undenfined][edit_services]">
		</div>
		<div class="iGroup">
			<label>Approve services</label>
			<input type="checkbox" name="privileges[undenfined][approve_services]">
		</div>
	</div>
	<div class="tab" id="organizerPriv" data-title="Organizer">
		<div class="iGroup">
			<label>Add people</label>
			<input type="checkbox" name="privileges[undenfined][organizer_add]">
		</div>
		<div class="iGroup">
			<label>Edit people</label>
			<input type="checkbox" name="privileges[undenfined][organizer_edit]">
		</div>
		<div class="iGroup">
			<label>Delete people</label>
			<input type="checkbox" name="privileges[undenfined][organizer_del]">
		</div>
	</div>
	<div class="tab" id="faqPriv" data-title="FAQ">
		<div class="iGroup">
			<label>Add question</label>
			<input type="checkbox" name="privileges[undenfined][faq_add]">
		</div>
		<div class="iGroup">
			<label>Edit question</label>
			<input type="checkbox" name="privileges[undenfined][faq_edit]">
		</div>
		<div class="iGroup">
			<label>Delete question</label>
			<input type="checkbox" name="privileges[undenfined][faq_del]">
		</div>
	</div>
	<div class="tab" id="cameraPriv" data-title="Camera">
		<div class="iGroup">
			<label>Add status</label>
			<input type="checkbox" name="privileges[undenfined][add_camera_status]">
		</div>
		<div class="iGroup">
			<label>Edit status</label>
			<input type="checkbox" name="privileges[undenfined][edit_camera_status]">
		</div>
		<div class="iGroup">
			<label>Delete activity</label>
			<input type="checkbox" name="privileges[undenfined][delete_camera_activity]">
		</div>
		<div class="iGroup">
			<label>Delete status</label>
			<input type="checkbox" name="privileges[undenfined][delete_camera_status]">
		</div>
	</div>
	<div class="tab" id="feedbacks" data-title="Feedbacks">
		<div class="iGroup">
			<label>Edit ratting</label>
			<input type="checkbox" name="privileges[undenfined][edit_fb_ratting]">
		</div>
	</div>
	<div class="tab" id="issuesPriv" data-title="Issues">
		<div class="iGroup">
			<label>Switch assigned staff</label>
			<input type="checkbox" name="privileges[undenfined][issue_assigned]">
		</div>
		<div class="iGroup">
			<label>Delete issue</label>
			<input type="checkbox" name="privileges[undenfined][del_issue]">
		</div>
		<div class="iGroup">
			<label>Can see issues from all stores</label>
			<input type="checkbox" name="privileges[undenfined][issues_show_all]">
		</div>
		<div class="iGroup">
			<label>Can see issues anywhere</label>
			<input type="checkbox" name="privileges[undenfined][issues_show_anywhere]">
		</div>
	</div>
	<div class="tab" id="chatPriv" data-title="Chat">
		<div class="iGroup">
			<label>Support chat</label>
			<input type="checkbox" name="privileges[undenfined][chat_support]">
		</div>
		<div class="iGroup">
			<label>Receive emails</label>
			<input type="checkbox" name="privileges[undenfined][email_receive]">
		</div>
		<div class="iGroup">
			<label>New email</label>
			<input type="checkbox" name="privileges[undenfined][email_new]">
		</div>
		<div class="iGroup">
			<label>Answer to email</label>
			<input type="checkbox" name="privileges[undenfined][email_answer]">
		</div>
		<div class="iGroup">
			<label>Delete email</label>
			<input type="checkbox" name="privileges[undenfined][email_delete]">
		</div>
	</div>
	<div class="tab" id="managePriv" data-title="Manage">
		<div class="iGroup">
			<label>Manager reports</label>
			<input type="checkbox" name="privileges[undenfined][manager_report]">
		</div>
	</div>
	<div class="tab" id="appointmentsPriv" data-title="Appointments">
		<div class="iGroup">
			<label>Confirm Appointment</label>
			<input type="checkbox" name="privileges[undenfined][confirm_appointment]">
		</div>
	</div>
	<div class="tab" id="workingPriv" data-title="Working time">
		<div class="iGroup">
			<label>Confirm working time</label>
			<input type="checkbox" name="privileges[undenfined][confirm_working_time]">
		</div>
	</div>
	<div class="tab" id="seo" data-title="SEO Standing">
		<div class="iGroup">
			<label>Access to the seo panel</label>
			<input type="checkbox" name="privileges[undenfined][seo_master]">
		</div>
		<div class="sTitle">404 errors</div>
		<div class="iGroup">
			<label>Fixed errors</label>
			<input type="checkbox" name="privileges[undenfined][seo_404_fixed]">
		</div>
		<div class="iGroup">
			<label>Delete errors</label>
			<input type="checkbox" name="privileges[undenfined][seo_404_del]">
		</div>
		<div class="sTitle">Images</div>
		<div class="iGroup">
			<label>Upload image</label>
			<input type="checkbox" name="privileges[undenfined][seo_images_upload]">
		</div>
		<div class="iGroup">
			<label>Compress image</label>
			<input type="checkbox" name="privileges[undenfined][seo_images_compress]">
		</div>
		<div class="sTitle">Redirects</div>
		<div class="iGroup">
			<label>Add redirect</label>
			<input type="checkbox" name="privileges[undenfined][seo_redirect_add]">
		</div>
		<div class="iGroup">
			<label>Edit redirect</label>
			<input type="checkbox" name="privileges[undenfined][seo_redirect_edit]">
		</div>
		<div class="iGroup">
			<label>Delete redirect</label>
			<input type="checkbox" name="privileges[undenfined][seo_redirect_del]">
		</div>
	</div>
	<div class="tab" id="multi_sending" data-title="Email multi sending">
		<div class="iGroup">
			<label>Access to the multi sending</label>
			<input type="checkbox" name="privileges[undenfined][multi_sending]">
		</div>
	</div>
	<div class="tab" id="archive_job" data-title="Archive job">
		<div class="iGroup">
			<label>View archive job</label>
			<input type="checkbox" name="privileges[undenfined][archive_job_view]">
		</div>
		<div class="iGroup">
			<label>Edit archive job</label>
			<input type="checkbox" name="privileges[undenfined][archive_job_edit]">
		</div>
		<div class="iGroup">
			<label>Delete archive job</label>
			<input type="checkbox" name="privileges[undenfined][archive_job_delete]">
		</div>
		<div class="iGroup">
			<label>Approve archive job</label>
			<input type="checkbox" name="privileges[undenfined][archive_job_approve]">
		</div>
	</div>
</div>
<script>
var hash = location.hash.split('#');
//Settings.privileges((hash[2] ? hash[2] : 2));

$(document).ready(function(){
	$('select[name="user_group"] > option[value="' + (hash[2] ? hash[2] : 2) + '"]').attr('selected', 'selected');
	$('select[name="user_group"]').change();
});
</script>