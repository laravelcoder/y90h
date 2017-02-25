<?php get_header(); global $post; ?>
<h1><?php echo the_title($post->ID);?></h1>
<div id="main-content" role="main" itemprop="mainContentOfPage" itemscope="itemscope" itemtype="http://schema.org/Blog">
	<div class="container">
		<div id="content-area" class="clearfix">
			<!--<div id="left-area">-->
		<?php
			if ( have_posts() ) :
				while ( have_posts() ) : the_post();
					$post_format = et_pb_post_format(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_post' ); ?> itemscope="itemscope" itemtype="http://schema.org/BlogPosting" itemprop="blogPost">

				<?php
					$thumb = '';

					$width = (int) apply_filters( 'et_pb_index_blog_image_width', 1080 );

					$height = (int) apply_filters( 'et_pb_index_blog_image_height', 675 );
					$classtext = 'et_pb_post_main_image';
					$titletext = get_the_title();
					$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Blogimage' );
					$thumb = $thumbnail["thumb"];

					et_divi_post_format_content();

					if ( ! in_array( $post_format, array( 'link', 'audio', 'quote' ) ) ) {
						if ( 'video' === $post_format && false !== ( $first_video = et_get_first_video() ) ) :
							printf(
								'<div class="et_main_video_container">
									%1$s
								</div>',
								$first_video
							);
						elseif ( 'on' == et_get_option( 'divi_thumbnails_index', 'on' ) && '' !== $thumb  ) : ?>
							<a href="<?php the_permalink(); ?>">
								<?php print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height ); ?>
							</a>
					<?php
						endif;
					} ?>

				<?php if ( ! in_array( $post_format, array( 'link', 'audio', 'quote', 'gallery' ) ) ) : ?>
					<?php if ( ! in_array( $post_format, array( 'link', 'audio' ) ) ) : ?>
						<h2 class="entry-title" itemprop="headline"><a itemprop="url" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php endif; ?>

					<?php
						et_divi_post_meta();

						if ( 'on' !== et_get_option( 'divi_blog_style', 'false' ) || ( is_search() && ( 'on' === get_post_meta( get_the_ID(), '_et_pb_use_builder', true ) ) ) )
							truncate_post( 270 );
						else
							the_content();
					?>
				<?php endif; ?>

					</article> <!-- .et_pb_post -->
			<?php
					endwhile;

					if ( function_exists( 'wp_pagenavi' ) )
						wp_pagenavi();
					else
						get_template_part( 'includes/navigation', 'index' );
				else :
					get_template_part( 'includes/no-results', 'index' );
				endif;
			?>
			<!--</div>--> <!-- #left-area -->
			<!-- <h1>hello</h1> -->
			<?php //get_sidebar(); ?>
		</div> <!-- #content-area -->
			<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<ins class="adsbygoogle"
	style="display:block"
	data-ad-client="ca-pub-7673167166774792"
	data-ad-slot="8332493866"
	data-ad-format="auto"></ins>
	<script>
		(adsbygoogle = window.adsbygoogle || []).push({});
	</script>
	</div> <!-- .container -->
</div> <!-- #main-content -->

<?php get_footer(); ?>
