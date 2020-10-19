<?php
switch($route[1]){
	
	case 'editor':
		$id = (int)$route[2];
		if($page = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_pages` WHERE id = '.$id)){	
			echo '
				<!DOCTYPE html>
				<html lang="en">
					<head>
						<meta charset="utf-8" />
						<meta http-equiv="x-ua-compatible" content="ie=edge" />
						<meta name="viewport" content="width=device-width, initial-scale=1" />
						<title>'.$page['title'].'</title>
						<link rel="stylesheet" href="/templates/site/css/codemirror.css">
						<style>
						body {
							position: fixed;
							display: flex;
							flex-direction: column;
							margin: 0;
							width: 100%;
							height: 100%;
						}
						.CodeMirror{
							position:absolute;
							width: 100%;
							height:100%;
						}
						header {
							padding: 10px;
							width: 100%;
							background: aliceblue;
						}
						body > div {
							position: relative;
							flex: 1;
						}
						</style>
					</head>
					<body>
						<header>
							<button onclick="autoFormatSelection()">Форматировать HTML</button>
							<button onclick="commentSelection(true)">Комментировать выделеное</button>
							<button onclick="commentSelection()">Раскомментировать выделеное</button>
							<button onclick="alert(\'ok\')" style="float: right;margin-right: 20px;">Сохранить</button>
						</header>
						<div>
							<textarea id="editor">'.htmlspecialchars($page['content']).'</textarea>
						</div>
						<script src="/templates/site/js/codemirror.js"></script>
						<script>
						  var editor = CodeMirror.fromTextArea(document.getElementById("editor"), {
							    mode: {
								name: "htmlmixed",
								scriptTypes: [
									{matches: /\/x-handlebars-template|\/x-mustache/i, mode: null},
									{matches: /(text|application)\/(x-)?vb(a|script)/i,mode: "vbscript"}
								]
								},
							    styleActiveLine: true,
							    lineNumbers: true,
							    matchBrackets : true
						  });
						  window.editor = editor;
					      
					      function getSelectedRange() {
					        return {
					        	from: editor.getCursor(true),
					        	to: editor.getCursor(false)
					        };
					      }
					      
					      function autoFormatSelection() {
						      	CodeMirror.commands["selectAll"](editor);
					        	var range = getSelectedRange();
								editor.autoFormatRange(range.from, range.to);
								editor.setCursor(1);
					      }
					      
					      function commentSelection(isComment) {
					        	var range = getSelectedRange();
								editor.commentRange(isComment, range.from, range.to);
					      } 
						</script>
					</body>
				</html>
			';
			die;
		}
	break;
	
	case 'view':
		$id = (int)$route[2];
		if($page = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_pages` WHERE id = '.$id)){
			$meta['title'] = $page['title'];
			$meta['description'] = $page['description'];
			$meta['keywords'] = $page['keywords'];
			$meta['canonical'] = $page['canonical'];
			
			tpl_set('page', [
				'name' => $page['title'],
				'content' => '<div class="ctnr">'.(
					$user['edit_pages'] ? '<div align="right"><a align="right" href="/pagelist/editor/'.$page['id'].'" target="_blank">Edit this page</a></div>' : (
						in_array(18, explode(',', $user['group_ids'])) ? '<div align="right"><a align="right" href="/pagelist/editor/'.$page['id'].'" target="_blank">Edit this page</a></div>' : ''
					)
				).'<h1 align="center">'.$page['name'].'</h1>'.str_ireplace(
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
			], [
				'owner' => in_array(1, explode(',', $user['group_ids']))
			], 'content');
		}
	break;
	
	default:
		echo '<ol>';
		foreach(db_multi_query('SELECT * FROM `'.DB_PREFIX.'_pages` ORDER BY `id` DESC', true) as $row){
			echo '<li><a href="/pagelist/view/'.$row['id'].'" target="_blank">'.$row['name'].'</a></li>';
		}
		echo '</ol>';
	die;
}