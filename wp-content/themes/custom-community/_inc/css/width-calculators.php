<?php

$loader  = '../../../../../wp-load.php';
if(file_exists($loader)){
    require_once $loader;
}
#header('Content-Type:text/css', TRUE);
global $cap;
$site_width = '';
$units = 'px';
if($cap->cc_responsive_enable){
    $site_width = '1200';
} else if($cap->website_width){
    $site_width = $cap->website_width;
    $units = $cap->website_width_unit;
} else {
    $site_width = '1000';
}
$new_site_width = get_content_width($site_width);

?>
#container .row-fluid .span8, .row-fluid .span8 {
	width: <?php echo $new_site_width . $units;?>;
}
div.page div.post-content, #wpbody #post-body-content {
    width : <?php echo get_post_content_width($new_site_width)?>px;
}
<?php

    /**
     * Get content width
     */
    function get_content_width($site_width){
        global $cap, $post, $bp;

        if($cap->cc_responsive_enable) {
            $cap->rightsidebar_width = 225;
            $cap->leftsidebar_width = 225;
        }
        
        $cap->archive_template = ($cap->archive_template == 'full-width' && defined('is_pro'))? $cap->archive_template : $cap->sidebar_position;
        
        if(defined('BP_VERSION') && bp_is_user() && $cap->bp_profile_sidebars == __('none', 'cc') ){
            return $site_width;
        } else if(defined('BP_VERSION') && bp_is_user() && $cap->bp_profile_sidebars != __('default', 'cc')){

            if($cap->bp_profile_sidebars == __('left', 'cc') || $cap->bp_profile_sidebars == __('left and right', 'cc')){
                $site_width -= $cap->leftsidebar_width;
            }
            if($cap->bp_profile_sidebars == __('right', 'cc') || $cap->bp_profile_sidebars == __('left and right', 'cc')){
                $site_width -= $cap->rightsidebar_width;
            }
            return $site_width;

        } else if((!is_page() || is_page('search') || is_search()) && !is_archive() || (function_exists('is_bbpress') && is_bbpress() && !is_archive())){

            $tpl = !empty($post) ? get_post_meta($post->ID, '_wp_page_template', TRUE) : FALSE;
            $tpl = empty($tpl) ? 'default' : $tpl;
            $affected = FALSE;

            if($cap->bp_profile_sidebars == __('default', 'cc') && $tpl == 'full-width.php'){
                return $site_width;
            }
            if ($cap->bp_profile_sidebars == __('default', 'cc') && ($tpl == '_pro/tpl-left-and-right-sidebar.php' || $tpl == '_pro/tpl-left-sidebar.php')) {
                 $site_width -= $cap->leftsidebar_width;
                 $affected = TRUE;
            } 
            if ($cap->bp_profile_sidebars == __('default', 'cc') && ($tpl == '_pro/tpl-left-and-right-sidebar.php' || $tpl == '_pro/tpl-right-sidebar.php')) {
                $site_width -= $cap->rightsidebar_width;
                $affected = TRUE;
            } 
            if($affected){
                return $site_width;
            }
            if($cap->bp_profile_sidebars == __('none', 'cc')){
                return $site_width;
            } elseif($cap->bp_profile_sidebars == __('left', 'cc') || ($cap->bp_profile_sidebars == __('default', 'cc') && $cap->sidebar_position == __('left','cc'))){
                $site_width -= $cap->leftsidebar_width;
            } if($cap->bp_profile_sidebars == __('right', 'cc') || ($cap->bp_profile_sidebars == __('default', 'cc') && $cap->sidebar_position == __('right','cc'))){
                $site_width -= $cap->rightsidebar_width;
            } else if($cap->bp_profile_sidebars == __('left and right', 'cc') || ($cap->bp_profile_sidebars == __('default', 'cc') && $cap->sidebar_position == __('left and right','cc'))){
                $site_width = $site_width - $cap->rightsidebar_width - $cap->leftsidebar_width;
            }
            return $site_width;
        } elseif (is_archive()) {
            if(defined('is_pro') && ($cap->archive_template == 'full-width' || $cap->archive_template == __('full-width', 'cc'))){
                return $site_width;
            } 
            
            if(is_archive() && ($cap->archive_template == 'left' || $cap->archive_template == 'left and right' || $cap->archive_template == __('left', 'cc') || $cap->archive_template == __("left and right",'cc'))){
                $site_width -= $cap->leftsidebar_width;  
            } 
            if($cap->archive_template == "right" || $cap->archive_template == "left and right" || $cap->archive_template == __("right",'cc') || $cap->archive_template == __("left and right",'cc')){
                $site_width -= $cap->rightsidebar_width;
            }
            
        } else {
            
            if(isset($post)){

                $detect = new TK_WP_Detect();
                $component = explode('-', $detect->tk_get_page_type());
                if(!empty($component[2])){
                    if($component[2] == 'groups' && !empty($component[3]) && (property_exists($bp, 'unfiltered_uri') && !empty($bp->unfiltered_uri[0]) && $bp->unfiltered_uri[0] != 'members')) {
                        if( ($cap->bp_groups_sidebars == 'default' && $cap->sidebar_position ==__('left and right','cc')) || $cap->bp_groups_sidebars == 'left' || $cap->bp_groups_sidebars == __('left','cc')  
                            || $cap->bp_groups_sidebars == 'left and right'  || $cap->bp_groups_sidebars == __('left and right','cc') ){ 
                            $site_width -= $cap->leftsidebar_width;
                        } 
                        if($cap->bp_groups_sidebars == 'default' || $cap->bp_groups_sidebars == 'right' || $cap->bp_groups_sidebars == __('right','cc')  
                            || $cap->bp_groups_sidebars == 'left and right'  || $cap->bp_groups_sidebars == __('left and right','cc')){
                            $site_width -= $cap->rightsidebar_width;
                        };
                        return $site_width;
                    } elseif((property_exists($bp, 'unfiltered_uri') && !empty($bp->unfiltered_uri[0]) && $bp->unfiltered_uri[0] == 'members') || bp_is_activity_component() || bp_is_profile_component() || bp_is_messages_component() || bp_is_friends_component() || bp_is_settings_component()) {

                        if( ($cap->bp_profile_sidebars == 'default' || $cap->sidebar_position == __('default','cc')) 
                            && ($cap->bp_profile_sidebars == 'left and right' || $cap->sidebar_position == __('left and right','cc') || $cap->sidebar_position == __('left','cc') || $cap->sidebar_position == 'left') 
                            || $cap->bp_profile_sidebars == 'left' || $cap->bp_profile_sidebars == __('left','cc') 
                            || $cap->bp_profile_sidebars == 'left and right' || $cap->bp_profile_sidebars == __('left and right','cc')  ){
                                $site_width -= $cap->leftsidebar_width;
                        } 
                        if( ($cap->bp_profile_sidebars == "default" || $cap->bp_profile_sidebars == __("default",'cc') ) 
                            && ($cap->sidebar_position == "right" || $cap->sidebar_position == __("right",'cc') || $cap->sidebar_position == "left and right" || $cap->sidebar_position == __("left and right",'cc')) 
                            || $cap->bp_profile_sidebars == 'right' || $cap->bp_profile_sidebars == __('right','cc') 
                            || $cap->bp_profile_sidebars == 'left and right' || $cap->bp_profile_sidebars == __('left and right','cc')  ){ 
                                $site_width -= $cap->rightsidebar_width;
                        }
                        return $site_width;
                    }  elseif($component[2] == 'members') {

                        if( $cap->sidebar_position ==__('left and right','cc') || $cap->sidebar_position ==__('left','cc') ) {
                            $site_width -= $cap->leftsidebar_width;
                        }
                        if( $cap->sidebar_position ==__('left and right','cc') || $cap->sidebar_position ==__('right','cc') ) {
                            $site_width -= $cap->rightsidebar_width;
                        }
                        return $site_width;
                    } else if($component[2] != 'forums'){

                        if( $cap->sidebar_position ==__('left and right','cc') || $cap->sidebar_position ==__('left','cc') ) {
                            $site_width -= $cap->leftsidebar_width;
                        }
                        if( $cap->sidebar_position ==__('left and right','cc') || $cap->sidebar_position ==__('right','cc') ) {
                            $site_width -= $cap->rightsidebar_width;
                        }
                        return $site_width;
                    }
                }

                $width_change = FALSE;
                $tmp = get_post_meta( $post->ID, '_wp_page_template', true );
                if($tmp == 'full-width.php'){
                    return $site_width;
                }

                // page template - if empty use "default" here... 
                if( $tmp == '' ) $tmp = 'default';

                //page template
                if( $tmp == '_pro/tpl-left-and-right-sidebar.php' || $tmp == '_pro/tpl-search-right-and-left-sidebar.php' ||
                    $tmp == '_pro/tpl-left-sidebar.php' || $tmp == '_pro/tpl-search-left-sidebar.php' ){
                    $site_width -= $cap->leftsidebar_width;
                    $width_change = TRUE;
                }
                if( $tmp == '_pro/tpl-left-and-right-sidebar.php' || $tmp == '_pro/tpl-search-right-and-left-sidebar.php'
                    || $tmp == '_pro/tpl-right-sidebar.php' || $tmp == '_pro/tpl-search-right-sidebar.php'){
                    $site_width -= $cap->rightsidebar_width;
                    $width_change = TRUE;
                }
                if($width_change){
                    return $site_width;
                }
                //global settings
                if( $cap->sidebar_position == __('left','cc') || $cap->sidebar_position == __('left and right','cc')){
                    $site_width -= $cap->leftsidebar_width;
                    $width_change = TRUE;
                }
                if( $cap->sidebar_position == __('right','cc') ||  $cap->sidebar_position == __('left and right','cc')){
                    $site_width -= $cap->rightsidebar_width;
                    $width_change = TRUE;
                }
                if($width_change){
                    return $site_width;
                }

            }
        }
        return $site_width;
    }
    function get_post_content_width($site_width){
        global $cap;
        $avatar_width = 50;//width of avatar image
        if(defined('BP_VERSION')){
            if(is_front_page()){ //for home page
                if($cap->default_homepage_hide_avatar === __('show', 'cc')){
                    $site_width -= $avatar_width;
                    $site_width -= 80;//margins width
                }
            }
            if(is_archive()){ //archives pages as category, tags etc.
                if($cap->posts_lists_hide_avatar === __('show', 'cc')){
                    $site_width -= $avatar_width;
                    $site_width -= 80;//margins width
                }
            }
            if(is_singular() && $cap->avatars_only_in_comments == __('yes', 'cc')){
                $site_width -= $avatar_width;
            }
        }
        
        return $site_width;
    }
    
?>
