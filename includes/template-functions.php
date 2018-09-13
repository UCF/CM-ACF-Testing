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
 * Returns markup for a given ACF flexible content layout.
 * Must be used within a have_rows() loop.
 *
 * @since 0.0.0
 * @author Jo Dickson
 * @param string $layout  ACF flexible content row layout
 * @param object $post  WP_Post object for the page
 */
function admissions_pagebuilder_get_row_content( $layout, $post ) {
	$wrapper_start   = '';
	$wrapper_end     = '';
	$container_start = '[container]';
	$container_end   = '[/container]';
	$row_start       = '[row]';
	$row_end         = '[/row]';
	$content         = '';

	// Get and assign content var for appliable layouts
	if ( in_array( $layout, array(
		'row_full_width',
		'subrow_custom',
		'row_fixed_width',
		'row_narrow'
	) ) ) {
		$content = get_sub_field( 'custom_content' );
	}

	// Create and assign wrapper element vars for applicable layouts
	if ( in_array( $layout, array(
		'row_full_width',
		'subrow_custom',
		'row_fixed_width',
		'row_narrow',
		'row_twocol_sidebar'
	) ) ) {
		$wrapper_elem  = get_sub_field( 'wrapper_element' );
		$wrapper_class = get_sub_field( 'wrapper_element_class' );
		$wrapper_id    = get_sub_field( 'wrapper_element_id' );
		$wrapper_attrs = array(
			'class' => $wrapper_class,
			'id'    => $wrapper_id
		);

		if ( have_rows( 'wrapper_element_attrs' ) ) {
			while ( have_rows( 'wrapper_element_attrs' ) ): the_row();
				$wrapper_attr_name = sanitize_key( get_sub_field( 'attribute_name' ) );
				$wrapper_attr_val  = esc_attr( get_sub_field( 'attribute_value' ) );

				if ( $wrapper_attr_name ) {
					$wrapper_attrs[$wrapper_attr_name] = $wrapper_attr_val;
				}
			endwhile;
		}

		if ( $wrapper_elem ) {
			$wrapper_start = "<{$wrapper_elem}";
			foreach ( $wrapper_attrs as $key => $val ) {
				$wrapper_start .= " {$key}=\"{$val}\"";
			}
			$wrapper_start .= '>';

			$wrapper_end   = "</{$wrapper_elem}>";
		}
	}

	// Print each layout's markup
	ob_start();

	switch ( $layout ) {
		// Handle Spotlights
		case 'row_spotlight':
		case 'subrow_spotlight':
			$spotlight = get_sub_field( 'spotlight' );
			echo admissions_get_spotlight_sc( $spotlight );
			break;

		// Handle Sections
		case 'row_section':
		case 'subrow_section':
			$section       = get_sub_field( 'section' );
			$section_class = get_sub_field( 'section_class' );
			$section_id    = get_sub_field( 'section_id' );
			$section_title = get_sub_field( 'section_title' );
			$attrs         = array();

			if ( $section_class ) {
				$attrs['class'] = esc_attr( $section_class );
			}
			if ( $section_id ) {
				$attrs['section_id'] = esc_attr( $section_id );
			}
			if ( $section_title ) {
				$attrs['title'] = esc_attr( $section_title );
			}

			echo admissions_get_section_sc( $section, $attrs );
			break;

		// Handle full-width content rows
		case 'row_full_width':
		case 'subrow_custom':
			if ( $content ) {
				echo $wrapper_start;
				echo $content;
				echo $wrapper_end;
			}
			break;

		// Handle fixed width content rows
		case 'row_fixed_width':
			if ( $content ) {
				echo $wrapper_start;
				echo $container_start;
				echo $content;
				echo $container_end;
				echo $wrapper_end;
			}
			break;

		// Handle narrow content rows
		case 'row_narrow':
			if ( $content ) {
				ob_start();

				echo $wrapper_start;
				echo $container_start;
				echo $row_start;
			?>
				[col lg="8" lg_offset="2"]<?php echo $content; ?>[/col]
			<?php
				echo $row_end;
				echo $container_end;
				echo $wrapper_end;

				echo ob_get_clean();
			}
			break;

		// Handle two column rows
		case 'row_twocol_sidebar':
			$main    = get_sub_field( 'main_content' );
			$sidebar = get_sub_field( 'sidebar_content' );

			if ( $main && $sidebar ) {
				$sidebar_pos = get_sub_field( 'sidebar_position' );

				ob_start();

				echo $wrapper_start;
				echo $container_start;
				echo $row_start;
			?>
			<?php if ( $sidebar_pos === 'left' ): ?>
				[col lg="4"]<?php echo admissions_pagebuilder_get_sidebar( $post ); ?>[/col]
				[col lg="7" lg_offset="1"]<?php echo $main; ?>[/col]
			<?php else: ?>
				[col lg="7"]<?php echo $main; ?>[/col]
				[col lg="4" lg_offset="1"]<?php echo admissions_pagebuilder_get_sidebar( $post ); ?>[/col]
			<?php endif; ?>
			<?php
				echo $row_end;
				echo $container_end;
				echo $wrapper_end;

				echo ob_get_clean();
			}
			break;
		default:
			break;
	}

	return ob_get_clean();
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
			echo admissions_pagebuilder_get_row_content( get_row_layout(), $post );
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
		while ( have_rows( 'page_content_rows', $post->ID ) ) : the_row();
			echo admissions_pagebuilder_get_row_content( get_row_layout(), $post );
		endwhile;
	}

	return ob_get_clean();
}


/**
 * Returns a boolean value reflecting whether or not the given WP_Post
 * object represents a page built using pagebuilder fields.
 *
 * @since 0.0.0
 * @author Jo Dickson
 * @param mixed $post  Post ID string/int, or WP_Post object
 * @return boolean
 */
function admissions_is_pagebuilder_page( $post ) {
	if ( is_numeric( $post ) ) {
		$post = get_post( $post );
	}

	if (
		$post instanceof WP_Post
		&& get_post_type( $post ) === 'page'
		&& get_page_template_slug( $post ) === ''  // 'Default' page template, OR page template is not yet set
	) {
		return true;
	}
	return false;
}


/**
 * Overrides post_content property on WP_Post objects to use generated
 * pagebuilder contents, where applicable.
 *
 * @since 0.0.0
 * @author Jo Dickson
 * @param object $post  WP_Post object
 */
function admissions_pagebuilder_set_content( $post ) {
	if ( admissions_is_pagebuilder_page( $post ) ) {
		$post->post_content = admissions_pagebuilder_get_content( $post );
	}
}

add_filter( 'the_post', 'admissions_pagebuilder_set_content', 10, 1 );


/**
 * Modifies the contents available in the standard page WYSIWYG editor
 * for pages built using pagebuilder tools.  Allows for folks to switch
 * from the Default Page template to another without losing their changes.
 *
 * @since 0.0.0
 * @author Jo Dickson
 * @param string $content  The post content passed into the content_edit_pre hook
 * @param int $post_id  The ID of the current Page
 * @return string
 */
function admissions_pagebuilder_set_post_editor( $content, $post_id ) {
	if ( admissions_is_pagebuilder_page( $post_id ) && have_rows( 'page_content_rows', $post_id ) ) {
		$post = get_post( $post_id );
		return admissions_pagebuilder_get_content( $post );
	}
	return $content;
}

add_filter( 'content_edit_pre', 'admissions_pagebuilder_set_post_editor', 10, 2 );
