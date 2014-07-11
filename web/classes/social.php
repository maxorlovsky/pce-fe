<?php
class Social
{
    private $config;
    
    function __construct($social = '') {
		$this->config = _cfg('social'); 
	}
	
	public function Verify($provider) {
        $_SESSION['errors'] = array();
		if(!$provider) {
            $_SESSION['errors'][] = 'Provider error';
			return false;
		}
		
		if(!isset($this->config[$provider])) {
            $_SESSION['errors'][] = 'No social config: '.$provider;
            return false;
        }
        
		$this->config = $this->config[$provider];
			
		if(isset($_SESSION['social'][$provider])) {
			return $this->{$provider.'Complete'}();
		}
        else if(method_exists($this, $provider.'Verify')) {
			return $this->{$provider.'Verify'}();
		}
        
        return false;
	}
	
	public function getToken($provider) {
		if(!method_exists($this, $provider)) {
			return '0;No social method: '.$provider;
		} 
		
		if(!isset($this->config[$provider])) {
            return '0;No social config: '.$provider;
        }
        
		$this->config = $this->config[$provider];
		
		return $this->$provider();
	}
	
	private function vkComplete($data = array()) {
		
		$user = $_POST;
		$user['password'] = 'social_vk';
		$user['social'] = 'vk';
		
		if(empty($data)) {
			if(!isset($_SESSION['social']) || !isset($_SESSION['social']['vk'])) return array('error'=>'auth error ('.__LINE__.')');
			$data = $_SESSION['social']['vk'];
		}
		
		$user['firstName'] = !empty($data['first_name']) ? $data['first_name'] : 'None';
		$user['lastName'] = !empty($data['last_name']) ? $data['last_name'] : 'None';
		$user['social_uid'] = $data['uid'];
		
		$user = User::socialLogin($user);
		
		if($user===false) return array('error'=>'auth error ('.__LINE__.')');
		
		return $user;
		
	}

	private function vkVerify() {
		
		if(!isset($_GET['code']) || empty($_GET['code'])) {
			if(isset($_GET['error']) && !empty($_GET['error'])) $err = $_GET['error'];
			else $err = 'Auth error';
		}
		
		$cfg = array(
		    'url'=>'https://oauth.vk.com/access_token',
			'get'=>array(
					'client_id'=>$this->config['id'],
					'client_secret'=>$this->config['private'],
					'code'=>$_GET['code'],
					'redirect_uri'=>_cfg('site').'/'._cfg('language').'/social/login/vk'
					),
		);
		
		$f = $this->oAuthRequest($cfg);
		if($f === false ) return array('error'=>'auth error ('.__LINE__.')');
		
		$f = json_decode($f,1);
		if(!isset($f['user_id']) || !isset($f['access_token'])) return array('error'=>'auth error ('.__LINE__.')');
		 
		$cfg = array(
			'url'=>'https://api.vk.com/method/getProfiles',
			'get'=>array(
					'uid'=>$f['user_id'],
					'access_token'=>$f['access_token']
			),
		);
		
		$f = $this->oAuthRequest($cfg);
		if($f === false ) return array('error'=>'auth error ('.__LINE__.')');
		
		$f = json_decode($f,1);
		if(!isset($f['response']) || !isset($f['response'][0]) || !isset($f['response'][0]['uid'])) return array('error'=>'auth error ('.__LINE__.')');
		
		$f = $f['response'][0];
		
		$_SESSION['social']['vk'] = $f;
		
		return $this->vkComplete($f);
		
	}
	
	private function vk() {
		if(isset($_SESSION['social']['vk'])) unset($_SESSION['social']['vk']);
		
		$url = 'http://oauth.vk.com/authorize'
			  .'?client_id='.$this->config['id']
			  .'&redirect_uri='._cfg('site').'/'._cfg('language').'/social/login/vk'
			  .'&response_type=code';
		
		return $url;
	}
	
	private function fbComplete($data = array()) {
		$user = $_POST;
		$user['password'] = 'social_fb';
		$user['social'] = 'fb';
	
		if(empty($data)) {
			if(!isset($_SESSION['social']) || !isset($_SESSION['social']['fb'])) {
                $_SESSION['errors'][] = 'Authorization error. Already inside! ('.__LINE__.')';
                return false;
            }
			$data = $_SESSION['social']['fb'];
		}
	
		$user['name'] = 'Anonymous';
		if(isset($data['email'])) {
			$user['email'] = $data['email'];
		}
		$user['social_uid'] = $data['id'];

		$user = User::socialLogin($user);
		if($user!==true) {
			if(!is_array($user)) {
                $user = json_decode($user,1);
            }
			
			if(isset($user['error']['email'])) {
                $_SESSION['errors'][] = 'Error '.$user['error']['email'].' ('.__LINE__.')';
                return false;
            }
			else {
                $_SESSION['errors'][] = 'Authorization error ('.__LINE__.')';
                return false;
            }
		}
		
		return true;
	}
	
