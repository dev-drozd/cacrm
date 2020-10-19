<?php
/**
 * @appointment Whatsapp class
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2019
 * @link        https://yoursite.com
 * This code is copyrighted
*/

class Result
{
    public $index;
    public $syncId;
    public $existing;

    public $nonExisting;
    public function __construct($index, $syncId, $existing, $nonExisting)
    {
        $this->index = $index;
        $this->syncId = $syncId;
        $this->existing = $existing;
        $this->nonExisting = $nonExisting;
    }
}
class WhatsProt
{
    public function __construct($number, $identity, $nickname, $debug = false)
    {
        $this->writer = new BinTreeNodeWriter();
        $this->reader = new BinTreeNodeReader();
        $this->debug = $debug;
        $this->phoneNumber = $number;
        if (!$this->checkIdentity($identity)) {
            $this->identity = $this->buildIdentity($identity);
        } else {
            $this->identity = $identity;
        }
        $this->name = $nickname;
        $this->loginStatus = static::DISCONNECTED_STATUS;
    }

    public function setChallengeName($filename){
        $this->challengeFilename = $filename;
    }

    public function addMsgOutQueue($node)
    {
        $this->outQueue[] = $node;
    }

    public function checkCredentials()
    {
        if (!$phone = $this->dissectPhone()) {
            throw new Exception('The provided phone number is not valid.');
        }
        $host = 'https://' . static::WHATSAPP_CHECK_HOST;
        $query = array(
            'cc' => $phone['cc'],
            'in' => $phone['phone'],
            'id' => $this->identity,
            'c' => 'cookie',
        );
        $response = $this->getResponse($host, $query);
        if ($response->status != 'ok') {
            $this->eventManager()->fireCredentialsBad($this->phoneNumber, $response->status, $response->reason);
            if ($this->debug) {
                print_r($query);
                print_r($response);
            }
            throw new Exception('There was a problem trying to request the code.');
        } else {
            $this->eventManager()->fireCredentialsGood(
                $this->phoneNumber,
                $response->login,
                $response->pw,
                $response->type,
                $response->expiration,
                $response->kind,
                $response->price,
                $response->cost,
                $response->currency,
                $response->price_expiration
            );
        }
        return $response;
    }

    public function codeRegister($code)
    {
        if (!$phone = $this->dissectPhone()) {
            throw new Exception('The provided phone number is not valid.');
        }
        $host = 'https://' . static::WHATSAPP_REGISTER_HOST;
        $query = array(
            'cc' => $phone['cc'],
            'in' => $phone['phone'],
            'id' => $this->identity,
            'code' => $code,
            'c' => 'cookie',
        );
        $response = $this->getResponse($host, $query);
        if ($response->status != 'ok') {
            $this->eventManager()->fireCodeRegisterFailed(
                $this->phoneNumber, 
                $response->status, 
                $response->reason, 
                $response->retry_after
            );
            if ($this->debug) {
                print_r($query);
                print_r($response);
            }
            throw new Exception('An error occurred registering the registration code from WhatsApp.');
        } else {
            $this->eventManager()->fireCodeRegister(
                $this->phoneNumber,
                $response->login,
                $response->pw,
                $response->type,
                $response->expiration,
                $response->kind,
                $response->price,
                $response->cost,
                $response->currency,
                $response->price_expiration
            );
        }
        return $response;
    }

    public function codeRequest($method = 'sms', $countryCode = null, $langCode = null)
    {
        if (!$phone = $this->dissectPhone()) {
            throw new Exception('The provided phone number is not valid.');
        }
        if ($countryCode == null && $phone['ISO3166'] != '') {
            $countryCode = $phone['ISO3166'];
        }
        if ($countryCode == null) {
            $countryCode = 'US';
        }
        if ($langCode == null && $phone['ISO639'] != '') {
            $langCode = $phone['ISO639'];
        }
        if ($langCode == null) {
            $langCode = 'en';
        }
        $token = generateRequestToken($phone['country'], $phone['phone']);
        $host = 'https://' . static::WHATSAPP_REQUEST_HOST;
        $query = array(
            'cc' => $phone['cc'],
            'in' => $phone['phone'],
            'to' => $this->phoneNumber,
            'lg' => $langCode,
            'lc' => $countryCode,
            'method' => $method,
            'mcc' => $phone['mcc'],
            'mnc' => '001',
            'token' => urlencode($token),
            'id' => $this->identity,
        );
        if ($this->debug) {
            print_r($query);
        }
        $response = $this->getResponse($host, $query);
        if ($this->debug) {
            print_r($response);
        }
        if ($response->status == 'ok') {
            $this->eventManager()->fireCodeRegister(
                $this->phoneNumber,
                $response->login,
                $response->pw,
                $response->type,
                $response->expiration,
                $response->kind,
                $response->price,
                $response->cost,
                $response->currency,
                $response->price_expiration
            );
        } else if ($response->status != 'sent') {
            if (isset($response->reason) && $response->reason == "too_recent") {
                $this->eventManager()->fireCodeRequestFailedTooRecent(
                    $this->phoneNumber, 
                    $method, 
                    $response->reason, 
                    $response->retry_after
                );
                $minutes = round($response->retry_after / 60);
                throw new Exception("Code already sent. Retry after $minutes minutes.");
            } else {
                $this->eventManager()->fireCodeRequestFailed(
                    $this->phoneNumber, 
                    $method, 
                    $response->reason, 
                    $response->param
                );
                throw new Exception('There was a problem trying to request the code.');
            }
        } else {
            $this->eventManager()->fireCodeRequest(
                $this->phoneNumber,
                $method,
                $response->length
            );
        }
        return $response;
    }

    public function connect()
    {
        $Socket = fsockopen(static::WHATSAPP_HOST, static::PORT);
        if ($Socket !== false) {
            stream_set_timeout($Socket, static::TIMEOUT_SEC, static::TIMEOUT_USEC);
            $this->socket = $Socket;
            $this->eventManager()->fireConnect(
                $this->phoneNumber,
                $this->socket
            );
        } else {
            if ($this->debug) {
                print_r("Firing onConnectError\n");
            }            
            $this->eventManager()->fireConnectError(
               $this->phoneNumber, 
               $this->socket                
            );
        }
    }

    public function disconnect()
    {
        if (is_resource($this->socket)) {
            fclose($this->socket);
            $this->eventManager()->fireDisconnect(
                $this->phoneNumber,
                $this->socket
            );
        }
    }

    public function eventManager()
    {
        if (!is_object($this->event)) {
            $this->event = new WhatsAppEvent();
        }
        return $this->event;
    }

    public function getMessages()
    {
        $ret = $this->messageQueue;
        $this->messageQueue = array();
        return $ret;
    }

    public function login()
    {
        $this->accountInfo = (array) $this->checkCredentials();
        if ($this->accountInfo['status'] == 'ok') {
            if ($this->debug) {
                print_r("New password received: " . $this->accountInfo['pw'] . "\n");
            }
            $this->password = $this->accountInfo['pw'];
        }
        $this->doLogin();
    }

    public function loginWithPassword($password)
    {
        $this->password = $password;
        $challengeData = @file_get_contents($this->challengeFilename);
        if($challengeData) {
            $this->challengeData = $challengeData;
        }
        $this->doLogin();
    }

    public function pollMessages($autoReceipt = true)
    {
        $stanza = $this->readStanza();
        while($stanza)
        {
            $this->processInboundData($stanza, $autoReceipt);
            $stanza = $this->readStanza();
        }
    }

    public function pollMessage($autoReceipt = true)
    {
        $stanza = $this->readStanza();
        if($stanza)
        {
            $this->processInboundData($stanza, $autoReceipt);
            return true;
        }
        return false;
    }

    public function sendActiveStatus()
    {
        $messageNode = new ProtocolNode("presence", array("type" => "active"), null, "");
        $this->sendNode($messageNode);
    }

    public function sendBroadcastAudio($targets, $path, $storeURLmedia = false, $fsize = 0, $fhash = "")
    {
        if (!is_array($targets)) {
            $targets = array($targets);
        }
        $this->sendMessageAudio($targets, $path, $storeURLmedia, $fsize, $fhash);
    }

    public function sendBroadcastImage($targets, $path, $storeURLmedia = false, $fsize = 0, $fhash = "")
    {
        if (!is_array($targets)) {
            $targets = array($targets);
        }
        $this->sendMessageImage($targets, $path, $storeURLmedia, $fsize, $fhash);
    }

