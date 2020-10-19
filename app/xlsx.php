<?php
/**
 * @appointment XLSX
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
*/
 
class Xlsx {
	private $sheets;
	private $hyperlinks;
	private $package;
	private $sharedstrings;
	const SCHEMA_OFFICEDOCUMENT  =  'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument';
	const SCHEMA_RELATIONSHIP  =  'http://schemas.openxmlformats.org/package/2006/relationships';
	const SCHEMA_SHAREDSTRINGS =  'http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings';
	const SCHEMA_WORKSHEETRELATION =  'http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet';
	
	public function __construct( $filename ) {
		$this->_unzip( $filename );
		$this->_parse();
	}
	public function sheets() {
		return $this->sheets;
	}

	public function sheetsCount() {
		return count($this->sheets);
	}

	public function worksheet( $worksheet_id ) {
		if ( isset( $this->sheets[ $worksheet_id ] ) ) {
			$ws = $this->sheets[ $worksheet_id ];
			
			if (isset($ws->hyperlinks)) {
				$this->hyperlinks = array();
				foreach( $ws->hyperlinks->hyperlink as $hyperlink ) {
					$this->hyperlinks[ (string) $hyperlink['ref'] ] = (string) $hyperlink['display'];
				}
			}
			
			return $ws;
		} 

		//else
			//throw new Exception('Worksheet '.$worksheet_id.' not found.');
	}
	
	public function dimension( $worksheet_id = 1 ) {
		$ws = $this->worksheet($worksheet_id);
		$ref = (string) $ws->dimension['ref'];
		$d = explode(':', $ref);
		$index = $this->_columnIndex( $d[1] );		
		return array( $index[0]+1, $index[1]+1);
	}

	public function rows( $worksheet_id = 1 ) {
		
		$ws = $this->worksheet( $worksheet_id);
		
		$rows = array();
		$curR = 0;
		$lastColumn = 0;

		foreach ($ws->sheetData->row as $r=>$row) {

			foreach ($row->c as $c) {

				list($curC,) = $this->_columnIndex((string) $c['r']);

				if ($curC > ($lastColumn+1)) {

					for ($i=($lastColumn+1); $i<$curC; $i++) {
						$rows[ $curR ][ $i ] = null;
					}
				}

				$rows[ $curR ][ $curC ] = $this->value($c);

				$lastColumn = $curC;
			}
			
			$curR++;
		}
		return $rows;
	}

	public function rowsEx( $worksheet_id = 1 ) {
		$rows = array();
		$curR = 0;
		if (($ws = $this->worksheet( $worksheet_id)) === false)
			return false;
		foreach ($ws->sheetData->row as $row) {
			
			foreach ($row->c as $c) {
				list($curC,) = $this->_columnIndex((string) $c['r']);
				$rows[ $curR ][ $curC ] = array(
					'name' => (string) $c['r'],
					'value' => $this->value($c),
					'href' => $this->href( $c ),
				);
			}
			$curR++;
		}
		return $rows;

	}

	protected function _columnIndex( $cell = 'A1' ) {
		
		if (preg_match("/([A-Z]+)(\d+)/", $cell, $matches)) {
			
			$col = $matches[1];
			$row = $matches[2];
			
			$colLen = strlen($col);
			$index = 0;

			for ($i = $colLen-1; $i >= 0; $i--)
				$index += (ord($col{$i}) - 64) * pow(26, $colLen-$i-1);

			return array($index-1, $row-1);
		} 

		//else
			//throw new Exception("Invalid cell index.");
	}
	public function value( $cell ) {
		$dataType = (string)$cell["t"];
		switch ($dataType) {
			case "s":
				if ((string)$cell->v != '') {
					$value = $this->sharedstrings[intval($cell->v)];
				} else {
					$value = '';
				}

				break;
				
			case "b":
				$value = (string)$cell->v;
				if ($value == '0') {
					$value = false;
				} else if ($value == '1') {
					$value = true;
				} else {
					$value = (bool)$cell->v;
				}

				break;
				
			case "inlineStr":
				$value = $this->_parseRichText($cell->is);
							
				break;
				
			case "e":
				if ((string)$cell->v != '') {
					$value = (string)$cell->v;
				} else {
					$value = '';
				}

				break;

			default:
				$value = (string)$cell->v;
				if (is_numeric($value) && $dataType != 's') {
					if ($value == (int)$value) $value = (int)$value;
					elseif ($value == (float)$value) $value = (float)$value;
					elseif ($value == (double)$value) $value = (double)$value;
				}
		}
		return $value;
	}
	public function href( $cell ) {
		return isset( $this->hyperlinks[ (string) $cell['r'] ] ) ? $this->hyperlinks[ (string) $cell['r'] ] : '';
	}

