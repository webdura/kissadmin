<?php
	class Mail_mimePart{
		var $_encoding;
		var $_subparts;
		var $_encoded;
		var $_headers;
		var $_body;
		
		function Mail_mimePart($body = '', $params = array()){
			if (!defined('MAIL_MIMEPART_CRLF')){
				define('MAIL_MIMEPART_CRLF', defined('MAIL_MIME_CRLF') ? MAIL_MIME_CRLF : "\r\n", TRUE);
			}
			foreach ($params as $key => $value){
				switch ($key){
					case 'content_type':
						$headers['Content-Type'] = $value . (isset($charset) ? '; charset="' . $charset . '"' : '');
						break;
					case 'encoding':
						$this->_encoding = $value;
						$headers['Content-Transfer-Encoding'] = $value;
						break;
					case 'cid':
						$headers['Content-ID'] = '<' . $value . '>';
						break;
					case 'disposition':
						$headers['Content-Disposition'] = $value . (isset($dfilename) ? '; filename="' . $dfilename . '"' : '');
						break;
					case 'dfilename':
						if (isset($headers['Content-Disposition'])){
							$headers['Content-Disposition'] .= '; filename="' . $value . '"';
						} else {
							$dfilename = $value;
						}
						break;
					case 'description':
						$headers['Content-Description'] = $value;
						break;
					case 'charset':
						if (isset($headers['Content-Type'])){
							$headers['Content-Type'] .= '; charset="' . $value . '"';
						} else {
							$charset = $value;
						}
						break;
				}
			}
			if (!isset($headers['Content-Type'])){
				$headers['Content-Type'] = 'text/plain';
			}
			if (!isset($this->_encoding)){
				$this->_encoding = '7bit';
			}
			$this->_encoded  = array();
			$this->_headers  = $headers;
			$this->_body     = $body;
		}
			
		function encode(){
			$encoded =& $this->_encoded;
			if (!empty($this->_subparts)){
				srand((double)microtime()*1000000);
				$boundary = '=_' . md5(uniqid(rand()) . microtime());
				$this->_headers['Content-Type'] .= ';' . MAIL_MIMEPART_CRLF . "\t" . 'boundary="' . $boundary . '"';
				for ($i = 0; $i < count($this->_subparts); $i++){
					$headers = array();
					$tmp = $this->_subparts[$i]->encode();
					foreach ($tmp['headers'] as $key => $value){
						$headers[] = $key . ': ' . $value;
					}
					$subparts[] = implode(MAIL_MIMEPART_CRLF, $headers) . MAIL_MIMEPART_CRLF . MAIL_MIMEPART_CRLF . $tmp['body'];
				}
				$encoded['body'] = '--'.$boundary.MAIL_MIMEPART_CRLF.implode('--'.$boundary.MAIL_MIMEPART_CRLF, $subparts).'--' .$boundary.'--'.MAIL_MIMEPART_CRLF;
			} else {
				$encoded['body'] = $this->_getEncodedData($this->_body, $this->_encoding) . MAIL_MIMEPART_CRLF;
			}
			$encoded['headers'] =& $this->_headers;
			return $encoded;
		}
		
		function &addSubPart($body, $params){
			$this->_subparts[] = new Mail_mimePart($body, $params);
			return $this->_subparts[count($this->_subparts) - 1];
		}
		
		function _getEncodedData($data, $encoding){
			switch ($encoding){
				case '8bit':
				case '7bit':
					return $data;
					break;
				case 'quoted-printable':
					return $this->_quotedPrintableEncode($data);
					break;
				case 'base64':
					return rtrim(chunk_split(base64_encode($data), 76, MAIL_MIMEPART_CRLF));
					break;
				default:
					return $data;
			}
		}
		
		function _quotedPrintableEncode($input , $line_max = 76){
			$lines  = preg_split("/\r?\n/", $input);
			$eol    = MAIL_MIMEPART_CRLF;
			$escape = '=';
			$output = '';
			while(list(, $line) = each($lines)){
				$linlen     = strlen($line);
				$newline = '';
				for ($i = 0; $i < $linlen; $i++){
					$char = substr($line, $i, 1);
					$dec  = ord($char);
					if (($dec == 32) AND ($i == ($linlen - 1))){
						$char = '=20';
					} elseif($dec == 9){ ;
					} elseif(($dec == 61) OR ($dec < 32 ) OR ($dec > 126)){
						$char = $escape . strtoupper(sprintf('%02s', dechex($dec)));
					}
					if ((strlen($newline) + strlen($char)) >= $line_max){
						$output  .= $newline . $escape . $eol;
						$newline  = '';
					}
					$newline .= $char;
				}
				$output .= $newline . $eol;
			}
			$output = substr($output, 0, -1 * strlen($eol));
			return $output;
		}
	}
?>