    public function sendBroadcastLocation($targets, $long, $lat, $name = null, $url = null)
    {
        if (!is_array($targets)) {
            $targets = array($targets);
        }
        $this->sendMessageLocation($targets, $long, $lat, $name, $url);
    }

    public function sendBroadcastMessage($targets, $message)
    {
    	$message = $this->parseMessageForEmojis($message);
        $bodyNode = new ProtocolNode("body", null, null, $message);
        $this->sendBroadcast($targets, $bodyNode, "text");
    }

    public function sendBroadcastVideo($targets, $path, $storeURLmedia = false, $fsize = 0, $fhash = "")
    {
        if (!is_array($targets)) {
            $targets = array($targets);
        }
        $this->sendMessageVideo($targets, $path, $storeURLmedia, $fsize, $fhash);
    }

    protected function sendClearDirty($categories)
    {
        $msgId = $this->createMsgId("cleardirty");
        $catnodes = array();
        foreach ($categories as $category) {
            $catnode = new ProtocolNode("clean", array("type" => $category), null, null);
            $catnodes[] = $catnode;
        }
        $node = new ProtocolNode("iq", array(
            "id" => $msgId,
            "type" => "set",
            "to" => "s.whatsapp.net",
            "xmlns" => "urn:xmpp:whatsapp:dirty"
                ), $catnodes, null);
        $this->sendNode($node);
    }
    public function sendClientConfig()
    {
        $phone = $this->dissectPhone();
        $attr = array();
        $attr["platform"] = "none";
        $attr["lc"] = $phone["ISO3166"];
        $attr["lg"] = $phone["ISO639"];
        $child = new ProtocolNode("config", $attr, null, "");
        $node = new ProtocolNode("iq", array("id" => $this->createMsgId("config"), "type" => "set", "xmlns" => "urn:xmpp:whatsapp:push", "to" => static::WHATSAPP_SERVER), array($child), null);
        $this->sendNode($node);
    }
    public function sendGetClientConfig()
    {
        $msgId = $this->createMsgId("sendconfig");
        $child = new ProtocolNode("config", array("xmlns" => "urn:xmpp:whatsapp:push", "sound" => 'sound'), null, null);
        $node = new ProtocolNode("iq", array(
            "id" => $msgId,
            "type" => "set",
            "to" => static::WHATSAPP_SERVER
                ), array($child), null);
        $this->sendNode($node);
        $this->waitForServer($msgId);
    }

    public function sendGetGroups()
    {
        $this->sendGetGroupsFiltered("participating");
    }

    public function sendGetGroupsInfo($gjid)
    {
        $msgId = $this->createMsgId("getgroupinfo");
        $child = new ProtocolNode("query", null, null, null);
        $node = new ProtocolNode("iq", array(
            "id" => $msgId,
            "type" => "get",
            "xmlns" => "w:g",
            "to" => $this->getJID($gjid)
                ), array($child), null);
        $this->sendNode($node);
        $this->waitForServer($msgId);
    }

    public function sendGetGroupsOwning()
    {
        $this->sendGetGroupsFiltered("owning");
    }

    public function sendGetGroupsParticipants($gjid)
    {
        $msgId = $this->createMsgId("getgroupparticipants");
        $child = new ProtocolNode("list", null, null, null);
        $node = new ProtocolNode("iq", array(
            "id" => $msgId,
            "type" => "get",
            "xmlns" => "w:g",
            "to" => $this->getJID($gjid)
                ), array($child), null);
        $this->sendNode($node);
        $this->waitForServer($msgId);
    }

    public function sendGetPrivacyBlockedList()
    {
        $msgId = $this->createMsgId("getprivacy");
        $child = new ProtocolNode("list", array(
            "name" => "default"
                ), null, null);
        $child2 = new ProtocolNode("query", array(
            "xmlns" => "jabber:iq:privacy"
                ), array($child), null);
        $node = new ProtocolNode("iq", array(
            "id" => $msgId,
            "type" => "get"
                ), array($child2), null);
        $this->sendNode($node);
        $this->waitForServer($msgId);
    }

    public function sendGetProfilePicture($number, $large = false)
    {
        $hash = array();
        $hash["type"] = "image";
        if (!$large) {
            $hash["type"] = "preview";
        }
        $picture = new ProtocolNode("picture", $hash, null, null);
        $hash = array();
        $hash["id"] = $this->createMsgId("getpicture");
        $hash["type"] = "get";
        $hash["xmlns"] = "w:profile:picture";
        $hash["to"] = $this->getJID($number);
        $node = new ProtocolNode("iq", $hash, array($picture), null);
        $this->sendNode($node);
        $this->waitForServer($hash["id"]);
    }

    public function sendGetRequestLastSeen($to)
    {
        $queryNode = new ProtocolNode("query", null, null, null);
        $messageHash = array();
        $messageHash["to"] = $this->getJID($to);
        $messageHash["type"] = "get";
        $messageHash["id"] = $this->createMsgId("lastseen");
        $messageHash["xmlns"] = "jabber:iq:last";
        $messageNode = new ProtocolNode("iq", $messageHash, array($queryNode), "");
        $this->sendNode($messageNode);
        $this->waitForServer($messageHash["id"]);
    }

    public function sendGetServerProperties()
    {
        $child = new ProtocolNode("props", null, null, null);
        $node = new ProtocolNode("iq", array(
            "id" => $this->createMsgId("getproperties"),
            "type" => "get",
            "xmlns" => "w",
            "to" => "s.whatsapp.net"
                ), array($child), null);
        $this->sendNode($node);
    }

    public function sendGetStatuses($jids)
    {
        if(!is_array($jids))
        {
            $jids = array($jids);
        }
        $children = array();
        foreach($jids as $jid)
        {
            $children[] = new ProtocolNode("user", array("jid" => $this->getJID($jid)), null, null);
        }
        $node = new ProtocolNode("iq", array(
            "to" => "s.whatsapp.net",
            "type" => "get",
            "xmlns" => "status",
            "id" => $this->createMsgId("getstatus")
        ), array(
            new ProtocolNode("status", null, $children, null)
        ), null);
        $this->sendNode($node);
    }

    public function sendGroupsChatCreate($subject, $participants = array())
    {
        $groupHash = array();
        $groupHash["action"] = "create";
        $groupHash["subject"] = $subject;
        $group = new ProtocolNode("group", $groupHash, null, "");
        $setHash = array();
        $setHash["id"] = $this->createMsgId("creategroup");
        $setHash["type"] = "set";
        $setHash["xmlns"] = "w:g";
        $setHash["to"] = static::WHATSAPP_GROUP_SERVER;
        $groupNode = new ProtocolNode("iq", $setHash, array($group), "");
        $this->sendNode($groupNode);
        $this->waitForServer($setHash["id"]);
        $groupId = $this->groupId;
        if (count($participants) > 0) {
            $this->sendGroupsParticipantsAdd($groupId, $participants);
        }
        return $groupId;
    }
    public function SendSetGroupSubject($gjid, $subject)
    {
        $child = new ProtocolNode("subject", array("value" => $subject), null, null);
        $node = new ProtocolNode("iq", array(
            "id" => $this->createMsgId("set_group_subject"),
            "type" => "set",
            "to" => $this->getJID($gjid),
            "xmlns" => "w:g"
        ), array($child), null);
        $this->sendNode($node);
    }

    public function sendGroupsChatEnd($gjid)
    {
        $gjid = $this->getJID($gjid);
        $msgID = $this->createMsgId("endgroup");
        $groupData = array();
        $groupData['id'] = $gjid;
        $groupNode = new ProtocolNode('group', $groupData, null, null);
        $leaveData = array();
        $leaveData["action"] = "delete";
        $leaveNode = new ProtocolNode("leave", $leaveData, array($groupNode), null);
        $iqData = array();
        $iqData["id"] = $msgID;
        $iqData["type"] = "set";
        $iqData["xmlns"] = "w:g";
        $iqData["to"] = static::WHATSAPP_GROUP_SERVER;
        $iqNode = new ProtocolNode("iq", $iqData, array($leaveNode), null);
        $this->sendNode($iqNode);
        $this->waitForServer($msgID);
    }

