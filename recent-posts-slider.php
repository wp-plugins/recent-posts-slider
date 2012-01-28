<?php
/*
Plugin Name: Recent Posts Slider
Plugin URI: http://recent-posts-slider.com
Description: Recent Posts Slider displays your blog's recent posts either with excerpt or thumbnail images using slider.
Version: 0.6.3
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
add_action('admin_print_styles', 'rps_admin_style');

//To perform action while publishing post i.e. creating the thumbnail of first image of post
add_action('publish_post','rps_publish_post');

//Add  the nedded styles & script
add_action('wp_print_styles', 'rps_add_style');
add_action('wp_head', 'rps_add_custom_style');
add_action('init', 'rps_add_script');

add_shortcode('rps', 'rps_show');

// register Rps widget
add_action('widgets_init', create_function('', 'return register_widget("RpsWidget");'));

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
	if ( $slider_content== 1 || $slider_content== 3 ) {
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
	$slider_content = get_option('rps_slider_content');
	$post_per_slide = get_option('rps_post_per_slide');
	$total_posts = get_option('rps_total_posts');
	$category_ids = get_option('rps_category_ids');
	$post_include_ids = get_option('rps_post_include_ids');
	$post_exclude_ids = get_option('rps_post_exclude_ids');
	
	$set_img_width = ($width/$post_per_slide) - 12;
	if($slider_content == 3){
		$set_img_width = (int)(($set_img_width/2) - 20);
	}
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
		$first_img_name_arr = get_post_custom_values('rps_custom_thumb', $val_p['post_ID']);
		$first_img_name = $first_img_name_arr['0'];

		if (function_exists('has_post_thumbnail') && has_post_thumbnail( $val_p['post_ID'] ) && empty($first_img_name)){
			$img_details = wp_get_attachment_image_src( get_post_thumbnail_id( $val_p['post_ID'] ), 'full' );
			$first_img_name = $img_details[0];
		}else{
			
			if(empty($first_img_name)){
				preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $val_p['post_content'], $matches);
			
				if ( count($matches) && isset($matches[1]) ) {
					$first_img_name = $matches[1][0];
				}	
			}
		}
		
		if(!empty($first_img_name)){
			$arr_img = explode('/',$first_img_name);
			unset($arr_img[0]);
			unset($arr_img[1]);
			unset($arr_img[2]);
			$first_img_src = $_SERVER['DOCUMENT_ROOT']."/".implode('/',$arr_img);
		}
		
		if( !empty($first_img_src) ){	
			$size = @getimagesize( $first_img_src );
			
			if ( $set_img_width > 0 && $set_img_height > 0 && $size){
				if($size[0] <= $set_img_width && $size[1] <= $set_img_height){
					$img_file = $first_img_src;
				}else {
					$img_file = image_resize($first_img_src,$set_img_width,$set_img_height,'true');
				}
			}
			
			if ( !empty($img_file) ) {
				$new_wrp_img_src = substr($img_file,strlen($_SERVER['DOCUMENT_ROOT']));
				
				if ( $rps_image_src = get_post_custom_values('_rps_img_src', $val_p['post_ID']) ) {
					$old_wrp_img_src = $rps_image_src['0'];
					
					if ( $old_wrp_img_src != $new_wrp_img_src ) {
						$old_img_path = $_SERVER['DOCUMENT_ROOT'].$old_wrp_img_src;
						if( !empty($old_wrp_img_src) ) {
							$is_delete = get_post_meta($val_p['post_ID'], '_rps_is_delete_img');
							if( is_file($old_img_path) && $is_delete[0] ){	
								@unlink($old_img_path);
							}			
						}
						update_post_meta($val_p['post_ID'], '_rps_img_src', $new_wrp_img_src);
					}
				} else {
					add_post_meta($val_p['post_ID'], '_rps_img_src', $new_wrp_img_src);
				}
				
				if($size[0] <= $set_img_width && $size[1] <= $set_img_height){
					if ( get_post_meta($val_p['post_ID'], '_rps_is_delete_img') ) {
						update_post_meta($val_p['post_ID'], '_rps_is_delete_img', 0);
					}
				}else {
					if ( get_post_meta($val_p['post_ID'], '_rps_is_delete_img') ) {
						update_post_meta($val_p['post_ID'], '_rps_is_delete_img', 1);
					}else{
						add_post_meta($val_p['post_ID'], '_rps_is_delete_img', 1);
					}
				}
			}
		}else{
			if ( $rps_image_src = get_post_custom_values('_rps_img_src', $val_p['post_ID']) ) {
				$old_wrp_img_src = $rps_image_src['0'];
				
				$old_img_path = $_SERVER['DOCUMENT_ROOT'].$old_wrp_img_src;
				if( !empty($old_wrp_img_src) ) {
					$is_delete = get_post_meta($val_p['post_ID'], '_rps_is_delete_img');
					if( is_file($old_img_path) && $is_delete[0] ){	
						@unlink($old_img_path);
						delete_post_meta($val_p['post_ID'], '_rps_img_src', $old_wrp_img_src);
						delete_post_meta($val_p['post_ID'], '_rps_is_delete_img');
					}
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

function rps_admin_style() {	
	wp_enqueue_style('rps-admin-style', WP_PLUGIN_URL.'/recent-posts-slider/css/rps-admin-style.css');
}

/** Link the needed stylesheet */
function rps_add_style() {
	wp_enqueue_style('rps-style', WP_PLUGIN_URL.'/recent-posts-slider/css/style.css');
}

