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
		
		$post_title_bg_color = $_POST['rps_post_title_bg_color'];
		update_option('rps_post_title_bg_color', $post_title_bg_color);
		
		$slider_speed = $_POST['rps_slider_speed'];
		update_option('rps_slider_speed', $slider_speed);
		
		$pagination_style = $_POST['rps_pagination_style'];
		update_option('rps_pagination_style', $pagination_style);
		
		$excerpt_words = $_POST['rps_excerpt_words'];
		update_option('rps_excerpt_words', $excerpt_words);
		
		$keep_excerpt_tags = $_POST['rps_keep_excerpt_tags'];
		update_option('rps_keep_excerpt_tags', $keep_excerpt_tags);
		
		$link_text = $_POST['rps_link_text'];
		update_option('rps_link_text', $link_text);
		
		$show_post_date = $_POST['rps_show_post_date'];
		update_option('rps_show_post_date', $show_post_date);
		
		$post_date_text = $_POST['rps_post_date_text'];
		update_option('rps_post_date_text', $post_date_text);
		
		$post_date_format = $_POST['rps_post_date_format'];
		update_option('rps_post_date_format', $post_date_format);
		
		$custom_css = $_POST['rps_custom_css'];
		update_option('rps_custom_css', $custom_css);
		
		if ( $slider_content== 1 || $slider_content== 3 )
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
		$post_title_bg_color = get_option('rps_post_title_bg_color');
		$slider_speed = get_option('rps_slider_speed');
		$pagination_style = get_option('rps_pagination_style');
		$excerpt_words = get_option('rps_excerpt_words');
		$keep_excerpt_tags = get_option('rps_keep_excerpt_tags');
		$link_text = get_option('rps_link_text');
		$show_post_date = get_option('rps_show_post_date');
		$post_date_text = get_option('rps_post_date_text');
		$post_date_format = get_option('rps_post_date_format');
		$custom_css = get_option('rps_custom_css');
	}
?>

<div class="wrap">
<?php echo "<h2>" . __( 'Recent Posts Slider Options', 'rps_opt' ) . "</h2>"; ?>
<p>
	In this page you can customize the plugin according to your needs. Having any issues <a href="http://rps.eworksphere.com/contact/" target="_blank">contact</a> me.
	<br/>And feel free to <a href="http://rps.eworksphere.com/donate/" target="_blank">donate</a> for this plugin :).
