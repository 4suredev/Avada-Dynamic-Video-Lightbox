<?php
/**
 * Plugin Name: Avada Dynamic Video Lightbox
 * Plugin URI: https://4sure.com.au
 * Description: Insert a youtube or vimeo link that plays in a lightbox. Use [lightbox_video url=""]. Add a custom thumbnail using the 'img' parameter.
 * Version: 1.0.7
 * Author: 4sure
 * Author URI: https://4sure.com.au
 */
define('VBL_PLUGIN_PATH', plugin_dir_url( __FILE__ ));
include_once( plugin_dir_path( __FILE__ ) . 'updater.php');
$updater = new Avada_dynamic_lightbox_updater( __FILE__ ); 
$updater->set_username( '4suredev' ); 
$updater->set_repository( 'Avada-Dynamic-Video-Lightbox' ); 
$updater->initialize(); 
if( ! class_exists( 'Avada_dynamic_lightbox_updater' ) ){
	include_once( plugin_dir_path( __FILE__ ) . 'updater.php' );
}
add_action( 'wp_enqueue_scripts', 'vbl_enqueue_styles' );
function vbl_enqueue_styles(){
    wp_enqueue_style( 'dynamic-video-lightbox', VBL_PLUGIN_PATH.'css/widget-styles.css' );
}
add_action('admin_enqueue_scripts', 'vbl_lightbox_admin_scripts');
function vbl_lightbox_admin_scripts($hook) {
    // Only add to the edit post/page admin page.
    if ('post.php' == $hook || 'post-new.php' == $hook || 'toplevel_page_access-manager' == $hook) {
        wp_enqueue_script('vbl_admin_scripts', VBL_PLUGIN_PATH.'js/admin-scripts.js');
        if (!wp_script_is('jquery-ui', 'enqueued')){
            wp_enqueue_script('jquery-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js');
        }
        
    }else{return;}
}
add_shortcode('lightbox_video', 'vbl_custom_lightbox_video');
function vbl_custom_lightbox_video($atts = array()){
    $args = shortcode_atts(
        array(
        'url' => '#',
        'img' => ''
    ), $atts);
    $html = '';
    if( $pos = strpos($args['url'], "youtube.com/watch") ){
        $video_id = strtok(str_replace("watch?v=", "", substr($args['url'], strpos($args['url'], 'watch?v='))), '&');
        if(strpos($video_id, '?')){
            $video_id = substr($video_id, 0, strpos($video_id, '?'));
        }
        if($args['img'] == '') $image = 'https://img.youtube.com/vi/'.$video_id.'/maxresdefault.jpg';
        else $image = $args['img'];
    }
    else if( $pos = strpos($args['url'], "youtube.com/embed") ){
        $video_id = strtok(str_replace("embed/", "", substr($args['url'], strpos($args['url'], 'embed'))), '?');
        if(strpos($video_id, '?')){
            $video_id = substr($video_id, 0, strpos($video_id, '?'));
        }
        if($args['img'] == '') $image = 'https://img.youtube.com/vi/'.$video_id.'/maxresdefault.jpg';
        else $image = $args['img'];
    }
    else if( $pos = strpos($args['url'], "youtu.be") ){
        $video_id = strtok(str_replace("youtu.be/", "", substr($args['url'], strpos($video, 'youtu.be/'))), '&');
        $video_id = str_replace("https://", "", $video_id);
        if(strpos($video_id, '?')){
            $video_id = substr($video_id, 0, strpos($video_id, '?'));
        }
        if($args['img'] == '') $image = 'https://img.youtube.com/vi/'.$video_id.'/maxresdefault.jpg';
        else $image = $args['img'];
    }
    else if( $pos = strpos($args['url'], "vimeo.com") ){
        $video = json_decode(wp_remote_get("https://vimeo.com/api/oembed.json?url=".$args['url'])['body']);
        $video_id = $video->video_id;
        if($args['img'] == '') $image = $video->thumbnail_url;
        else $image = $args['img'];
    }
    $html .= '<div class="dynamic-video-lightbox"><a href="'.$args['url'].'" class="video-link-preview" target="lightbox" rel="iLightbox"><i class="fas fa-play" style="font-size: 24px; color: #fff;"></i><div class="thumbnail-wrap"><img class="thumbnail" loading="lazy" src="'.$image.'" height="200" width="100%"></div></a></div>';
    return $html;
}
function vbl_add_lightbox_media_button() {
    $the_page = get_current_screen();
    $current_page = $the_page->post_type;
    $allowed = array(
        'post',
        'page',
        'product',
        'tribe_events'
    );
    if (in_array($current_page, $allowed, false) || $the_page->base == 'toplevel_page_access-manager' || $the_page->base == 'post'){
        printf( '<a href="%s" class="button generate-lightbox-video-shortcode">' . '<span class="wp-media-buttons-icon dashicons dashicons-shortcode"></span> %s' . '</a>', '#', __( 'Insert Video Lightbox', 'textdomain' ) );
    }
}
add_action( 'media_buttons', 'vbl_add_lightbox_media_button');
add_action('admin_bar_menu', 'vbl_add_toolbar_items', 100);
function vbl_add_toolbar_items($admin_bar){
    $admin_bar->add_menu( array(
        'id'    => 'youtube-vimeo-lightbox',
        'title' => 'Youtube/Vimeo Lightbox',
        'href'  => '',
        'meta'  => array(
            'onclick' => "navigator.clipboard.writeText(\"[lightbox_video url= img=]\");"            
        ),
    ));
}