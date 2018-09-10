<?php

/**
 * Determine if a given Spotlight's layout is suitable for use in the
 * Two Column template's sidebar.
 *
 * Suitable layouts are defined in ADMISSIONS_TWOCOL_SIDEBAR_SPOTLIGHT_LAYOUTS.
 *
 * @since 0.0.0
 * @author Jo Dickson
 * @param object $spotlight  a WP_Post object for the Spotlight post
 * @return bool
 */
function admissions_sidebar_spotlight_layout_isvalid( $spotlight ) {
	$valid = false;

	if ( $spotlight instanceof WP_Post ) {
		$layout = $spotlight->ucf_spotlight_layout;

		$valid_layouts = unserialize( ADMISSIONS_TWOCOL_SIDEBAR_SPOTLIGHT_LAYOUTS );
		if ( in_array( $layout, $valid_layouts ) ) {
			$valid = true;
		}
	}

	return $valid;
}


/**
 * Returns a shortcode to display a given Spotlight.
 *
 * Performs sanity checks to ensure the Spotlight's layout is suitable for use
 * in a sidebar (e.g. isn't using the 'horizontal' layout) if $sidebar=true.
 *
 * Requires the UCF Spotlights Plugin.
 *
 * @since 0.0.0
 * @author Jo Dickson
 * @param object $spotlight  a WP_Post object for the Spotlight post
 * @param bool $sidebar  Whether or not the Spotlight is being displayed in a sidebar (and requires layout validity checks)
 * @return string  A [ucf-spotlight] shortcode, or empty string on failure
 */
function admissions_get_spotlight_sc( $spotlight, $sidebar=false ) {
	$retval = '';
	$layout_isvalid = ( $sidebar === true ) ? admissions_sidebar_spotlight_layout_isvalid( $spotlight ) : true;

	if (
		$spotlight instanceof WP_Post
		&& shortcode_exists( 'ucf-spotlight' )
		&& $layout_isvalid
	) {
		$retval = "[ucf-spotlight slug='{$spotlight->post_name}']";
	}

	return $retval;
}


/**
 * Returns a shortcode to display a given Section.
 *
 * Requires the UCF Sections Plugin.
 *
 * @since 0.0.0
 * @author Jo Dickson
 * @param object $section  a WP_Post object for the Section post
 * @param array $args  Extra params to pass to the shortcode
 * @return string  A [ucf-section] shortcode, or empty string on failure
 */
function admissions_get_section_sc( $section, $args=array() ) {
	$retval = '';

	if (
		$section instanceof WP_Post
		&& shortcode_exists( 'ucf-section' )
	) {
		$retval = "[ucf-section id='{$section->ID}'";

		if ( isset( $args['class'] ) ) {
			$retval .= " class='{$args['class']}'";
		}
		if ( isset( $args['title'] ) ) {
			$retval .= " title='{$args['title']}'";
		}
		if ( isset( $args['section_id'] ) ) {
			$retval .= " section_id='{$args['section_id']}'";
		}

		$retval .= "]";
	}

	return $retval;
}


/**
 * Returns generated sidebar content for a page or page row built using
 * pagebuilder fields.
 *
 * @since 0.0.0
 * @author Jo Dickson
 * @param object $post  a WP_Post object for the Page
 * @return string
 */
function admissions_pagebuilder_get_sidebar( $post ) {
	ob_start();

	if ( $post instanceof WP_Post && have_rows( 'sidebar_content', $post->ID ) ) {
		while ( have_rows( 'sidebar_content', $post->ID ) ) : the_row();
			switch ( get_row_layout() ) {
				case 'row_spotlight':
				case 'subrow_spotlight':
					$spotlight = get_sub_field( 'spotlight' );
					echo admissions_get_spotlight_sc( $spotlight, true );
					break;
				case 'row_section':
				case 'subrow_section':
					$section = get_sub_field( 'section' );
					echo admissions_get_section_sc( $section );
					break;
				case 'row_custom':
				case 'subrow_custom':
					$custom = get_sub_field( 'custom_content' );
					echo $custom;
					break;
				default:
					break;
			}
		endwhile;
	}

	return ob_get_clean();
}


/**
 * Returns generated post content for a page built using pagebuilder fields.
 *
 * @since 0.0.0
 * @author Jo Dickson
 * @param object $post  a WP_Post object for the Page
 * @return string
 */
function admissions_pagebuilder_get_content( $post ) {
	ob_start();

	if ( $post instanceof WP_Post && have_rows( 'page_content_rows', $post->ID ) ) {
		$container_start = '[container class="mb-4 mb-md-5"]';
		$container_end   = '[/container]';
		$row_start       = '[row]';
		$row_end         = '[/row]';

		while ( have_rows( 'page_content_rows', $post->ID ) ) : the_row();
			$layout = get_row_layout();

			switch ( $layout ) {
				case 'row_spotlight':
					$spotlight = get_sub_field( 'spotlight' );
					echo admissions_get_spotlight_sc( $spotlight );
					break;
				case 'row_section':
					$section = get_sub_field( 'section' );
					echo admissions_get_section_sc( $section );
					break;
				case 'row_full_width':
					$custom = get_sub_field( 'custom_content' );
					if ( $custom ) {
						echo $custom;
					}
					break;
				case 'row_fixed_width':
					$custom = get_sub_field( 'custom_content' );
					if ( $custom ) {
						echo $container_start;
						echo $custom;
						echo $container_end;
					}
					break;
				case 'row_narrow':
					$custom = get_sub_field( 'custom_content' );
					if ( $custom ) {
						ob_start();

						echo $container_start;
						echo $row_start;
					?>
						[col lg="8" lg_offset="2"]
							<?php echo $custom; ?>
						[/col]
					<?php
						echo $row_end;
						echo $container_end;

						echo ob_get_clean();
					}
					break;
				case 'row_twocol_left':
				case 'row_twocol_right':
					$main = get_sub_field( 'main_content' );
					$sidebar = get_sub_field( 'sidebar_content' );
					if ( $main && $sidebar ) {
						ob_start();

						echo $container_start;
						echo $row_start;
					?>

					<?php if ( $layout === 'row_twocol_left' ): ?>
						[col lg="4"]
							<?php echo admissions_pagebuilder_get_sidebar( $post ); ?>
						[/col]
						[col lg="7" lg_offset="1"]
							<?php echo $main; ?>
						[/col]
					<?php else: ?>
						[col lg="7"]
							<?php echo $main; ?>
						[/col]
						[col lg="4" lg_offset="1"]
							<?php echo admissions_pagebuilder_get_sidebar( $post ); ?>
						[/col]
					<?php endif; ?>

					<?php
						echo $row_end;
						echo $container_end;

						echo ob_get_clean();
					}
					break;
				default:
					break;
			}

		endwhile;
	}

	return ob_get_clean();
}


function admissions_pagebuilder_set_content( $post ) {
	if (
		$post instanceof WP_Post
		&& get_post_type( $post ) === 'page'
		&& get_page_template_slug( $post ) === ''  // 'Default' page template, OR page template is not yet set
	) {
		$post->post_content = admissions_pagebuilder_get_content( $post );
	}
}

add_filter( 'the_post', 'admissions_pagebuilder_set_content', 10, 1 );
