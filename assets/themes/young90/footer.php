<?php if ( 'on' == et_get_option( 'divi_back_to_top', 'false' ) ) : ?>

	<span class="et_pb_scroll_top et-pb-icon"></span>

<?php endif;

if ( ! is_page_template( 'page-template-blank.php' ) ) : ?>

			<footer id="main-footer">
				<?php get_sidebar( 'footer' ); ?>


		<?php
			if ( has_nav_menu( 'footer-menu' ) ) : ?>

				<div id="et-footer-nav">
					<div class="container">
						<div class="distributor">Independent Distributor 101226339 for <a href="http://www.youngevity.com/" rel="nofollow">Youngevity</a> - M. Weekes</div>
						
						<?php
							wp_nav_menu( array(
								'theme_location' => 'footer-menu',
								'depth'          => '1',
								'menu_class'     => 'bottom-nav',
								'container'      => '',
								'fallback_cb'    => '',
							) );
						?>
						
						
					</div>
				</div> <!-- #et-footer-nav -->

			<?php endif; ?>

				<div id="footer-bottom">
					<div class="container clearfix">
					<!-- <div class="trustseal"><img src="https://www.naturesfix.com/assets/uploads/2015/04/RapidSSL_SEAL-90x50.gif" alt="Rapid SSL trust seal" /></div> -->
				<?php
					if ( false !== et_get_option( 'show_footer_social_icons', true ) ) {
						get_template_part( 'includes/social_icons', 'footer' );
					}
				?>
					

						<p id="footer-info">&copy; <?php echo date('Y');?> Young90Health. All Rights Reserved.</p>
					</div>	<!-- .container -->
				</div>
			</footer> <!-- #main-footer -->
		</div> <!-- #et-main-area -->

<?php endif; // ! is_page_template( 'page-template-blank.php' ) ?>

	</div> <!-- #page-container -->

	<?php wp_footer(); ?>
    <script  type="text/javascript">
$('.input-text.qty.text').click(function(){
	alert();
	});
</script>
	<!-- Google Code for Remarketing Tag -->
<!-- ------------------------------------------------
Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ads/remarketingsetup
------------------------------------------------- -->
<script type="text/javascript"> 
/* <![CDATA[ */ 
var google_conversion_id = 973430199; 
var google_custom_params = window.google_tag_params; 
var google_remarketing_only = true; 
/* ]]> */ 
</script> 
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/950216969/?value=0&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

</body>
</html>