<?php

/*
Plugin Name: Como MP3 PLayer
Plugin URI: http://www.comocreative.com/
Version: 1.0.0
Author: Como Creative LLC
Description: Plugin to enable a HTML5 MP3 PLayer 
Shortcode example: [como-mp3 id='' class='' controls='true/false' preload='' width='' height='' album='' band='' template='']
Custom templates can be created in your theme in a folder named "como-mp3-player" 
*/

defined('ABSPATH') or die('No Hackers!');

// Include plugin updater.
require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/updater.php' );

class Como_MP3_Player_Shortcode {
	static $add_script;
	static $add_style;
	static function init() {
		add_shortcode('como-mp3-player', array(__CLASS__, 'handle_shortcode'));
		add_action('init', array(__CLASS__, 'register_script'));
		add_action('wp_footer', array(__CLASS__, 'print_script'));
	}
	
	static function handle_shortcode($atts) {
		self::$add_style = false;
		self::$add_script = true;
		
		if (!is_admin()) {
			$output = '';
			unset($player);
			$player['playerID'] = (isset($atts['player_id']) ? $atts['player_id'] : '');
			$player['class'] = (isset($atts['class']) ? $atts['class'] : '');
			$player['preload'] = (isset($atts['preload']) ? $atts['preload'] : '');
			$player['width'] = (isset($atts['width']) ? $atts['width'] : '');
			$player['height'] = (isset($atts['height']) ? $atts['height'] : '');
			$player['songs'] = (isset($atts['songs']) ? $atts['songs'] : '');
			$player['modal'] = (isset($atts['modal']) ? $atts['modal'] : false);
			$player['template'] = (isset($atts['template']) ? $atts['template'] : '');

			if (post_type_exists('album')) {
				$player['album'] = (isset($atts['album']) ? $atts['album'] : '');
				$player['band'] = (isset($atts['band']) ? $atts['band'] : '');

				$args = array('post_type'=>'album','post_status'=>'publish');
				if (!empty($player['album'])) { // If Album ID is specified
					$args['p'] = $player['album'];
					$args['posts_per_page'] = 1;
				} elseif (!empty($player['band'])) { // If Band ID is specified
					$args['meta_query'] = array(
						'relation' => 'OR',
						array(
							'key'     => 'como-album-band',
							'value'   => $player['band'],
							'compare' => 'LIKE',
						),
						array(
							'key'     => 'como-album-songs',
							'value'   => $player['band'],
							'compare' => 'LIKE'
						)
					); 
					if ((isset($atts['orderby'])) || (isset($atts['order']))) {
						if (isset($atts['orderby'])) {
							$args['orderby'] = $atts['orderby'];
						} else {
							$args['meta_key'] = 'como-album-year'; 
							$args['orderby'] = 'meta_value_num';
							$args['order'] = 'DESC';
						}
						$args['orderby'] = ((isset($atts['order'])) ? $atts['order'] : 'DESC');
					} else {
						$args['meta_key'] = 'como-album-year'; 
						$args['orderby'] = 'meta_value_num';
						$args['order'] = 'DESC';
					}
					$args['posts_per_page'] = -1;
				} else {
					$args['posts_per_page'] = -1;
				}
				
				$query = new WP_Query( $args );
				if ($query->have_posts()) { 
					unset($album_array);
					$album_array = array();
					$album_array['playerID'] = (($player['playerID']) ? $player['playerID'] : '');
					while ($query->have_posts()) {
						$query->the_post(); 
						unset($alb);
						$alb['id'] = get_the_id();
						$meta = get_post_meta($alb['id']);
						$alb['title'] = get_the_title();
						$alb['year'] = ((isset($meta['como-album-year'])) ? $meta['como-album-year'][0] : '');
						$alb['release'] = ((isset($meta['como-album-release-number'])) ? $meta['como-album-release-number'][0] : '');
						$alb['type'] = ((isset($meta['como-album-type'])) ? $meta['como-album-type'][0] : '');
						$alb['format'] = ((isset($meta['como-album-format'])) ? $meta['como-album-format'][0] : '');
						$alb['band'] = ((isset($meta['como-album-band'])) ? $meta['como-album-band'][0] : '');
						$alb['songs'] = ((isset($meta['como-album-songs'])) ? $meta['como-album-songs'][0] : '');
						$term_obj_list = get_the_terms($alb['id'], 'album-label');
						$alb['label'] = ((is_array($term_obj_list)) ? join(', ', wp_list_pluck($term_obj_list, 'name')) : '');
						$alb['link'] = get_permalink();
						$alb['slug'] = get_post_field('post_name', $alb['id']);
						$alb['excerpt'] = wpautop(get_the_excerpt());
						$alb['content'] = get_the_content();
						$album_array[] = $alb;
					}
				}
			
				// Check for modal 
				$templateVersion = (($player['modal']) ? '-modal' : '');
				
				if ($player['template']) {
					$temp = (is_child_theme() ? get_stylesheet_directory() : get_template_directory() ) . '/como-mp3-player/'. $player['template'] . $templateVersion .'.php';
					if (file_exists($temp)) {
						include($temp);
					} else {
						include(plugin_dir_path( __FILE__ ) .'templates/default'. $templateVersion .'.php');
					}
				} else {
					include(plugin_dir_path( __FILE__ ) .'templates/default'. $templateVersion .'.php');
				}
				$output = $albumDisplay;
			}
			if ($output) { return $output; }
		}
	}
	
