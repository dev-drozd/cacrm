<?php
return defined('ENGINE') ? 
array (
  'title' => 
  array (
    'en' => 'We know Computers',
    'ru' => 'Тестовый сайт',
    'uk' => 'Тестовий сайт',
  ),
  'description' => 
  array (
    'en' => 'We repair computers, laptops and any kind of high tech and smart devices. All in one solutions provider, from electronics repairs, to software development and marketing strategy! Free consultation.',
    'ru' => 'Описание тестового сайта',
    'uk' => 'Опис теставого сайту',
  ),
  'keywords' => 
  array (
    'en' => 'Computer repair shop, Apple, iPhone, Laptop, repair computer, repair tablet, repair iPhone, repair broken screen, repair laptop, repair laptop screen, virus removal',
    'ru' => 'Ключевое слово 1,Ключевое слово 2, Ключевое слово 3',
    'uk' => 'Ключове слово 1, Ключове слово 2, Ключове слово 3',
  ),
  'title_services' => 
  array (
    'en' => 'Your Company Services Page',
  ),
  'keywords_services' => 
  array (
    'en' => 'computer repair, virus removal, all services, Your Company service page',
  ),
  'description_services' => 
  array (
    'en' => 'Here are all of our services which we provide to our customers related to any job or technical work which our customers need performed',
  ),
  'title_blog' => 
  array (
    'en' => 'Blogs located here',
  ),
  'keywords_blog' => 
  array (
    'en' => 'blog related to pc, computer blogs, blogging it industry',
  ),
  'description_blog' => 
  array (
    'en' => 'The blog page',
  ),
  'admin_uri' => 'admin',
  'maxlifetime' => '90',
  'lang' => 'en',
  'multi_lang' => true,
  'lang_list' => 
  array (
    0 => 'en',
    1 => 'ru',
    2 => 'uk',
  ),
  'object_ips' => 
  array (
    2 => '172.100.110.127',
    3 => '142.105.136.120',
    4 => '108.44.39.202',
    5 => '24.213.252.134',
    6 => '50.245.10.101',
    7 => '71.105.25.10',
    0 => '818 CENTRAL AVE',
    8 => '66.108.186.15',
    9 => '',
  ),
  'user_points' => 
  array (
    'trade_in' => 
    array (
      'icon' => 'shopping-cart',
      'points' => 2.0,
      'selling' => 6.0,
    ),
    'make_transaction' => 
    array (
      'icon' => 'credit-card',
      'points' => 0.69999999999999996,
    ),
    'open_cash' => 
    array (
      'icon' => 'calculator',
      'points' => 0.5,
    ),
    'close_cash' => 
    array (
      'icon' => 'money',
      'points' => 0.5,
      'not_closed' => -0.5,
    ),
    'new_purchase' => 
    array (
      'icon' => 'cart-plus',
      'points' => 0.5,
      'points_return' => -3.0,
      'points_confirmation' => 1.0,
    ),
    'new_user' => 
    array (
      'icon' => 'user',
      'points' => 1.0,
    ),
    'new_service' => 
    array (
      'icon' => 'check-square-o',
      'points' => 1.0,
    ),
    'new_inventory' => 
    array (
      'icon' => 'laptop',
      'points' => 1.0,
      'sell_inventory' => 6.0,
    ),
    'punch_in' => 
    array (
      'icon' => 'play',
      'points' => 0.29999999999999999,
    ),
    'feedback' => 
    array (
      'icon' => 'paper-plane',
      'points' => 0.5,
      'email_points' => 0.5,
      'chat_points' => 0.5,
      'call_points' => 0.5,
      'user_points' => 0.5,
    ),
    'issue_forfeit' => 
    array (
      'icon' => 'suitcase',
      'points' => -1.0,
    ),
    'camera_statuses' => 
    array (
      'icon' => 'camera',
      'points' => 0,
    ),
    'user_suspention' => 
    array (
      'icon' => 'address-card',
      'points' => 0,
    ),
    'site_chat' => 
    array (
      'icon' => 'comments',
      'points' => 1,
    ),
    'ecommerce' => 
    array (
      'icon' => 'cart-plus',
      'points' => 0.0,
      'add_to_store' => 1.0,
    ),
    'appointment' => 
    array (
      'icon' => 'handshake-o',
      'points' => 1,
    ),
    'archive_job' => 
    array (
      'icon' => 'book',
      'points' => 5.0,
    ),
    'bugs_adding' => 
    array (
      'icon' => 'bug',
      'points' => 3.0,
    ),
  ),
  'refferal_points' => 5,
  'offline' => 0,
  'offline_msg' => 'The site is down and will be back up shortly. Thank you for your patience!',
  'cookies' => 30,
  'cache_host' => '/var/run/memcached/memcached.sock:0',
  'cache_key' => 'SCsCS$4s4sw',
  'cache_sel' => '1',
  'timezone' => 'US/Eastern',
  'user_group' => '2',
  'device_form' => '<div style=\\"width:780px;\\">    <div class=\\"uForm print\\">        <div class=\\"uTitle dClear\\">            <div class=\\"aCenter inv\\">                Invoice #{id}                <br>                    {date}                <br>                    {store_cell}            </div>            <div class=\\"uName wid50 aCenter\\">                {logo}                <br>                    {store_name}, {store_address}            </div>            <div class=\\"uName wid50 aCenter\\" style=\\"margin-top: 60px\\">                {invoice-barcode}                <br>                <b>                    {name}                </b>                <br>                    {address}            </div>        </div>        {invoices}        <div class=\\"tbl payInfo\\">            <div class=\\"tr\\">                <div class=\\"th\\">                    Item                </div>                <div class=\\"th w10\\">                    Qty                </div>                <div class=\\"th w100\\">                    Amount                </div>                <div class=\\"th w10\\">                    Tax                </div>            </div>            {onsite}{issues}{inventory}{purchases}{tradein}            <br>            <div class=\\"tr\\">                <div class=\\"td\\">                    {discount-name}                </div>                <div class=\\"td w10\\">                </div>                <div class=\\"td w100\\">                    {discount-percent}                </div>                <div class=\\"td w10\\">                </div>            </div>            <div class=\\"tr\\">                <div class=\\"td\\">                    {onsite-date}                </div>                <div class=\\"td w10\\">                </div>                <div class=\\"td w100\\">                                    </div>                <div class=\\"td w10\\">                </div>            </div>        </div>        <div class=\\"dClear\\">            <div class=\\"tbl payTotalInfo\\">                <div class=\\"tr\\">                    <div class=\\"td aRight invInfo\\">                        Subtotal                    </div>                    <div class=\\"td tAmount\\">                        $                        <span id=\\"subtotal\\">                            {subtotal}                        </span>                    </div>                </div>                <div class=\\"tr\\">                    <div class=\\"td aRight invInfo\\">                        Tax                    </div>                    <div class=\\"td tAmount\\">                        $                        <span id=\\"tax\\">                            {tax}                        </span>                    </div>                </div>                {service_charge}                <div class=\\"tr\\">                    <div class=\\"td aRight invInfo\\">                        Total                    </div>                    <div class=\\"td tAmount\\">                        $                        <span id=\\"total\\">                            {total}                        </span>                    </div>                </div>            </div>        </div>        <div class=\\"dClear\\">            <div class=\\"tbl payTotalInfo\\">                <div class=\\"tr\\">                    <div class=\\"td aRight invInfo\\">                        Paid                    </div>                    <div class=\\"td tAmount\\">                        $                        <span id=\\"paid\\">                            {paid}                        </span>                    </div>                </div>                <div class=\\"tr\\">                    <div class=\\"td aRight invInfo\\">                        Due                    </div>                    <div class=\\"td tAmount\\">                        $                        <span id=\\"due\\">                            {due}                        </span>                    </div>                </div>            </div>        </div>    </div>    <div class=\\"more\\">    </div></div>',
  'invoice_email' => '<h1 align=\\"center\\">Invoice</h1><h2 align=\\"center\\">{date}</h2><h3 align=\\"center\\">{store_cell}</h3> <br>{logo}<br><b>{name}</b><br>{address}<br><br><br>{invoices}<table border=\\"1\\"><tr><td width=\\"380\\">Item</td><td width=\\"100\\">Qty</td><td width=\\"200\\">Amount</td><td width=\\"100\\">Tax</td></tr>{onsite}{issues}{inventory}<br><tr> <td>{discount-name}</td><td width=\\"50\\"></td><td width=\\"100\\">{discount-percent}</td><td width=\\"50\\"></td></tr></table><br><br><table border=\\"1\\"><tr><td width=\\"200\\" align=\\"right\\">Subtotal</td><td width=\\"200\\" align=\\"right\\">${subtotal}</td></tr><tr><td width=\\"200\\" align=\\"right\\">Tax</td><td width=\\"200\\" align=\\"right\\">${tax}</td></tr><tr><td width=\\"200\\" align=\\"right\\">Total</td><td width=\\"200\\" align=\\"right\\">${total}</td></tr><tr><td width=\\"200\\" align=\\"right\\">Paid</td><td width=\\"200\\" align=\\"right\\">${paid}</td></tr><tr><td width=\\"200\\" align=\\"right\\">Due</td><td width=\\"200\\" align=\\"right\\">${due}</td></tr></table>',
  'intTitle' => 'Welcome to Your Company',
  'intContent' => '',
  'sms_onsite' => 'ssm form',
  'form_onsite' => '{logo} {customer_name} {customer_lastname} {customer_phone} {customer_address} {customer_email} {service_name} {price} {time_left} {used_time} {date} {service_start_date} {service_end_date}',
  'price_formula' => 
  array (
    50 => 60.0,
    10 => 125.0,
    100 => 50.0,
    1 => 35.0,
    200 => 30.0,
    300 => 25.0,
    400 => 20.0,
  ),
  'referral_points' => 
  array (
    1 => 1,
    2 => 3,
    3 => 5,
    10 => 15,
    20 => 23,
    30 => 34,
    40 => 50,
    100 => 150,
  ),
  'referral_discounts' => 
  array (
    0 => 5,
    10 => 10,
    25 => 12,
    50 => 15,
    100 => 20,
  ),
  'currency' => 
  array (
    'USD' => 
    array (
      'symbol' => '$',
    ),
    'UAH' => 
    array (
      'symbol' => '₴',
    ),
  ),
  'sms' => 
  array (
    'send_interval' => 1.6000000000000001,
    'send_limit' => 1000,
    'to_day' => 
    array (
      'date' => '09.08.20',
      'count' => 13,
    ),
  ),
  'quick_sell' => '56',
  'min_purchase' => '0',
  'tablet_user' => 'yoursite',
  'tablet_password' => '12345',
  'order_form' => '18',
  'min_forfeit' => '6',
  'login_id' => '6tk83YJK',
  'transaction_key' => '2LSBt2Fy88fy8v2s',
  'issue_min_total' => '5',
  'emails' => 
  array (
    0 => 
    array (
      'login' => 'admin@yoursite.com',
      'password' => '2Z8a4M2q',
      'default' => 1,
      'read' => 1,
    ),
    1 => 
    array (
      'login' => 'pavelz@yoursite.com',
      'password' => '2Z8a4M2q',
      'default' => 0,
      'read' => 0,
    ),
    2 => 
    array (
      'login' => 'info@yoursite.com',
      'password' => '2Z8a4M2q',
      'default' => 0,
      'read' => 0,
    ),
    3 => 
    array (
      'login' => 'voicemail@yoursite.com',
      'password' => '2Z8a4M2q',
      'default' => 0,
      'read' => 0,
    ),
  ),
  'camera_period' => '2019-02-01 / 2019-03-12',
  'date' => '',
  'fDate' => '',
  'cash_charge' => 0,
  'credit_charge' => 4,
  'min_lack' => '-30',
  'max_lack' => '30',
  'format_date' => 'y-h-d',
  'title_self_services' => 
  array (
    'en' => 'Self check in page',
  ),
  'keywords_self_services' => 
  array (
    'en' => 'enter customer info to checkin, customer self checkin',
  ),
  'description_self_services' => 
  array (
    'en' => 'This page includes the ability for the customer to be able to submit their information to add the job to our system',
  ),
  'title_store' => 
  array (
    'en' => 'Store page',
  ),
  'keywords_store' => 
  array (
    'en' => 'store computer repair, computer repair store',
  ),
  'description_store' => 
  array (
    'en' => 'The store computer repair page',
  ),
)
 : die("Hacking attempt!");
?>