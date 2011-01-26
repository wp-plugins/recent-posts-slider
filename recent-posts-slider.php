<?php
/*
Plugin Name: Recent Posts Slider
Plugin URI: http://rps.eworksphere.com
Description: Recent Posts Slider displays your blog's recent posts either with excerpt or thumbnail images using slider.
Version: 0.1
Author: Neha Goel
*/

/*  Copyright 2011  Neha Goel  (email : rps@eworksphere.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//To perform action while activating pulgin i.e. creating the thumbnail of first image of  all posts
register_activation_hook( __FILE__, 'rps_activate' );

//Create menu for configure page
add_action('admin_menu', 'rps_admin_actions');

//To perfor action while publishing post i.e. creating the thumbnail of first image of post
add_action('publish_post','rps_publish_post');

//Add  the nedded styles & script
add_action('wp_print_styles', 'rps_add_style');
add_action('wp_print_scripts', 'rps_add_script');

add_shortcode('rps', 'rps_show');
/** 
*Set the default options while activating the pugin & create thumbnails of first image of all the posts
*/
function rps_activate() {
	$width = get_option('rps_width');
	if ( empty($width) ) {
		$width = '500';
		update_option('rps_width', $width);
	}
	
	$height = get_option('rps_height');
	if ( empty($height) ) {
		$height = '250';
		update_option('rps_height', $height);
	}

	$post_per_slide = get_option('rps_post_per_slide');
	if ( empty($post_per_slide) ) {
		$post_per_slide = '2';
		update_option('rps_post_per_slide', $post_per_slide);
	}
	
	$total_posts = get_option('rps_total_posts');
	if ( empty($total_posts) ) {
		$total_posts = '6';
		update_option('rps_total_posts', $total_posts);
	}
	
	$slider_content = get_option('rps_slider_content');
	if ( empty($slider_content) ) {
		$slider_content = '1';
		update_option('rps_slider_content', $slider_content);
	}
	rps_post_img_thumb();
}

/** 
*Perform operations while publishing post
*/
function rps_publish_post(){
	$slider_content = get_option('rps_slider_content');
	if ( $slider_content== 1 ) {
		global $post;
		rps_post_img_thumb( (int) $post->ID );
	} else {
		return;
	}
}

/** 
*It creates thumbnails of first image of post
 * @param $post_id
 * @return void
*/
function rps_post_img_thumb($post_id = NULL ){
	
	$width = get_option('rps_width');
	$height = get_option('rps_height');
	$post_per_slide = get_option('rps_post_per_slide');
	$total_posts = get_option('rps_total_posts');
	$category_ids = get_option('rps_category_ids');
	$post_include_ids = get_option('rps_post_include_ids');
	$post_exclude_ids = get_option('rps_post_exclude_ids');
	
	$set_img_width = ($width/$post_per_slide) - 12;
	$set_img_height = $height - 54;
	
	if ( empty($post_id) ) {
		$args = array(
			'numberposts'     => $total_posts,
			'offset'          => 0,
			'category'        => $category_ids,
			'orderby'         => 'post_date',
			'order'           => 'DESC',
			'include'         => $post_include_ids,
			'exclude'         => $post_exclude_ids,
			'post_type'       => 'post',
			'post_status'     => 'publish' );
		$recent_posts = get_posts( $args );
		if ( count($recent_posts)< $total_posts ) {
			$total_posts	= count($recent_posts);
		}
		
		foreach( $recent_posts as $key=>$val ) {
			$post_details[$key]['post_ID'] = $val->ID;
			$post_details[$key]['post_content'] = $val->post_content;
		}
	} else {
		$post_details['0']['post_ID'] = $post_id;
		$get_post_details = get_post( $post_id );
		$post_details['0']['post_content'] = $get_post_details->post_content;
	}
	
	foreach ( $post_details as $key_p=> $val_p ) {
		$first_img_name = '';
		$img_name='';
		$first_img_src = '';
		preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $val_p['post_content'], $matches);
		
		if ( count($matches) && isset($matches[1]) ) {
			$first_img_name = $matches[1][0];
		}	
		
		$img_files = get_children("post_parent=".$val_p['post_ID']."&post_type=attachment&post_mime_type=image");
		
		foreach ( $img_files as $key=>$val ) {
			$img_details=wp_get_attachment_image_src($key,'full');
			$img_src = get_post_meta($key,'_wp_attached_file','true');
			$img_name = substr($img_src, 0, (strrpos($img_src, '.')));
		
			if ( strrpos($first_img_name, $img_name) ) {
				$first_img_src = $img_src;
			}
		}
		if( !empty($first_img_src) ){
			$upload_dir = wp_upload_dir();
			
			if ( $set_img_width > 0 && $set_img_height > 0 ){
				$img_desc = image_make_intermediate_size($upload_dir['basedir'].'/'.$first_img_src,$set_img_width,$set_img_height,'true');
			}
			
			if ( !empty($img_desc['file']) ) {
				if ( $rps_image_src = get_post_custom_values('_rps_img_src', $val_p['post_ID']) ) {
					$old_wrp_img_src = $rps_image_src['0'];
					$old_wrp_img_src."<br/>";
					$new_wrp_img_src = trim($upload_dir['subdir'].'/'.$img_desc['file'],'/');
					
					if ( $old_wrp_img_src != $new_wrp_img_src ) {
						if( is_file($old_wrp_img_src) ){	
							@unlink($old_wrp_img_src);
						}			
						update_post_meta($val_p['post_ID'], '_rps_img_src', $new_wrp_img_src);
					}
				} else {
					add_post_meta($val_p['post_ID'], '_rps_img_src', trim($upload_dir['subdir'].'/'.$img_desc['file'],'/'));
				}
			}
		}
	}
	return;
}