	private function fbVerify() {
		if(!isset($_GET['code']) || empty($_GET['code'])) {
			if(isset($_GET['error']) && !empty($_GET['error'])) {
                $_SESSION['errors'][] = $_GET['error'];
            }
			else {
                $_SESSION['errors'][] = 'Authorization error';
            }
            
            return false;
		}
	
		$cfg = array(
            'url'=>'https://graph.facebook.com/oauth/access_token',
            'get'=>array(
                'code'=>$_GET['code'],
                'redirect_uri'=>_cfg('site').'/run/social/fb',
                'client_id'=>$this->config['id'],
                'client_secret'=>$this->config['private'],
                //'grant_type'=>'client_credentials'
            ),
		);
	
		$f = $this->oAuthRequest($cfg);
		if($f === false ) {
            $_SESSION['errors'][] = 'Authorization error ('.__LINE__.')';
            return false;
        }
	
		parse_str($f,$f);
		 
		if(!isset($f['access_token'])) {
            $_SESSION['errors'][] = 'Access token error ('.__LINE__.')';
            return false;
        }
	
		$cfg = array(
            'url'=>'https://graph.facebook.com/me',
            'get'=>array(
                'access_token'=>$f['access_token'],
            ),
		);
	
		$f = $this->oAuthRequest($cfg);
		if($f === false ) {
            $_SESSION['errors'][] = 'Authorization error ('.__LINE__.')';
            return false;
        }
	
		$f = json_decode($f,1);
		
		if(!isset($f['id'])) {
            $_SESSION['errors'][] = 'Authorization error ('.__LINE__.')';
            return false;
        }
	
		$_SESSION['social']['fb'] = $f;
	
		return $this->fbComplete($f);
	}
	
	private function fb() {
		if (isset($_SESSION['social']['fb'])) {
            unset($_SESSION['social']['fb']);
        }
	
		$url = 'https://www.facebook.com/dialog/oauth'
				.'?client_id='.$this->config['id']
				.'&redirect_uri='._cfg('site').'/run/social/fb'
				.'&scope=public_profile,email'
				.'&response_type=code';
	
		return $url;
	}
	
	private function gpComplete($data = array()) {
	
		$user = $_POST;
		$user['password'] = 'social_gp';
		$user['social'] = 'gp';
	
		if(empty($data)) {
			if(!isset($_SESSION['social']) || !isset($_SESSION['social']['ya'])) return array('error'=>'auth error ('.__LINE__.')');
			$data = $_SESSION['social']['gp'];
		}
	
		$user['firstName'] = !empty($data['given_name']) ? $data['given_name'] : 'None';
		$user['lastName'] = !empty($data['family_name']) ? $data['family_name'] : 'None';
		$user['social_uid'] = $data['id'];
		if(isset($data['email'])) {
			$user['email'] = $data['email'];
			$user['i_agree'] = 1;
		}
	
		$user =  User::socialLogin($user);
	
		if($user===false) return array('error'=>'auth error ('.__LINE__.')');
	
		if($user!==1) {
			if(!is_array($user)) $user = json_decode($user,1);
				
			if(isset($user['error']['email'])) return array('error'=>$user['error']['email']);
			else return array('error'=>'Auth error '.__LINE__);
		}
	
		header('Location: '._cfg('site').'/'._cfg('language'));
		die();
	
	}
	
	private function gpVerify() {
	
		if(!isset($_GET['code']) || empty($_GET['code'])) {
			if(isset($_GET['error']) && !empty($_GET['error'])) $err = $_GET['error'];
			else $err = 'Auth error';
	
			return $err;
		}
	
		$cfg = array(
				'url'=>'https://accounts.google.com/o/oauth2/token',
				'post'=>array(
						'code'=>$_GET['code'],
						'redirect_uri'=>_cfg('site').'/'._cfg('language').'/social/login/gp',
						'grant_type'=>'authorization_code',
						'client_id'=>$this->config['id'],
						'client_secret'=>$this->config['private'],
				),
		);
	
		$f = $this->oAuthRequest($cfg);
		if($f === false ) return array('error'=>'auth error ('.__LINE__.')');
	
		$f = json_decode($f,1);
		if(!isset($f['access_token'])) return array('error'=>'auth error ('.__LINE__.')');
	
		$cfg = array(
				'url'=>'https://www.googleapis.com/oauth2/v1/userinfo',
				//'headers'=>array('Authorization: OAuth '.$f['access_token']),
				'get'=>array(
					'access_token'=>$f['access_token'],
				),
		);
	
		$f = $this->oAuthRequest($cfg);
		if($f === false ) return array('error'=>'auth error ('.__LINE__.')');
	
	
		$f = json_decode($f,1);
		if(!isset($f['id'])) return array('error'=>'auth error ('.__LINE__.')');
	
		$_SESSION['social']['gp'] = $f;
	
		return $this->gpComplete($f);
	}
	
