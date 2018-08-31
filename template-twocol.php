<?php
/**
 * Template Name: Two Column
 * Template Post Type: page, post
 */
?>
<?php get_header(); the_post(); ?>

<article class="<?php echo $post->post_status; ?> post-list-item">
	<div class="container mt-4 mt-sm-5 mb-5 pb-sm-4">

		<?php if ( $post->page_lead_text ) : ?>
		<div class="lead">
			<?php echo apply_filters( 'the_content', $post->page_lead_text ); ?>
		</div>
		<?php endif; ?>

		<div class="row">
			<div class="col-lg-8">
				<?php the_content(); ?>
			</div>
			<div class="col-lg-4 pl-lg-5">
				<hr class="hidden-lg-up my-4 my-md-5">
				TODO
			</div>
		</div>
	</div>
</article>

<?php get_footer(); ?>
