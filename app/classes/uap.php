<?php


class Uap {

  private static $result,
				 $pairs,
				 $brace,
				 $loose;

  private static function SetDefaults() {
    self::$result = array();
    self::$result = array(
      'OS_NAME'     => '',
      'OS_VERSION'  => '',
      'OS_LINUX'    => false,
      'BR_NAME'     => '',
      'BR_VERSION'  => '',
      'HW_TYPE'     => 'desktop',
      'HW_NAME'     => '',
      'HW_BRAND'    => '',
      'APPLE_BUILD' => '',
    );
  }

  private static function SetVal($key, $val) {
    if (!array_key_exists($key, self::$result)) {
      throw new Exception('Undefined key="'. $key .'"');
    }
    self::$result[$key] = $val;
  }

  private static function GetVal($key) {
    if (!array_key_exists($key, self::$result)) {
      throw new Exception('Undefined key="'. $key .'"');
    }
    return self::$result[$key];
  }
  
  private static function GetResult() {
    return self::$result;
  }
  
  private static function Tokenize($ua) {
    self::$pairs = array();
    self::$brace = array();
    self::$loose = array();
    if (preg_match_all('/(\((\S[^\)]+)|([^\(\)\s\/]+\/[^\(\)\s\/]+)|(\b[a-zA-Z][^\/]*[a-zA-Z\d\.]+)(\ |$))+/', $ua, $matches)) {
      foreach ($matches[2] as $v) {
        if (!empty($v)) {
          self::$brace[] = $v;
        }
      }
      foreach ($matches[4] as $v) {
        if (!empty($v)) {
          self::$loose[] = $v;
        }
      }
	  
      foreach ($matches[3] as $v) {
        if (!empty($v)) {
          list($key, $val) = explode('/', $v);
          self::$pairs[$key] = $val;
        }
      }
    }
  }
  
