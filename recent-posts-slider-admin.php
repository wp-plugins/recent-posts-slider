<?php 
	if ( $_POST['rps_opt_hidden'] == 'Y' ) {
		
		$width = $_POST['rps_width'];
		if ( is_numeric($width) )
			update_option('rps_width', $width);
		else
			$error[] = "Please enter width in numbers.";
		
		$height = $_POST['rps_height'];
		if ( is_numeric($height) )
			update_option('rps_height', $height);
		else
			$error[] = "Please enter height in numbers.";
		
		$post_per_slide = $_POST['rps_post_per_slide'];
		update_option('rps_post_per_slide', $post_per_slide);
		
		$total_posts = $_POST['rps_total_posts'];
		if ( is_numeric($total_posts) )
			update_option('rps_total_posts', $total_posts);
		else
			$error[] = "Please enter total posts in numbers.";
		
		$slider_content = $_POST['rps_slider_content'];
		update_option('rps_slider_content', $slider_content);
		
		$category_ids = $_POST['rps_category_ids'];
		update_option('rps_category_ids', $category_ids);
		
		$post_include_ids = $_POST['rps_post_include_ids'];
		update_option('rps_post_include_ids', $post_include_ids);
		
		$post_exclude_ids = $_POST['rps_post_exclude_ids'];
		update_option('rps_post_exclude_ids', $post_exclude_ids);
		
		$post_title_color = $_POST['rps_post_title_color'];
		update_option('rps_post_title_color', $post_title_color);
		
		$slider_speed = $_POST['rps_slider_speed'];
		update_option('rps_slider_speed', $slider_speed);
		
		$pagination_style = $_POST['rps_pagination_style'];
		update_option('rps_pagination_style', $pagination_style);
		
		$excerpt_words = $_POST['rps_excerpt_words'];
		update_option('rps_excerpt_words', $excerpt_words);
		
		if ( $slider_content== 1 || $slider_content== 3)
			rps_post_img_thumb();
		?>
		<?php if( empty($error) ){ ?>
		<div class="updated"><p><strong><?php _e('Settings saved.', 'wp-rp' ); ?></strong></p></div>
		<?php }else{ ?>
		<div class="error"><p><strong><?php 
			foreach ( $error as $key=>$val ) {
				_e($val); 
				echo "<br/>";
			}
		?></strong></p></div>
		<?php }
	} else {
		$width = get_option('rps_width');
		$height = get_option('rps_height');
		$post_per_slide = get_option('rps_post_per_slide');
		$total_posts = get_option('rps_total_posts');
		$slider_content = get_option('rps_slider_content');
		$category_ids = get_option('rps_category_ids');
		$post_include_ids = get_option('rps_post_include_ids');
		$post_exclude_ids = get_option('rps_post_exclude_ids');
		$post_title_color = get_option('rps_post_title_color');
		$slider_speed = get_option('rps_slider_speed');
		$pagination_style = get_option('rps_pagination_style');
		$excerpt_words = get_option('rps_excerpt_words');
	}
?>

<div class="wrap">
<?php    echo "<h2>" . __( 'Recent Posts Slider Options', 'rps_opt' ) . "</h2>"; ?>

<form name="rps_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<input type="hidden" name="rps_opt_hidden" value="Y">
	<p>	
		<?php _e("Width"); ?> 
		<input type="text" name="rps_width" value="<?php echo $width; ?>" size="20"><?php _e(" ex : 200" ); ?>
	</p>
	<p>	
		<?php _e("Height"); ?> 
		<input type="text" name="rps_height" value="<?php echo $height; ?>" size="20"><?php _e(" ex : 150" ); ?>
	</p>
	<p>	
		<?php _e("Total Posts"); ?> 
		<input type="text" name="rps_total_posts" value="<?php echo $total_posts; ?>" size="20">
	</p>
	<p>	
		<?php _e("No. of post to show per slide"); ?> 
		<select name="rps_post_per_slide">
			<?php for( $i=1; $i<=10; $i++ ){ ?>
				<option value="<?php echo $i; ?>" <?php if($post_per_slide==$i){echo 'selected';} ?>><?php echo $i; ?></option>
			<?php } ?>
		</select>
	</p>
	<p>	
		<?php _e("Slider content"); ?> 
		<select name="rps_slider_content">
			<option value="1" <?php if($slider_content==1){echo 'selected';} ?>>Show Post Thumbnails</option>
			<option value="2" <?php if($slider_content==2){echo 'selected';} ?>>Show Excerpt</option>
			<option value="3" <?php if($slider_content==3){echo 'selected';} ?>>Show Both</option>
		</select>
	</p>
	<p>	
		<?php _e("Category IDs"); ?> 
		<input type="text" name="rps_category_ids" value="<?php echo $category_ids; ?>" size="40">
		<?php _e(" ex : 1,2,3,-4 (Use negative id to exclude)" ); ?>
	</p>
	<p>	
		<?php _e("Posts to include"); ?> 
		<input type="text" name="rps_post_include_ids" value="<?php echo $post_include_ids; ?>" size="40">
		<?php _e("Seperated by commas"); ?>
	</p>
	<p>	
		<?php _e("Posts to exclude"); ?> 
		<input type="text" name="rps_post_exclude_ids" value="<?php echo $post_exclude_ids; ?>" size="40">
		<?php _e("Seperated by commas"); ?>
	</p>
	<p>	
		<?php _e("Posts Title Color"); ?> 
		<input type="text" name="rps_post_title_color" value="<?php echo $post_title_color; ?>" size="40">
		<?php _e(" ex : ef4534" ); ?>
	</p>
	<p>	
		<?php _e("Slider Speed"); ?> 
		<input type="text" name="rps_slider_speed" value="<?php echo $slider_speed; ?>" size="40">
		<?php _e(" ex : 10 (It is in seconds)" ); ?>
	</p>
	<p>	
		<?php _e("Pagination Style"); ?> 
		<select name="rps_pagination_style">
			<option value="1" <?php if($pagination_style==1){echo 'selected';} ?>>Numbers</option>
			<option value="2" <?php if($pagination_style==2){echo 'selected';} ?>>Dots</option>
		</select>
	</p>
	<p>	
		<?php _e("Excerpt Words"); ?> 
		<input type="text" name="rps_excerpt_words" value="<?php echo $excerpt_words; ?>" size="40">
		<?php _e(" ex : 10" ); ?>
	</p>
	<p class="submit">
	<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
	</p>
</form>
</div>