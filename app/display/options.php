<?php

	// Check that the K2 folder has no spaces
	$dir_has_spaces = (strpos(TEMPLATEPATH, ' ') !== false);

	// Get post meta format
	$entrymeta1 = get_option('k2entrymeta1');
	if ( empty($entrymeta1) ) {
		$entrymeta1 = __('Published by %author% on %date% in %categories%. %comments%. %tags%.', 'k2');
	}

	global $wp_themes;
	$wp_themes[$current]['Stylesheet Files'][] = "/css/k2.css";
	print_r($wp_themes);
	echo $wp_themes;
?>

<div class="wrap">
	<?php if ( isset($_GET['defaults']) ): ?>
	<div class="updated fade">
		<p><?php _e('K2 has been restored to default settings.', 'k2'); ?></p>
	</div>
	<?php endif; ?>

	<?php if ( isset($_GET['saved']) ): ?>
	<div class="updated fade">
		<p><?php _e('K2 Options have been updated', 'k2'); ?></p>
	</div>
	<?php endif; ?>

	<?php if ($dir_has_spaces): ?>
		<div class="error">
		<?php printf( __('The K2 directory: <strong>%s</strong>, contains spaces. For K2 to function properly, you will need to remove the spaces from the directory name.', 'k2'), TEMPLATEPATH ); ?>
		</div>
	<?php endif; ?>

	<?php do_action('k2_options_top'); ?>

	<?php if ( function_exists('screen_icon') ) screen_icon(); ?>
	<h2><?php _e('K2 Options', 'k2'); ?></h2>
	<form action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" method="post" id="k2-options">
		<ul class="options-list">

			<li>
				<h3 class="main-label"><label for="k2-advnav"><?php _e('Use Advanced Navigation?', 'k2'); ?></label></h3>

				<p class="main-option">
					<input id="k2-advnav" name="k2[advnav]" type="checkbox" value="1" <?php checked('1', get_option('k2livesearch')); ?> />

					&nbsp;&nbsp;&nbsp;<label for="k2-animations" style="vertical-align: baseline"><?php _e('and animations', 'k2'); ?></label>
					<input id="k2-animations" name="k2[animations]" type="checkbox" value="1" <?php checked('1', get_option('k2animations')); ?> />

				</p>

				<p class="description"><?php _e('Seamlessly search and navigate old posts.', 'k2'); ?></p>

			</li>

			<li>
				<h3 class="main-label"><label for="k2-archives"><?php _e('Use the Archives Page?', 'k2'); ?></label></h3>

				<p class="main-option">
					<input id="k2-archives" name="k2[archives]" type="checkbox" value="add_archive" <?php checked('1', get_option('k2archives')); ?> />
				</p>

				<p class="description"><?php _e('Installs a pre-made archives page.', 'k2'); ?></p>
			</li>

			<li>
				<h3><?php _e('Post Entry', 'k2'); ?></h3>

				<p class="description">
					<?php _e('Use the following keywords: %author%, %categories%, %comments%, %date%, %tags% and %time%. <!--You can also use third-party shortcodes.-->', 'k2'); ?>
				</p>

				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="k2-entry-meta-1"><?php _e('Top Meta:', 'k2'); ?></label>
							</th>
							<td>
								<input id="k2-entry-meta-1" name="k2[entrymeta1]" type="text" value="<?php form_option('k2entrymeta1'); ?>" />
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="k2-entry-meta-2"><?php _e('Bottom Meta:', 'k2'); ?></label>
							</th>
							<td>
								<input id="k2-entry-meta-2" name="k2[entrymeta2]" type="text" value="<?php form_option('k2entrymeta2'); ?>" />
							</td>
						</tr>
					</tbody>
				</table>


				<div id="meta-preview" class="postbox">
					<h3 class="handle"><span><?php _e('Preview', 'k2'); ?></span></h3>
					<?php
						query_posts('showposts=1&what_to_show=posts&order=desc&post_status=publish');
						if ( have_posts() ): the_post();
					?>
					<div id="entry-<?php the_ID(); ?>" class="inside">
						<div class="entry-header">
							<h5 class="entry-title"><a href="#" rel="bookmark" title='<?php printf( __('Permanent Link to "%s"', 'k2'), esc_html(strip_tags(the_title('', '', false)), 1) ); ?>'><?php the_title(); ?></a></h5>

							<div class="entry-meta">
								<?php k2_entry_meta(1); ?>
							</div> <!-- .entry-meta -->
						</div> <!-- .entry-header -->

						<div class="entry-content">
							<?php the_excerpt(); ?>
						</div> <!-- .entry-content -->

						<div class="entry-footer">
							<div class="entry-meta">
								<?php k2_entry_meta(2); ?>
							</div><!-- .entry-meta -->
						</div><!-- .entry-footer -->
					</div> <!-- #entry-ID -->
					<?php endif; ?>
				</div>
			</li>

			<?php /* K2 Hook */ do_action('k2_display_options'); ?>
		</ul>

		<div class="submit">
			<?php wp_nonce_field('k2options'); ?>
			<input type="hidden" name="k2-options-submit" value="k2-options-submit" />

			<input type="submit" id="save" name="save" class="button-primary" value="<?php esc_attr_e('Save Changes', 'k2'); ?>" />

			<input type="submit" name="restore-defaults" id="restore-defaults" onClick="return confirmDefaults();" value="<?php esc_attr_e('Revert to K2 Defaults', 'k2'); ?>" class="button-secondary" />
			<input type="submit" name="default-widgets" id="default-widgets-btn" class="button-secondary" value="<?php esc_attr_e('Install a Default Set of Widgets', 'k2'); ?>" />
		</div><!-- .submit -->
	</form>

</div><!-- .wrap -->
