<?php
namespace ghopunk\Helpers;

class Cookie {
	
	public $domain;
	public $path;
	public $expired;
	
	public function __construct( $path=false, $expired=false ){
		$this->domain = $_SERVER['HTTP_HOST'];
		if( empty($path) ) {
			$path = '/';
		}
		$this->path = $path;
		
		if( empty($expired) ) {
			$expired = time() + (3600 * 24 * 1); // 1 day
		}
		$this->expired = $expired;
	}
	
	public function set( $name, $value=false, $expired=false ){
		//if empty asumtion is remove
		if( empty($value) ){
			return $this->remove($name);
		}
		if( !empty($expired) ){
			$this->expired = $expired;
		}
		if( PHP_VERSION_ID < 70300 ){
			$expiryTime = gmdate( 'D, d-M-Y H:i:s T', $this->expired );
			if( Is::httpsSite() ){
				$samesite = 'SameSite=None; Secure';
			} else {
				$samesite = 'SameSite=Lax;';
			}
			header( 'Set-Cookie: ' . $name . '=' . urlencode($value) . '; path=' . $this->path . '; domain=' . $this->domain . '; expires=' . $expiryTime . '; ' . $samesite, false );
		} else {
			if( Is::httpsSite() ){
				$samesite = 'None';
			} else {
				$samesite = 'Lax';
			}
			$cookie_options = [
								'expires' 	=> $this->expired,
								'path' 		=> $this->path,
								'domain' 	=> $this->domain,
								'secure' 	=> Is::httpsSite(),
								'samesite' 	=> $samesite
							];
			setcookie( $name, $value, $cookie_options );
		}
	}

	public function remove($name){
		if( !$this->get($name) ){
			return false;
		}
		$value 		= '';
		$expired	= time() - 3600;
		if( PHP_VERSION_ID < 70300 ){
			$expiryTime = gmdate( 'D, d-M-Y H:i:s T', $expired);
			if( Is::httpsSite() ){
				$samesite = 'SameSite=None; Secure';
			} else {
				$samesite = 'SameSite=Lax;';
			}
			header( 'Set-Cookie: ' . $name . '=deleted; path=' . $this->path . '; domain=' . $this->domain . '; expires=' . $expiryTime . '; ' . $samesite );
		} else {
			if( Is::httpsSite() ){
				$samesite = 'None';
			} else {
				$samesite = 'Lax';
			}
			$cookie_options = [
								'expires' 	=> $expired,
								'path' 		=> $this->path,
								'domain' 	=> $this->domain,
								'secure' 	=> Is::httpsSite(),
								'samesite' 	=> $samesite
							];
			setcookie( $name, $value, $cookie_options );
		}
	}

	public function get( $name=false ){
		$value = false;
		if( !empty( $name ) && isset( $_COOKIE[$name] ) ){
			$value = $_COOKIE[$name];
		}
		return $value;
	}
	
}