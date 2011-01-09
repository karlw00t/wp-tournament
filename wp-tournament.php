<?php
/*
Plugin Name: WP-Tournament
Description: Allow you to set up tournament between things
Version: 1.0
Author: Joshua Williams
Author URI: http://URI_Of_The_Plugin_Author
License: MIT
*/

class wpTournaments {
	var $meta_fields = array("wpT-teams");
	function wpTournaments() {
		register_post_type('Tournaments', array(
			'label' => __('Tournaments'),
			'singular_label' => __('Tournament'),
			'public' => true,
			'show_ui' => true, // UI in admin panel
			'_builtin' => false, // It's a custom post type, not built in
			'_edit_link' => 'post.php?post=%d',
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => array("slug" => "tournament"), // Permalinks
			'query_var' => "tournaments", // This goes to the WP_Query schema
			'supports' => array('title', 'excerpt', 'editor' /*,'custom-fields'*/) // Let's use custom fields for debugging purposes only
		));
		add_action("admin_init", array(&$this, "admin_init"));
		add_action("wp_insert_post", array(&$this, "wp_insert_post"), 10, 2);
	}

	function admin_init() {
		// Custom meta boxes for the edit podcast screen
		add_meta_box("1-Round", "First Round", array(&$this, "meta_options"), "Tournaments" );
	}
	
	// Admin post meta contents
	function meta_options() {
		global $post;
		$args = array(
			'post_type' => 'Teams',
			'numberposts' => -1,
		);
?>
<form>
<?php
		$team_posts = get_posts($args);
		$this->build_options('team_a', $team_posts);
		echo '<br />';
		$this->build_options('team_b', $team_posts);
?>
<br />
<input type="hidden" value="add-new" name="add-new" />
<input type="submit" value="Submit" />
</form>
<hr />

<?php
		var_dump(get_post_meta($post->ID, 'team_count'));
		//var_dump(delete_post_meta($post->ID, 'team_count'));
	}

	function build_options($name, $args) {
?>
<select>
<option>Select Team</option>
<?php
		foreach ($args as $post) {
?>
<option value="" <?php echo $selected_text ?>><?php echo $post->post_title ?></option>
<?php
		}
?>
</select>
<?php
		
	}

	function wp_insert_post($post_id, $post = null) {
		if ($post->post_type == "Tournaments") {
			if ($_POST['add-new']) {
				$key = 'team_count';
				$current = get_post_meta($post->ID, $key);
				
				if (!update_post_meta($post_id, $key, $current[] = array($_POST['team_a'], $_POST['team_b']))) {
					// Or add the meta data
					add_post_meta($post_id, $key,  array(array($_POST['team_a'], $_POST['team_b'])));
				}
			}
			// Loop through the POST data
			foreach ($this->meta_fields as $key) {
				$value = @$_POST[$key];
				if (empty($value)) {
					delete_post_meta($post_id, $key);
					continue;
				}

				// If value is a string it should be unique
				if (!is_array($value)) {
					// Update meta
					if (!update_post_meta($post_id, $key, $value)) {
						// Or add the meta data
						add_post_meta($post_id, $key, $value);
					}
				} else {
					// If passed along is an array, we should remove all previous data
					delete_post_meta($post_id, $key);
					
					// Loop through the array adding new values to the post meta as different entries with the same name
					foreach ($value as $entry) add_post_meta($post_id, $key, $entry);
				}
			}
		}
	}
}

class wpTeams{
	function wpTeams() {
		register_post_type('Teams', array(
			'label' => __('Teams'),
			'singular_label' => __('Team'),
			'public' => true,
			'show_ui' => true, // UI in admin panel
			'_builtin' => false, // It's a custom post type, not built in
			'_edit_link' => 'post.php?post=%d',
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => array("slug" => "team"), // Permalinks
			'query_var' => "teams", // This goes to the WP_Query schema
			'supports' => array('title','author', 'excerpt', 'editor' /*,'custom-fields'*/) // Let's use custom fields for debugging purposes only
		));
	}
}


// Initiate the plugin
add_action("init", "wpTournaments");
function wpTournaments() { global $p30; $p30 = new wpTournaments(); }
add_action("init", "wpTeams");
function wpTeams() { global $p30; $p30 = new wpTeams(); }