</p>
<form name="rps_form" method="post" action="<?php echo admin_url('options-general.php').'?page='.$_GET['page']; ?>">
	<input type="hidden" name="rps_opt_hidden" value="Y">
	<div class="metabox-holder">
		<div class="postbox"> 
			<h3>Slider Options</h3>
				<div class="slide-opt-left wd1">
					<ul>
						<li>
							<label for="width">Width</label>
							<input type="text" name="rps_width" value="<?php echo $width; ?>" size="9" /> px 
							<span>Total width of the slider (ex : 200)</span>
						</li>
						<li>
							<label for="no_of_posts_per_slide">No. of post to show per slide</label>
							<select name="rps_post_per_slide">
								<?php for( $i=1; $i<=10; $i++ ){ ?>
									<option value="<?php echo $i; ?>" <?php if($post_per_slide==$i){echo 'selected';} ?>><?php echo $i; ?></option>
								<?php } ?>
							</select>
						</li>
					</ul>
				</div>
				<div class="slide-opt-left wd1">
					<ul>
						<li>
							<label for="height">Height</label>
							<input type="text" name="rps_height" value="<?php echo $height; ?>" size="9" /> px 
							<span>Total height of the slider (ex : 150)</span>
						</li>
						<li>
							<label for="slider_speed">Slider Speed</label>
							<input type="text" name="rps_slider_speed" value="<?php echo $slider_speed; ?>" size="9" />
							<span>ex : 10 (in seconds)</span>
						</li>
					</ul>
				</div>
				<div class="slide-opt-left">
					<ul>
						<li>
							<label for="total_posts">Total Posts</label>
							<input type="text" name="rps_total_posts" value="<?php echo $total_posts; ?>" size="9" />
							<span>No of posts to show in a slider</span>
						</li>
						<li>
							<label for="pagination_style">Pagination Style</label>
							<select name="rps_pagination_style">
								<option value="1" <?php if($pagination_style==1){echo 'selected';} ?>>Numbers</option>
								<option value="2" <?php if($pagination_style==2){echo 'selected';} ?>>Dots</option>
								<option value="3" <?php if($pagination_style==3){echo 'selected';} ?>>No Pagination</option>
							</select>
						</li>
					</ul>
				</div>
				<div class="div-clear"></div>
		</div>
	</div>
	<div class="metabox-holder">
		<div class="postbox"> 
			<h3>Slider Content Options</h3>
			<div class="slide-opt-left wd1">
				<ul>
					<li>
						<label for="slider_content">Slider content</label>
						<select name="rps_slider_content">
							<option value="1" <?php if($slider_content==1){echo 'selected';} ?>>Show Post Thumbnails</option>
							<option value="2" <?php if($slider_content==2){echo 'selected';} ?>>Show Excerpt</option>
							<option value="3" <?php if($slider_content==3){echo 'selected';} ?>>Show Both</option>
						</select>
					</li>
					<li>
						<label for="category_id">Category IDs</label>
						<input type="text" name="rps_category_ids" value="<?php echo $category_ids; ?>" size="40" />
						<span>ex : 1,2,3,-4 (Use negative id to exclude)</span>
					</li>
					<li>
						<label for="posts_title_color">Posts Title Color</label>
						<input type="text" name="rps_post_title_color" value="<?php echo $post_title_color; ?>" size="40" />
						<span>ex : ef4534</span>
					</li>
					<li>
						<label for="excerpt_words">Excerpt Words</label>
						<input type="text" name="rps_excerpt_words" value="<?php echo $excerpt_words; ?>" size="40" />
						<span>ex : 10</span>
						Don't remove tags &nbsp; <input type="checkbox" name="rps_keep_excerpt_tags" value="yes" <?php if ($keep_excerpt_tags=="yes") { echo 'checked="checked"';}  ?> />
					</li>
				</ul>
			</div>
			<div class="slide-opt-left wd1">
				<ul>
					<li>
						<label for="posts_to_include">Posts to include</label>
						<input type="text" name="rps_post_include_ids" value="<?php echo $post_include_ids; ?>" size="40" />
						<span>Seperated by commas</span>
					</li>
					<li>
						<label for="posts_to_exclude">Posts to exclude</label>
						<input type="text" name="rps_post_exclude_ids" value="<?php echo $post_exclude_ids; ?>" size="40" />
						<span>Seperated by commas</span>
					</li>
					<li>
						<label for="posts_title_bg_color">Posts Title Backgroud Color</label>
						<input type="text" name="rps_post_title_bg_color" value="<?php echo $post_title_bg_color; ?>" size="40" />
						<span>ex : ef4534</span>
					</li>
					<li>
						<label for="set_link_text">Set Link Text</label>
						<input type="text" name="rps_link_text" value="<?php echo $link_text; ?>" size="40" />
						<span>ex : [more]</span>
					</li>
				</ul>
			</div>
			<div class="slide-opt-left">
				<ul>
					<li class="post_date">
						<label for="show_post_date">Post Date Settings</label>
						Show &nbsp; <input type="checkbox" name="rps_show_post_date" value="yes" <?php if ($show_post_date=="yes") { echo 'checked="checked"';}  ?> />
						<span></span>Text Before Date <br/><input type="text" name="rps_post_date_text" value="<?php echo $post_date_text; ?>" size="30" />
						<span>ex : Posted On</span>
						Date Format <br/><input type="text" name="rps_post_date_format" value="<?php echo $post_date_format; ?>" size="30" />
						<span>ex. F j, Y<br/>(F = Month name | j = Day of the month <br/> S = ordinal suffix for the day of the month | Y = Year)</span>
					</li>
				</ul>
			</div>
			<div class="div-clear"></div>
		</div>
	</div>
	<div class="metabox-holder">
		<div class="postbox"> 
			<h3>Custom CSS</h3>	
			<div class="div-left wd2 space">
				<label>Modify the css to suite your needs.</label><br/><br/>
				<textarea name="rps_custom_css" rows="15" cols="70" /><?php echo stripslashes($custom_css); ?></textarea>
			</div>
			<div class="div-left wd2 space">
				<br/><br/>Ex. <br/>
				<br/>
				To change the color of post date.<br/><br/>
				#rps .post-date{<br/><br/>
				&nbsp;&nbsp;&nbsp;color:#A92D20;<br/><br/>
				}
				
			</div>
			<div class="div-clear"></div>
		</div>
	</div>
	<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</form>
</div>