<?php

function latest_posts_sidebar_module($args) {
	global $post;

	extract($args);

	$query = 'showposts='.sbm_get_option('num_posts');
	$k2asidescategory = get_option('k2asidescategory');
	if ((get_option('k2asidesposition') == '1') and ($k2asidescategory != '0')) {
		$query .= '&cat=-' . $k2asidescategory;
	}

	echo($before_module . $before_title . $title . $after_title);
	?>
	<span class="metalink"><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('RSS Feed for Blog Entries','k2_domain'); ?>" class="feedlink"><img src="<?php bloginfo('template_directory'); ?>/images/feed.png" alt="RSS" /></a></span>

		<ul>
		<?php
			$latest = new WP_Query($query);
			foreach ($latest->posts as $post) {
				setup_postdata($post);
		?>
			<li><a href="<?php the_permalink(); ?>" title="<?php echo wp_specialchars(strip_tags(get_the_title()), 1); ?>"><?php the_title(); ?></a></li>
		<?php } // End Latest loop ?>
		</ul>
	<?php
	echo($after_module);
}

function latest_posts_sidebar_module_control() {
	if(isset($_POST['latest_posts_module_num_posts'])) {
		sbm_update_option('num_posts', $_POST['latest_posts_module_num_posts']);
	}

	?>
		<p><label for="latest-posts-module-num-posts"><?php _e('Number of posts:', 'k2_domain'); ?></label> <input id="latest-posts-module-num-posts" name="latest_posts_module_num_posts" type="text" value="<?php echo(sbm_get_option('num_posts')); ?>" size="2" /></p>
	<?php
}

register_sidebar_module('Latest posts module', 'latest_posts_sidebar_module', 'sb-latest', array('num_posts' => 10));
register_sidebar_module_control('Latest posts module', 'latest_posts_sidebar_module_control');

?>
