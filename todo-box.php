<?php

/*
Plugin Name: Todo-box
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: To do meta box with priority and check box
Version: 1.43
Author: Milan Todorovic
Author URI: http://milantodorovic.nl
License: A "Slug" license name e.g. GPL2
*/


class mpt_Custom_Meta_Box {

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
			add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
		}

	}

	/**
	 * Meta box initialization.
	 */
	public function init_metabox() {
		add_action( 'add_meta_boxes', array( $this, 'add_metabox'  )        );
		add_action( 'save_post',      array( $this, 'save_custom_meta_box' ), 10, 3 );
		}

	public function add_metabox() {
		add_meta_box(
			'my-meta-box',
			'To Do',
			array( $this, 'custom_meta_box_markup' ),
			'post',
			'advanced',
			'high',
			null
		);

	}




	public function custom_meta_box_markup($object)
	{
			wp_nonce_field(basename(__FILE__), "meta-box-nonce");

			?>
			<div>
				<label for="meta-box-text">To do!</label>

				<textarea name="meta-box-text" id="meta-textarea" cols="26" rows="7"><?php echo get_post_meta($object->ID, "meta-box-text", true); ?></textarea>

				<br>

				<label for="meta-box-dropdown">Priority</label>
				<select name="meta-box-dropdown">
					<?php
					$option_values = array(1, 2, 3, 4, 5);

					foreach($option_values as $key => $value)
					{
						if($value == get_post_meta($object->ID, "meta-box-dropdown", true))
						{
							?>
							<option selected><?php echo $value; ?></option>
							<?php
						}
						else
						{
							?>
							<option><?php echo $value; ?></option>
							<?php
						}
					}
					?>
				</select>

				<br>

				<label for="meta-box-checkbox">Done</label>
				<?php
				$checkbox_value = get_post_meta($object->ID, "meta-box-checkbox", true);

				if($checkbox_value == "")
				{
					?>
					<input name="meta-box-checkbox" type="checkbox" value="true">
					<?php
				}
				else if($checkbox_value == "true")
				{
					?>
					<input name="meta-box-checkbox" type="checkbox" value="true" checked>
					<?php
				}
				?>
			</div>
			<?php
	}

	public function save_custom_meta_box($post_id, $post)
		{

			// Verify the nonce. If isn't there, stop the script
			if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
				return $post_id;

			// Stop the script if the user does not have edit permissions
			if (!current_user_can("edit_post", $post_id))
				return $post_id;

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}
			// Check the user's permissions.
			if ( 'page' == $_POST['post_type'] ) {
				if ( ! current_user_can( 'edit_page', $post_id ) ) {
					return $post_id;
				}
			} else {
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return $post_id;
				}
			}
			$slug = "post";
			if ($slug != $post->post_type)
				return $post_id;

			$meta_box_text_value = "";
			$meta_box_dropdown_value = "";
			$meta_box_checkbox_value = "";

			// Save

			if(isset($_POST["meta-box-text"]))
			{
				$meta_box_text_value = esc_attr($_POST["meta-box-text"]);
			}
			update_post_meta($post_id, "meta-box-text", $meta_box_text_value);

			if(isset($_POST["meta-box-dropdown"]))
			{
				$meta_box_dropdown_value = esc_attr($_POST["meta-box-dropdown"]);
			}
			update_post_meta($post_id, "meta-box-dropdown", $meta_box_dropdown_value);

			if(isset($_POST["meta-box-checkbox"]))
			{
				$meta_box_checkbox_value = esc_attr($_POST["meta-box-checkbox"]);
			}
			update_post_meta($post_id, "meta-box-checkbox", $meta_box_checkbox_value);
		}




}
new mpt_Custom_Meta_Box();