	// Register & Print Scripts
	static function register_script() {
		wp_register_script('como_mp3player_script', plugins_url('js/como-mp3-player.min.js', __FILE__), array('jquery'), '1.0', true);
	}
	static function print_script() {
		if ( ! self::$add_script )
			return;
		wp_print_scripts('como_mp3player_script');
	}
}
Como_MP3_Player_Shortcode::init();

/********* TinyMCE Button Add-On ***********/
add_action( 'after_setup_theme', 'commoMp3_button_setup' );
if (!function_exists('commoMp3_button_setup')) {
    function commoMp3_button_setup() {
        add_action( 'init', 'commoMp3_button' );
    }
}
if ( ! function_exists( 'commoMp3_button' ) ) {
    function commoMp3_button() {
        if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
            return;
        }
        if ( get_user_option( 'rich_editing' ) !== 'true' ) {
            return;
        }
        add_filter( 'mce_external_plugins', 'commoMp3_add_buttons' );
        add_filter( 'mce_buttons', 'commoMp3_register_buttons' );
    }
}
if ( ! function_exists( 'commoMp3_add_buttons' ) ) {
    function commoMp3_add_buttons( $plugin_array ) {
        $plugin_array['comoVideoButton'] = plugin_dir_url( __FILE__ ) .'js/tinymce_button.js';
        return $plugin_array;
    }
}
if ( ! function_exists( 'commoMp3_register_buttons' ) ) {
    function commoMp3_register_buttons( $buttons ) {
        array_push( $buttons, 'comoVideoButton' );
        return $buttons;
    }
}

//add_action ( 'after_wp_tiny_mce', 'commoMp3_tinymce_extra_vars' );
if ( !function_exists( 'commoMp3_tinymce_extra_vars' ) ) {
	function commoMp3_tinymce_extra_vars() { 
		// Get Templates
		$playerTemplates[] = array('value'=>'default','text'=>'Default');
		$templateDir = (is_child_theme() ? get_stylesheet_directory() : get_template_directory() ) . '/como-mp3-player/';
		if (($templateDir !== false) && is_dir($templateDir)) {
			if ($handle = opendir($templateDir)) {
				while (false !== ($entry = readdir($handle))) {
					if ($entry != "." && $entry != "..") {
						$playerTemplates[] = array('value'=>basename($entry, '.php'),'text'=>basename($entry, '.php'));
					}
				}
				closedir($handle);
			}
		}
		$playerTemplates = json_encode($playerTemplates);
		?>
		<script type="text/javascript">
			var tinyMCE_video = <?php echo json_encode(
				array(
					'button_name' => esc_html__('Embed Video', 'commoMp3'),
					'button_title' => esc_html__('Embed Video', 'commoMp3'),
					'video_template_select_options' => $playerTemplates
				)
			);
			?>;
		</script><?php
	} 	
}