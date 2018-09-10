<?php
/**
 * Template Name: Two Column (Right Sidebar)
 * Template Post Type: page, post
 */
?>
<?php get_header(); the_post(); ?>

<article class="<?php echo $post->post_status; ?> post-list-item">
	<div class="container mt-4 mt-sm-5 mb-5 pb-sm-4">

		<?php if ( $post->page_lead_text ) : ?>
		<div class="lead mb-4 mb-md-5">
			<?php echo apply_filters( 'the_content', $post->page_lead_text ); ?>
		</div>
		<?php endif; ?>

		<div class="row">
			<div class="col-lg-7">
				<?php the_content(); ?>
			</div>

			<?php
			$sidebar = admissions_pagebuilder_get_sidebar( $post );
			if ( $sidebar ):
			?>
			<div class="col-lg-4 offset-lg-1">
				<hr class="hidden-lg-up my-4 my-md-5">
				<aside>
					<?php echo apply_filters( 'the_content', $sidebar ); ?>
				</aside>
			</div>
			<?php endif; ?>
		</div>
	</div>
</article>

<?php get_footer(); ?>
