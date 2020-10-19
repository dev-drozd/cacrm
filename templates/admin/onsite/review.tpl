<section class="mngContent fullw" id="onsite">

    <div class="sTitle">
        <span class="fa fa-chevron-right"></span>Onsite #{id}

        <a href="/im/34093?text=Onsite;{id}" onclick="Page.get(this.href); return false;" class="mesBtn"><span class="fa fa-exclamation-circle" aria-hidden="true"></span></a>

        <div class="uMore">
            <span class="togMore" onclick="$(this).next().show();">
				<span class="fa fa-ellipsis-v showMob"></span>
            <span class="showFll">Options</span>
            </span>
            <ul style="">
                <li id="view_invoice"><a href="/invoices/view/{invoice-id}" onclick="Page.get(this.href);"><span class="fa fa-credit-card"></span> View Invoice</a></li>

                <li><a href=""><span class="fa fa-times"></span> Delete</a></li>
            </ul>
        </div>
    </div>
    <div class="userInfo">

        <div class="userInfo">

            <div class="uTitle dClear">
                <figure>
                    <span class="fa fa-user-secret"></span>
                </figure>
                <div class="uName">
                    <div>
                        <p><a href="/users/view/{customer-id}" onclick="Page.get(this.href); return false;">{customer-name}</a> <a href="/users/edit/{customer-id}" onclick="Page.get(this.href); return false;" class="eBtn"><span class="fa fa-pencil"></span></a></p>
                        <p><b><a href="tel:{customer-phone}">{customer-phone}</a></b></p>
                        <p><a href="mailto:{customer-email}">{customer-email}</a></p>
						<br>
                        <p><i>{customer-address}</i></p>
                    </div>
                    <div class="slbSt" style="display: none;">
                        <div class="sl_st"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="uTitle dClear">
            <div class="uName flLeft">
				<p><a href="" target="_blank" class="eBtn">{service-name}</a> <a href="" target="_blank" class="eBtn"><span class="fa fa-pencil"></span></a></p>
				<p>
					<b>Date of service:</b> {service-date}
				</p>
                <div>
                    <div class="inv_info">
						<p>{issue}</p>
                    </div>
                </div>
            </div>
            <div class="dClear"></div>

        </div>

    </div>
</section>