  public static function get($ua) {
    self::SetDefaults();
    self::Tokenize($ua);
    if (!empty(self::$brace[0])) {
      // OS and version
      if (preg_match('/(iPhone|iPad|iPod)\ OS\ (\d+[_\.]\d+[_\.]?\d*)\ like\ Mac/', self::$brace[0], $matches)) {
        self::SetVal('OS_NAME',    'iOS');
        self::SetVal('OS_VERSION', str_replace('_', '.', $matches[2]));
        self::SetVal('HW_TYPE',    'mobile');
        self::SetVal('HW_NAME',    $matches[1]);
        self::SetVal('HW_BRAND',   'Apple');
      }
      elseif (preg_match('/Intel\ Mac\ OS\ X\ (\d+[_\.]\d+[_\.]?\d*)/', self::$brace[0], $matches)) {
        self::SetVal('OS_NAME',    'macOS');
        self::SetVal('OS_VERSION', str_replace('_', '.', $matches[1]));
        self::SetVal('HW_TYPE',    'desktop');
        self::SetVal('HW_NAME',    'Macintosh');
        self::SetVal('HW_BRAND',   'Apple');
      }
      elseif (preg_match('/Windows\ NT\ (\d+\.\d+\.?\d*)/', self::$brace[0], $matches)) {
        self::SetVal('OS_NAME',    'Windows');
        self::SetVal('OS_VERSION', $matches[1]);
      }
      // Nokia5250/10.0.011 (SymbianOS/9.4; U; Series60/5.0 Mozilla/5.0; Profile/MIDP-2.1 Configuration/CLDC-1.1 ) AppleWebKit/525 (KHTML, like Gecko) Safari/525 3gpp-gba
      elseif (preg_match('/SymbianOS\/(\d+\.\d+\.?\d*);/', self::$brace[0], $matches)) {
        self::SetVal('OS_NAME',    'SymbianOS');
        self::SetVal('OS_VERSION', $matches[1]);
      }
      if (strpos(self::$brace[0], 'Linux') !== false || strpos(self::$brace[0], 'X11;') !== false) {
        self::SetVal('OS_NAME',    'Linux');
        self::SetVal('OS_LINUX',   true);
        self::SetVal('OS_VERSION', 0);
      }
      if (preg_match('/\ Android\ (\d+\.\d+\.?\d*)/', self::$brace[0], $matches)) {
        self::SetVal('OS_NAME',    'Android');
        self::SetVal('OS_VERSION', $matches[1]);
        self::SetVal('OS_LINUX',   true);
      }
      elseif (strpos(self::$brace[0], 'Ubuntu') !== false) {
        self::SetVal('OS_NAME',    'Ubuntu');
        self::SetVal('OS_LINUX',   true);
      }
      elseif (strpos(self::$brace[0], 'Fedora') !== false) {
        self::SetVal('OS_NAME',    'Fedora');
        self::SetVal('OS_LINUX',   true);
      }
      elseif (strpos($ua, 'Arch Linux') !== false) {
        self::SetVal('OS_NAME',    'Arch');
        self::SetVal('OS_LINUX',   true);
      }
      elseif (preg_match('/Linux (\d+\.[\d\.]+)-ARCH/', $ua, $matches)) {
        self::SetVal('OS_NAME',    'Arch');
        self::SetVal('OS_VERSION', $matches[1]);
        self::SetVal('OS_LINUX',   true);
      }
      elseif (strpos($ua, 'Red Hat') !== false) {
        self::SetVal('OS_NAME',    'Red Hat');
        self::SetVal('OS_LINUX',   true);
      }
      elseif (strpos($ua, 'FreeBSD') !== false) {
        self::SetVal('OS_NAME',    'FreeBSD');
        self::SetVal('OS_LINUX',   true);
      }
      elseif (strpos($ua, 'OpenBSD') !== false) {
        self::SetVal('OS_NAME',    'OpenBSD');
        self::SetVal('OS_LINUX',   true);
      }
      elseif (strpos($ua, 'NetBSD') !== false) {
        self::SetVal('OS_NAME',    'NetBSD');
        self::SetVal('OS_LINUX',   true);
      }
      elseif (preg_match('/ Tizen (\d+\.[\d\.]+)/', self::$brace[0], $matches)) {
        self::SetVal('OS_NAME',    'Tizen');
        self::SetVal('OS_VERSION', $matches[1]);
        self::SetVal('OS_LINUX',   true);
      }
      elseif (preg_match('/Windows\ Phone\ (\d+\.[\d\.]+);\ Android\ (\d+\.[\d\.]+)/', self::$brace[0], $matches)) {
        self::SetVal('OS_LINUX',   true);
        self::SetVal('OS_NAME',    'Windows Phone');
        self::SetVal('OS_VERSION', $matches[1]);
        self::SetVal('HW_TYPE',    'mobile');
      }
      // Explorer
      if (preg_match('/Windows\ NT\ \d+\.\d+;.*\ Trident\/7\.0;.*\ rv:11\.0$/', self::$brace[0])) {
        self::SetVal('BR_NAME',    'Explorer');
        self::SetVal('BR_VERSION', '11.0');
      }
      elseif (preg_match('/;\ MSIE\ (\d+\.\d+[a-z]?);\ Windows\ /', self::$brace[0], $matches)) {
        self::SetVal('BR_NAME',    'Explorer');
        self::SetVal('BR_VERSION', $matches[1]);
      }
      // TV
      if (substr(self::$brace[0], 0, 9) == 'SMART-TV;') {
        self::SetVal('HW_TYPE', 'desktop');
      }
    }
    // Safari
    if (!empty(self::$pairs['Safari'])) {
      // Nokia5250/10.0.011 (SymbianOS/9.4; U; Series60/5.0 Mozilla/5.0; Profile/MIDP-2.1 Configuration/CLDC-1.1 ) AppleWebKit/525 (KHTML, like Gecko) Safari/525 3gpp-gba
      if (preg_match('/^Nokia5250\//', self::$brace[0]) && !empty(self::$pairs['Nokia5250'])) {
        self::SetVal('BR_NAME',    'Nokia Browser');
        self::SetVal('BR_VERSION', self::$pairs['Nokia5250']);
        self::SetVal('HW_TYPE',    'mobile');
        self::SetVal('HW_BRAND',   'Nokia');
      }
      elseif (!empty(self::$pairs['Chrome'])) {
        self::SetVal('BR_NAME',    'Chrome');
        self::SetVal('BR_VERSION', self::$pairs['Chrome']);
      }
      elseif (!empty(self::$pairs['CriOS'])) {
        self::SetVal('BR_NAME',    'CriOS');
        self::SetVal('BR_VERSION', self::$pairs['CriOS']);
      }
      elseif (!empty(self::$pairs['OPR'])) {
        self::SetVal('BR_NAME',    'Opera');
        self::SetVal('BR_VERSION', self::$pairs['OPR']);
      }
      elseif (!empty(self::$pairs['Epiphany'])) {
        self::SetVal('BR_NAME',    'Epiphany');
        self::SetVal('BR_VERSION', self::$pairs['Epiphany']);
      }
      elseif (!empty(self::$pairs['Version'])) {
        self::SetVal('BR_NAME',    'Safari');
        self::SetVal('BR_VERSION', self::$pairs['Version']);
      }
      // Build code
      if (self::GetVal('HW_BRAND') == 'Apple' && !empty(self::$pairs['Mobile'])) {
        self::SetVal('APPLE_BUILD', self::$pairs['Mobile']);
        if (self::GetVal('BR_NAME') == 'Safari') {
          self::SetVal('BR_NAME',    'Mobile Safari');
        }
      }
    }
    // Opera
    if (!empty(self::$pairs['Opera']) && !empty(self::$pairs['Version'])) {
      self::SetVal('BR_NAME',    'Opera');
      self::SetVal('BR_VERSION', self::$pairs['Version']);
    }
    if (!empty(self::$loose[0]) && substr(self::$loose[0], 0, 6) == 'Opera ') {
      self::SetVal('BR_NAME',    'Opera');
      self::SetVal('BR_VERSION', self::EndExplode(' ', self::$loose[0]));
    }
    // Firefox
    if (!empty(self::$pairs['Firefox'])) {
      self::SetVal('BR_NAME',    'Firefox');
      self::SetVal('BR_VERSION', self::$pairs['Firefox']);
    }
    if (!empty(self::$pairs['Iceweasel'])) {
      self::SetVal('BR_NAME',    'Iceweasel');
      self::SetVal('BR_VERSION', self::$pairs['Iceweasel']);
    }
    if (!empty(self::$pairs['YaBrowser'])) {
      self::SetVal('BR_NAME',    'YaBrowser');
      self::SetVal('BR_VERSION', self::$pairs['YaBrowser']);
    }
    if (!empty(self::$pairs['UCBrowser'])) {
      self::SetVal('BR_NAME',    'UC');
      self::SetVal('BR_VERSION', self::$pairs['UCBrowser']);
    }
    if (!empty(self::$pairs['MQQBrowser'])) {
      self::SetVal('BR_NAME',    'QQ');
      self::SetVal('BR_VERSION', self::$pairs['MQQBrowser']);
    }
    elseif (!empty(self::$pairs['QQBrowser'])) {
      self::SetVal('BR_NAME',    'QQ');
      self::SetVal('BR_VERSION', self::$pairs['QQBrowser']);
    }
    if (!empty(self::$pairs['coc_coc_browser'])) {
      self::SetVal('BR_NAME',    'CocCoc');
      self::SetVal('BR_VERSION', self::$pairs['coc_coc_browser']);
    }
    if (!empty(self::$pairs['Maxthon'])) {
      self::SetVal('BR_NAME',    'Maxthon');
      self::SetVal('BR_VERSION', self::$pairs['Maxthon']);
    }
    if (!empty(self::$pairs['Lunascape'])) {
      self::SetVal('BR_NAME',    'Lunascape');
      self::SetVal('BR_VERSION', self::$pairs['Lunascape']);
    }
    if (!empty(self::$pairs['Netsurf'])) {
      self::SetVal('BR_NAME',    'Netsurf');
      self::SetVal('BR_VERSION', self::$pairs['Netsurf']);
    }
    elseif (!empty(self::$pairs['InternetSurfboard'])) {
      self::SetVal('BR_NAME',    'Netsurf');
      self::SetVal('BR_VERSION', self::$pairs['InternetSurfboard']);
    }
    // Edge
    if (!empty(self::$pairs['Edge'])) {
      self::SetVal('BR_NAME',    'Edge');
      self::SetVal('BR_VERSION', self::$pairs['Edge']);
    }
    // Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/66.0.3349.0 Safari/537.36
    if (self::GetVal('BR_NAME') == '' && !empty(self::$pairs)) {
      foreach (self::$pairs as $key => $val) {
        if (preg_match('/Chrome$/', $key)) {
          self::SetVal('BR_NAME',    'Chrome');
          self::SetVal('BR_VERSION', $val);
          break;
        }
      }
    }
    // Debian
    if (!empty(self::$pairs['Debian'])) {
      self::SetVal('OS_NAME',    'Debian');
      self::SetVal('OS_VERSION', self::$pairs['Debian']);
      self::SetVal('OS_LINUX',   true);
    }
    elseif (!empty(self::$pairs['Fedora'])) {
      self::SetVal('OS_NAME',    'Fedora');
      self::SetVal('OS_VERSION', self::$pairs['Fedora']);
      self::SetVal('OS_LINUX',   true);
    }
    if (self::GetVal('HW_TYPE') !== 'mobile') {
      if (preg_match('/(tablet|pad|mobile|phone|symbian|android|ipod|ios|blackberry|webos|nokia)/i', $ua)) {
        self::SetVal('HW_TYPE', 'mobile');
      }
    }
    return self::GetResult();
  }
  
  private static function EndExplode($glue, $str) {
    if (strpos($str, $glue) === false) {
      return $str;
    }
    $str = explode($glue, $str);
    return end($str);
  }
}