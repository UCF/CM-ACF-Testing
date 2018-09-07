<?php get_header(); the_post(); ?>

<article class="<?php echo $post->post_status; ?> post-list-item">
	<!-- <div class="container mt-4 mt-sm-5 mb-5 pb-sm-4">

	</div> -->

	<?php while ( have_rows( 'page_content_rows' ) ) : the_row(); ?>
	<div><!-- TODO add fields that let you adjust this elem, classes, ID, etc. -->
		<?php
		switch ( get_row_layout() ) {
			case 'row_spotlight':
				$spotlight = get_sub_field( 'spotlight' );
				// TODO allow all spotlights
				// TODO genericize function for non-twocol layouts
				echo admissions_twocol_display_spotlight( $spotlight );
				break;
			case 'row_section':
				$section = get_sub_field( 'section' );
				// TODO genericize function for no-twocol layouts
				echo admissions_twocol_display_section( $section );
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
					ob_start();
				?>
				<div class="container mb-4 mb-md-5">
					<?php echo $custom; ?>
				</div>
				<?php
					echo ob_get_clean();
				}
				break;
			case 'row_narrow':
				$custom = get_sub_field( 'custom_content' );
				if ( $custom ) {
					ob_start();
				?>
				<div class="container mb-4 mb-md-5">
					<div class="row">
						<div class="col-lg-8 offset-lg-2">
							<?php echo $custom; ?>
						</div>
					</div>
				</div>
				<?php
					echo ob_get_clean();
				}
				break;
			case 'row_twocol_left':
				$main = get_sub_field( 'main_content' );
				$sidebar = get_sub_field( 'sidebar_content' );
				if ( $main && $sidebar ) {
					ob_start();
				?>
				<div class="container mb-4 mb-md-5">
					<div class="row">
						<div class="col-lg-4">
							<?php
							while ( have_rows( 'sidebar_content' ) ) : the_row();
								switch ( get_row_layout() ) {
									case 'subrow_spotlight':
										$spotlight = get_sub_field( 'spotlight' );
										// TODO allow all spotlights
										// TODO genericize function for non-twocol layouts
										echo admissions_twocol_display_spotlight( $spotlight );
										break;
									case 'subrow_section':
										$section = get_sub_field( 'section' );
										// TODO genericize function for no-twocol layouts
										echo admissions_twocol_display_section( $section );
										break;
									case 'subrow_custom_content':
										$custom = get_sub_field( 'custom_content' );
										echo $custom;
										break;
									default:
										break;
								}
							endwhile;
							?>
						</div>
						<div class="col-lg-7 offset-lg-1">
							<?php echo $main; ?>
						</div>
					</div>
				</div>
				<?php
					echo ob_get_clean();
				}
				break;
			case 'row_twocol_right':
				$main = get_sub_field( 'main_content' );
				$sidebar = get_sub_field( 'sidebar_content' );
				if ( $main && $sidebar ) {
					ob_start();
				?>
				<div class="container mb-4 mb-md-5">
					<div class="row">
						<div class="col-lg-7">
							<?php echo $main; ?>
						</div>
						<div class="col-lg-4 offset-lg-1">
							<?php
							while ( have_rows( 'sidebar_content' ) ) : the_row();
								switch ( get_row_layout() ) {
									case 'subrow_spotlight':
										$spotlight = get_sub_field( 'spotlight' );
										// TODO allow all spotlights
										// TODO genericize function for non-twocol layouts
										echo admissions_twocol_display_spotlight( $spotlight );
										break;
									case 'subrow_section':
										$section = get_sub_field( 'section' );
										// TODO genericize function for no-twocol layouts
										echo admissions_twocol_display_section( $section );
										break;
									case 'subrow_custom_content':
										$custom = get_sub_field( 'custom_content' );
										echo $custom;
										break;
									default:
										break;
								}
							endwhile;
							?>
						</div>
					</div>
				</div>
				<?php
					echo ob_get_clean();
				}
				break;
			default:
				break;
		}
		?>
	</div>
	<?php endwhile; ?>
</article>

<?php get_footer(); ?>
