<?php
namespace CNP;
/**
 * cnp_add_acf_organisms_to_content
 *
 * Filters the ACF organisms into the content filter, so that
 * organism content can be located during site searches.
 *
 * @since 0.1
 *
 * @see (calls) cnp_get_acf_organism_markup
 * @global object $post Post object.
 *
 * @param string $content The post content.
 *
 * @return string The modified post content.
 */
function add_acf_organisms_to_content( $content ) {

	global $post;

	// Check the post for layouts data
	$all_layouts = get_field( 'layouts', $post->ID );

	// If we have layouts, get the layout markup.
	if ( ! empty( $all_layouts ) ) {

		// Loop through the layouts
		foreach ( $all_layouts as $layout_index => $layout_data ) {

			// E.g., a layout named "layout_content" has a PHP class titled "CNP\ACF_Content."
			// "layout_post_list" becomes "CNP\ACF_PostList"
			$acf_atom_or_organism_class_suffix = str_replace( ' ', '', ucwords( str_replace( [ 'layout', '_' ], [ '', ' ' ], $layout_data['acf_fc_layout'] ) ) );
			$acf_atom_or_organism_class_name   = 'CNP\\ACF_' . $acf_atom_or_organism_class_suffix;

			if ( class_exists( $acf_atom_or_organism_class_name ) ) {
				$atom_or_organism = new $acf_atom_or_organism_class_name( $layout_data );
				$atom_or_organism->get_markup();

				$content = apply_filters( $atom_or_organism->name . '_before', $content );

				if ( '' !== $atom_or_organism->markup ) {
					$content .= $atom_or_organism->markup;
				}

				$content = apply_filters( $atom_or_organism->name . '_after', $content );
			}
		}
	}

	return $content;

}

add_filter( 'the_content', 'CNP\add_acf_organisms_to_content', 10, 1 );