    public function sendGroupsLeave($gjids)
    {
        if (!is_array($gjids)) {
            $gjids = array($this->getJID($gjids));
        }
        $nodes = array();
        foreach ($gjids as $gjid) {
            $nodes[] = new ProtocolNode("group", array("id" => $this->getJID($gjid)), null, null);
        }
        $leave = new ProtocolNode("leave", array('action'=>'delete'), $nodes, null);
        $hash = array();
        $hash["id"] = $this->createMsgId("leavegroups");
        $hash["to"] = static::WHATSAPP_GROUP_SERVER;
        $hash["type"] = "set";
        $hash["xmlns"] = "w:g";
        $node = new ProtocolNode("iq", $hash, array($leave), null);
        $this->sendNode($node);
        $this->waitForServer($hash["id"]);
    }

    public function sendGroupsParticipantsAdd($groupId, $participants)
    {
        if(!is_array($participants)) {
            $participants = array($participants);
        }
        $this->sendGroupsChangeParticipants($groupId, $participants, 'add');
    }
	
    public function sendGroupsParticipantsRemove($groupId, $participants)
    {
        if(!is_array($participants)) {
            $participants = array($participants);
        }
        $this->sendGroupsChangeParticipants($groupId, $participants, 'remove');
    }

    public function sendMessage($to, $txt, $id = null)
    {
        $txt = $this->parseMessageForEmojis($txt);
        $bodyNode = new ProtocolNode("body", null, null, $txt);
        $id = $this->sendMessageNode($to, $bodyNode, $id);
        $this->waitForServer($id);
        return $id;
    }

    public function sendMessageAudio($to, $filepath, $storeURLmedia = false)
    {
    	if ($fsize==0 || $fhash == "")
    	{
        	$allowedExtensions = array('3gp', 'caf', 'wav', 'mp3', 'wma', 'ogg', 'aif', 'aac', 'm4a');
        	$size = 10 * 1024 * 1024;
        	return $this->sendCheckAndSendMedia($filepath, $size, $to, 'audio', $allowedExtensions, $storeURLmedia);
        }
        else{
    		$this->sendRequestFileUpload($fhash, 'audio', $fsize, $filepath, $to);
    		return true;   
    	}
    }

    public function sendMessageComposing($to)
    {
        $this->sendChatState($to, "composing");
    }

    public function sendMessageImage($to, $filepath, $storeURLmedia = false, $fsize = 0, $fhash = "")
    {
    	if ($fsize==0 || $fhash == "")
    	{
        	$allowedExtensions = array('jpg', 'jpeg', 'gif', 'png');
        	$size = 5 * 1024 * 1024;
        	return $this->sendCheckAndSendMedia($filepath, $size, $to, 'image', $allowedExtensions, $storeURLmedia);
        }
        else{
        $this->sendRequestFileUpload($fhash, 'image', $fsize, $filepath, $to);
    	return true;  
    	}
    }

    public function sendMessageLocation($to, $long, $lat, $name = null, $url = null)
    {
        $mediaHash = array();
        $mediaHash['xmlns'] = "urn:xmpp:whatsapp:mms";
        $mediaHash['type'] = "location";
        $mediaHash['latitude'] = $lat;
        $mediaHash['longitude'] = $long;
        $mediaHash['name'] = $name;
        $mediaHash['url'] = $url;
        $mediaNode = new ProtocolNode("media", $mediaHash, null, null);
        if (is_array($to)) {
            $id = $this->sendBroadcast($to, $mediaNode, "media");
        } else {
            $id = $this->sendMessageNode($to, $mediaNode);
        }
        $this->waitForServer($id);
    }

    public function sendMessagePaused($to)
    {
        $this->sendChatState($to, "paused");
    }
    protected function sendChatState($to, $state)
    {
        $node = new ProtocolNode("chatstate", array("to" => $this->getJID($to)), array(new ProtocolNode($state, null, null, null)), null);
        $this->sendNode($node);
    }

    public function sendMessageVideo($to, $filepath, $storeURLmedia = false, $fsize = 0, $fhash = "")
    {
    	if ($fsize==0 || $fhash == "")
    	{
        	$allowedExtensions = array('3gp', 'mp4', 'mov', 'avi');
        	$size = 20 * 1024 * 1024;
        	return $this->sendCheckAndSendMedia($filepath, $size, $to, 'video', $allowedExtensions, $storeURLmedia);
        }
        else{
    		$this->sendRequestFileUpload($fhash, 'video', $fsize, $filepath, $to);
    		return true;   
    	}
    }

    public function sendNextMessage()
    {
        if (count($this->outQueue) > 0) {
            $msgnode = array_shift($this->outQueue);
            $msgnode->refreshTimes();
            $this->lastId = $msgnode->getAttribute('id');
            $this->sendNode($msgnode);
        } else {
            $this->lastId = false;
        }
    }

    public function sendOfflineStatus()
    {
        $messageNode = new ProtocolNode("presence", array("type" => "inactive"), null, "");
        $this->sendNode($messageNode);
    }

    public function sendPong($msgid)
    {
        $messageHash = array();
        $messageHash["to"] = static::WHATSAPP_SERVER;
        $messageHash["id"] = $msgid;
        $messageHash["type"] = "result";
        $messageNode = new ProtocolNode("iq", $messageHash, null, "");
        $this->sendNode($messageNode);
        $this->eventManager()->fireSendPong(
            $this->phoneNumber, 
            $msgid
        );
    }
    public function sendAvailableForChat($nickname = null)
    {
        $presence = array();
        if($nickname)
        {
            $this->name = $nickname;
        }
        $presence['name'] = $this->name;
        $node = new ProtocolNode("presence", $presence, null, "");
        $this->sendNode($node);
    }

    public function sendPresence($type = "active")
    {
        $presence = array();
        $presence['type'] = $type;
        $node = new ProtocolNode("presence", $presence, null, "");
        $this->sendNode($node);
        $this->eventManager()->fireSendPresence(
            $this->phoneNumber, 
            $presence['type'],
            $this->name
        );
    }

    public function sendPresenceSubscription($to)
    {
        $node = new ProtocolNode("presence", array("type" => "subscribe", "to" => $this->getJID($to)), null, "");
        $this->sendNode($node);
    }

    public function sendSetGroupPicture($gjid, $path)
    {
        $this->sendSetPicture($gjid, $path);
    }

    public function sendSetPrivacyBlockedList($blockedJids = array())
    {
        if (!is_array($blockedJids)) {
            $blockedJids = array($blockedJids);
        }
        $items = array();
        foreach ($blockedJids as $index => $jid) {
            $item = new ProtocolNode("item", array(
                "type" => "jid",
                "value" => $this->getJID($jid),
                "action" => "deny",
                "order" => $index + 1
                    ), null, null);
            $items[] = $item;
        }
        $child = new ProtocolNode("list", array("name" => "default"), $items, null);
        $child2 = new ProtocolNode("query", array("xmlns" => "jabber:iq:privacy"), array($child), null);
        $node = new ProtocolNode("iq", array(
            "id" => $this->createMsgId("setprivacy"),
            "type" => "set"
                ), array($child2), null);
        $this->sendNode($node);
    }

    public function sendSetProfilePicture($path)
    {
        $this->sendSetPicture($this->phoneNumber, $path);
    }

    public function sendSetRecoveryToken($token)
    {
        $child = new ProtocolNode("pin", array("xmlns" => "w:ch:p"), null, $token);
        $node = new ProtocolNode("iq", array(
            "id" => $this->createMsgId("settoken"),
            "type" => "set",
            "to" => "s.whatsapp.net"
                ), array($child), null);
        $this->sendNode($node);
    }

    public function sendStatusUpdate($txt)
    {
        $child = new ProtocolNode("status", null, null, $txt);
        $node = new ProtocolNode("iq", array(
            "to" => "s.whatsapp.net",
            "type" => "set",
            "id" => $this->createMsgId("sendstatus"),
            "xmlns" => "status"
        ), array($child), null);
        $this->sendNode($node);
        $this->eventManager()->fireSendStatusUpdate(
            $this->phoneNumber, 
            $txt
        );
    }

    public function sendVcard($to, $name, $vCard)
    {
        $vCardAttribs = array();
        $vCardAttribs['name'] = $name;
        $vCardNode = new ProtocolNode("vcard", $vCardAttribs, null, $vCard);
        $mediaAttribs = array();
        $mediaAttribs["type"] = "vcard";
        $mediaNode = new ProtocolNode("media", $mediaAttribs, array($vCardNode), "");
        $this->sendMessageNode($to, $mediaNode);
    }
    

