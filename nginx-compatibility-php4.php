<?php
/*
Plugin Name: nginx Compatibility (PHP4)
Plugin URI: http://blog.sjinks.pro/wordpress-plugins/nginx-compatibility/
Description: Makes WordPress more friendly to nginx.
Version: 0.2.5
Author: Vladimir Kolesnikov
Author URI: http://blog.sjinks.pro/
License: BSD
*/

	defined('ABSPATH') or die();

	class SjNginxCompat
	{
		var $have_nginx;

		function& instance()
		{
			static $self = false;
			if (!$self) {
				$self = new SjNginxCompat();
			}

			return $self;
		}

		function SjNginxCompat()
		{
			$this->have_nginx = ('nginx' == substr($_SERVER['SERVER_SOFTWARE'], 0, 5));
			if ($this->have_nginx) {
				add_filter('got_rewrite', array(&$this, 'got_rewrite'), 999);

				// For compatibility with several plugins and nginx HTTPS proxying schemes
				if (empty($_SERVER['HTTPS']) || 'off' == $_SERVER['HTTPS']) {
					unset($_SERVER['HTTPS']);
				}
			}
		}

		function got_rewrite($got)
		{
			return true;
		}

		function haveNginx()
		{
			return $this->have_nginx;
		}
	}

	$nginx_compat = &SjNginxCompat::instance();
	if ($nginx_compat->haveNginx() && !function_exists('wp_redirect')) {

		function wp_redirect($location, $status = 302)
		{
			$location = apply_filters('wp_redirect', $location, $status);

			if (empty($location)) {
				return false;
			}

			$status = apply_filters('wp_redirect_status', $status, $location);
			if ($status < 300 || $status > 399) {
				$status = 302;
			}

			$location = wp_sanitize_redirect($location);
			header('Location: ' . $location, true, $status);
		}

	}
?>