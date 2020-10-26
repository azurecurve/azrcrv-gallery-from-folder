<?php
/**
 * ------------------------------------------------------------------------------
 * Plugin Name: Gallery From Folder
 * Description: Gallery for all images in folder.
 * Version: 1.0.0
 * Author: azurecurve
 * Author URI: https://development.azurecurve.co.uk/classicpress-plugins/
 * Plugin URI: https://development.azurecurve.co.uk/classicpress-plugins/azrcrv-gallery-from-folder/
 * Text Domain: gallery-from-folder
 * Domain Path: /languages
 * ------------------------------------------------------------------------------
 * This is free software released under the terms of the General Public License,
 * version 2, or later. It is distributed WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Full
 * text of the license is available at https://www.gnu.org/licenses/gpl-2.0.html.
 * ------------------------------------------------------------------------------
 */

// Prevent direct access.
if (!defined('ABSPATH')){
	die();
}

// include plugin menu
require_once(dirname( __FILE__).'/pluginmenu/menu.php');
add_action('admin_init', 'azrcrv_create_plugin_menu_gff');

// include update client
require_once(dirname(__FILE__).'/libraries/updateclient/UpdateClient.class.php');

/**
 * Setup actions, filters and shortcodes.
 *
 * @since 1.0.0
 *
 */
// add actions
add_action('admin_init', 'azrcrv_gff_set_default_options');
add_action('admin_menu', 'azrcrv_gff_create_admin_menu');
add_action('admin_post_azrcrv_gff_save_options', 'azrcrv_gff_save_options');
add_action('the_posts', 'azrcrv_gff_check_for_shortcode');
add_action('plugins_loaded', 'azrcrv_gff_load_languages');

// add filters
add_filter('plugin_action_links', 'azrcrv_gff_add_plugin_action_link', 10, 2);
add_filter('codepotent_update_manager_image_path', 'azrcrv_gff_custom_image_path');
add_filter('codepotent_update_manager_image_url', 'azrcrv_gff_custom_image_url');

// add shortcodes
add_shortcode('gallery-from-folder', 'azrcrv_gff_shortcode');
add_shortcode('gff', 'azrcrv_gff_shortcode');

/**
 * Load language files.
 *
 * @since 1.0.0
 *
 */
function azrcrv_gff_load_languages() {
    $plugin_rel_path = basename(dirname(__FILE__)).'/languages';
    load_plugin_textdomain('gallery-from-folder', false, $plugin_rel_path);
}

/**
 * Check if shortcode on current page and then load css and jqeury.
 *
 * @since 1.0.0
 *
 */
function azrcrv_gff_check_for_shortcode($posts){
    if (empty($posts)){
        return $posts;
	}
	
	// array of shortcodes to search for
	$shortcodes = array(
						'gallery-from-folder',
						'gff',
						);
	
    // loop through posts
    $found = false;
    foreach ($posts as $post){
		// loop through shortcodes
		foreach ($shortcodes as $shortcode){
			// check the post content for the shortcode
			if (has_shortcode($post->post_content, $shortcode)){
				$found = true;
				// break loop as shortcode found in page content
				break 2;
			}
		}
	}
 
    if ($found){
		// as shortcode found call functions to load css and jquery
        azrcrv_gff_load_css();
    }
    return $posts;
}
	
/**
 * Load plugin css.
 *
 * @since 1.0.0
 *
 */
function azrcrv_gff_load_css(){
	wp_enqueue_style('azrcrv-gff', plugins_url('assets/css/style.css', __FILE__));
}

/**
 * Set default options for plugin.
 *
 * @since 1.0.0
 *
 */
function azrcrv_gff_set_default_options($networkwide){
	
	$option_name = 'azrcrv-gff';
	
	$new_options = array(
						'default-folder' => '',
						'updated' => strtotime('2020-10-26'),
			);
	
	// set defaults for multi-site
	if (function_exists('is_multisite') && is_multisite()){
		// check if it is a network activation - if so, run the activation function for each blog id
		if ($networkwide){
			global $wpdb;

			$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			$original_blog_id = get_current_blog_id();

			foreach ($blog_ids as $blog_id){
				switch_to_blog($blog_id);
				
				azrcrv_gff_update_options($option_name, $new_options, false);
			}

			switch_to_blog($original_blog_id);
		}else{
			azrcrv_gff_update_options( $option_name, $new_options, false);
		}
		if (get_site_option($option_name) === false){
			azrcrv_gff_update_options($option_name, $new_options, true);
		}
	}
	//set defaults for single site
	else{
		azrcrv_gff_update_options($option_name, $new_options, false);
	}
}