	public function sendBroadcastVcard($targets, $name, $vCard)
    {
        $vCardAttribs = array();
        $vCardAttribs['name'] = $name;
        $vCardNode = new ProtocolNode("vcard", $vCardAttribs, null, $vCard);
        $mediaAttribs = array();
        $mediaAttribs["type"] = "vcard";
        $mediaNode = new ProtocolNode("media", $mediaAttribs, array($vCardNode), "");
        $this->sendBroadcast($targets, $mediaNode, "media");
    }

    public function setNewMessageBind($bind)
    {
        $this->newMsgBind = $bind;
    }

    public function uploadFile($file)
    {
        $data['file'] = "@" . $file;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_URL, static::WHATSAPP_UPLOAD_HOST);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        curl_close($ch);
        $xml = simplexml_load_string($response);
        $url = strip_tags($xml->dict->string[3]->asXML());
        if (!empty($url)) {
            $this->eventManager()->fireUploadFile(
                $this->phoneNumber, 
                basename($file), 
                $url
            );
            return $url;
        } else {
            $this->eventManager()->fireUploadFileFailed(
                $this->phoneNumber, 
                basename($file)
            );
            return false;
        }
    }

    public function waitForMessageReceipt()
    {
        $received = false;
        do {
            $this->pollMessages();
            $msgs = $this->getMessages();
            foreach ($msgs as $m) {
                if ($m->getTag() == "message") {
                    if ($m->getChild('received') != null && $m->getAttribute('retry') != null) {
                        $received = true;
                    } elseif ($m->getChild('received') != null && $m->getAttribute('retry') != null) {
                        throw new Exception('There was a problem trying to send the message, please retry.');
                    }
                }
            }
        } while (!$received);
    }

    public function waitForServer($id, $timeout = 5)
    {
        $time = time();
        $this->serverReceivedId = false;
        do {
            $this->pollMessage();
        } while ($this->serverReceivedId !== $id && time() - $time < $timeout);
    }

    protected function authenticate()
    {
        $keys = KeyStream::GenerateKeys(base64_decode($this->password), $this->challengeData);
        $this->inputKey = new KeyStream($keys[2], $keys[3]);
        $this->outputKey = new KeyStream($keys[0], $keys[1]);
        $phone = $this->dissectPhone();
        $array = "\0\0\0\0" . $this->phoneNumber . $this->challengeData;
        $response = $this->outputKey->EncodeMessage($array, 0, 4, strlen($array) - 4);
        return $response;
    }

    protected function createAuthNode()
    {
        $authHash = array();
        $authHash["xmlns"] = "urn:ietf:params:xml:ns:xmpp-sasl";
        $authHash["mechanism"] = "WAUTH-2";
        $authHash["user"] = $this->phoneNumber;
        $data = $this->createAuthBlob();
        $node = new ProtocolNode("auth", $authHash, null, $data);
        return $node;
    }
    protected function createAuthBlob()
    {
        if($this->challengeData) {
            $key = wa_pbkdf2('sha1', base64_decode($this->password), $this->challengeData, 16, 20, true);
            $this->inputKey = new KeyStream($key[2], $key[3]);
            $this->outputKey = new KeyStream($key[0], $key[1]);
            $this->reader->setKey($this->inputKey);
            $phone = $this->dissectPhone();
            $array = "\0\0\0\0" . $this->phoneNumber . $this->challengeData . time() . static::WHATSAPP_USER_AGENT . " MccMnc/" . str_pad($phone["mcc"], 3, "0", STR_PAD_LEFT) . "001";
            $this->challengeData = null;
            return $this->outputKey->EncodeMessage($array, 0, strlen($array), false);
        }
        return null;
    }

    protected function createAuthResponseNode()
    {
        $resp = $this->authenticate();
        $respHash = array();
        $respHash["xmlns"] = "urn:ietf:params:xml:ns:xmpp-sasl";
        $node = new ProtocolNode("response", $respHash, null, $resp);
        return $node;
    }

    protected function createFeaturesNode()
    {
        $parent = new ProtocolNode("stream:features", null, null, null);
        return $parent;
    }

    protected function createMsgId($prefix)
    {
        $msgid = "$prefix-" . time() . '-' . $this->messageCounter;
        $this->messageCounter++;
        return $msgid;
    }

    protected function debugPrint($debugMsg)
    {
        if ($this->debug) {
            echo $debugMsg;
        }
    }

    protected function dissectPhone()
    {
        if (($handle = fopen(dirname(__FILE__).'/countries.csv', 'rb')) !== false) {
            while (($data = fgetcsv($handle, 1000)) !== false) {
                if (strpos($this->phoneNumber, $data[1]) === 0) {
                    fclose($handle);
                    $mcc = explode("|", $data[2]);
                    $mcc = $mcc[0];
                    if(substr($data[1], 0, 1) == "1")
                    {
                        $data[1] = "1";
                    }
                    $phone = array(
                        'country' => $data[0],
                        'cc' => $data[1],
                        'phone' => substr($this->phoneNumber, strlen($data[1]), strlen($this->phoneNumber)),
                        'mcc' => $mcc,
                        'ISO3166' => @$data[3],
                        'ISO639' => @$data[4]
                    );
                    $this->eventManager()->fireDissectPhone(
                        $this->phoneNumber,
                        $phone['country'],
                        $phone['cc'],
                        $phone['phone'],
                        $phone['mcc'],
                        $phone['ISO3166'],
                        $phone['ISO639']
                    );
                    return $phone;
                }
            }
            fclose($handle);
        }
        $this->eventManager()->fireDissectPhoneFailed(
            $this->phoneNumber
        );
        return false;
    }

    protected function doLogin()
    {
        $this->writer->resetKey();
        $this->reader->resetKey();
        $resource = static::WHATSAPP_DEVICE . '-' . static::WHATSAPP_VER . '-' . static::PORT;
        $data = $this->writer->StartStream(static::WHATSAPP_SERVER, $resource);
        $feat = $this->createFeaturesNode();
        $auth = $this->createAuthNode();
        $this->sendData($data);
        $this->sendNode($feat);
        $this->sendNode($auth);
        $this->pollMessages();
        $cnt = 0;
        do {
            $this->pollMessages();
            if($this->challengeData != null) {
                $data = $this->createAuthResponseNode();
                $this->sendNode($data);
                $this->reader->setKey($this->inputKey);
                $this->writer->setKey($this->outputKey);
                $this->pollMessages();
            }
        } while ($this->challengeData == null && ($cnt++ < 100) && (strcmp($this->loginStatus, static::DISCONNECTED_STATUS) == 0));
        
        if(strcmp($this->loginStatus, static::DISCONNECTED_STATUS) == 0)
		{
			throw new Exception('Login Failure');
		}
		else
		{
            $this->eventManager()->fireLogin(
                $this->phoneNumber
            );
            $this->sendAvailableForChat();
		}
    }
    protected function buildIdentity($identity)
    {
        return strtolower(urlencode(sha1($identity, true)));
    }
    protected function checkIdentity($identity)
    {
        return (strlen(urldecode($identity)) == 20);
    }
    public function sendSync(array $numbers, $mode = "full", $context = "registration", $index = 0, $last = true)
    {
        $users = array();
        foreach ($numbers as $number) { 
            $users[] = new ProtocolNode("user", null, null, (substr($number, 0, 1) != '+')?('+' . $number):($number));
        }
        $id = $this->createMsgId("sendsync_");
        $node = new ProtocolNode("iq", array(
            "to" => $this->getJID($this->phoneNumber),
            "type" => "get",
            "id" => $id,
            "xmlns" => "urn:xmpp:whatsapp:sync"
        ), array(
            new ProtocolNode("sync", array(
                "mode" => $mode,
                "context" => $context,
                "sid" => "".((time() + 11644477200) * 10000000),
                "index" => "".$index,
                "last" => $last ? "true" : "false"
            ), $users, null)
        ), null);
        $this->sendNode($node);
        return $id;
    }

    protected function getJID($number)
    {
        if (!stristr($number, '@')) {
            if (stristr($number, '-')) {
                $number .= "@" . static::WHATSAPP_GROUP_SERVER;
            } else {
                $number .= "@" . static::WHATSAPP_SERVER;
            }
        }
        return $number;
    }

    protected function getMediaFile($filepath, $maxsizebytes = 1048576)
    {
        if (filter_var($filepath, FILTER_VALIDATE_URL) !== false) {
            $this->mediaFileInfo = array();
            $this->mediaFileInfo['url'] = $filepath;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "$filepath");
            curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_NOBODY, true);
            if (curl_exec($curl) === false) {
                return false;
            }
            $info = curl_getinfo($curl);
            $this->mediaFileInfo['filesize'] = $info['download_content_length'];
            $this->mediaFileInfo['filemimetype'] = $info['content_type'];
            $this->mediaFileInfo['fileextension'] = pathinfo(parse_url($this->mediaFileInfo['url'], PHP_URL_PATH), PATHINFO_EXTENSION);
            if ($this->mediaFileInfo['filesize'] < $maxsizebytes) {
                $this->mediaFileInfo['filepath'] = tempnam(getcwd() . '/' . static::MEDIA_FOLDER, 'WHA');
                $fp = fopen($this->mediaFileInfo['filepath'], 'w');
                if ($fp) {
                    curl_setopt($curl, CURLOPT_NOBODY, false);
                    curl_setopt($curl, CURLOPT_BUFFERSIZE, 1024);
                    curl_setopt($curl, CURLOPT_FILE, $fp);
                    curl_exec($curl);
                    fclose($fp);
                } else {
                    unlink($this->mediaFileInfo['filepath']);
                    curl_close($curl);
                    return false;
                }
                curl_close($curl);
                return true;
            } else {
                curl_close($curl);
                return false;
            }
        } else if (file_exists($filepath)) {
            $this->mediaFileInfo['filesize'] = filesize($filepath);
            if ($this->mediaFileInfo['filesize'] < $maxsizebytes) {
                $this->mediaFileInfo['filepath'] = $filepath;
                $this->mediaFileInfo['fileextension'] = pathinfo($filepath, PATHINFO_EXTENSION);
                $this->mediaFileInfo['filemimetype'] = mime_content_type($filepath);
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    protected function getResponse($host, $query)
    {
        $url = $host . '?';
        foreach ($query as $key => $value) {
            $url .= $key . '=' . $value . '&';
        }
        $url = rtrim($url, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, static::WHATSAPP_USER_AGENT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: text/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }

    protected function processChallenge($node)
    {
        $this->challengeData = $node->getData();
    }

    protected function processInboundData($data, $autoReceipt = true)
    {
        $node = $this->reader->nextTree($data);
        if( $node != null ) {
            $this->processInboundDataNode($node, $autoReceipt);
        }
    }

    protected function processInboundDataNode(ProtocolNode $node, $autoReceipt = true) {
        $this->debugPrint($node->nodeString("rx  ") . "\n");
        $this->serverReceivedId = $node->getAttribute('id');
        
        if ($node->getTag() == "challenge") {
            $this->processChallenge($node);
        } 
        elseif($node->getTag() == "failure"  )
		{
			$this->loginStatus = static::DISCONNECTED_STATUS;
			
		}
        elseif ($node->getTag() == "success") {
            $this->loginStatus = static::CONNECTED_STATUS;
            $challengeData = $node->getData();
            file_put_contents($this->challengeFilename, $challengeData);
            $this->writer->setKey($this->outputKey);
        } elseif($node->getTag() == "failure")
        {
            $this->eventManager()->fireLoginFailed(
                $this->phoneNumber,
                $node->getChild(0)->getTag()
            );
        }
        elseif($node->getTag() == '' && $node->getAttribute("class") == "message")
        {
            $this->eventManager()->fireMessageReceivedServer(
                $this->phoneNumber,
                $node->getAttribute('from'),
                $node->getAttribute('id'),
                $node->getAttribute('class')
            );
        }
        elseif($node->getTag() == 'receipt')
        {
            $this->eventManager()->fireMessageReceivedClient(
                $this->phoneNumber,
                $node->getAttribute('from'),
                $node->getAttribute('id'),
                $node->getAttribute('class'),
                $node->getAttribute('t')
            );
        }
        if ($node->getTag() == "message") {
            array_push($this->messageQueue, $node);
            if ($node->hasChild('x') && $this->lastId == $node->getAttribute('id')) {
                $this->sendNextMessage();
            }
            if ($this->newMsgBind && $node->getChild('body')) {
                $this->newMsgBind->process($node);
            }
            if ($node->getAttribute("type") == "text" && $node->getChild('body') != null) {
                $author = $node->getAttribute("participant");
                if($author == "")
                {
                    $this->eventManager()->fireGetMessage(
                        $this->phoneNumber,
                        $node->getAttribute('from'),
                        $node->getAttribute('id'),
                        $node->getAttribute('type'),
                        $node->getAttribute('t'),
                        $node->getAttribute("notify"),
                        $node->getChild("body")->getData()
                    );
                }
                else
                {
                    $this->eventManager()->fireGetGroupMessage(
                        $this->phoneNumber,
                        $node->getAttribute('from'),
                        $author,
                        $node->getAttribute('id'),
                        $node->getAttribute('type'),
                        $node->getAttribute('t'),
                        $node->getAttribute("notify"),
                        $node->getChild("body")->getData()
                    );
                }
                if($autoReceipt)
                {
                    $this->sendMessageReceived($node);
                }
            }
            if ($node->hasChild('notification') && $node->getChild('notification')->getAttribute('type') == 'picture') {
                if ($node->getChild('notification')->hasChild('set')) {
                    $this->eventManager()->fireProfilePictureChanged(
                        $this->phoneNumber,
                        $node->getAttribute('from'),
                        $node->getAttribute('id'),
                        $node->getAttribute('t')
                    );
                } else if ($node->getChild('notification')->hasChild('delete')) {
                    $this->eventManager()->fireProfilePictureDeleted(
                        $this->phoneNumber,
                        $node->getAttribute('from'),
                        $node->getAttribute('id'),
                        $node->getAttribute('t')
                    );
                }
            }
            if ($node->getAttribute("type") == "media" && $node->getChild('media') != null) {
                if ($node->getChild("media")->getAttribute('type') == 'image') {
                    $this->eventManager()->fireGetImage(
                        $this->phoneNumber,
                        $node->getAttribute('from'),
                        $node->getAttribute('id'),
                        $node->getAttribute('type'),
                        $node->getAttribute('t'),
                        $node->getAttribute('notify'),
                        $node->getChild("media")->getAttribute('size'),
                        $node->getChild("media")->getAttribute('url'),
                        $node->getChild("media")->getAttribute('file'),
                        $node->getChild("media")->getAttribute('mimetype'),
                        $node->getChild("media")->getAttribute('filehash'),
                        $node->getChild("media")->getAttribute('width'),
                        $node->getChild("media")->getAttribute('height'),
                        $node->getChild("media")->getData()
                    );
                } elseif ($node->getChild("media")->getAttribute('type') == 'video') {
                    $this->eventManager()->fireGetVideo(
                        $this->phoneNumber,
                        $node->getAttribute('from'),
                        $node->getAttribute('id'),
                        $node->getAttribute('type'),
                        $node->getAttribute('t'),
                        $node->getAttribute('notify'),
                        $node->getChild("media")->getAttribute('url'),
                        $node->getChild("media")->getAttribute('file'),
                        $node->getChild("media")->getAttribute('size'),
                        $node->getChild("media")->getAttribute('mimetype'),
                        $node->getChild("media")->getAttribute('filehash'),
                        $node->getChild("media")->getAttribute('duration'),
                        $node->getChild("media")->getAttribute('vcodec'),
                        $node->getChild("media")->getAttribute('acodec'),
                        $node->getChild("media")->getData()
                    );
                } elseif ($node->getChild("media")->getAttribute('type') == 'audio') {
                    $this->eventManager()->fireGetAudio(
                        $this->phoneNumber,
                        $node->getAttribute('from'),
                        $node->getAttribute('id'),
                        $node->getAttribute('type'),
                        $node->getAttribute('t'),
                        $node->getAttribute('notify'),
                        $node->getChild("media")->getAttribute('size'),
                        $node->getChild("media")->getAttribute('url'),
                        $node->getChild("media")->getAttribute('file'),
                        $node->getChild("media")->getAttribute('mimetype'),
                        $node->getChild("media")->getAttribute('filehash'),
                        $node->getChild("media")->getAttribute('duration'),
                        $node->getChild("media")->getAttribute('acodec')
                    );
                } elseif ($node->getChild("media")->getAttribute('type') == 'vcard') {
                    $this->eventManager()->fireGetvCard(
                        $this->phoneNumber,
                        $node->getAttribute('from'),
                        $node->getAttribute('id'),
                        $node->getAttribute('type'),
                        $node->getAttribute('t'),
                        $node->getAttribute('notify'),
                        $node->getChild("media")->getChild("vcard")->getAttribute('name'),
                        $node->getChild("media")->getChild("vcard")->getData()
                    );
                } elseif ($node->getChild("media")->getAttribute('type') == 'location') {
                    $url = $node->getChild("media")->getAttribute('url');
                    $name = $node->getChild("media")->getAttribute('name');
                    $this->eventManager()->fireGetLocation(
                        $this->phoneNumber,
                        $node->getAttribute('from'),
                        $node->getAttribute('id'),
                        $node->getAttribute('type'),
                        $node->getAttribute('t'),
                        $node->getAttribute('notify'),
                        $name,
                        $node->getChild("media")->getAttribute('longitude'),
                        $node->getChild("media")->getAttribute('latitude'),
                        $url,
                        $node->getChild("media")->getData()
                    );
                }
                if($autoReceipt)
                {
                    $this->sendMessageReceived($node);
                }
            }
            if ($node->getChild('received') != null) {
                $this->eventManager()->fireMessageReceivedClient(
                    $this->phoneNumber,
                    $node->getAttribute('from'),
                    $node->getAttribute('id'),
                    $node->getAttribute('type'),
                    $node->getAttribute('t')
                );
            }
            if ($node->getAttribute('type') == "subject") {
                print_r($node);
                $reset_from = explode('@', $node->getAttribute('from'));
                $reset_author = explode('@',$node->getAttribute('author'));
                $this->eventManager()->fireGetGroupsSubject(
                    $this->phoneNumber,
                    reset($reset_from),
                    $node->getAttribute('t'),
                    reset($reset_author),
                    reset($reset_author),
                    $node->getChild(0)->getAttribute('name'),
                    $node->getChild(2)->getData()
                );
            }
        }
        if ($node->getTag() == "presence" && $node->getAttribute("status") == "dirty") {
            $categories = array();
            if (count($node->getChildren()) > 0)
                foreach ($node->getChildren() as $child) {
                    if ($child->getTag() == "category") {
                        $categories[] = $child->getAttribute("name");
                    }
                }
            $this->sendClearDirty($categories);
        }
	if (strcmp($node->getTag(), "presence") == 0
            && strncmp($node->getAttribute('from'), $this->phoneNumber, strlen($this->phoneNumber)) != 0
            && strpos($node->getAttribute('from'), "-") == false) {
            $presence = array();
            if($node->getAttribute('type') == null){
            $this->eventManager()->firePresence(
                $this->phoneNumber,
                $node->getAttribute('from'),
                $presence['type'] = "available"
            );
            }
            else{
            $this->eventManager()->firePresence(
                $this->phoneNumber,
                $node->getAttribute('from'),
                $presence['type'] = "unavailable"
            );
            }
        }
        if ($node->getTag() == "presence"
            && strncmp($node->getAttribute('from'), $this->phoneNumber, strlen($this->phoneNumber)) != 0
            && strpos($node->getAttribute('from'), "-") !== false
            && $node->getAttribute('type') != null) {
            $groupId = self::parseJID($node->getAttribute('from'));
            if ($node->getAttribute('add') != null) {
                $this->eventManager()->fireGroupsParticipantsAdd(
                    $this->phoneNumber,
                    $groupId,
                    self::parseJID($node->getAttribute('add'))
                );
            } elseif ($node->getAttribute('remove') != null) {
                $this->eventManager()->fireGroupsParticipantsRemove(
                    $this->phoneNumber,
                    $groupId,
                    self::parseJID($node->getAttribute('remove')),
                    self::parseJID($node->getAttribute('author'))
                );
            }
        }
        if (strcmp($node->getTag(), "chatstate") == 0
            && strncmp($node->getAttribute('from'), $this->phoneNumber, strlen($this->phoneNumber)) != 0
            && strpos($node->getAttribute('from'), "-") == false) {
            if($node->getChild(0)->getTag() == "composing"){
            	$this->eventManager()->fireMessageComposing(
                	$this->phoneNumber,
                	$node->getAttribute('from'),
                	$node->getAttribute('id'),
                	"composing",
                	$node->getAttribute('t')
            	);
            }
            else{
            	$this->eventManager()->fireMessagePaused(
                	$this->phoneNumber,
                	$node->getAttribute('from'),
                	$node->getAttribute('id'),
                	"paused",
                	$node->getAttribute('t')
            	);
            }          	
        }
        if ($node->getTag() == "iq"
            && $node->getAttribute('type') == "get"
            && $node->getChild(0)->getTag() == "ping") {
            $this->eventManager()->firePing(
                $this->phoneNumber,
                $node->getAttribute('id')
            );
            $this->sendPong($node->getAttribute('id'));
        }
        if ($node->getTag() == "iq"
            && $node->getChild("sync") != null) {
            $sync = $node->getChild('sync');
            $existing = $sync->getChild("in");
            $nonexisting = $sync->getChild("out");
            $existingUsers = array();
            if (!empty($existing)) {
                foreach ($existing->getChildren() as $child) {
                    $existingUsers[$child->getData()] = $child->getAttribute("jid");
                }
            }
            $failedNumbers = array();
            if (!empty($nonexisting)) {
                foreach ($nonexisting->getChildren() as $child) {
                    $failedNumbers[] = str_replace('+', '', $child->getData());
                }
            }
            $index = $sync->getAttribute("index");
            $result = new SyncResult($index, $sync->getAttribute("sync"), $existingUsers, $failedNumbers);
            $this->eventManager()->fireGetSyncResult($result);
        }
        if ($node->getTag() == "receipt") {
            $this->eventManager()->fireGetReceipt(
                $node->getAttribute('from'),
                $node->getAttribute('id'),
                $node->getAttribute('offline'),
                $node->getAttribute('retry')
            );
        }
        if ($node->getTag() == "iq"
            && $node->getAttribute('type') == "result") {
            if ($node->getChild("query") != null) {
                if ($node->getChild(0)->getAttribute('xmlns') == 'jabber:iq:privacy') {
                    $this->eventManager()->fireGetPrivacyBlockedList(
                        $this->phoneNumber,
                        $node->getChild(0)->getChild(0)->getChildren()
                    );
                }
                $this->eventManager()->fireGetRequestLastSeen(
                    $this->phoneNumber,
                    $node->getAttribute('from'),
                    $node->getAttribute('id'),
                    $node->getChild(0)->getAttribute('seconds')
               );
                array_push($this->messageQueue, $node);
            }
            if ($node->getChild("props") != null) {
                $props = array();
                foreach($node->getChild(0)->getChildren() as $child) {
                    $props[$child->getAttribute("name")] = $child->getAttribute("value");
                }
                $this->eventManager()->fireGetServerProperties(
                    $this->phoneNumber,
                    $node->getChild(0)->getAttribute("version"),
                    $props
               );
            }
            if ($node->getChild("picture") != null) {
                $this->eventManager()->fireGetProfilePicture(
                    $this->phoneNumber,
                    $node->getAttribute("from"),
                    $node->getChild("picture")->getAttribute("type"),
                    $node->getChild("picture")->getData()
                );
            }
            if ($node->getChild("media") != null || $node->getChild("duplicate") != null) {
                $this->processUploadResponse($node);
            }
            if ($node->nodeIdContains("group")) {
                $groupList = array();
                if ($node->getChild(0) != null) {
                    foreach ($node->getChildren() as $child) {
                        $groupList[] = $child->getAttributes();
                    }
                }
                if($node->nodeIdContains('creategroup')){
                    $this->groupId = $node->getChild(0)->getAttribute('id');
                    $this->eventManager()->fireGroupsChatCreate(
                        $this->phoneNumber,
                        $this->groupId
                    );
                }
                if($node->nodeIdContains('endgroup')){
                    $this->groupId = $node->getChild(0)->getChild(0)->getAttribute('id');
                    $this->eventManager()->fireGroupsChatEnd(
                        $this->phoneNumber,
                        $this->groupId
                    );
                }
                if($node->nodeIdContains('getgroups')){
                    $this->eventManager()->fireGetGroups(
                        $this->phoneNumber,
                        $groupList
                    );
                }
                if($node->nodeIdContains('getgroupinfo')){
                    $this->eventManager()->fireGetGroupsInfo(
                        $this->phoneNumber,
                        $groupList
                    );
                }
                if($node->nodeIdContains('getgroupparticipants')){
                    $groupId = self::parseJID($node->getAttribute('from'));
                    $this->eventManager()->fireGetGroupParticipants(
                        $this->phoneNumber,
                        $groupId,
                        $groupList
                    );
                }
            }
            if($node->getChild("status") != null)
            {
                $child = $node->getChild("status");
                foreach($child->getChildren() as $status)
                {
                    $this->eventManager()->fireGetStatus(
                        $this->phoneNumber,
                        $status->getAttribute("jid"),
                        "requested",
                        $node->getAttribute("id"),
                        $status->getAttribute("t"),
                        $status->getData()
                    );
                }
            }
        }
        if ($node->getTag() == "iq" && $node->getAttribute('type') == "error") {
                    $this->eventManager()->fireGetError(
                        $this->phoneNumber,
                        $node->getAttribute( 'id' ),
                        $node->getChild(0)
                    );
        }
        $children = $node->getChild(0);
        if ($node->getTag() == "stream:error" && empty($children) == false && $node->getChild(0)->getTag() == "system-shutdown")
        {
            throw new Exception('Error system-shutdown');
        }
        if($node->getTag() == "notification")
        {
            $name = $node->getAttribute("notify");
            $type = $node->getAttribute("type");
            switch($type)
            {
                case "status":
                    $this->eventManager()->fireGetStatus(
                        $this->phoneNumber, 
                        $node->getAttribute("from"),
                        $node->getChild(0)->getTag(),
                        $node->getAttribute("id"),
                        $node->getAttribute("t"),
                        $node->getChild(0)->getData());
                    break;
                case "picture":
                    break;
                case "contacts":
                    break;
                case "participant":
                    break;
                default:
                    throw new Exception("Method $type not implemented");
            }
            $this->sendNotificationAck($node);
        }
        if($node->getTag() == "ib")
        {
            foreach($node->getChildren() as $child)
            {
                switch($child->getTag())
                {
                    case "dirty":
                        $this->sendClearDirty(array($child->getAttribute("type")));
                        break;
                    case "offline":
                        break;
                    default:
                        throw new Exception("ib handler for " . $child->getTag() . " not implemented");
                }
            }
        }
        if($node->getTag() == "ack")
        {
			
        }
    }

    protected function sendNotificationAck($node)
    {
        $from = $node->getAttribute("from");
        $to = $node->getAttribute("to");
        $participant = $node->getAttribute("participant");
        $id = $node->getAttribute("id");
        $type = $node->getAttribute("type");
        $attributes = array();
        if($to)
            $attributes["from"] = $to;
        if($participant)
            $attributes["participant"] = $participant;
        $attributes["to"] = $from;
        $attributes["class"] = "notification";
        $attributes["id"] = $id;
        $attributes["type"] = $type;
        $ack = new ProtocolNode("ack", $attributes, null, null);
        $this->sendNode($ack);
    }
    
    protected function processMediaImage($node)
    {
        $media = $node->getChild("media");
        if ($media != null) {
            $filename = $media->getAttribute("file");
            $url = $media->getAttribute("url");
            $data = $media->getData();
            $fp = @fopen(static::MEDIA_FOLDER . "/thumb_" . $filename, "w");
            if ($fp) {
                fwrite($fp, $data);
                fclose($fp);
            }
            $data = file_get_contents($url);
            $fp = @fopen(static::MEDIA_FOLDER . "/" . $filename, "w");
            if ($fp) {
                fwrite($fp, $data);
                fclose($fp);
            }
        }
    }
	
    protected function processProfilePicture($node)
    {
        $pictureNode = $node->getChild("picture");
        if ($pictureNode != null) {
            $type = $pictureNode->getAttribute("type");
            $data = $pictureNode->getData();
            if ($type == "preview") {
                $filename = static::PICTURES_FOLDER . "/preview_" . $node->getAttribute("from") . ".jpg";
            } else {
                $filename = static::PICTURES_FOLDER . "/" . $node->getAttribute("from") . ".jpg";
            }
            $fp = @fopen($filename, "w");
            if ($fp) {
                fwrite($fp, $data);
                fclose($fp);
            }
        }
    }
	
    protected function processTempMediaFile($storeURLmedia)
    {
        if (isset($this->mediaFileInfo['url'])) {
            if ($storeURLmedia) {
                if (is_file($this->mediaFileInfo['filepath'])) {
                    rename($this->mediaFileInfo['filepath'], $this->mediaFileInfo['filepath'] . $this->mediaFileInfo['fileextension']);
                }
            } else {
                if (is_file($this->mediaFileInfo['filepath'])) {
                    unlink($this->mediaFileInfo['filepath']);
                }
            }
        }
    }
	
    protected function processUploadResponse($node)
    {
        $id = $node->getAttribute("id");
        $messageNode = @$this->mediaQueue[$id];
        if ($messageNode == null) {
            $this->eventManager()->fireMediaUploadFailed(
                $this->phoneNumber,
                $id,
                $node,
                $messageNode,
                "Message node not found in queue"
            );
            return false;
        }
        $duplicate = $node->getChild("duplicate");
        if ($duplicate != null) {
            $url = $duplicate->getAttribute("url");
            $filesize = $duplicate->getAttribute("size");
            $filehash = $duplicate->getAttribute("filehash");
            $filetype = $duplicate->getAttribute("type");
            $exploded = explode("/", $url);  
            $filename = array_pop($exploded);
        } else {
            $json = WhatsMediaUploader::pushFile($node, $messageNode, $this->mediaFileInfo, $this->phoneNumber);
            if (!$json) {
                $this->eventManager()->fireMediaUploadFailed(
                    $this->phoneNumber,
                    $id,
                    $node,
                    $messageNode,
                    "Failed to push file to server"
                );
                return false;
            }
            $url = $json->url;
            $filesize = $json->size;
            $filehash = $json->filehash;
            $filetype = $json->type;
            $filename = $json->name;
        }
        $mediaAttribs = array();
        $mediaAttribs["xmlns"] = "urn:xmpp:whatsapp:mms";
        $mediaAttribs["type"] = $filetype;
        $mediaAttribs["url"] = $url;
        $mediaAttribs["file"] = $filename;
        $mediaAttribs["size"] = $filesize;
        $mediaAttribs["hash"] = $filehash;
        $filepath = $this->mediaQueue[$id]['filePath'];
        $to = $this->mediaQueue[$id]['to'];
        switch ($filetype) {
            case "image":
                $icon = createIcon($filepath);
                break;
            case "video":
                $icon = createVideoIcon($filepath);
                break;
            default:
                $icon = '';
                break;
        }
        $mediaNode = new ProtocolNode("media", $mediaAttribs, null, $icon);
        if (is_array($to)) {
            $this->sendBroadcast($to, $mediaNode, "media");
        } else {
            $this->sendMessageNode($to, $mediaNode);
        }
        $this->eventManager()->fireMediaMessageSent(
            $this->phoneNumber,
            $to,
            $id,
            $filetype,
            $url,
            $filename,
            $filesize,
            $filehash,
            $icon
        );
        return true;
    }
	
    public function readStanza()
    {
        $buff = '';
        if($this->socket != null)
        {
            $header = @fread($this->socket, 3);
            if(strlen($header) == 0)
            {
                return;
            }
            if(strlen($header) != 3)
            {
                throw new Exception("Failed to read stanza header");
            }
            $treeLength = 0;
            $treeLength = ord($header[1]) << 8;
            $treeLength |= ord($header[2]) << 0;
            $buff = @fread($this->socket, $treeLength);
            $trlen = $treeLength;
            $len = strlen($buff);
            $prev = 0;
            while(strlen($buff) < $treeLength)
            {
                $toRead = $treeLength - strlen($buff);
                $buff .= @fread($this->socket, $toRead);
                if($len == strlen($buff))
                {
                    break;
                }
                $len = strlen($buff);
            }
            if (strlen($buff) != $treeLength) {
                throw new Exception("Tree length did not match received length (buff = " . strlen($buff) . " & treeLength = $treeLength)");
            } else
            if (@feof($this->socket)) {
                $error = "socket EOF, closing socket...";
                fclose($this->socket);
                $this->socket = null;
                $this->eventManager()->fireClose(                        
                    $this->phoneNumber, 
                    $error
                );
            }
            $buff = $header . $buff;
        }
        else
        {
            throw new Exception("Socket closed");
        }
        return $buff;
    }
	
    protected function sendCheckAndSendMedia($filepath, $maxSize, $to, $type, $allowedExtensions, $storeURLmedia)
    {
        if ($this->getMediaFile($filepath, $maxSize) == true) {
            if (in_array($this->mediaFileInfo['fileextension'], $allowedExtensions)) {
                $b64hash = base64_encode(hash_file("sha256", $this->mediaFileInfo['filepath'], true));
                $this->sendRequestFileUpload($b64hash, $type, $this->mediaFileInfo['filesize'], $this->mediaFileInfo['filepath'], $to);
                $this->processTempMediaFile($storeURLmedia);
                return true;
            } else {
                $this->processTempMediaFile($storeURLmedia);
                return false;
            }
        } else {
            return false;
        }
    }

    protected function sendBroadcast($targets, $node, $type)
    {
        if (!is_array($targets)) {
            $targets = array($targets);
        }
        $serverNode = new ProtocolNode("server", null, null, "");
        $xHash = array();
        $xHash["xmlns"] = "jabber:x:event";
        $xNode = new ProtocolNode("x", $xHash, array($serverNode), "");
        $toNodes = array();
        foreach ($targets as $target) {
            $jid = $this->getJID($target);
            $hash = array("jid" => $jid);
            $toNode = new ProtocolNode("to", $hash, null, null);
            $toNodes[] = $toNode;
        }
        $broadcastNode = new ProtocolNode("broadcast", null, $toNodes, null);
        $messageHash = array();
        $messageHash["to"] = "broadcast";
        $messageHash["type"] = $type;
        $id = $this->createMsgId("broadcast");
        $messageHash["id"] = $id;
        $messageNode = new ProtocolNode("message", $messageHash, array($broadcastNode, $xNode, $node), null);
        $this->sendNode($messageNode);
        $this->eventManager()->fireSendMessage(
            $this->phoneNumber,
            $targets,
            $messageHash["id"],
            $node
        );
        return $id;
    }

    protected function sendData($data)
    {
        if($this->socket != null)
        {
            fwrite($this->socket, $data, strlen($data));
        }
    }

    protected function sendGetGroupsFiltered($type)
    {
        $msgID = $this->createMsgId("getgroups");
        $child = new ProtocolNode("list", array(
            "type" => $type
                ), null, null);
        $node = new ProtocolNode("iq", array(
            "id" => $msgID,
            "type" => "get",
            "xmlns" => "w:g",
            "to" => "g.us"
                ), array($child), null);
        $this->sendNode($node);
        $this->waitForServer($msgID);
    }

    protected function sendGroupsChangeParticipants($groupId, $participants, $tag)
    {
        $_participants = array();
        foreach ($participants as $participant) {
            $_participants[] = new ProtocolNode("participant", array("jid" => $this->getJID($participant)), null, "");
        }
        $childHash = array();
        $child = new ProtocolNode($tag, $childHash, $_participants, "");
        $setHash = array();
        $setHash["id"] = $this->createMsgId("participants");
        $setHash["type"] = "set";
        $setHash["xmlns"] = "w:g";
        $setHash["to"] = $this->getJID($groupId);
        $node = new ProtocolNode("iq", $setHash, array($child), "");
        $this->sendNode($node);
        $this->waitForServer($setHash["id"]);
    }
	
    protected function sendMessageNode($to, $node, $id = null)
    {
        $serverNode = new ProtocolNode("server", null, null, "");
        $xHash = array();
        $xHash["xmlns"] = "jabber:x:event";
        $xNode = new ProtocolNode("x", $xHash, array($serverNode), "");
        $notify = array();
        $notify['xmlns'] = 'urn:xmpp:whatsapp';
        $notify['name'] = $this->name;
        $notnode = new ProtocolNode("notify", $notify, null, "");
        $request = array();
        $request['xmlns'] = "urn:xmpp:receipts";
        $reqnode = new ProtocolNode("request", $request, null, "");
        $messageHash = array();
        $messageHash["to"] = $this->getJID($to);
        if($node->getTag() == "body")
        {
            $messageHash["type"] = "text";
        }
        else
        {
            $messageHash["type"] = "media";
        }
        $messageHash["id"] = ($id == null?$this->createMsgId("message"):$id);
        $messageHash["t"] = time();
        $messageNode = new ProtocolNode("message", $messageHash, array($xNode, $notnode, $reqnode, $node), "");
        $this->sendNode($messageNode);
        $this->eventManager()->fireSendMessage(
            $this->phoneNumber,
            $this->getJID($to),
            $messageHash["id"],
            $node
        );
        return $messageHash["id"];
    }

    protected function sendMessageReceived($msg, $type = null)
    {
        if($type)
        {
            $messageHash["type"] = $type;
        }
        $messageHash = array();
        $messageHash["to"] = $msg->getAttribute("from");
        $messageHash["id"] = $msg->getAttribute("id");
        $messageNode = new ProtocolNode("receipt", $messageHash, null, null);
        $this->sendNode($messageNode);
        $this->eventManager()->fireSendMessageReceived(
            $this->phoneNumber,
            $msg->getAttribute("id"),
            $msg->getAttribute("from"),
            $type
        );
    }

    protected function sendNode($node, $encrypt = true)
    {
        $this->debugPrint($node->nodeString("tx  ") . "\n");
        $this->sendData($this->writer->write($node, $encrypt));
    }


    protected function sendRequestFileUpload($b64hash, $type, $size, $filepath, $to)
    {
        $hash = array();
        $hash["hash"] = $b64hash;
        $hash["type"] = $type;
        $hash["size"] = $size;
        $mediaNode = new ProtocolNode("media", $hash, null, null);
        $hash = array();
        $id = $this->createMsgId("upload");
        $hash["id"] = $id;
        $hash["to"] = static::WHATSAPP_SERVER;
        $hash["type"] = "set";
        $hash["xmlns"] = "w:m";
        $node = new ProtocolNode("iq", $hash, array($mediaNode), null);
        if (!is_array($to)) {
            $to = $this->getJID($to);
        }
        $messageId = $this->createMsgId("message");
        $this->mediaQueue[$id] = array("messageNode" => $node, "filePath" => $filepath, "to" => $to, "message_id" => $messageId);
        $this->sendNode($node);
        $this->waitForServer($hash["id"]);
    }


    protected function sendSetPicture($jid, $filepath)
    {
    	if(stripos($filepath, 'http') == 0 && !preg_match('/\s/',$filepath)){
		$extension = end(explode(".", $filepath));
		$newImageName = rand(0, 100000);
		$imagePath = static::PICTURES_FOLDER."/".$newImageName.".jpg";
		if($extension == 'jpg'){
			copy($filepath, $imagePath);
			$filepath = $imagePath;
		}
	}
        preprocessProfilePicture($filepath);
        $fp = @fopen($filepath, "r");
        if ($fp) {
            $data = fread($fp, filesize($filepath));
            if ($data) {
                $picture = new ProtocolNode("picture", null, null, $data);
                $icon = createIconGD($filepath, 96, true);
                $thumb = new ProtocolNode("picture", array("type" => "preview"), null, $icon);
                $hash = array();
                $nodeID = $this->createMsgId("setphoto");
                $hash["id"] = $nodeID;
                $hash["to"] = $this->getJID($jid);
                $hash["type"] = "set";
                $hash["xmlns"] = "w:profile:picture";
                $node = new ProtocolNode("iq", $hash, array($picture, $thumb), null);
                $this->sendNode($node);
                $this->waitForServer($nodeID);
            }
        }
    }
	
    private function parseMessageForEmojis($txt)
    {
        $matches = null;
        preg_match_all('/##(.*?)##/', $txt, $matches, PREG_SET_ORDER);
        if (is_array($matches)) {
            foreach ($matches as $emoji) {
                $txt = str_ireplace($emoji[0], $this->unichr((string) $emoji[1]), $txt);
            }
        }
        return $txt;
    }
	
    private function unichr($int)
    {
        $string = null;
        $multiChars = explode('_', $int);
        foreach ($multiChars as $char) {
            $string .= mb_convert_encoding('&#' . intval($char, 16) . ';', 'UTF-8', 'HTML-ENTITIES');
        }
        return $string;
    }
	
    public static function parseJID($jid)
    {
        $parts = explode('@', $jid);
        $parts = reset($parts);
        return $parts;
    }
}