<?php

/**
 * Plugin Name: Featured Post Plugin
 * Plugin URI: http://andrea-campos.com/featured-post-plugin
 * Description: Easily add a featured post to your site via widget or template tag
 * Version: 1.0
 * Author: Andrea Campos
 * Author URI: http://andrea-campos.com
 * License: GPL2
 */
/*  Copyright 2015 Andrea Campos  (email : andrea@andrea-campos.com)

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

// add featured post checkbox
add_action( 'add_meta_boxes', 'cd_meta_box_add' );
function cd_meta_box_add()
{
    add_meta_box( 'featured-post', 'Featured Post', 'cd_meta_box_cb', 'post', 'side', 'default' );
}

function cd_meta_box_cb($post)
{
 $check = get_post_meta( $post->ID, 'featured_check', true );

  wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
?>
    <p>
        <input type="checkbox" id="my_meta_box_check" name="my_meta_box_check" <?php checked( $check, 'on' ); ?> />
        <label for="my_meta_box_check">Set as featured post</label>
    </p> 
    <?php
}

// update featured post when post is saved
add_action( 'save_post', 'cd_meta_box_save' );
function cd_meta_box_save( $post_id )
{
    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
     
    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;
     
    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post' ) ) return;

    if ( isset( $_POST['my_meta_box_check'] ) ){
    	$chk = 'on';
    	$last_featured = get_featured_post();
    	if ($last_featured->have_posts()){
    		update_post_meta( $last_featured->post->ID, 'featured_check', 'off' );
    	}
    } else {
    	$chk = 'off';
	}
    update_post_meta( $post_id, 'featured_check', $chk );
}

// return featured post as WP query object
function get_featured_post() {
	$args = array(
	'meta_key' => 'featured_check',
	'meta_value' => 'on'
);
	return new WP_Query( $args );
}

// show featured post
function display_featured($show_image=false, $show_text=true, $use_excerpt=true){
	$the_query = get_featured_post();
	if (!$the_query->have_posts()){
		echo "Coming soon!";
		return;
	}
	while ( $the_query->have_posts() ) {
		$the_query->the_post(); 
		if( $show_text ){ 
			echo '<span class="featured-title"><a href="';
			the_permalink();
			echo '">';
			the_title(); 
			echo '</a></span>';
		}
		if ( $show_image && has_post_thumbnail() ){
      echo "<div class='featured-image'>";
      the_post_thumbnail();
      echo "</div>";
		} 
		if ( $show_text ){
			echo '<p class="featured-text-content">'; 
			$use_excerpt ? the_excerpt() : the_content();
      echo '<a class="featured-more" href="';
      the_permalink();
      echo '">Read more</a></p>';
		}
	}
}

// featured post widget class
class FeatPostWidget extends WP_Widget
{
  function FeatPostWidget()
  {
    $widget_ops = array('classname' => 'FeatPostWidget', 'description' => 'Displays the featured post' );
    $this->WP_Widget('FeatPostWidget', 'Featured Post', $widget_ops);
  }
 
  function form($instance)
  {
    if (!$instance){
    	$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'use_image' => 'off', 'use_text' => 'on', 'use_excerpt' => 'on' ) );
	  }
  	$title = $instance['title'];
  	if ($instance['use_text'] != 'on') {
  		$instance['use_excerpt'] = '';
  	}
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label>
  </p>
  <p>
    <input class="checkbox" type="checkbox" <?php checked( $instance['use_image'], 'on' ); ?> id="<?php echo $this->get_field_id( 'use_image' ); ?>" name="<?php echo $this->get_field_name( 'use_image' ); ?>" /> 
    <label for="<?php echo $this->get_field_id( 'use_image' ); ?>"><?php _e('Display featured image', 'example'); ?></label>
</p>
  <p>
    <input class="checkbox" type="checkbox" <?php checked( $instance['use_text'], 'on' ); ?> id="<?php echo $this->get_field_id( 'use_text' ); ?>" name="<?php echo $this->get_field_name( 'use_text' ); ?>" /> 
    <label for="<?php echo $this->get_field_id( 'use_text' ); ?>"><?php _e('Display featured text content', 'example'); ?></label>
</p>
  <p>
    <input style = "margin-left: 25px;" class="checkbox" type="checkbox" <?php checked( $instance['use_excerpt'], 'on' ); ?> id="<?php echo $this->get_field_id( 'use_excerpt' ); ?>" name="<?php echo $this->get_field_name( 'use_excerpt' ); ?>" /> 
    <label for="<?php echo $this->get_field_id( 'use_excerpt' ); ?>">Use the excerpt</label>
</p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['use_image'] = $new_instance['use_image'];
    $instance['use_text'] = $new_instance['use_text'];
    $instance['use_excerpt'] = $new_instance['use_excerpt'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
   	$use_image = ($instance['use_image'] == 'on') ? true : false;
   	$use_text = ($instance['use_text'] == 'on') ? true : false;
   	$use_excerpt = ($instance['use_excerpt'] == 'on') ? true : false;
      echo $before_widget;
    $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title)) echo $before_title . $title . $after_title;

    display_featured($use_image, $use_text, $use_excerpt);
    echo $after_widget;
  }
 
}

function register_my_widget() {
    register_widget( 'FeatPostWidget' );
}

add_action('widgets_init', 'register_my_widget');