/** Create menu for options page */
function rps_admin_actions() {
    add_options_page('Recent Posts Slider', 'Recent Posts Slider', 'manage_options', 'recent-posts-slider', 'rps_admin');
}

/** To perform admin page functionality */
function rps_admin() {
    	if ( !current_user_can('manage_options') )
      		wp_die( __('You do not have sufficient permissions to access this page.') );
	include('recent-posts-slider-admin.php');
}

/** Link the needed stylesheet */
function rps_add_style() {
	wp_enqueue_style('rps-style', WP_PLUGIN_URL.'/recent-posts-slider/css/style.css');
}

/** Link the needed script */
function rps_add_script() {
	if ( !is_admin() ){
		wp_enqueue_script('rps-jquery',WP_PLUGIN_URL.'/recent-posts-slider/js/jquery.min.js');
	}
}

/** To show slider 
 * @return output
*/
function rps_show() {	
	$width = get_option('rps_width');
	$height = get_option('rps_height');
	$post_per_slide = get_option('rps_post_per_slide');
	$total_posts = get_option('rps_total_posts');
	$slider_content = get_option('rps_slider_content');
	$category_ids = get_option('rps_category_ids');
	$post_include_ids = get_option('rps_post_include_ids');
	$post_exclude_ids = get_option('rps_post_exclude_ids');
	
	$excerpt_length = '';
	$excerpt_length = abs( (($width-40)/20) * (($height-55)/15) );
	/*if ( ($width) > $height)
	$excerpt_length = $excerpt_length - (($excerpt_length * 5) /100);
	else
	$excerpt_length = $excerpt_length - (($excerpt_length * 30) /100);*/
	
	$post_details = NULL;
	$args = array(
			'numberposts'     => $total_posts,
			'offset'          => 0,
			'category'        => $category_ids,
			'orderby'         => 'post_date',
			'order'           => 'DESC',
			'include'         => $post_include_ids,
			'exclude'         => $post_exclude_ids,
			'post_type'       => 'post',
			'post_status'     => 'publish' );
	$recent_posts = get_posts( $args );
	
	if ( count($recent_posts)< $total_posts ) {
		$total_posts	= count($recent_posts);
	}
	
	foreach ( $recent_posts as $key=>$val ) {
		$post_details[$key]['post_title'] = $val->post_title;
		$post_details[$key]['post_permalink'] = get_permalink($val->ID);
		
		if ( $slider_content == 2 ) {
			if ( !empty($val->post_excerpt) ) 
				$post_details[$key]['post_excerpt'] = create_excerpt($val->post_excerpt, $excerpt_length);
			else
				$post_details[$key]['post_excerpt'] = create_excerpt($val->post_content, $excerpt_length);
		} elseif ( $slider_content == 1 ) {
			$post_details[$key]['post_first_img'] = get_post_meta($val->ID, '_rps_img_src');
		}
	}
	
	$upload_dir = wp_upload_dir();
	$output .= '<!--Automatic Image Slider w/ CSS & jQuery with some customization-->';
	$output .='<script type="text/javascript">jQuery.noConflict();
 // <![CDATA[
jQuery(document).ready(function() {

	//Set Default State of each portfolio piece
	jQuery(".paging").show();
	jQuery(".paging a:first").addClass("active");
	
	jQuery(".slide").css({"width" : '.$width.'});
	jQuery(".window").css({"width" : '.($width).'});
	jQuery(".window").css({"height" : '.$height.'});

	jQuery(".col").css({"width" : '.(($width/$post_per_slide)-2).'});
	jQuery(".col").css({"height" : '.($height-4).'});
	
	var imageWidth = jQuery(".window").width();
	var imageSum = jQuery(".slider div").size();
	var imageReelWidth = imageWidth * imageSum;
	
	//Adjust the image reel to its new size
	jQuery(".slider").css({"width" : imageReelWidth});

	//Paging + Slider Function
	rotate = function(){	
		var triggerID = $active.attr("rel") - 1; //Get number of times to slide
		var sliderPosition = triggerID * imageWidth; //Determines the distance the image reel needs to slide

		jQuery(".paging a").removeClass("active"); 
		$active.addClass("active");
		
		//Slider Animation
		jQuery(".slider").animate({ 
			left: -sliderPosition
		}, 500 );
		
	}; 
	
	//Rotation + Timing Event
	rotateSwitch = function(){		
		play = setInterval(function(){ //Set timer - this will repeat itself every 3 seconds
			$active = jQuery(".paging a.active").next();
			if ( $active.length === 0) { //If paging reaches the end...
				$active = jQuery(".paging a:first"); //go back to first
			}
			rotate(); //Trigger the paging and slider function
		}, 7000);
	};
	
	rotateSwitch(); //Run function on launch
	
	//On Hover
	jQuery(".slider a").hover(function() {
		clearInterval(play); //Stop the rotation
	}, function() {
		rotateSwitch(); //Resume rotation
	});	
	
	//On Click
	jQuery(".paging a").click(function() {	
		$active = jQuery(this); //Activate the clicked paging
		//Reset Timer
		clearInterval(play); //Stop the rotation
		rotate(); //Trigger rotation immediately
		rotateSwitch(); // Resume rotation
		return false; //Prevent browser jump to link anchor
	});	
});
// ]]>
</script>';

$output .= '<div id="rps">
            <div class="window">	
                <div class="slider">';
		$p=0;
		for ( $i = 1; $i <= $total_posts; $i+=$post_per_slide ) {
			$output .= '<div class="slide">';
					for ( $j = 1; $j <= $post_per_slide; $j++ ) {
						$output .= '<div class="col"><p class="post-title"><a href="'.$post_details[$p]['post_permalink'].'"><span>'.$post_details[$p]['post_title'].'</span></a></p></h4>';
						if ( $slider_content == 2 ){
							$output .= '<p class="slider-content">'.$post_details[$p]['post_excerpt'].'</p></div>';
						}elseif ( $slider_content == 1 ){
							$output .= '<p class="slider-content-img">';
							if( !empty($post_details[$p]['post_first_img']['0']) )
							$output .= '<a href="'.$post_details[$p]['post_permalink'].'"><center><img src="'.$upload_dir['baseurl'].'/'.$post_details[$p]['post_first_img']['0'].'" /></center></a>';
							$output .= '</p></div>';			
						}	
						$p++;
						if ( $p == $total_posts )
							$p = 0;
					}
					$output .= '<div class="clr"></div>
				</div>';
		}
		$output .= '
                </div>
            </div>
            <div class="paging">';
		if ( ($total_posts%$post_per_slide)==0 )
			$paging  = $total_posts/$post_per_slide; 
		else
			$paging  = ($total_posts/$post_per_slide) + 1;  
		
		for ( $p = 1; $p <= $paging; $p++ ) {
			$output .= '<a href="#" rel="'.$p.'">'.$p.'</a>';
                }
            $output .= '</DIV>
        </div><div class="rps-clr"></div>'; 
	echo $output;
	return;
}

/** Create post excerpt manually
 * @param $post_content
 * @param $excerpt_length
 * @return post_excerpt or  void
*/
function create_excerpt( $post_content, $excerpt_length ){
	$post_excerpt = strip_shortcodes($post_content);
	$post_excerpt = str_replace(']]>', ']]&gt;', $post_excerpt);
	$post_excerpt = strip_tags($post_excerpt);
	$post_excerpt_rps = substr( $post_excerpt, 0, $excerpt_length );
	if ( !empty($post_excerpt_rps) ) {
		if ( strlen($post_excerpt) > strlen($post_excerpt_rps) ){
			$post_excerpt_rps =substr( $post_excerpt_rps, 0, strrpos($post_excerpt_rps,' '));
			$post_excerpt_rps .= "...";
		}	
		return $post_excerpt_rps;
	} else {
		return;
	}
}
?>