<?php
/**
 * Template Name: Two Column
 * Template Post Type: page, post
 */
?>
<?php get_header(); the_post(); ?>

<article class="<?php echo $post->post_status; ?> post-list-item">
	<div class="container mt-4 mt-sm-5 mb-5 pb-sm-4">
		<p class="lead">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sit amet lacus pretium, scelerisque nisl at, aliquet lacus.</p>
		<div class="row">
			<div class="col-lg-7">
				<?php the_content(); ?>
			</div>
			<div class="col-lg-4 offset-lg-1">
				<hr class="hidden-lg-up my-4 my-md-5">
				<img class="img-fluid mb-4" src="https://placehold.it/300x350">
				<h2 class="h4">Dummy Spotlight</h2>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam sit amet lacus pretium, scelerisque nisl at, aliquet lacus. Quisque non lectus eu nibh dictum feugiat.</p>
			</div>
		</div>
	</div>
</article>

<?php get_footer(); ?>
