<?php

function storeGetPostStatus($post) {
	return !!$post['declined'];
}

function storeGetPostStatusText($post) {
	if ($post['declined']) {
		$ret = 'Declined';
		if ($post['declined_comment']) {
			$ret .= '. Comment: ' . $post['declined_comment'];
		}

		return $ret;
	}

	return '';
}

function storeCanDeclinePost($post) {
	if ($post['declined'] || $post['confirm']) {
		return false;
	}

	return true;
}

function storeCanConfirmPost($post) {
	return !storeCanDeclinePost($post);
}