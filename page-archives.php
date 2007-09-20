<?php /*
	Template Name: Archives (Do Not Use Manually)
*/ ?>

<?php /* Counts the posts, comments and categories on your blog */
	$numposts = $wpdb->get_var("SELECT COUNT(1) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type != 'page'");
	if (0 < $numposts) $numposts = number_format($numposts); 
	
	$numcomms = $wpdb->get_var("SELECT COUNT(1) FROM $wpdb->comments WHERE comment_approved = '1'");
	if (0 < $numcomms) $numcomms = number_format($numcomms);
	
	$numcats = count(get_all_category_ids());
?>

<?php get_header(); ?>

<div class="content">
	<div id="primary">
		<div id="notices"></div>

		<div id="current-content" class="hfeed">

			<?php the_post(); ?>

			<div id="post-<?php the_ID(); ?>" class="<?php k2_post_class(); ?>">

				<div class="page-head">
					<h2><a href="<?php the_permalink(); ?>" rel="bookmark" title='<?php printf( __('Permanent Link to "%s"','k2_domain'), wp_specialchars(strip_tags(the_title('', '', false)),1) ); ?>'><?php the_title(); ?></a></h2>
					<?php edit_post_link(__('Edit','k2_domain'), '<span class="entry-edit">','</span>'); ?>
				</div>

				<div class="entry-content">

					<p><?php printf(__('This is the frontpage of the %1$s archives. Currently the archives are spanning %2$s posts and %3$s comments, contained within the meager confines of %4$s categories. Through here, you will be able to move down into the archives by way of time or category. If you are looking for something specific, perhaps you should try the search on the sidebar.','k2_domain'), get_bloginfo('name'), $numposts, $numcomms, $numcats); ?></p>

					<?php if (function_exists('wp_tag_cloud')) { ?>
					<h3><?php _e('Tag Cloud','k2_domain'); ?></h3>
					<div id="tag-cloud">
					<?php if (function_exists('wp_tag_cloud')) wp_tag_cloud(); ?>
					</div>
					<?php } // End Tag Check ?>

					<h3><?php _e('Browse by Month','k2_domain'); ?></h3>
					<ul class="archive-list">
						<?php wp_get_archives('show_post_count=1'); ?>
					</ul>

					<br class="clear" />

					<h3><?php _e('Browse by Category','k2_domain'); ?></h3>
					<ul class="archive-list">
						<?php wp_list_cats('hierarchical=0&optioncount=1'); ?>
					</ul>

					<br class="clear" />

				</div> <!-- .entry-content -->
			</div> <!-- #post-ID -->

		</div> <!-- #current-content .hfeed -->

		<div id="dynamic-content"></div>
	</div> <!-- #primary -->

	<?php get_sidebar(); ?>

</div> <!-- .content -->
	
<?php get_footer(); ?>