/**
 * Update options.
 *
 * @since 1.0.0
 *
 */
function azrcrv_gff_update_options($option_name, $new_options, $is_network_site){
	if ($is_network_site == true){
		if (get_site_option($option_name) === false){
			add_site_option($option_name, $new_options);
		}else{
			$options = get_site_option($option_name);
			if (!isset($options['updated']) OR $options['updated'] < $new_options['updated'] ){
				$options['updated'] = $new_options['updated'];
				update_site_option($option_name, azrcrv_gff_update_default_options($options, $new_options));
			}
		}
	}else{
		if (get_option($option_name) === false){
			add_option($option_name, $new_options);
		}else{
			$options = get_option($option_name);
			if (!isset($options['updated']) OR $options['updated'] < $new_options['updated'] ){
				$options['updated'] = $new_options['updated'];
				update_option($option_name, azrcrv_gff_update_default_options($options, $new_options));
			}
		}
	}
}

/**
 * Add default options to existing options.
 *
 * @since 1.0.0
 *
 */
function azrcrv_gff_update_default_options( &$default_options, $current_options ) {
    $default_options = (array) $default_options;
    $current_options = (array) $current_options;
    $updated_options = $current_options;
    foreach ($default_options as $key => &$value) {
        if (is_array( $value) && isset( $updated_options[$key])){
            $updated_options[$key] = azrcrv_gff_update_default_options($value, $updated_options[$key]);
        } else {
			$updated_options[$key] = $value;
        }
    }
    return $updated_options;
}

/**
 * Add gallery from folder action link on plugins page.
 *
 * @since 1.0.0
 *
 */
function azrcrv_gff_add_plugin_action_link($links, $file){
	static $this_plugin;

	if (!$this_plugin){
		$this_plugin = plugin_basename(__FILE__);
	}

	if ($file == $this_plugin){
		$settings_link = '<a href="'.get_bloginfo('wpurl').'/wp-admin/admin.php?page=azrcrv-gff"><img src="'.plugins_url('/pluginmenu/images/Favicon-16x16.png', __FILE__).'" style="padding-top: 2px; margin-right: -5px; height: 16px; width: 16px;" alt="azurecurve" />'.esc_html__('Settings' ,'gallery-from-folder').'</a>';
		array_unshift($links, $settings_link);
	}

	return $links;
}

/**
 * Add to menu.
 *
 * @since 1.0.0
 *
 */
function azrcrv_gff_create_admin_menu(){
	//global $admin_page_hooks;
	
	add_submenu_page("azrcrv-plugin-menu"
						,esc_html__("Gallery From Folder Settings", "gallery-from-folder")
						,esc_html__("Gallery From Folder", "gallery-from-folder")
						,'manage_options'
						,'azrcrv-gff'
						,'azrcrv_gff_display_options');
}

/**
 * Custom plugin image path.
 *
 * @since 1.0.0
 *
 */
function azrcrv_gff_custom_image_path($path){
    if (strpos($path, 'azrcrv-gallery-from-folder') !== false){
        $path = plugin_dir_path(__FILE__).'assets/pluginimages';
    }
    return $path;
}

/**
 * Custom plugin image url.
 *
 * @since 1.0.0
 *
 */
function azrcrv_gff_custom_image_url($url){
    if (strpos($url, 'azrcrv-gallery-from-folder') !== false){
        $url = plugin_dir_url(__FILE__).'assets/pluginimages';
    }
    return $url;
}

/**
 * Display Settings page.
 *
 * @since 1.0.0
 *
 */