	private function gp() {
		if(isset($_SESSION['social']['gp'])) unset($_SESSION['social']['gp']);
	
		$url = 'https://accounts.google.com/o/oauth2/auth'
				.'?redirect_uri='._cfg('site').'/'._cfg('language').'/social/login/gp'
				.'&client_id='.$this->config['id']
				.'&scope=https://www.googleapis.com/auth/userinfo.email'
				.'&response_type=code';
	
		return $url;
	}
	
	private function twComplete($data = array()) {
	
		$user = $_POST;
		$user['password'] = 'social_tw';
		$user['social'] = 'tw';
	
		if(empty($data)) {
			if(!isset($_SESSION['social']) || !isset($_SESSION['social']['tw'])) return array('error'=>'auth error ('.__LINE__.')');
			$data = $_SESSION['social']['tw'];
		}
	
		$user['firstName'] = !empty($data['screen_name']) ? $data['screen_name'] : 'None';
		$user['lastName'] = !empty($data['name']) ? $data['name'] : 'None';
		$user['social_uid'] = $data['id'];
	
		$user = User::socialLogin($user);
	
		if($user===false) return array('error'=>'auth error ('.__LINE__.')');
	
		return $user;
	
	
	}
	
	function twVerify() {
		$params = array(
            'url'	=> 'https://api.twitter.com/oauth/access_token',
            'callback' => urlencode(_cfg('site').'/'._cfg('language').'/social/login/twitter'),
            'id'    => $this->config['id'],
            'secret'=> $this->config['private'],
            'token'=> $_GET['oauth_token'],
            'verifier'=>$_GET['oauth_verifier'],
            'nonce' =>	md5(uniqid(rand(), true)),
            'time'  => time(),
		);
	
		// oauth_token_secret получаем из сессии, которую зарегистрировали
		// во время запроса request_token
		$oauth_token_secret = $_SESSION['social']['twitter']['oauth_token_secret'];
	
		$oauth_base_text = "GET&";
		$oauth_base_text .= urlencode($params['url'])."&";
		$oauth_base_text .= urlencode("oauth_consumer_key=".$params['id']."&");
		$oauth_base_text .= urlencode("oauth_nonce=".$params['nonce']."&");
		$oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
		$oauth_base_text .= urlencode("oauth_token=".$params['token']."&");
		$oauth_base_text .= urlencode("oauth_timestamp=".$params['time']."&");
		$oauth_base_text .= urlencode("oauth_verifier=".$params['verifier']."&");
		$oauth_base_text .= urlencode("oauth_version=1.0");
	
		$key = $params['secret']."&".$_SESSION['social']['twitter']['oauth_token_secret'];
		$oauth_signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));
	
		$cfg = array(
			'url'=>$params['url'],
			'get'=>array(
				'oauth_nonce'=>$params['nonce'],
				'oauth_signature_method'=>'HMAC-SHA1',
				'oauth_timestamp'=>$params['time'],
				'oauth_consumer_key'=>$params['id'],
				'oauth_token'=>$params['token'],
				'oauth_verifier'=>$params['verifier'],
				'oauth_signature'=>$oauth_signature,
				'oauth_version'=>'1.0'
			)		
		);
		
		$f = $this->oAuthRequest($cfg);
		parse_str($f, $f);
	
		$oauth_nonce = md5(uniqid(rand(), true));
	
		// время когда будет выполняться запрос (в секундых)
		$oauth_timestamp = time();
	
		$oauth_token = $f['oauth_token'];
		$oauth_token_secret = $f['oauth_token_secret'];
		$screen_name = $f['screen_name'];
	
		$oauth_base_text = "GET&";
		$oauth_base_text .= urlencode('https://api.twitter.com/1.1/users/show.json').'&';
		$oauth_base_text .= urlencode('oauth_consumer_key='.$params['id'].'&');
		$oauth_base_text .= urlencode('oauth_nonce='.$oauth_nonce.'&');
		$oauth_base_text .= urlencode('oauth_signature_method=HMAC-SHA1&');
		$oauth_base_text .= urlencode('oauth_timestamp='.$oauth_timestamp."&");
		$oauth_base_text .= urlencode('oauth_token='.$oauth_token."&");
		$oauth_base_text .= urlencode('oauth_version=1.0&');
		$oauth_base_text .= urlencode('screen_name=' . $screen_name);
	
		$key = $params['secret'] . '&' . $oauth_token_secret;
		$signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));
	
		// Формируем GET-запрос
		$cfg = array(
		    'url'=>'https://api.twitter.com/1.1/users/show.json',
			'get'=>array(
                'oauth_consumer_key'=>$params['id'],
                'oauth_nonce'=>$oauth_nonce,
                'oauth_signature'=>$signature,
                'oauth_signature_method'=>'HMAC-SHA1',
                'oauth_timestamp'=>$oauth_timestamp,
                'oauth_token'=>$oauth_token,
                'oauth_version'=>'1.0',
                'screen_name'=>$screen_name
			)
		);

		$f = $this->oAuthRequest($cfg);
	
		$f = json_decode($f,1);
		if(!isset($f['id'])) return array('error'=>'auth error ('.__LINE__.')');
		
		$_SESSION['social']['tw'] = $f;
		
		return $this->twComplete($f);
	}
	
	private function tw() {
		if(isset($_SESSION['social']['tw'])) unset($_SESSION['social']['tw']);
		
		$params = array(
			'url'	=> 'https://api.twitter.com/oauth/request_token',
			'callback' => urlencode(_cfg('site').'/run/social/tw'),		
			'id'    => $this->config['id'],
			'secret'=> $this->config['private'],
			'nonce' =>	md5(uniqid(rand(), true)),
			'time'  => time(),
		);
		
		// ПОРЯДОК ПАРАМЕТРОВ ДОЛЖЕН БЫТЬ ИМЕННО ТАКОЙ!
		// Т.е. сперва oauth_callback -> oauth_consumer_key -> ... -> oauth_version.
		$oauth_base_text = "GET&";
		$oauth_base_text .= urlencode($params['url'])."&";
		$oauth_base_text .= urlencode("oauth_callback=".$params['callback']."&");
		$oauth_base_text .= urlencode("oauth_consumer_key=".$params['id']."&");
		$oauth_base_text .= urlencode("oauth_nonce=".$params['nonce']."&");
		$oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
		$oauth_base_text .= urlencode("oauth_timestamp=".$params['time']."&");
		$oauth_base_text .= urlencode("oauth_version=1.0");
		
		// Формируем ключ
		// На конце строки-ключа должен быть амперсанд & !!!
		$key = $params['secret']."&";
		
		// Формируем oauth_signature
		$signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));
		
		// Формируем GET-запрос
		$url = $params['url'];
		$url .= '?oauth_callback='.$params['callback'];
		$url .= '&oauth_consumer_key='.$params['id'];
		$url .= '&oauth_nonce='.$params['nonce'];
		$url .= '&oauth_signature='.urlencode($signature);
		$url .= '&oauth_signature_method=HMAC-SHA1';
		$url .= '&oauth_timestamp='.$params['time'];
		$url .= '&oauth_version=1.0';
		
		// Выполняем запрос
		$response = file_get_contents($url);
		parse_str($response,$response);
		
		$_SESSION['social']['twitter']['oauth_token_secret'] = $response['oauth_token_secret'];
        
		return 'https://api.twitter.com/oauth/authorize?oauth_token='.$response['oauth_token'];
	}
	
	private function oAuthRequest($cfg) {
		$ch = curl_init();
	
		if(isset($cfg['get'])) {
			foreach($cfg['get'] as $k=>$v) {
				$cfg['get'][$k] = $k.'='.urlencode($v);
			}
				
			$cfg['url'] = $cfg['url'] . '?'.implode('&',$cfg['get']);
		} 
	
		$curlOptions = array (
            CURLOPT_URL => $cfg['url'],
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => isset($cfg['headers']) ? $cfg['headers'] : array(),
            CURLOPT_VERBOSE => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_RETURNTRANSFER => TRUE,
		);
		
		if(isset($cfg['post'])) {
			$curlOptions[CURLOPT_POST] = 1;
			$curlOptions[CURLOPT_POSTFIELDS] = $cfg['post'];
		}
	
		curl_setopt_array($ch,$curlOptions);
		$response = curl_exec($ch); // run the whole process
		$status = curl_getinfo($ch);
		curl_close($ch);
	
	
		if($status['http_code']!=200) {
            $_SESSION['errors'][] = $response.' ('.__LINE__.')';
			if(_cfg('env')=='dev') {
                echo '<pre>';
				print_r($cfg);
				echo $response;
				print_r($status);
                echo '</pre>';
			}
			return false;
		}
	
		return $response;
	}
}