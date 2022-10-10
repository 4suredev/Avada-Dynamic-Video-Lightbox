<?php
/**
 * Plugin Name: Avada Dynamic Video Lightbox
 * Plugin URI: https://4sure.com.au
 * Description: Insert a youtube or vimeo link that plays in a lightbox. Use [lightbox_video url=""]. Add a custom thumbnail using the 'img' parameter.
 * Version: 1.0.1
 * Author: 4sure
 * Author URI: https://4sure.com.au
 */
define('VBL_PLUGIN_PATH', home_url().'/wp-content/plugins/avada-dynamic-video-lightbox/');
include_once( plugin_dir_path( __FILE__ ) . 'updater.php');
$updater = new Disable_dynamic_lightbox_updater( __FILE__ ); 
$updater->set_username( '4suredev' ); 
$updater->set_repository( 'Avada-Dynamic-Video-Lightbox' ); 
$updater->initialize(); 
if( ! class_exists( 'Disable_dynamic_lightbox_updater' ) ){
	include_once( plugin_dir_path( __FILE__ ) . 'updater.php' );
}
add_action( 'wp_enqueue_scripts', 'vbl_enqueue_styles' );
function vbl_enqueue_styles(){
    wp_enqueue_style( 'dynamic-video-lightbox', VBL_PLUGIN_PATH.'css/widget-styles.css' );
}
add_shortcode('lightbox_video', 'custom_lightbox_video');
function custom_lightbox_video($atts = array()){
    $args = shortcode_atts(
        array(
        'url' => '#',
        'img' => ''
    ), $atts);
    $html = '';
    if( $pos = strpos($args['url'], "youtube.com/watch") ){
        $video_id = strtok(str_replace("watch?v=", "", substr($args['url'], strpos($args['url'], 'watch?v='))), '&');
        if($args['img'] == '') $image = 'https://img.youtube.com/vi/'.$video_id.'/maxresdefault.jpg';
        else $image = $args['img'];
    }
    else if( $pos = strpos($args['url'], "youtube.com/embed") ){
        $video_id = strtok(str_replace("embed/", "", substr($args['url'], strpos($args['url'], 'embed'))), '?');
        if($args['img'] == '') $image = 'https://img.youtube.com/vi/'.$video_id.'/maxresdefault.jpg';
        else $image = $args['img'];
    }
    else if( $pos = strpos($args['url'], "youtu.be") ){
        $video_id = strtok(str_replace("youtu.be/", "", substr($args['url'], strpos($video, 'youtu.be/'))), '&');
        $video_id = str_replace("https://", "", $video_id);
        if($args['img'] == '') $image = 'https://img.youtube.com/vi/'.$video_id.'/maxresdefault.jpg';
        else $image = $args['img'];
    }
    else if( $pos = strpos($args['url'], "vimeo.com") ){
        $video = json_decode(wp_remote_get("https://vimeo.com/api/oembed.json?url=".$args['url'])['body']);
        $video_id = $video->video_id;
        if($args['img'] == '') $image = $video->thumbnail_url;
        else $image = $args['img'];
    }
    $html .= '<div class="dynamic-video-lightbox"><a href="'.$args['url'].'" class="video-link-preview" target="lightbox" rel="iLightbox"><i class="fa fa-play" style="font-size: 24px; color: #fff;"></i><div class="thumbnail-wrap"><img class="thumbnail" loading="lazy" src="'.$image.'" height="200" width="100%"></div></a></div>';
    return $html;
    return 'test';
}