<?php
	require_once('mimePart.php');
	class htmlMimeMail{
		var $html;
		var $text;
		var $output;
		var $html_text;
		var $html_images;
		var $image_types;
		var $build_params;
		var $attachments;
		var $headers;
		var $is_built;
		var $return_path;
		var $smtp_params;
		
		function htmlMimeMail(){
			$this->html_images = array();
			$this->headers     = array();
			$this->is_built    = false;
			$this->image_types = array('gif'=>'image/gif','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','jpe'=>'image/jpeg','bmp'=>'image/bmp','png'=>'image/png','tif'=>'image/tiff','tiff'=>'image/tiff','swf'=>'application/x-shockwave-flash');
			$this->build_params['html_encoding'] = 'quoted-printable';
			$this->build_params['text_encoding'] = '7bit';
			$this->build_params['html_charset']  = 'ISO-8859-1';
			$this->build_params['text_charset']  = 'ISO-8859-1';
			$this->build_params['head_charset']  = 'ISO-8859-1';
			$this->build_params['text_wrap']     = 998;
			if (!empty($GLOBALS['HTTP_SERVER_VARS']['HTTP_HOST'])){
				$helo = $GLOBALS['HTTP_SERVER_VARS']['HTTP_HOST'];
			} elseif (!empty($GLOBALS['HTTP_SERVER_VARS']['SERVER_NAME'])){
				$helo = $GLOBALS['HTTP_SERVER_VARS']['SERVER_NAME'];
			} else {
				$helo = 'localhost';
			}
			$this->smtp_params['host'] = 'localhost';
			$this->smtp_params['port'] = 25;
			$this->smtp_params['helo'] = $helo;
			$this->smtp_params['auth'] = false;
			$this->smtp_params['user'] = '';
			$this->smtp_params['pass'] = '';
			$this->headers['MIME-Version'] = '1.0';
		}
		
		function getFile($filename){
			$return = '';
			if ($fp = fopen($filename, 'rb')){
				while (!feof($fp)){
					$return .= fread($fp, 1024);
				}
				fclose($fp);
				return $return;
			} else {
				return false;
			}
		}
		
		function setCrlf($crlf = "\n"){
			if (!defined('CRLF')){
				define('CRLF', $crlf, true);
			}
			if (!defined('MAIL_MIMEPART_CRLF')){
				define('MAIL_MIMEPART_CRLF', $crlf, true);
			}
		}
		
		function setSMTPParams($host = null, $port = null, $helo = null, $auth = null, $user = null, $pass = null){
			if (!is_null($host)) $this->smtp_params['host'] = $host;
			if (!is_null($port)) $this->smtp_params['port'] = $port;
			if (!is_null($helo)) $this->smtp_params['helo'] = $helo;
			if (!is_null($auth)) $this->smtp_params['auth'] = $auth;
			if (!is_null($user)) $this->smtp_params['user'] = $user;
			if (!is_null($pass)) $this->smtp_params['pass'] = $pass;
		}
		
		function setTextEncoding($encoding = '7bit'){
			$this->build_params['text_encoding'] = $encoding;
		}
		
		function setHtmlEncoding($encoding = 'quoted-printable'){
			$this->build_params['html_encoding'] = $encoding;
		}
		
		function setTextCharset($charset = 'ISO-8859-1'){
			$this->build_params['text_charset'] = $charset;
		}
		
		function setHtmlCharset($charset = 'ISO-8859-1'){
			$this->build_params['html_charset'] = $charset;
		}
		
		function setHeadCharset($charset = 'ISO-8859-1'){
			$this->build_params['head_charset'] = $charset;
		}
		
		function setTextWrap($count = 998){
			$this->build_params['text_wrap'] = $count;
		}
		
		function setHeader($name, $value){
			$this->headers[$name] = $value;
		}
		
		function setSubject($subject){
			$this->headers['Subject'] = $subject;
		}
		
		function setFrom($from){
			$this->headers['From'] = $from;
		}
		
		function setReturnPath($return_path){
			$this->return_path = $return_path;
		}
		
		function setCc($cc){
			$this->headers['Cc'] = $cc;
		}
		
		function setBcc($bcc){
			$this->headers['Bcc'] = $bcc;
		}
		
		function setText($text = ''){
			$this->text = $text;
		}
		
		function setHtml($html, $text = null, $images_dir = null){
			$this->html      = $html;
			$this->html_text = $text;
			if (isset($images_dir)){
				$this->_findHtmlImages($images_dir);
			}
		}
		
		function _findHtmlImages($images_dir){
			while (list($key,) = each($this->image_types)){
				$extensions[] = $key;
			}
			preg_match_all('/(?:"|\')([^"\']+\.('.implode('|', $extensions).'))(?:"|\')/Ui', $this->html, $images);
			for ($i=0; $i<count($images[1]); $i++){
				if (file_exists($images_dir . $images[1][$i])){
					$html_images[] = $images[1][$i];
					$this->html = str_replace($images[1][$i], basename($images[1][$i]), $this->html);
				}
			}
			if (!empty($html_images)){
				$html_images = array_unique($html_images);
				sort($html_images);
				for ($i=0; $i<count($html_images); $i++){
					if ($image = $this->getFile($images_dir.$html_images[$i])){
						$ext = substr($html_images[$i], strrpos($html_images[$i], '.') + 1);
						$content_type = $this->image_types[strtolower($ext)];
						$this->addHtmlImage($image, basename($html_images[$i]), $content_type);
					}
				}
			}
		}
		
		function addHtmlImage($file, $name = '', $c_type='application/octet-stream'){
			$this->html_images[] = array('body'=>$file,'name'=>$name,'c_type'=>$c_type,'cid'=>md5(uniqid(time())));
		}
		
		function addAttachment($file, $name = '', $c_type='application/octet-stream', $encoding = 'base64'){
			$this->attachments[] = array('body'=>$file,'name'=>$name,'c_type'=>$c_type,'encoding'=>$encoding);
		}
		
		function &_addTextPart(&$obj, $text){
			$params['content_type'] = 'text/plain';
			$params['encoding']     = $this->build_params['text_encoding'];
			$params['charset']      = $this->build_params['text_charset'];
			if (is_object($obj)){
				return $obj->addSubpart($text, $params);
			} else {
				return new Mail_mimePart($text, $params);
			}
		}
		
		function &_addHtmlPart(&$obj){
			$params['content_type'] = 'text/html';
			$params['encoding']     = $this->build_params['html_encoding'];
			$params['charset']      = $this->build_params['html_charset'];
			if (is_object($obj)){
				return $obj->addSubpart($this->html, $params);
			} else {
				return new Mail_mimePart($this->html, $params);
			}
		}
		
		function &_addMixedPart(){
			$params['content_type'] = 'multipart/mixed';
			return new Mail_mimePart('', $params);
		}
		
		function &_addAlternativePart(&$obj){
			$params['content_type'] = 'multipart/alternative';
			if (is_object($obj)){
				return $obj->addSubpart('', $params);
			} else {
				return new Mail_mimePart('', $params);
			}
		}
		
		function &_addRelatedPart(&$obj){
			$params['content_type'] = 'multipart/related';
			if (is_object($obj)){
				return $obj->addSubpart('', $params);
			} else {
				return new Mail_mimePart('', $params);
			}
		}
		
		function &_addHtmlImagePart(&$obj, $value){
			$params['content_type'] = $value['c_type'];
			$params['encoding']     = 'base64';
			$params['disposition']  = 'inline';
			$params['dfilename']    = $value['name'];
			$params['cid']          = $value['cid'];
			$obj->addSubpart($value['body'], $params);
		}
		
		function &_addAttachmentPart(&$obj, $value){
			$params['content_type'] = $value['c_type'];
			$params['encoding']     = $value['encoding'];
			$params['disposition']  = 'attachment';
			$params['dfilename']    = $value['name'];
			$obj->addSubpart($value['body'], $params);
		}
		
		function buildMessage($params = array()){
			if (!empty($params)){
				while (list($key, $value) = each($params)){
					$this->build_params[$key] = $value;
				}
			}
			if (!empty($this->html_images)){
				foreach ($this->html_images as $value){
					$this->html = str_replace($value['name'], 'cid:'.$value['cid'], $this->html);
				}
			}
			$null        = null;
			$attachments = !empty($this->attachments) ? true : false;
			$html_images = !empty($this->html_images) ? true : false;
			$html        = !empty($this->html)        ? true : false;
			$text        = isset($this->text)         ? true : false;
			switch (true){
				case $text AND !$attachments:
					$message = &$this->_addTextPart($null, $this->text);
					break;
				case !$text AND $attachments AND !$html:
					$message = &$this->_addMixedPart();
					for ($i=0; $i<count($this->attachments); $i++){
						$this->_addAttachmentPart($message, $this->attachments[$i]);
					}
					break;
				case $text AND $attachments:
					$message = &$this->_addMixedPart();
					$this->_addTextPart($message, $this->text);
					for ($i=0; $i<count($this->attachments); $i++){
						$this->_addAttachmentPart($message, $this->attachments[$i]);
					}
					break;
				case $html AND !$attachments AND !$html_images:
					if (!is_null($this->html_text)){
						$message = &$this->_addAlternativePart($null);
						$this->_addTextPart($message, $this->html_text);
						$this->_addHtmlPart($message);
					} else {
						$message = &$this->_addHtmlPart($null);
					}
					break;
				case $html AND !$attachments AND $html_images:
					if (!is_null($this->html_text)){
						$message = &$this->_addAlternativePart($null);
						$this->_addTextPart($message, $this->html_text);
						$related = &$this->_addRelatedPart($message);
					} else {
						$message = &$this->_addRelatedPart($null);
						$related = &$message;
					}
					$this->_addHtmlPart($related);
					for ($i=0; $i<count($this->html_images); $i++){
						$this->_addHtmlImagePart($related, $this->html_images[$i]);
					}
					break;
				case $html AND $attachments AND !$html_images:
					$message = &$this->_addMixedPart();
					if (!is_null($this->html_text)){
						$alt = &$this->_addAlternativePart($message);
						$this->_addTextPart($alt, $this->html_text);
						$this->_addHtmlPart($alt);
					} else {
						$this->_addHtmlPart($message);
					}
					for ($i=0; $i<count($this->attachments); $i++){
						$this->_addAttachmentPart($message, $this->attachments[$i]);
					}
					break;
				case $html AND $attachments AND $html_images:
					$message = &$this->_addMixedPart();
					if (!is_null($this->html_text)){
						$alt = &$this->_addAlternativePart($message);
						$this->_addTextPart($alt, $this->html_text);
						$rel = &$this->_addRelatedPart($alt);
					} else {
						$rel = &$this->_addRelatedPart($message);
					}
					$this->_addHtmlPart($rel);
					for ($i=0; $i<count($this->html_images); $i++){
						$this->_addHtmlImagePart($rel, $this->html_images[$i]);
					}
					for ($i=0; $i<count($this->attachments); $i++){
						$this->_addAttachmentPart($message, $this->attachments[$i]);
					}
					break;
			}
			if (isset($message)){
				$output = $message->encode();
				$this->output   = $output['body'];
				$this->headers  = array_merge($this->headers, $output['headers']);
				srand((double)microtime()*10000000);
				//$message_id = sprintf('<%s.%s@%s>', base_convert(time(), 10, 36), base_convert(rand(), 10, 36), !empty($GLOBALS['HTTP_SERVER_VARS']['HTTP_HOST']) ? $GLOBALS['HTTP_SERVER_VARS']['HTTP_HOST'] : $GLOBALS['HTTP_SERVER_VARS']['SERVER_NAME']);
				$message_id = sprintf('<%s.%s@%s>', base_convert(time(), 10, 36), base_convert(rand(), 10, 36), !empty($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : $_SERVER['SERVER_NAME']);
				$this->headers['Message-ID'] = $message_id;
				$this->is_built = true;
				return true;
			} else {
				return false;
			}
		}
		
		function _encodeHeader($input, $charset = 'ISO-8859-1'){
			preg_match_all('/(\w*[\x80-\xFF]+\w*)/', $input, $matches);
			foreach ($matches[1] as $value){
				$replacement = preg_replace('/([\x80-\xFF])/e', '"=" . strtoupper(dechex(ord("\1")))', $value);
				$input = str_replace($value, '=?' . $charset . '?Q?' . $replacement . '?=', $input);
			}
			return $input;
		}
		
		function send($recipients, $type = 'mail'){
			if (!defined('CRLF')){
				$this->setCrlf($type == 'mail' ? "\n" : "\r\n");
			}
			if (!$this->is_built){
				$this->buildMessage();
			}
			switch ($type){
				case 'mail':
					$subject = '';
					if (!empty($this->headers['Subject'])){
						$subject = $this->_encodeHeader($this->headers['Subject'], $this->build_params['head_charset']);
						unset($this->headers['Subject']);
					}
					foreach ($this->headers as $name => $value){
						$headers[] = $name . ': ' . $this->_encodeHeader($value, $this->build_params['head_charset']);
					}
					$to = $this->_encodeHeader(implode(', ', $recipients), $this->build_params['head_charset']);
					if (!empty($this->return_path)){
						$result = mail($to, $subject, $this->output, implode(CRLF, $headers), '-f' . $this->return_path);
					} else {
						$result = mail($to, $subject, $this->output, implode(CRLF, $headers));
					}
					if ($subject !== ''){
						$this->headers['Subject'] = $subject;
					}
					return $result;
					break;
				case 'smtp':
					require_once('smtp.php');
					require_once('RFC822.php');
					$smtp = &smtp::connect($this->smtp_params);
					foreach ($recipients as $recipient){
						$addresses = Mail_RFC822::parseAddressList($recipient, $this->smtp_params['helo'], null, false);
						foreach ($addresses as $address){
							$smtp_recipients[] = sprintf('%s@%s', $address->mailbox, $address->host);
						}
					}
					unset($addresses);
					unset($address);
					foreach ($this->headers as $name => $value){
						if ($name == 'Cc' OR $name == 'Bcc'){
							$addresses = Mail_RFC822::parseAddressList($value, $this->smtp_params['helo'], null, false);
							foreach ($addresses as $address){
								$smtp_recipients[] = sprintf('%s@%s', $address->mailbox, $address->host);
							}

						}
						if ($name == 'Bcc'){
							continue;
						}
						$headers[] = $name . ': ' . $this->_encodeHeader($value, $this->build_params['head_charset']);
					}
					$headers[] = 'To: ' . $this->_encodeHeader(implode(', ', $recipients), $this->build_params['head_charset']);
					$send_params['headers']    = $headers;
					$send_params['recipients'] = array_values(array_unique($smtp_recipients));
					$send_params['body']       = $this->output;
					if (isset($this->return_path)){
						$send_params['from'] = $this->return_path;
					} elseif (!empty($this->headers['From'])){
						$from = Mail_RFC822::parseAddressList($this->headers['From']);
						$send_params['from'] = sprintf('%s@%s', $from[0]->mailbox, $from[0]->host);
					} else {
						$send_params['from'] = 'postmaster@' . $this->smtp_params['helo'];
					}
					if (!$smtp->send($send_params)){
						$this->errors = $smtp->errors;
						return false;
					}
					return true;
					break;
			}
		}
		
		function getRFC822($recipients){
			$this->setHeader('Date', date('D, d M y H:i:s O'));
			if (!defined('CRLF')){
				$this->setCrlf($type == 'mail' ? "\n" : "\r\n");
			}
			if (!$this->is_built){
				$this->buildMessage();
			}
			if (isset($this->return_path)){
				$headers[] = 'Return-Path: ' . $this->return_path;
			}
			foreach ($this->headers as $name => $value){
				$headers[] = $name . ': ' . $value;
			}
			$headers[] = 'To: ' . implode(', ', $recipients);
			return implode(CRLF, $headers) . CRLF . CRLF . $this->output;
		}
	}
?>