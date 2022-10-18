jQuery(document).ready(function($){
    $('.generate-lightbox-video-shortcode').click(function(e){
        var btn = $(this);
        e.preventDefault();
        navigator.clipboard.writeText('[lightbox_video url="" img=""]');
        $(btn).html('<span class="wp-media-buttons-icon dashicons dashicons-shortcode"></span> Copied to clipboard');
        setTimeout(() => {
            $(btn).html('<span class="wp-media-buttons-icon dashicons dashicons-shortcode"></span> Insert Video Lightbox');
        }, 1000);
    });
})