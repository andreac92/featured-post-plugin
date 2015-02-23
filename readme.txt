Featured Post Plugin Readme

This plugin allows you to feature a post in a designated area of your site. You can choose to show the "featured image" of your featured post, along with its text content. In place of the full text content, you can show the post excerpt instead.

== Installing the plugin ==
Upload the "Featured Post Plugin" folder to the "/wp-content/plugins" directory, then activate the plugin in the Plugins menu of Wordpress.

== Choosing your featured post ==
Go to the editor of the post you want to feature. In the "Featured Post" box on the right, check the box to make it your featured post. Update your post, and you're done! Any previously featured post will now be replaced with the newly featured post.

== How to choose a designated area for your featured post ==
-With a widget: Go to the Appearance > Widgets page. Choose the widget area you want your featured post to be shown in (such as in a sidebar or other supported widget area of your theme) and drag the "Featured Post" widget to it. From here you can choose whether you want to show the featured image or not, and whether you want to show the full text content or just the excerpt.

-By editing theme files: If you're comfortable with editing your theme files, you can place the featured post by using a simple template tag wherever you want it to appear:
display_featured($use_image, $use_text, $use_excerpt);

The parameters are as follows-
$use_image (optional): true to show the featured image, false (default) if not
$use_text (optional): true (default) to show any text content, false if not
$use_excerpt (optional): true (default) to show the excerpt text, false to show the full text content

For example, if we wanted to show the featured post with the default settings (no image, use the excerpt), place the below function in the theme file where you'd like it to appear:
<?php display_featured(); ?>

If you want even greater control, you can call get_featured_post() to return a WP_Query object of the featured post.

== Styling your featured post ==
You can style your featured post title with the span class "featured-title". The featured image is wrapped in a div with the class "featured-image". You can style the featured post text with the p class "featured-text-content", and the "read more" link with the class "featured-more".

Note-- If you want to use featured images, make sure the following line of code is present in your functions.php file: 
add_theme_support( 'post-thumbnails' );