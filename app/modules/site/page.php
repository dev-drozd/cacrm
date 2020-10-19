<?php

$id = (int)$route[1];
if($route[1] == 'print' && isset($_POST['title'])){
	$meta['title'] = $_POST['title'];
	$meta['description'] = $_POST['description'];
	$meta['keywords'] = $_POST['keywords'];
	$meta['canonical'] = $page['canonical'];
	tpl_set('page', [
		'content' => '<div class="ctnr">'.(
			$user['edit_pages'] ? '<div align="right"><a align="right" href="https://crm.yoursite.com/store/pages/edit/'.$page['id'].'">Edit this page</a></div>' : (
				in_array(18, explode(',', $user['group_ids'])) ? '<div align="right"><a align="right" href="https://seo.yoursite.com/pages/edit/'.$page['id'].'">Edit this page</a></div>' : ''
			)
		).'<h1 align="center">'.$page['title'].'</h1>'.str_ireplace(
		['{phone}', '{quote-form}'], ['<span class="hdr-phone.page"><a href="tel:1-802-447-8528">1-802-447-8528</a></span>', '
		<div class="quote-form">
		<div class="quote-steps">
		'.(
			$user['id'] ? '' : '
			<div class="input-group fw">
				<label>Company </label>
				<input type="text" placeholder="Company name" name="company">
			</div>
			<div class="input-group fw">
				<label><span class="red">*</span> Name</label>
				<input type="text" placeholder="Name" name="name">
			</div>
			<div class="input-group fw">
				<label><span class="red">*</span> Lastname</label>
				<input type="text" placeholder="Lastname" name="lastname">
			</div>
			<div class="input-group fw">
				<label><span class="red">*</span> Phone</label>
				<input type="tel" name="phone" class="quote-phone" placeholder="+X (XXX) XXX-XXXX" value="+1">
			</div>
			<div class="input-group fw">
				<label><span class="red">*</span> Email</label>
				<input type="email" placeholder="Email" name="email">
			</div>'
		).'
			<div class="qs-step active">
				<div class="input-group fw">
					<label><span class="red">*</span> Store</label>
					<select name="store" class="store-select"></select>
				</div>
				<div class="input-group fw">
					<label><span class="red">*</span> Your issue</label>
					<textarea cols="3" placeholder="Issues" name="issue"></textarea>
				</div>
				<div class="submit-group">
					<button class="btn btnLogin" type="button" onclick="quote.send(this);">Send</button>
				</div>
			</div>
		</div>
		</div>'], $page['content'].'<div>{quote-form}</div>'
		).'</div>'
	], [], 'content');
} else if($route[1] == 'save'){
	/*if(in_array($user['id'], [16, 17]) && (
		$id = intval($_POST['id'])
	)){
		db_query('UPDATE `'.DB_PREFIX.'_pages` SET content = \''.db_escape_string($_POST['content']).'\' WHERE id = '.$id);
	}
	die;*/
} else if($page = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_pages` WHERE id = '.$id)){
	$cache_page = APP_DIR.'/cache/pages/'.$id.'.tmp';
	
	if($page['confirm'])
		file_put_contents($cache_page, serialize($page));
	else
		$page = file_exists($cache_page) ? unserialize(file_get_contents($cache_page)) : [];
	
	if($page){
		if($id > 0 && $page['pathname']){
			header('Location: /'.$page['pathname'], true, 301);
			die;
		}
		$meta['title'] = $page['title'];
		$meta['description'] = $page['description'];
		$meta['keywords'] = $page['keywords'];
		$meta['canonical'] = $page['canonical'];
		
		tpl_set('page', [
			'content' => '<div class="ctnr">'.(
				$user['edit_pages'] ? '<div align="right"><a align="right" href="https://crm.yoursite.com/store/pages/edit/'.$page['id'].'">Edit this page</a></div>' : (
					in_array(18, explode(',', $user['group_ids'])) ? '<div align="right"><a align="right" href="https://seo.yoursite.com/pages/edit/'.$page['id'].'">Edit this page</a></div>' : ''
				)
			).'<h1 align="center">'.$page['title'].'</h1>'.str_ireplace(
			['{phone}', '{quote-form}'], ['<span class="hdr-phone.page"><a href="tel:1-802-447-8528">1-802-447-8528</a></span>', '
			<div class="quote-form">
			<div class="quote-steps">
			'.(
				$user['id'] ? '' : '
				<div class="input-group fw">
					<label>Company </label>
					<input type="text" placeholder="Company name" name="company">
				</div>
				<div class="input-group fw">
					<label><span class="red">*</span> Name</label>
					<input type="text" placeholder="Name" name="name">
				</div>
				<div class="input-group fw">
					<label><span class="red">*</span> Lastname</label>
					<input type="text" placeholder="Lastname" name="lastname">
				</div>
				<div class="input-group fw">
					<label><span class="red">*</span> Phone</label>
					<input type="tel" name="phone" class="quote-phone" placeholder="+X (XXX) XXX-XXXX" value="+1">
				</div>
				<div class="input-group fw">
					<label><span class="red">*</span> Email</label>
					<input type="email" placeholder="Email" name="email">
				</div>'
			).'
				<div class="qs-step active">
					<div class="input-group fw">
						<label><span class="red">*</span> Store</label>
						<select name="store" class="store-select"></select>
					</div>
					<div class="input-group fw">
						<label><span class="red">*</span> Your issue</label>
						<textarea cols="3" placeholder="Issues" name="issue"></textarea>
					</div>
					<div class="submit-group">
						<button class="btn btnLogin" type="button" onclick="quote.send(this);">Send</button>
					</div>
				</div>
			</div>
			</div>'], $page['content'].'<div>{quote-form}</div>'
			).'</div>'
		], [], 'content');
	} else {
		is_ajax() or http_response_code(503);
		$meta['title'] = 'Page temporarily unavailable';
		$tpl_content['content'] = '<div class="err404 ctnr"><h1>503</h1><p>Page temporarily unavailable</p></div>';	
	}
	
} else {
	is_ajax() or http_response_code(404);
	$meta['title'] = 'Page not found';
	$tpl_content['content'] = '<div class="err404 ctnr"><h1>404</h1><p>Page not found</p></div>';
}
/*

<script>
	$(document).keydown(function(e) {
		if ((e.key == \'s\' || e.key == \'S\' ) && (e.ctrlKey || e.metaKey)){
			e.preventDefault();
			var clone = $(\'#page\').clone();
			clone.find(\'script:last-child\').remove();
			$.post(\'/page/save\', {
				id: '.$id.',
				content: clone.html()
			}, function(){
				alert(\'OK\');
			});
			return false;
		}
		return true;
	});
	</script>
*/
?>
