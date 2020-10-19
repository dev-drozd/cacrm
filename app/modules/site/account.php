<?php
/**
 * @appointment Cabinet
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
 
switch($route[1]){
    
    /* 
    * Unsubscribe
    */
    case 'unsubscribe':
        $exp = explode('-', base64_decode($route[2]));
        tpl_set('account/unsubscribe', [
            'email' => $exp[0],
            'token' => $exp[1]
        ], [], 'content');
    break;


    /* 
    * Self services
    */
    case 'self-services':
        $meta['title'] = $config['title_self_services']['en'];
        $meta['description'] = $config['description_self_services']['en'];
        $meta['keywords'] = $config['keywords_self_services']['en'];
        echo tpl_set('account/self_services', $meta, []);
        die;
    break;
    
    /* 
    * Send appointment
    */
    case 'send_app':
        is_ajax() or die('Hacking attempt!');
        $user_id = intval($user['id']);
        $date = text_filter($_POST['date'], 10, false);
        $time = text_filter($_POST['time'], 5, false);
        $object = intval($_POST['object']);
        
        if (!$user_id)
            die('no_user');
        
        if (!$object)
            die('no_object');
        
        db_query('
            INSERT INTO `'.DB_PREFIX.'_users_appointments` SET 
                customer_id = \''.$user_id.'\',
                date = \''.$date.' '.$time.'\',
                object_id = \''.$object.'\'
        ');
        
        $id = intval(mysqli_insert_id($db_link));
        
        db_query('
            INSERT INTO `'.DB_PREFIX.'_notifications` SET 
                type = \'new_appointment\', 
                customer_id = '.$user_id.', 
                id = '.$id
        );
        
        $cid = db_multi_query('
            SELECT 
                u.sms,
                o.name
            FROM `'.DB_PREFIX.'_users` u,
                `'.DB_PREFIX.'_objects` o
            WHERE u.id = '.$user_id.' AND o.id = '.$object
        );
        
        if ($cid['sms'] AND $user_id != 17)
            send_sms($cid['sms'], 'You have successfully scheduled a free diagnosis appointment at Your Company of '.$cid['name'].' on '.date('l', strtotime($date)).' '.date('F jS', strtotime($date)).' at '.date('hA', strtotime($date)));
            
        die('OK');
    break;
    
    /*
    * Send service
    */
    case 'send_service':
        $id = intval($_POST['id']);
        $service_id = intval($_POST['service']);
        $object_id = intval($_POST['object']);
        $time = text_filter($_POST['time'], 8, false);
        $date_start = text_filter($_POST['date_start'], 20, false);
        $date_end = text_filter($_POST['date_end'], 20, false);
        $date_ex = explode('-', $date_end);
        
        $now = date('Y-m-d H:m:s', time());
        $service = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_inventory_onsite` WHERE id = '.$service_id);
        $tax = db_multi_query('SELECT tax FROM `'.DB_PREFIX.'_objects` WHERE id = '.$object_id);
        
        db_query('INSERT INTO `'.DB_PREFIX.'_users_onsite` SET
            customer_id = '.$id.',
            staff_id = '.$user['id'].',
            onsite_id = '.$service_id.',
            date = \''.$now.'\',
            date_start = \''.$date_start.'\',
            date_end = \''.$date_end.'\',
            date_control = \''.$now.'\',
            left_time = '.($service['time'] * 3600).',
            calls = '.$service['calls'].',
            last_object = '.$object_id.',
            last_event = \'create\',
            selected_time = \''.$time.'\',
            update_date = \''.($date_start ?: date('Y-m-d', time())).'\'
        ');
        
        db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count + 1 WHERE name = \'e_onsite\'');
        
        echo 'OK';
        die;
    break;
    
    /*
    *  Add issue
    */
    case 'add_issue':
        is_ajax() or die('Hacking attempt!');
        $i_id = intval($_POST['id']);
        $store_id = intval($_POST['store']);
        
        db_query(
            'INSERT INTO `'.DB_PREFIX.'_issues` SET
                inventory_id =\''.$i_id.'\',
                customer_id =\''.$user['id'].'\',
                object_owner =\''.$store_id.'\',
                description =\''.text_filter($_POST['descr'], null, false).'\''
        );
        
        $id = intval(
            mysqli_insert_id($db_link)
        );
        
        db_query(
            'UPDATE `'.DB_PREFIX.'_inventory` SET
                object_owner =\''.$store_id.'\',
                object_id =\''.$store_id.'\',
                status_id = 11
            WHERE id = '.$i_id
        );
        
        db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count + 1 WHERE name = \'e_issues\'');

        print_r(json_encode([
            'id' => $id,
            'date' => date('Y-m-d h:m:s')
        ], true));
        die;
    break;
    
    /*
    *  Del inventory
    */
    case 'del_inventory':
        is_ajax() or die('Hacking attempt!');
        $id = intval($_POST['id']);
        db_query('DELETE FROM `'.DB_PREFIX.'_inventory` WHERE id = '.$id);
        if(mysqli_affected_rows($db_link)){
            exit('OK');
        } else
            exit('ERR');
    break;
    
    /*
    * On site all
    */
    case 'AllOnsite':
        is_ajax() or die('Hacking attempt!');
        
        $lId = intval($_POST['lId']);
        $oId = intval($_POST['oId']);
        $nIds = ids_filter($_POST['nIds']);
        $query = text_filter($_POST['query'], 100, false);
        $all = db_multi_query('
            SELECT SQL_CALC_FOUND_ROWS
                id,
                name,
                price,
                currency
            FROM `'.DB_PREFIX.'_inventory_onsite` 
            WHERE confirmed = 1 AND (FIND_IN_SET('.$oId.', object_id) OR object_id = \'\')'.(
                $nIds ? ' AND id NOT IN('.$nIds.')' : ''
            ).(
                $lId ? ' AND id < '.$lId : ''
            ).(
                $query ? ' AND name LIKE \'%'.$query.'%\'' : ''
            ).' ORDER BY id DESC LIMIT 20', true
        );
        if ($all) {
            $all = array_map(function($a) use($config) {
                $a['currency'] = $config['currency'][$a['currency'] ?: 'USD']['symbol'];
                return $a;
            }, $all);
        }
        $res_count = intval(
            mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]
        );
        
        die(json_encode([
            'list' => $all,
            'count' => $res_count,
        ]));
        
        die;
    break;
    
    /*
    * Select categories
    */
    case 'allCategories':
        $id = intval($_POST['id']);
        $lId = intval($_POST['lId']);
        $nIds = ids_filter($_POST['nIds']);
        if($_REQUEST['q'])
            $_REQUEST['query'] = $_REQUEST['q'];
        $query = text_filter($_POST['query'], 100, false);
        $categories = db_multi_query('
            SELECT SQL_CALC_FOUND_ROWS tb1.id, CONCAT(
            IFNULL(tb2.name, \'\'), IF(
                tb2.name IS NOT NULL, \' <span class="fa fa-angle-right"></span> \', \'\'
            ), tb1.name
            ) as name
                FROM `'.DB_PREFIX.'_inventory_categories` tb1
                LEFT JOIN `'.DB_PREFIX.'_inventory_categories` tb2
                ON tb1.parent_id = tb2.id
                WHERE 1'.(
                $lId ? ' AND tb1.id < '.$lId : ''
            ).(
                $query ? ' AND (tb1.name LIKE \'%'.$query.'%\' OR tb2.name LIKE \'%'.$query.'%\')' : ''
            ).(
                in_array($_POST['type'], ['service', 'inventory']) ? ' AND tb1.type = \''.$_POST['type'].'\' ' : ''
            ).($nIds ? ' AND tb1.id NOT IN('.$nIds.')' : '').(
                $id ? ' AND tb1.id != '.$id.' AND tb1.parent_id != '.$id : ''
            ).' ORDER BY tb1.id DESC LIMIT 20', true
        );
        
        // Get count
        $res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
        die(json_encode([
            'list' => $categories,
            'count' => $res_count,
        ]));
    break;
    
    /*
    * Select categories
    */
    case 'allCategories2':
        $id = intval($_REQUEST['id']);
        $lId = intval($_REQUEST['lId']);
        $nIds = ids_filter($_REQUEST['nIds']);
        if($_REQUEST['q'])
            $_REQUEST['query'] = $_REQUEST['q'];
        $query = text_filter($_REQUEST['query'], 100, false);
        $categories = db_multi_query('
            SELECT SQL_CALC_FOUND_ROWS tb1.id, CONCAT(
            IFNULL(tb2.name, \'\'), IF(
                tb2.name IS NOT NULL, \' <span class="fa fa-angle-right"></span> \', \'\'
            ), tb1.name
            ) as name
                FROM `'.DB_PREFIX.'_inventory_categories` tb1
                LEFT JOIN `'.DB_PREFIX.'_inventory_categories` tb2
                ON tb1.parent_id = tb2.id
                WHERE 1'.(
                $lId ? ' AND tb1.id < '.$lId : ''
            ).(
                $query ? ' AND (tb1.name LIKE \''.$query.'%\' OR tb2.name LIKE \''.$query.'%\')' : ''
            ).($nIds ? ' AND tb1.id NOT IN('.$nIds.')' : '').(
                $id ? ' AND tb1.id != '.$id.' AND tb1.parent_id != '.$id : ''
            ).' ORDER BY tb1.name ASC LIMIT 20', true
        );
        
        // Get count
        $res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
        die(json_encode([
            'list' => $categories,
            'count' => $res_count,
        ]));
    break;
    
    /*
    * Get all statuses
    */
    case 'allStatuses':
        $id = intval($_POST['id']);
        $lId = intval($_POST['lId']);
        $nIds = ids_filter($_POST['nIds']);
        $query = text_filter($_POST['query'], 100, false);
        $statuses = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name FROM `'.DB_PREFIX.'_inventory_status` WHERE 1'.(
            $lId ? ' AND id < '.$lId : ''
        ).(
            $query ? ' AND `name` \'%'.$query.'%\'': ''
        ).($nIds ? ' AND id NOT IN('.$nIds.')' : '').' ORDER BY `id` DESC LIMIT 20', true);
        $res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
        die(json_encode([
            'list' => $statuses,
            'count' => $res_count,
        ]));
    break;
    
    /*
    * Get all OS
    */
    case 'allOS':
        $id = intval($_POST['id']);
        $lId = intval($_POST['lId']);
        $nIds = ids_filter($_POST['nIds']);
        $query = text_filter($_POST['query'], 100, false);
        $statuses = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name FROM `'.DB_PREFIX.'_inventory_os` WHERE 1'.(
            $lId ? ' AND id < '.$lId : ''
        ).(
            $query ? ' AND `name` \'%'.$query.'%\'': ''
        ).($nIds ? ' AND id NOT IN('.$nIds.')' : '').' ORDER BY `id` DESC LIMIT 20', true);
        $res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
        die(json_encode([
            'list' => $statuses,
            'count' => $res_count,
        ]));
    break;
    
    /*
    * Get all Models
    */
    case 'allModels':
        $id = intval($_POST['id']);
        $lId = intval($_POST['lId']);
        $nIds = ids_filter($_POST['nIds']);
        $brand = intval($_POST['brand']);
        if($_REQUEST['q'])
            $_REQUEST['query'] = $_REQUEST['q'];
        $query = text_filter($_POST['query'], 100, false);
        $models = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name FROM `'.DB_PREFIX.'_inventory_models` WHERE 1'.(
            $lId ? ' AND id < '.$lId : ''
        ).(
            $brand ? ' AND category_id = '.$brand : ' AND category_id = 0'
        ).(
            $query ? ' AND `name`\'%'.$query.'%\'': ''
        ).($nIds ? ' AND id NOT IN('.$nIds.')' : '').' ORDER BY `name` ASC LIMIT 20', true);
        $res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
        die(json_encode([
            'list' => $models,
            'count' => $res_count,
        ]));
    break;
    
    /*
    * Get all Models
    */
    case 'allModels2':
        is_ajax() or die('Hacking attempt!');
        $id = intval($_REQUEST['id']);
        $lId = intval($_REQUEST['lId']);
        $nIds = ids_filter($_REQUEST['nIds']);
        $type = intval($_REQUEST['type']);
        if($_REQUEST['q'])
            $_REQUEST['query'] = $_REQUEST['q'];
        $query = text_filter($_REQUEST['query'], 100, false);
        $models = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name FROM `'.DB_PREFIX.'_inventory_models` WHERE 1'.(
            $lId ? ' AND id < '.$lId : ''
        ).(
            $type ? ' AND category_id = '.$type : ' AND category_id < 0'
        ).(
            $query ? ' AND name LIKE \''.$query.'%\'': ''
        ).($nIds ? ' AND id NOT IN('.$nIds.')' : '').' ORDER BY `name` ASC LIMIT 20', true);
        $res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
        die(json_encode([
            'list' => $models,
            'count' => $res_count,
        ]));
    break;
    
    /*
    * Get all types
    */
/*     case 'allTypes':
        $id = intval($_POST['id']);
        $lId = intval($_POST['lId']);
        $nIds = ids_filter($_POST['nIds']);
        if($_REQUEST['q']) $_REQUEST['query'] = $_REQUEST['q'];
        $query = text_filter($_POST['query'], 100, false);
        $type = text_filter($_POST['type'], 100, false) ?: 'inventory';
        
        $types = db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
            t.id, t.name
        FROM `'.DB_PREFIX.'_inventory_types` t 
        WHERE 1'.(
            $lId ? ' AND t.id < '.$lId : ''
        ).(
            $query ? ' AND t.name LIKE \'%'.$query.'%\'': ''
        ).($nIds ? ' AND t.id NOT IN('.$nIds.')' : '').' ORDER BY t.id DESC LIMIT 20', true);
        $res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
        die(json_encode([
            'list' => $types,
            'count' => $res_count,
        ]));
    break; */
    
    case 'allTypes':
        $id = intval($_REQUEST['id']);
        $lId = intval($_REQUEST['lId']);
        $nIds = ids_filter($_REQUEST['nIds']);
        if($_REQUEST['q'])
            $_REQUEST['query'] = $_REQUEST['q'];
        $query = text_filter($_REQUEST['query'], 100, false);
        $name = intval($_REQUEST['name']);
        $type = text_filter($_REQUEST['type'], 100, false) ?: 'inventory';
        
        $types = db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
            t.id, t.name
        FROM `'.DB_PREFIX.'_inventory_types` t 
        WHERE 1'.(
            $lId ? ' AND t.id < '.$lId : ''
        ).(
            $query ? ' AND t.name LIKE \''.$query.'%\'': ''
        ).($nIds ? ' AND t.id NOT IN('.$nIds.')' : '').' '.($_REQUEST['q'] ? '' : 'ORDER BY t.name ASC').' LIMIT 20', true);
        $res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
        die(json_encode([
            'list' => $types,
            'count' => $res_count,
        ]));
    break;
    
    /*
    * Get objects
    */
    case 'allObjects':
        $lId = intval($_POST['lId']);
        $nIds = ids_filter($_POST['nIds']);
        $query = text_filter($_POST['query'], 100, false);
        $objects = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name FROM `'.DB_PREFIX.'_objects` WHERE 1'.(
            $nIds ? ' AND id NOT IN('.$nIds.')' : ''
        ).(
            $lId ? ' AND id < '.$lId : ''
        ).(
            $query ? ' AND name LIKE \'%'.$query.'%\'': ''
        ).' ORDER BY `id` DESC LIMIT 20', true);
        
        // Get count
        $res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
        die(json_encode([
            'list' => $objects,
            'count' => $res_count,
        ]));
    break;
    
    /*
    * Send user
    */
    case 'editUser':
        is_ajax() or die('hacking');
        $id = intval($user['id']);
        $type = $id ? 'edit' : 'add';
        
        // Filters
        $name = text_filter($_POST['name'], 25, false);
        $lastname = text_filter($_POST['lastname'], 25, false);
        $phone = text_filter($_POST['phone'], 250, false);
        $address = text_filter($_POST['address'], 255, false);
        $ver = text_filter($_POST['addressConf'], 10, false);
        $email = text_filter($_POST['email'], 50, false);
        $password = text_filter($_POST['password'], 32, false);
        
        // Check
        preg_match("/^[a-zA-Za-яА-ЯїЇіІЄєЎўҐґ'0-9-_\.\s`]{1,255}$/iu", $_POST['name']) or die('Name_not_valid');
        preg_match("/^[a-zA-Za-яА-ЯїЇіІЄєЎўҐґ'0-9-_\.\s`]{1,70}$/iu", $_POST['lastname']) or die('Lastname_not_valid');
        preg_match("/^[0-9-(-+,\s]+$/", $phone) or die('phone_not_valid');
        $e = db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_users` WHERE email = \''.$email.'\''.(
            $id ? ' AND id != '.$id : ''
        ));
        if(!filter_var($email, FILTER_VALIDATE_EMAIL) OR $e['count'] > 0){
            echo 'err_email';
            die;            
        }
        $sql = '';
        if($_POST['del_image']) $sql .= ' image = \'\',';
        if($password) $sql .= ' hid = \''.md5(md5($password).md5($_SERVER['REMOTE_ADDR']).time()).'\', password = \''.md5(md5($_POST['password'])).'\',';
        db_query((
            $id ? 'UPDATE' : 'INSERT INTO'
        ).' `'.DB_PREFIX.'_users` SET
                name = \''.$name.'\',
                lastname = \''.$lastname.'\',
                phone = \''.$phone.'\',
                sms = \''.text_filter($_POST['sms'], 20, false).'\',
                address = \''.$address.'\''.$sql.',
                email = \''.$email.'\''.(
            $id ? ' WHERE id = '.$id : ''
        ));
        
        $id = $id ? $id : intval(mysqli_insert_id($db_link));
        
        // Is file upload
        if($_FILES){
            
            // Upload max file size
            $max_size = 10;
            
            // path
            $dir = ROOT_DIR.'/uploads/images/';
            
            // Is not dir
            if(!is_dir($dir.$id)){
                @mkdir($dir.$id, 0777);
                @chmod($dir.$id, 0777);
            }
            
            $dir = $dir.$id.'/';
            
            // temp file
            $tmp = $_FILES['image']['tmp_name'];
            
            $type = mb_strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            
            if($_FILES['image']['size'] >= 1024*$max_size*1024){
                echo 'err_file_size';
                die;
            }
            
            // New name
            $rename = $_FILES['image']['name'].'.'.$type;
            
            // Upload image
            if(move_uploaded_file($tmp, $dir.$rename)){
                
                $img = new Imagick($dir.$rename);
                
                // 1920
                if($img->getImageWidth() > 1920){
                    $img->resizeImage(1920, 0, imagick::FILTER_LANCZOS, 0.9);
                    auto_rotate_image($img);
                    $img->stripImage();
                    $img->writeImage($dir.$rename);
                }
                
                // 300x300
                $img->cropThumbnailImage(300, 300);
                $img->stripImage();
                $img->writeImage($dir.'preview_'.$rename);
                
                // 94x94
                $img->cropThumbnailImage(94, 94);
                $img->stripImage();
                $img->writeImage($dir.'thumb_'.$rename);
                $img->destroy();
                
                db_query('UPDATE `'.DB_PREFIX.'_users` SET image = \''.$rename.'\' WHERE id = '.$id);
            }
        }
        echo $id;
        die;
    break;
    
    /*
    * Edit user
    */
    case 'edit': 
        $id = $user['id'];
        if ($id) {
            $meta['title'] = 'Edit profile';
        
            if($id) $u = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_users` WHERE id = '.$id);
            
            $phones = '';
            $i = 0;
            foreach(explode(',', $u['phone']) as $ph) {
                $n = explode(' ', $ph);
                $phones .= '<div class="sPhone">
                            <span class="fa fa-times rd'.($i == 0 ? ' hide' : '').'" onclick="'.($i == 0 ? '' : '$(this).parent().remove();').'"></span>
                            <select name="phoneCode">
                                <option value="0">None</option>
                                <option value="+1"'.($n[0] == '+1' ? ' selected' : '').'>+1</option>
                                <option value="+3"'.($n[0] == '+3' ? ' selected' : '').'>+3</option>
                            </select>
                            <span class="wr">(</span>
                            <input type="number" name="code" onkeyup="phones.next(this, 3);" value="'.$n[1].'" max="">
                            <span class="wr">)</span>
                            <input type="number" name="part1" onkeyup="phones.next(this, 7);" value="'.$n[2].'">
                            <input type="number" name="part2" value="'.$n[3].'">
                            <input type="checkbox" name="sms" onchange="phones.onePhone(this);" '.(trim($ph) === $u['sms'] ? ' checked' : '').'>
                        </div>';
                $i ++;
            }
            
            tpl_set('account/form', [
                'id' => $id,
                'address' => $u['address'],
                'email' => $u['email'],
                'name' => $u['name'],
                'phone' => $phones,
                'lastname' => $u['lastname'],
                'image' => $u['image']
            ], [
                'photo' => $u['image'],
                'no-phone' => !$u['phone']
            ], 'content');
        } else {
            tpl_set('login', [
            ], [
            ], 'content');
        }    
    break;
    
    /*
    *  Send inventory
    */
    case 'send_inventory':
        is_ajax() or die('Hacking attempt!');
        $id = intval($_POST['id']);
        
        db_query((
            $id ? 'UPDATE' : 'INSERT INTO'
        ).' `'.DB_PREFIX.'_inventory` SET
                type_id =\''.text_filter($_POST['type'], 50, false).'\',
                model =\''.text_filter($_POST['model'], 50, false).'\',
                ver_os =\''.text_filter($_POST['os_version'], 50, false).'\',
                serial =\''.text_filter($_POST['serial'], 50, false).'\',
                customer_id = \''.intval($user['id']).'\',
                object_id = \''.intval($_POST['object']).'\',
                status_id = \'11\',
                category_id = \''.intval($_POST['category']).'\',
                os_id = \''.intval($_POST['os']).'\',
                type = \'stock\''.(
                    $id ? ' WHERE id = '.$id : ''
            )
        );
        
        $id = $id ? $id : intval(
            mysqli_insert_id($db_link)
        );

        echo $id;

        die;
    break;
    
    case 'send_self_services':
        $name = trim(text_filter($_POST['name'], 25, false));
        $lastname = trim(text_filter($_POST['lastname'], 25, false));
        $email = text_filter($_POST['email'], 50, false);
        $phone = text_filter(str_ireplace(['(',')','-'], '', $_POST['phone']), 255, false);
        $zipcode = text_filter($_POST['zipcode'], 255, false);
        $address = text_filter($_POST['address'], 255, false);
        
        $device_password = text_filter($_POST['device_password'], 255, false);
        
        if($name && $lastname && $email && $phone && $zipcode){
            preg_match("/^[0-9-(-+,\s]+$/", $phone) or die('phone_not_valid');
            $e = db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_users` WHERE email = \''.$email.'\' AND del = 0');
            if(!filter_var($email, FILTER_VALIDATE_EMAIL) OR ($e['count'] > 0 AND $email != 'noreply@yoursite.com')){
                echo 'Email exists';
                die;            
            }
            
            $password = substr(md5(uniqid()), 0, 6);
            $hid = md5(md5($password).md5($_SERVER['REMOTE_ADDR']).time());
                
            db_query('INSERT INTO `'.DB_PREFIX.'_users` SET
                name = \''.$name.'\',
                lastname = \''.$lastname.'\',
                phone = \''.$phone.'\',
                sms = \''.$phone.'\',
                group_ids = 5,
                address = \''.$address.'\',
                zipcode = \''.$zipcode.'\',
                email = \''.$email.'\',
                hid = \''.$hid.'\',
                password = \''.md5(md5($password)).'\''
            );
            
            // Headers
            $headers  = 'MIME-Version: 1.0'."\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
            $headers .= 'To: '.$email. "\r\n";
            $headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";

            // Send
            mail($email, 'Registering to Your Company', '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>Registering to Your Company</title>
            </head>
            <body style="background: #f6f6f6; text-align: center;">
                <div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 600px; max-width: 100%; background: #ffffff; border: 1px solid #ddd; padding: 20px; font-family: monospace; font-size: 14px; line-height: 24px; color: #828282; text-align: center; margin: 30px auto;">
                    <div style="margin: -20px -20px 0; padding: 20px;">
                        <a href="https://yoursite.com/">
                            <img src="https://yoursite.com/templates/site/img/logo.png" style="width: 60%; margin: 25px 0;">
                        </a>
                    </div>
                    <div style="padding: 0 30px 30px;">
                        <p>Thank you for registering with <br><b>Your Company inc</b>.</p>
                        <p>Please use the details below for entrance to new platform:</p>
                        <div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 300px; background: #f1f8fb; padding: 30px; color: #768b94; text-align: left; max-width: 100%; margin: 30px auto 0;">
                            Link: <a href="https://yoursite.com" style="color: #0e92d4;">Click here</a><br>
                            Login: '.$email.'<br>
                            Password: '.$password.'
                        </div>
                    </div>
                </div>
            </body>
            </html>', $headers);
            
            $customer_id = (int)mysqli_insert_id($db_link);
            
            if(isset($_FILES['photo']) AND $_FILES['photo']['error'] == 0){
                
                // Upload max file size
                $max_size = 25;
                
                // path
                $dir = ROOT_DIR.'/uploads/images/users/';
                
                // Is not dir
                if(!is_dir($dir.$customer_id)){
                    @mkdir($dir.$customer_id, 0777);
                    @chmod($dir.$customer_id, 0777);
                }
                
                $dir = $dir.$customer_id.'/';
                
                // temp file
                $tmp = $_FILES['photo']['tmp_name'];
                
                $type = mb_strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                
                if($_FILES['photo']['size'] >= 1024*$max_size*1024){
                    echo 'Maximum image size 25MB';
                    die;
                }
                
                // New name
                $rename = uniqid('', true).'.'.$type;
                
                // Upload image
                if(move_uploaded_file($tmp, $dir.$rename)){
                    
                    $img = new Imagick($dir.$rename);
                    
                    // 1920
                    if($img->getImageWidth() > 1920){
                        $img->resizeImage(1920, 0, imagick::FILTER_LANCZOS, 0.9);
                    }
                    
                    auto_rotate_image($img);
                    
                    $img->stripImage();
                    
                    $img->writeImage($dir.$rename);
                    
                    // 300x300
                    $img->cropThumbnailImage(300, 300);
                    $img->writeImage($dir.'preview_'.$rename);
                    
                    // 94x94
                    $img->cropThumbnailImage(94, 94);
                    $img->writeImage($dir.'thumb_'.$rename);
                    $img->destroy();
                    
                    db_query('UPDATE `'.DB_PREFIX.'_users` SET image = \''.$rename.'\' WHERE id = '.$customer_id);
                }
            }
        }
        
        // INVENTORY
        $type_id = (int)$_POST['type_id'];
        $brand = (int)$_POST['brand'];
        $model = (int)$_POST['model'];
        $store_id = intval($_POST['store_id']);
        
        // Create inventory
        if($type_id && ($brand OR $_POST['brand-new']) && ($model OR $_POST['model-new'])){
            
            if($_POST['brand-new']){
                $cat = [];
                $cat = db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_inventory_categories` WHERE name = \''.trim(text_filter($_POST['brand-new'], 100, false)).'\'');
                if (!$cat){
                    $brand = db_query('INSERT INTO `'.DB_PREFIX.'_inventory_categories` SET name = \''.trim(text_filter($_POST['brand-new'], 100, false)).'\'');
                    $brand = intval(
                        mysqli_insert_id($db_link)
                    );
                } else
                    $brand = $cat['id'];
            }
            
            if($_POST['model-new']){
                $mdl = [];
                $mdl = db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_inventory_models` WHERE name = \''.trim(text_filter($_POST['model-new'], 100, false)).'\' AND category_id = '.$brand);
                if(!$mdl){
                    $model = db_query('INSERT INTO `'.DB_PREFIX.'_inventory_models` SET name = \''.trim(text_filter($_POST['model-new'], 100, false)).'\''.(
                        $brand ? ', category_id = '.$brand : ''
                    ));
                    $model = intval(
                        mysqli_insert_id($db_link)
                    );
                } else
                    $model = $mdl['id'];
            }
            
            db_query('INSERT INTO `'.DB_PREFIX.'_inventory` SET
                opt_charger =\'NO\',
                device_password =\''.$device_password.'\',
                model_id =\''.$model.'\',
                currency = \'USD\',
                type_id = \''.$type_id.'\',
                customer_id = \''.intval($customer_id).'\',
                object_id = \''.$store_id.'\',
                object_owner = \''.$store_id.'\',
                category_id = \''.$brand.'\',
                type = \'stock\',
                cr_date = Now()'
            );
            $inventory_id = (int)mysqli_insert_id($db_link);
        }
        
        // ISSUE
        db_query('INSERT INTO `'.DB_PREFIX.'_issues` SET
            customer_id = '.$customer_id.',
            object_owner = '.$store_id.',
            date = Now(),
            status_id = 11,
            description = \''.text_filter($_POST['issue'], 2000, false).'\',
            currency = \'USD\',
            inventory_id = '.$inventory_id
        );
        
        $issue_id = (int)mysqli_insert_id($db_link);
        
        db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
            issue_id = '.$issue_id.',
            date = Now(),
            user = 0,
            changes = \'New job\',
            object_id = '.$store_id
        );
        db_query('UPDATE `'.DB_PREFIX.'_inventory` SET is_issue = 1, pickup = 0 WHERE id = '.$inventory_id);
        echo 'OK';
        die;
    break;
    
    /*
    *  View cabinet
    */
    default:
        $id = $user['id'];
        /* if ($id == 17)
            $id = 2; */
        if ($id) {
            if($row = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_users` WHERE id = '.$id)){
                $meta['title'] = $row['name'].' '.$row['lastname'];
                /*if($devices = db_multi_query('
                        SELECT SQL_CALC_FOUND_ROWS
                            tb1.id as id, 
                            tb1.model,
                            tb1.type_id,
                            tb1.category_id,
                            tb1.os_id,
                            tb1.ver_os,
                            tb1.serial,
                            tb2.name as type_name,
                            tb3.name as location_name,
                            tb4.name as os_name,
                            tb6.name as category_name,
                            tb7.id as user_id,
                            tb7.name,
                            tb7.lastname,
                            tb7.phone,
                            tb7.address,
                            tb7.email,
                            tb8.name as status
                        FROM `'.DB_PREFIX.'_inventory` tb1
                        LEFT JOIN `'.DB_PREFIX.'_inventory_types` tb2
                            ON tb1.type_id = tb2.id
                        LEFT JOIN `'.DB_PREFIX.'_objects_locations` tb3
                            ON tb1.location_id = tb3.id
                        LEFT JOIN `'.DB_PREFIX.'_inventory_os` tb4
                            ON tb1.os_id = tb4.id
                        LEFT JOIN `'.DB_PREFIX.'_inventory_types` tb5
                            ON tb1.type_id = tb5.id
                        LEFT JOIN `'.DB_PREFIX.'_inventory_categories` tb6
                            ON tb1.category_id = tb6.id
                        LEFT JOIN `'.DB_PREFIX.'_inventory_status` tb8
                            ON tb1.status_id = tb8.id
                        LEFT JOIN `'.DB_PREFIX.'_users` tb7
                            ON tb7.id = '.$id.'
                        WHERE tb1.del = 0 AND tb1.accessories = 0 AND tb1.customer_id = '.$id, true
                    )){
                        foreach($devices as $item){
                            foreach(db_multi_query(
                                'SELECT i.* FROM `'.DB_PREFIX.'_issues` i WHERE inventory_id = '.$item['id']
                            , true) as $issue){
                                tpl_set('/account/invIssue', [
                                    'id' => $issue['id'],
                                    'description' => $issue['description'],
                                    'date' => $issue['date']
                                ], [], 'issues');
                            }
                            tpl_set('/account/inventory', [
                                'id' => $item['id'],
                                'user-name' => $u['name'],
                                'user-lastname' => $u['lastname'],
                                'name' => $item['name'],
                                'model' => $item['model'],
                                'serial' => $item['serial'],
                                'status' => $item['status'],
                                'type-id' => $item['type_id'],
                                'category-id' => $item['category_id'],
                                'os-id' => $item['os_id'],
                                'os-ver' => $item['ver_os'],
                                'os' => $item['os_name'],
                                'category' => $item['category_name'],
                                'location' => $item['location_name'],
                                'issues' => $tpl_content['issues'],
                                'type' => $item['type_name']
                            ], [
                                'has-issue' => $tpl_content['issues']
                            ], 'devices');
                            unset($tpl_content['issues']);
                        }
                    }
            }
            
            foreach(db_multi_query(
                'SELECT id, date, total, paid, conducted FROM `'.DB_PREFIX.'_invoices` WHERE customer_id = '.$id
            , true) as $invoice){
                tpl_set('/account/usInvoice', [
                    'id' => $invoice['id'],
                    'date' => $invoice['date'],
                    'total' => '$'.number_format($invoice['total'], 2, '.', ''),
                    'paid' => '$'.number_format($invoice['paid'], 2, '.', ''),
                    'due' => '$'.number_format($invoice['total'] - $invoice['paid'], 2, '.', ''),
                    'status' => $invoice['conducted'] ? 'Paid' : 'Unpaid',
                    'date' => $invoice['date']
                ], [], 'invoices');
            }

            if ($onsite = db_multi_query('
                SELECT 
                    o.*,
                    SEC_TO_TIME(TIMESTAMPDIFF(SECOND, \''.date('Y-m-d H:i:s', time()).'\', o.date_end)) as date_left,
                    s.name as service_name,
                    i.id as add_invoice
                FROM `'.DB_PREFIX.'_users_onsite` o
                LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` s
                    ON s.id = o.onsite_id
                LEFT JOIN `'.DB_PREFIX.'_invoices` i
                    ON o.id = i.add_onsite
                WHERE o.customer_id = '.$id, true
            )) {
                foreach($onsite as $os) {
                    $date = explode('-', $os['date_end']);
                    tpl_set('/account/onsite', [
                        'id' => $os['id'],
                        'onsite_id' => $os['onsite_id'],
                        'name' => $os['service_name'],
                        'time_left' => $os['left_time'],
                        'date_left' => intval($date[0]) ? (intval($os['date_left']) >= 0 ? $os['date_left'] : 0) : '<span class="inf">&infin;</span>',
                        'date_start' => strtotime($os['date_start']) > 0 ? $os['date_start'] : '<span class="inf">&infin;</span>',
                        'date_end' => strtotime($os['date_end']) > 0 ? $os['date_end'] : '<span class="inf">&infin;</span>'
                    ], [
                        'use' => intval($date[0]) ? ($os['left_time'] > 0 AND $os['date_left'] > 0 ? 1 : 0) : ($os['left_time'] > 0 ? 1 : 0),
                    ], 'onsite');
                }
            }
            
            // get appointments
            foreach(db_multi_query(
                'SELECT 
                    a.*,
                    o.name
                FROM `'.DB_PREFIX.'_users_appointments` a
                LEFT JOIN `'.DB_PREFIX.'_objects` o
                    ON o.id = a.object_id
                WHERE a.customer_id = '.$id
            , true) as $app){
                tpl_set('/account/app', [
                    'id' => $app['id'],
                    'date' => $app['date'],
                    'object' => $app['name']
                ], [], 'appointments');
            }
            
            foreach(db_multi_query('
                SELECT u.id, u.name, u.lastname, u.image
                FROM `'.DB_PREFIX.'_user_referrals` r 
                INNER JOIN `'.DB_PREFIX.'_users` u 
                ON r.from_invited = u.id 
                WHERE r.for_invited = '.$id
            , true) as $referral){
                tpl_set('/account/referral', [
                    'id' => $referral['id'],
                    'name' => $referral['name'],
                    'lastname' => $referral['name'],
                    'ava' => $referral['image']
                ], [
                    'ava' => $referral['image'],
                ], 'referrals');
            }*/
            
                if ($orders = db_multi_query('SELECT SQL_CALC_FOUND_ROWS
                        o.id,
                        o.total,
                        o.currency,
                        DATE(o.date) as date,
                        s.name as status,
                        s.color,
                        s.alt_color,
                        d.name as delivery
                    FROM `'.DB_PREFIX.'_orders` o 
                    LEFT JOIN `'.DB_PREFIX.'_orders_status` s 
                        ON s.id = o.status_id
                    LEFT JOIN `'.DB_PREFIX.'_orders_delivery` d 
                        ON d.id = o.delivery_id
                    WHERE o.del = 0 AND customer_id = '.$id, true)) {
                    foreach($orders as $o) {
                        tpl_set('/account/order', [
                            'id' => $o['id'],
                            'date' => $o['date'],
                            'total' => $o['total'],
                            'currency' => $config['currency'][$o['currency'] ?: 'USD']['symbol'],
                            'status' => $o['status'],
                            'status-color' => $o['alt_color'],
                            'delivery' => $o['delivery'],
                        ], [
                        ], 'orders');
                    }
                }
            }
                    
            tpl_set('account/main', [
                'id' => $row['id'],
                'name' => $row['name'],
                'lastname' => $row['lastname'],
                'address' => $row['address'],
                'phone' => $row['phone'],
                'email' => $row['email'],
                'image' => $row['image'],
                'points' => $row['ref_points'],
                'devices' => $tpl_content['devices'] ?? '<div class="noContent">No devices</div>',
                'invoices' => $tpl_content['invoices'] ?? '<div class="noContent">No invoices</div>',
                'onsite' => $tpl_content['onsite'] ?? '<div class="noContent">No onsite services</div>',
                'referrals' => $tpl_content['referrals'] ?? '<div class="noContent">No referrals</div>',
                'orders' => $tpl_content['orders'] ?? '<div class="noContent">No orders</div>',
                'appointments' => $tpl_content['appointments'] ?? '<div class="noContent">No appointments</div>',
                'devcount' => count($devices)
            ], [
                'photo' => $row['image'],
                'address' => $row['address']
            ], 'content');
        } else {
            tpl_set('login', [
            ], [
            ], 'content');
        }
    break;
}
?>