function rps_add_custom_style() {
	echo "<style type=\"text/css\" media=\"screen\">" . stripslashes(get_option('rps_custom_css')) . "</style>";
}

/** Link the needed script */
function rps_add_script() {
	if ( !is_admin() ){
		wp_enqueue_script( 'jquery' );
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
	$post_title_color = get_option('rps_post_title_color');
	$post_title_bg_color = get_option('rps_post_title_bg_color');
	$slider_speed = get_option('rps_slider_speed');
	$pagination_style = get_option('rps_pagination_style');
	$excerpt_words = get_option('rps_excerpt_words');
	$show_post_date = get_option('rps_show_post_date');
	$post_date_text = get_option('rps_post_date_text');
	$post_date_format = get_option('rps_post_date_format');
	
	if(empty($post_date_text)){
		$post_date_text = "Posted On:";
	}
	
	if(empty($post_date_format)){
		$post_date_format = "j-F-Y";
	}
	
	if ( empty($slider_speed) ) {
		$slider_speed = 7000;
	}else{
		$slider_speed = $slider_speed * 1000;
	}
	if ( empty($post_title_color) ){
		$post_title_color = "#666";
	}else{
		$post_title_color = "#".$post_title_color;
	}
	$post_title_bg_color_js = "";
	if ( !empty($post_title_bg_color) ){
		$post_title_bg_color_js = "#".$post_title_bg_color;
	}
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
	
	if ( ($total_posts%$post_per_slide)==0 )
		$paging  = $total_posts/$post_per_slide; 
	else
		$paging  = ($total_posts/$post_per_slide) + 1;  
	
	foreach ( $recent_posts as $key=>$val ) {
		$post_details[$key]['post_title'] = $val->post_title;
		$post_details[$key]['post_permalink'] = get_permalink($val->ID);
		
		if ( $slider_content == 2 ) {
			if ( !empty($val->post_excerpt) ) 
				$post_details[$key]['post_excerpt'] = create_excerpt($val->post_excerpt, $excerpt_length, $post_details[$key]['post_permalink'], $excerpt_words);
			else
				$post_details[$key]['post_excerpt'] = create_excerpt($val->post_content, $excerpt_length, $post_details[$key]['post_permalink'], $excerpt_words);
		}elseif ( $slider_content == 1 ) {
			$post_details[$key]['post_first_img'] = get_post_meta($val->ID, '_rps_img_src');
		}elseif ( $slider_content == 3 ) {
			$post_details[$key]['post_first_img'] = get_post_meta($val->ID, '_rps_img_src');
			if ( !empty($val->post_excerpt) ) 
				$post_details[$key]['post_excerpt'] = create_excerpt($val->post_excerpt, ($excerpt_length/2)-10, $post_details[$key]['post_permalink'], $excerpt_words);
			else
				$post_details[$key]['post_excerpt'] = create_excerpt($val->post_content, ($excerpt_length/2)-10, $post_details[$key]['post_permalink'], $excerpt_words);
		}
		if ( $show_post_date ){
			$post_details[$key]['post_date'] = date_i18n($post_date_format,strtotime($val->post_date));	
		}
	}
	
	//$upload_dir = wp_upload_dir();
	$output = '<!--Automatic Image Slider w/ CSS & jQuery with some customization-->';
	$output .='<script type="text/javascript">
	$j = jQuery.noConflict();
	$j(document).ready(function() {';

	//Set Default State of each portfolio piece
	if ($pagination_style != '3' ){
		$output .='$j("#rps .paging").show();';
	}
	$output .='$j("#rps .paging a:first").addClass("active");
	
	$j(".slide").css({"width" : '.$width.'});
	$j("#rps .window").css({"width" : '.($width).'});
	$j("#rps .window").css({"height" : '.$height.'});

	$j("#rps .col").css({"width" : '.(($width/$post_per_slide)-2).'});
	$j("#rps .col").css({"height" : '.($height-4).'});
	$j("#rps .col p.post-title span").css({"color" : "'.($post_title_color).'"});
	$j("#rps .post-date").css({"top" : '.($height-20).'});
	$j("#rps .post-date").css({"width" : '.(($width/$post_per_slide)-12).'});';
	
	if (!empty($post_title_bg_color_js)){
		$output .='$j("#rps .col p.post-title").css({"background-color" : "'.($post_title_bg_color_js).'"});';
	}
	
	$output .='var imageWidth = $j("#rps .window").width();
	//var imageSum = $j("#rps .slider div").size();
	var imageReelWidth = imageWidth * '.$paging.';
	
	//Adjust the image reel to its new size
	$j("#rps .slider").css({"width" : imageReelWidth});

	//Paging + Slider Function
	rotate = function(){	
		var triggerID = $active.attr("rel") - 1; //Get number of times to slide
		//alert(triggerID);
		var sliderPosition = triggerID * imageWidth; //Determines the distance the image reel needs to slide

		$j("#rps .paging a").removeClass("active"); 
		$active.addClass("active");
		
		//Slider Animation
		$j("#rps .slider").stop(true,false).animate({ 
			left: -sliderPosition
		}, 500 );
	}; 
	var play;
	//Rotation + Timing Event
	rotateSwitch = function(){		
		play = setInterval(function(){ //Set timer - this will repeat itself every 3 seconds
			$active = $j("#rps .paging a.active").next();
			if ( $active.length === 0) { //If paging reaches the end...
				$active = $j("#rps .paging a:first"); //go back to first
			}
			rotate(); //Trigger the paging and slider function
		}, '.$slider_speed.');
	};
	
	rotateSwitch(); //Run function on launch
	
	//On Hover
	$j("#rps .slider a").hover(function() {
		clearInterval(play); //Stop the rotation
	}, function() {
		rotateSwitch(); //Resume rotation
	});	
	
	//On Click
	$j("#rps .paging a").click(function() {	
		$active = $j(this); //Activate the clicked paging
		//Reset Timer
		clearInterval(play); //Stop the rotation
		rotate(); //Trigger rotation immediately
		rotateSwitch(); // Resume rotation
		return false; //Prevent browser jump to link anchor
	});	
});

</script>';

$output .= '<div id="rps">
            <div class="window">	
                <div class="slider">';
		$p=0;
		for ( $i = 1; $i <= $total_posts; $i+=$post_per_slide ) {
			$output .= '<div class="slide">';
					for ( $j = 1; $j <= $post_per_slide; $j++ ) {
						$output .= '<div class="col"><p class="post-title"><a href="'.$post_details[$p]['post_permalink'].'"><span>'.$post_details[$p]['post_title'].'</span></a></p>';
						if ( $slider_content == 2 ){
							$output .= '<p class="slider-content">'.$post_details[$p]['post_excerpt'];
							if($show_post_date){
								$output .= '<div class="post-date">'.$post_date_text.' '.$post_details[$p]['post_date'].'</div>';
							}
							$output .= '</p></div>';
						}elseif ( $slider_content == 1 ){
							$output .= '<p class="slider-content-img">';
							if( !empty($post_details[$p]['post_first_img']['0']) ){
								$rps_img_src_path = $post_details[$p]['post_first_img']['0'];
								if(!empty($rps_img_src_path)){
									$output .= '<a href="'.$post_details[$p]['post_permalink'].'"><center><img src="'.$rps_img_src_path.'" /></center></a>';
								}
							}
							if($show_post_date){
								$output .= '<div class="post-date">'.$post_date_text.' '.$post_details[$p]['post_date'].'</div>';
							}
							$output .= '</p></div>';			
						}elseif ( $slider_content == 3 ){
							$output .= '<p class="slider-content-both">';
							if( !empty($post_details[$p]['post_first_img']['0']) || !empty($post_details[$p]['post_excerpt'])){
								$rps_img_src_path = $post_details[$p]['post_first_img']['0'];
								if(!empty($rps_img_src_path)){
									$output .= '<a href="'.$post_details[$p]['post_permalink'].'"><img src="'.$rps_img_src_path.'" align="left" /></a>';
								}
								$output .= $post_details[$p]['post_excerpt'];
							}
							if($show_post_date){
								$output .= '<div class="post-date">'.$post_date_text.' '.$post_details[$p]['post_date'].'</div>';
							}
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
				for ( $p = 1; $p <= $paging; $p++ ) {
					if( $pagination_style == '2' ){
						$output .= '<a href="#" rel="'.$p.'">&bull;</a>';
					}elseif( $pagination_style == '1' ){
						$output .= '<a href="#" rel="'.$p.'">'.$p.'</a>';
					}elseif( $pagination_style == '3' ){
						$output .= '<a href="#" rel="'.$p.'">&nbsp;</a>';
					}
				}
            $output .= '</div>
        </div><div class="rps-clr"></div>'; 
	return $output;
}

/** Create post excerpt manually
 * @param $post_content
 * @param $excerpt_length
 * @return post_excerpt or  void
*/
function create_excerpt( $post_content, $excerpt_length, $post_permalink, $excerpt_words=NULL){
	$keep_excerpt_tags = get_option('rps_keep_excerpt_tags');
	
	if(!$keep_excerpt_tags){
		$post_excerpt = strip_shortcodes($post_content);
		$post_excerpt = str_replace(']]>', ']]&gt;', $post_excerpt);
		$post_excerpt = strip_tags($post_excerpt);
	}else{
		$post_excerpt = $post_content;
	}
	
	$link_text = get_option('rps_link_text');
	if(!empty($link_text)){
		$more_link = $link_text;
	}else{
		$more_link = "[more]";
	}
	if( !empty($excerpt_words) ){	
		if ( !empty($post_excerpt) ) {
			$words = explode(' ', $post_excerpt, $excerpt_words + 1 );	
			array_pop($words);
			array_push($words, ' <a href="'.$post_permalink.'">'.$more_link.'</a>');
			$post_excerpt_rps = implode(' ', $words);
			return $post_excerpt_rps;
		} else {
			return;
		}
	}else{
		$post_excerpt_rps = substr( $post_excerpt, 0, $excerpt_length );
		if ( !empty($post_excerpt_rps) ) {
			if ( strlen($post_excerpt) > strlen($post_excerpt_rps) ){
				$post_excerpt_rps =substr( $post_excerpt_rps, 0, strrpos($post_excerpt_rps,' '));
			}	
			$post_excerpt_rps .= ' <a href="'.$post_permalink.'">'.$more_link.'</a>';
			return $post_excerpt_rps;
		} else {
			return;
		}
	}
}

/**
 * RpsWidget Class
 */
class RpsWidget extends WP_Widget {
    /** constructor */
    function RpsWidget() {
        parent::WP_Widget(false, $name = 'Recent Posts Slider', array( 'description' => __( "Your blogs recent post using slider") ));	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
	echo $before_widget;
        if ( $title )
		echo $before_title . $title . $after_title; 
		if (function_exists('rps_show')) echo rps_show(); 
		echo $after_widget; 
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {				
        $title = esc_attr($instance['title']);
        ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <?php 
    }

} // class RpsWidget
?>