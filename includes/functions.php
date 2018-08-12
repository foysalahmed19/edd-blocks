<?php

/**
 * Output a list of download categories
 *
 * @since 1.0.0
 */
function edd_blocks_list( $atts = array() ) {
	ob_start();
	
	// Get the terms.
	$terms = get_terms( array(
		'taxonomy'   => 'download_category',
		'orderby'    => $atts['orderBy'],
		'order'      => $atts['order'],
		'hide_empty' => true !== $atts['showEmpty'] ? true : false,
	) );
	
	// Classes.
	$classes = array( 'edd-download-categories' );
	$classes[] = 'columns-' . $atts['columns'];
	$classes[] = $atts['align'] ? 'align' . $atts['align'] : '';
	$classes[] = $atts['className'];
	$classes = implode( ' ', array_filter( $classes ) );

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) : ?>
		<div class="<?php echo $classes; ?>">
		<?php foreach ( $terms as $term ) :
			$description   = $term->description;
			$count         = $term->count;
			$attachment_id = get_term_meta( $term->term_id, 'download_category_image_id', true );
		?>
			<div class="edd-download-category">

				<?php if ( true === $atts['showThumbnails'] && $attachment_id ) : ?>
				<a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="edd-download-category-image">
				<?php echo wp_get_attachment_image( $attachment_id, 'thumbnail' );  ?>
				</a>
				<?php endif; ?>

				<?php if ( true === $atts['showName'] ) : ?>
				<div class="edd-download-category-title">
					<h3 class="edd_download_title"><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo $term->name; ?></a></h3>
					<?php if ( true === $atts['showCount'] ) : ?>
					<span class="edd-download-category-count">(<?php echo $count; ?>)</span>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<?php if ( true === $atts['showDescription'] ) : ?>
				<p class="edd-download-category-description"><?php echo $description; ?></p>
				<?php endif; ?>

			</div>
		<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php
	$display = ob_get_clean();

	return $display;

}

/**
 * Register rest fields.
 *
 * @since 1.0.0
 */
function edd_blocks_register_rest_fields() {
	register_rest_field( 'download_category',
		'meta',
		array(
			'get_callback'    => 'edd_blocks_term_meta_callback',
			'update_callback' => null,
			'schema'          => null,
		)
	);
}
add_action( 'rest_api_init', 'edd_blocks_register_rest_fields' );

/**
 * Get term meta.
 *
 * @since 1.0.0
 */
function edd_blocks_term_meta_callback( $object, $field_name, $request ) {

	// Get the term ID.
	$term_id = $object['id'];

	// Get the image ID.
	$image_id = get_term_meta( $term_id, 'download_category_image_id', true );

	// Build meta array.
	$meta = array( 'image' => wp_get_attachment_image( $image_id ) );

	return $meta;

}