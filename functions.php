<?php
if ( ! isset( $content_width ) )
	$content_width = 560;

/** Tell WordPress to run photostream_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'photostream_setup' );

if ( ! function_exists( 'photostream_setup' ) ):
function photostream_setup() {
	
	// Post Format support. You can also use the legacy "gallery" or "asides" (note the plural) categories.
	add_theme_support( 'post-formats', array( 'image', 'gallery' ) );
	
}
endif;

/**
 * Remove inline styles printed when the gallery shortcode is used.
 */
add_filter( 'use_default_gallery_style', '__return_false' );

/**
 * Deprecated way to remove inline styles printed when the gallery shortcode is used.
 *
 * This function is no longer needed or used. Use the use_default_gallery_style
 * filter instead, as seen above.
 *
 */
function photostream_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
// Backwards compatibility with WordPress 3.0.
if ( version_compare( $GLOBALS['wp_version'], '3.1', '<' ) )
	add_filter( 'gallery_style', 'photostream_remove_gallery_css' );
	
if ( ! function_exists( 'photostream_comment' ) ) :
function photostream_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 60 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', 'photostream' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'photostream' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'photostream' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'photostream' ), ' ' );
			?>
		</div><!-- .comment-meta .commentmetadata -->

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'photostream' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'photostream' ), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;
/* Props Noel http://blog.noel.io/tweet-this-wordpress-function/ */
function ms_tweet_this() {
	global $post;
	$tweet = sprintf( __('Currently reading %1$s %2$s'), $post->post_title, wp_get_shortlink() );
	echo '<a class="tweethis" href="http://twitter.com/home?status=' . urlencode( $tweet ) . '">Tweet this</a>';
}
function ms_archives_column() {
	// Grab the archives. Return the output
	$get_archives = wp_get_archives( 'echo=0' );
	// Split into array items
	$archives_array = explode('</li>',$get_archives);
	// Amount of archives (count of items in array)
	$results_total = count($archives_array);
	// How many columns to display
	$archives_per_list = ceil($results_total / 3);
	// Counter number for tagging onto each list
	$list_number = 1;
	// Set the archive result counter to zero
	$result_number = 0;
	?>
	<ul class="archive_col" id="archive-col-<?php echo $list_number; ?>">
	<?php
	foreach($archives_array as $archive) {
		$result_number++;

		if($result_number % $archives_per_list == 0) {
			$list_number++;
			echo $archive.'</li>
			</ul>
			<ul class="archive_col" id="archive-col-'.$list_number.'">';
		}
		else {
			echo $archive.'</li>';
		}
	}
}
?>