function azrcrv_gff_display_options(){
	if (!current_user_can('manage_options')){
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'gallery-from-folder'));
    }
	
	// Retrieve plugin configuration options from database
	$options = get_option('azrcrv-gff');
	?>
	<div id="azrcrv-n-general" class="wrap">
		<fieldset>
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<?php if(isset($_GET['settings-updated'])){ ?>
				<div class="notice notice-success is-dismissible">
					<p><strong><?php esc_html_e('Settings have been saved.', 'gallery-from-folder'); ?></strong></p>
				</div>
			<?php } ?>
			<form method="post" action="admin-post.php">
				<input type="hidden" name="action" value="azrcrv_gff_save_options" />
				<input name="page_options" type="hidden" value="copyright" />
				
				<!-- Adding security through hidden referrer field -->
				<?php wp_nonce_field('azrcrv-gff', 'azrcrv-gff-nonce'); ?>
				<table class="form-table">
				
					<tr><td colspan="2">
						<?php _e('<p>Gallery From Folder is a simple plugin which will read a folder and display a gallery of all thumbnails with links to the original full-size image.</p>
						
						<p>The <strong>[gallery-from-folder]</strong> shortcode can be provided with either an <strong>alt_id</strong> parameter referning a specific name, or a <strong>post_id</strong> for a ClassicPress post.</p>
						
						<p>An example of the shortcode is <strong>[gallery-from-folder alt_id="sample-gallery"]</strong></p>', 'gallery-from-folder'); ?>
					</td></tr>
					
					<tr><th scope="row"><label for="default-folder"><?php esc_html_e('Default Folder', 'gallery-from-folder'); ?></label></th><td>
						<input name="default-folder" type="text" id="default-folder" value="<?php if (strlen($options['default-folder']) > 0){ echo stripslashes($options['default-folder']); } ?>" class="regular-text" />
						<p class="description" id="default-folder-description"><?php esc_html_e('Specify the folder which contains the image galleries (such as wp-content/galleries).', 'gallery-from-folder'); ?></p></td>
					</td></tr>
				
				</table>
				<input type="submit" value="Save Changes" class="button-primary"/>
			</form>
		</fieldset>
	</div>
	<?php
}

/**
 * Save settings.
 *
 * @since 1.0.0
 *
 */
function azrcrv_gff_save_options(){
	// Check that user has proper security level
	if (!current_user_can('manage_options')){
		wp_die(esc_html__('You do not have permissions to perform this action', 'gallery-from-folder'));
	}
	// Check that nonce field created in configuration form is present
	if (! empty($_POST) && check_admin_referer('azrcrv-gff', 'azrcrv-gff-nonce')){
	
		// Retrieve original plugin options array
		$options = get_option('azrcrv-gff');
		
		$option_name = 'default-folder';
		if (isset($_POST[$option_name])){
			$options[$option_name] = sanitize_text_field($_POST[$option_name]);
		}
		
		// Store updated options array to database
		update_option('azrcrv-gff', $options);
		
		// Redirect the page to the configuration form that was processed
		wp_redirect(add_query_arg('page', 'azrcrv-gff&settings-updated', admin_url('admin.php')));
		exit;
	}
}

/**
 * Gallery from folder shortcode.
 *
 * @since 1.0.0
 *
 */
function azrcrv_gff_shortcode($atts, $content = null){
	$args = shortcode_atts(array(
		'post_id' => 0,
		'alt_id' => 0,
	), $atts);
	$post_id = $args['post_id'];
	$alt_id = $args['alt_id'];
	
	$gallery = '';
	
	if (strlen($alt_id) > 0){
		$folder_name = $alt_id;
	}else{
		$post = get_post($post_id);
		$folder_name = $post->post_name;
	}
	
	if (strlen($folder_name) > 0){
		$options = get_option('azrcrv-gff');
		
		$thumbnail_dir = $options['default-folder'].'/'.$folder_name.'/thumbnails/';
		$thumbnail_path = ABSPATH.$thumbnail_dir;
		$thumbnail_url = trailingslashit(get_site_url()).$thumbnail_dir;
		
		$image_dir = $options['default-folder'].'/'.$folder_name.'/';
		$image_path = ABSPATH.$image_dir;
		$image_url = trailingslashit(get_site_url()).$image_dir;
		
		$image_count = 0;
		$thumbnails = array();
		if (is_dir($thumbnail_path)){
	
			if ($directory = opendir($thumbnail_path)){
				while (($file = readdir($directory)) !== false){
					if ($file != '.' and $file != '..' and $file != 'Thumbs.db' and $file != 'index.php'){
						$thumbnails[] = $file;
					}
				}
				closedir($directory);
			}
			asort($thumbnails);
			
			foreach ($thumbnails as $thumbnail){
				$gallery .= '<a href="'.$image_url.esc_html($thumbnail).'"><img src="'.$thumbnail_url.esc_html($thumbnail).'" alt="'.esc_html($thumbnail).'" /></a>';
			}
			$gallery = '<div class="azrcrv-gff-row">'.$gallery.'</div>';
		}
	}
	
	return $gallery;
	
}