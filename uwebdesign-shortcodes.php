<?php
/**
 * Plugin Name: uWebDesign Shortcodes
 * Plugin URI: https://github.com/websanya/uwebdesign_plugin_shortcodes
 * Description: Плагин с шорткодами для комьюнити сайта uWebDesign.
 * Version: 1.0.0
 * Author: Alexander Goncharov
 * Author URI: https://websanya.ru
 * Bitbucket Plugin URI: https://github.com/websanya/uwebdesign_plugin_shortcodes
 * Bitbucket Branch: master
 */

/**
 * Class UwebShortcodes.
 */
Class UwebShortcodes {

	/**
	 * Initializes the plugin.
	 */
	public function __construct() {
		//* Displays two articles by id, or two random articles.
		add_shortcode( 'uwd_articles', array( $this, 'uwd_articles_display' ) );
		//* Displays tweet this link with the permalink & some stuff.
		add_shortcode( 'tweet_this', array( $this, 'tweet_this_display' ) );
		//* Displays SmartApe link.
		add_shortcode( 'smartape', array( $this, 'smartape_display' ) );
	}

	/**
	 * Callback for 'uwd_articles' shortcode.
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	public function uwd_articles_display( $atts ) {
		//* Collect values, combining passed in values and defaults.
		$values = shortcode_atts( array(
			'id' => 'rand',
		), $atts );

		//* Form the arguments for WP_Query.
		$args = array(
			'post_type'           => array( 'post', 'weekly', 'video' ),
			'ignore_sticky_posts' => true,
			'posts_per_page'      => 2,
		);

		if ( $values['id'] == 'rand' ) {
			//* Set random order if no ids specified.
			$args['orderby'] = 'rand';
		} else {
			//* Remove all the whitespace.
			$id_string = preg_replace( '/\s+/', '', $values['id'] );
			//* Explode the string into array.
			$id = explode( ',', $id_string );
			//* Set the array to the query.
			$args['post__in'] = $id;
		}

		$wp_query = new WP_Query( $args );

		//* Start the buffer.
		ob_start();

		if ( $wp_query->have_posts() ) {
			?>
			<div class="entry-inner-container">
				<?php
				while ( $wp_query->have_posts() ) {
					$wp_query->the_post();

					$entry_image_id     = get_post_thumbnail_id();
					$entry_image_object = wp_get_attachment_image_src( $entry_image_id, 'uwd-custom-medium' );
					$entry_image_src    = $entry_image_object[0];
					$entry_image_width  = $entry_image_object[1];
					$entry_image_height = $entry_image_object[2];
					$entry_image_alt    = get_post_meta( $entry_image_id, '_wp_attachment_image_alt', true );
					?>
					<div class="entry-inner-item">
						<img class="entry-inner-item-image" width="<?php echo $entry_image_width; ?>"
						     height="<?php echo $entry_image_height; ?>"
						     src="<?php echo $entry_image_src; ?>" alt="<?php echo $entry_image_alt; ?>"
						     title="<?php echo $entry_image_alt; ?>">
						<h2 class="entry-inner-item-title">
							<?php the_title(); ?>
						</h2>
						<div class="entry-inner-item-content">
							<?php the_content(); ?>
						</div>
					</div>
					<?php
				}
				?>
			</div>
			<?php
		}

		//* Get all the content.
		$html = ob_get_contents();

		//* Clean the buffer.
		ob_end_clean();

		//* Reset the WP_Query.
		wp_reset_postdata();

		return $html;
	}

	/**
	 * Callback for 'tweet_this' shortcode.
	 * 
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	public function tweet_this_display( $atts, $content ) {
		//* Let's build this bitch.
		$output = '<a class="tweet-this" rel="nofollow" href="http://twitter.com/home?status=%22'
		          . $content
		          . '%22 на @uwebdesgn%0A%0A'
		          . get_permalink() .
		          '" target="_blank">«'
		          . $content
		          . '»<span class="tweet-this-inner"> (Твитните <span class="dashicons dashicons-twitter"></span>)</span></a>';

		return $output;
	}

	/**
	 * Callback for 'smartape' shortcode.
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	public function smartape_display( $atts, $content ) {
		//* Let's build this bitch too.
		$output = '<a class="smartape-this" href="https://cp.smartape.ru/mancgi/partnerprogram?partner=2922&project=1"'
		          . 'target="_blank">'
		          . $content
		          . '<span class="smartape-this-icon dashicons dashicons-arrow-down-alt"></span>'
		          . '<img class="smartape-this-image" src="http://www.smartape.ru/wp-content/themes/smartape/images/logo.png" alt="smartape"></a>';

		return $output;
	}

}

//* Initialize the plugin.
$shortcodes_object = new UwebShortcodes();