	protected function _unzip( $filename ) {
		$this->datasec = array();
		$this->package = array(
			'filename' => $filename,
			'mtime' => filemtime( $filename ),
			'size' => filesize( $filename ),
			'comment' => '',
			'entries' => array()
		);
		$oF = @fopen($filename, 'rb');
		if( $oF===false )
		{
			//throw new Exception('Could not open file: "'.$filename.'"' );
		}

		$vZ = @fread($oF, $this->package['size']);
		fclose($oF);
		$aE = explode("\x50\x4b\x05\x06", $vZ);
		$aP = unpack('x16/v1CL', $aE[1]);
		$this->package['comment'] = substr($aE[1], 18, $aP['CL']);
		$this->package['comment'] = strtr($this->package['comment'], array("\r\n" => "\n", "\r" => "\n"));
		$aE = explode("\x50\x4b\x01\x02", $vZ);
		$aE = explode("\x50\x4b\x03\x04", $aE[0]);
		array_shift($aE);
		foreach ($aE as $vZ) {
			$aI = array();
			$aI['E']  = 0;
			$aI['EM'] = '';
//			$aP = unpack('v1VN/v1GPF/v1CM/v1FT/v1FD/V1CRC/V1CS/V1UCS/v1FNL', $vZ);
			$aP = unpack('v1VN/v1GPF/v1CM/v1FT/v1FD/V1CRC/V1CS/V1UCS/v1FNL/v1EFL', $vZ);
//			$bE = ($aP['GPF'] && 0x0001) ? TRUE : FALSE;
			$bE = false;
			$nF = $aP['FNL'];
			$mF = $aP['EFL'];
			if ($aP['GPF'] & 0x0008) {
				$aP1 = unpack('V1CRC/V1CS/V1UCS', substr($vZ, -12));

				$aP['CRC'] = $aP1['CRC'];
				$aP['CS']  = $aP1['CS'];
				$aP['UCS'] = $aP1['UCS'];

				$vZ = substr($vZ, 0, -12);
			}
			$aI['N'] = substr($vZ, 26, $nF);

			if (substr($aI['N'], -1) == '/') {
				continue;
			}
			$aI['P'] = dirname($aI['N']);
			$aI['P'] = $aI['P'] == '.' ? '' : $aI['P'];
			$aI['N'] = basename($aI['N']);

			$vZ = substr($vZ, 26 + $nF + $mF);

			if (strlen($vZ) != $aP['CS']) {
			  $aI['E']  = 1;
			  $aI['EM'] = 'Compressed size is not equal with the value in header information.';
			} else {
				if ($bE) {
					$aI['E']  = 5;
					$aI['EM'] = 'File is encrypted, which is not supported from this class.';
				} else {
					switch($aP['CM']) {
						case 0:
							break;
						case 8:
							$vZ = gzinflate($vZ);
							break;
						case 12:
							if (! extension_loaded('bz2')) {
								if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
								  @dl('php_bz2.dll');
								} else {
								  @dl('bz2.so');
								}
							}
							if (extension_loaded('bz2')) {
								$vZ = bzdecompress($vZ);
							} else {
								$aI['E']  = 7;
								$aI['EM'] = "PHP BZIP2 extension not available.";
							}
							break;
						default:
						  $aI['E']  = 6;
						  $aI['EM'] = "De-/Compression method {$aP['CM']} is not supported.";
					}
					if (! $aI['E']) {
						if ($vZ === FALSE) {
							$aI['E']  = 2;
							$aI['EM'] = 'Decompression of data failed.';
						} else {
							if (strlen($vZ) != $aP['UCS']) {
								$aI['E']  = 3;
								$aI['EM'] = 'Uncompressed size is not equal with the value in header information.';
							} else {
								if (crc32($vZ) != $aP['CRC']) {
									$aI['E']  = 4;
									$aI['EM'] = 'CRC32 checksum is not equal with the value in header information.';
								}
							}
						}
					}
				}
			}

			$aI['D'] = $vZ;
			$aI['T'] = mktime(($aP['FT']  & 0xf800) >> 11,
							  ($aP['FT']  & 0x07e0) >>  5,
							  ($aP['FT']  & 0x001f) <<  1,
							  ($aP['FD']  & 0x01e0) >>  5,
							  ($aP['FD']  & 0x001f),
							  (($aP['FD'] & 0xfe00) >>  9) + 1980);

			//$this->Entries[] = &new SimpleUnzipEntry($aI);
			$this->package['entries'][] = array(
				'data' => $aI['D'],
				'error' => $aI['E'],
				'error_msg' => $aI['EM'],
				'name' => $aI['N'],
				'path' => $aI['P'],
				'time' => $aI['T']
			);

		}
	}
	public function getPackage() {
		return $this->package;
	}
	public function getEntryData( $name ) {
		$dir = dirname( $name );
		$name = basename( $name );
		foreach( $this->package['entries'] as $entry)
			if ( $entry['path'] == $dir && $entry['name'] == $name)
				return $entry['data'];
	}
	public function unixstamp( $excelDateTime ) {
		$d = floor( $excelDateTime );
		$t = $excelDateTime - $d;
		return ($d > 0) ? ( $d - 25569 ) * 86400 + $t * 86400 : $t * 86400;
	}
	protected function _parse() {
		$this->sharedstrings = array();
		$this->sheets = array();
		$relations = simplexml_load_string( $this->getEntryData("_rels/.rels") );
		foreach ($relations->Relationship as $rel) {
			if ($rel["Type"] == Xlsx::SCHEMA_OFFICEDOCUMENT) {
				$workbookRelations = simplexml_load_string($this->getEntryData( dirname($rel["Target"]) . "/_rels/" . basename($rel["Target"]) . ".rels") );
				$workbookRelations->registerXPathNamespace("rel", Xlsx::SCHEMA_RELATIONSHIP);
				$sharedStringsPath = $workbookRelations->xpath("rel:Relationship[@Type='" . Xlsx::SCHEMA_SHAREDSTRINGS . "']");
				$sharedStringsPath = (string)$sharedStringsPath[0]['Target'];              
				$xmlStrings = simplexml_load_string($this->getEntryData( dirname($rel["Target"]) . "/" . $sharedStringsPath) );            
				if (isset($xmlStrings) && isset($xmlStrings->si)) {
					foreach ($xmlStrings->si as $val) {
						if (isset($val->t)) {
							$this->sharedstrings[] = (string)$val->t;
						} elseif (isset($val->r)) {
							$this->sharedstrings[] = $this->_parseRichText($val);
						}
					}
				}
				foreach ($workbookRelations->Relationship as $workbookRelation) {
					if ($workbookRelation["Type"] == Xlsx::SCHEMA_WORKSHEETRELATION) {
						$this->sheets[ str_replace( 'rId', '', (string) $workbookRelation["Id"]) ] =
							simplexml_load_string( $this->getEntryData( dirname($rel["Target"]) . "/" . dirname($workbookRelation["Target"]) . "/" . basename($workbookRelation["Target"])) );
					}
				}
				
				break;
			}
		}
		ksort($this->sheets);
	}
    protected function _parseRichText($is = null) {
        $value = array();

        if (isset($is->t)) {
            $value[] = (string)$is->t;
        } else {
            foreach ($is->r as $run) {
                $value[] = (string)$run->t;
            }
        }

        return implode(' ', $value);
    }
}
?>