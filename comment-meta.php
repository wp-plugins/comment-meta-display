<?php /*

**************************************************************************
Plugin Name: Comment Meta Display
Plugin URI: http://wordpress.org/extend/plugins/comment-meta-display/
Description: Adds a box to the edit comments page which shows the Comment Meta.
Author: SparkWeb Interactive, Inc.
Version: 1.0
Author URI: http://www.soapboxdave.com/

**************************************************************************

Copyright (C) 2011 SparkWeb Interactive, Inc.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

**************************************************************************/

function cme_admin_init() {
	if (isset($_REQUEST['action'])) {
		if ($_REQUEST['action'] == "editcomment") {
			add_meta_box('cme-meta-box', __('Comment Meta'), 'cme_comment_meta_box', 'comment', 'normal');
		}
	}
}
add_action('admin_init', 'cme_admin_init');

function cme_comment_meta_box($comment) {
	global $wpdb;
	$comment_id = $comment->comment_ID;

	?>
	<div id="postcustomstuff">
	<div id="ajax-response"></div>
	<?php


	$comment_meta = $wpdb->get_results("SELECT * FROM $wpdb->commentmeta WHERE comment_id = $comment_id", ARRAY_A);
	$update_nonce = "";
	if ($comment_meta) :
		?>
		<table id="list-table">
			<thead>
			<tr>
				<th class="left"><?php _ex( 'Name', 'meta name' ) ?></th>
				<th><?php _e( 'Value' ) ?></th>
			</tr>
			</thead>
			<tbody id='the-list' class='list:meta'>
		<?php

		$count = 0;
		foreach ($comment_meta as $entry) :

			if ( !$update_nonce )
				$update_nonce = wp_create_nonce( 'add-meta' );

			$r = '';
			++ $count;
			if ( $count % 2 )
				$style = 'alternate';
			else
				$style = '';
			if ('_' == $entry['meta_key'] { 0 } )
				$style .= ' hidden';

			if ( is_serialized( $entry['meta_value'] ) ) {
				if ( is_serialized_string( $entry['meta_value'] ) ) {
					// this is a serialized string, so we should display it
					$entry['meta_value'] = maybe_unserialize( $entry['meta_value'] );
				} else {
					// this is a serialized array/object so we should NOT display it
					--$count;
					return;
				}
			}

			$entry['meta_key'] = esc_attr($entry['meta_key']);
			$entry['meta_value'] = esc_textarea( $entry['meta_value'] ); // using a <textarea />
			$entry['meta_id'] = (int) $entry['meta_id'];

			$delete_nonce = wp_create_nonce( 'delete-meta_' . $entry['meta_id'] );

			echo "\n\t<tr id='meta-{$entry['meta_id']}' class='$style'>";
			echo "\n\t\t<td class='left'><label class='screen-reader-text' for='meta[{$entry['meta_id']}][key]'>" . __( 'Key' ) . "</label><input name='meta[{$entry['meta_id']}][key]' id='meta[{$entry['meta_id']}][key]' tabindex='6' type='text' size='20' value='" . $entry['meta_key'] . "' />";

			echo "</td>";

			echo "\n\t\t<td><label class='screen-reader-text' for='meta[{$entry['meta_id']}][value]'>" . __( 'Value' ) . "</label><textarea name='meta[{$entry['meta_id']}][value]' id='meta[{$entry['meta_id']}][value]' tabindex='6' rows='2' cols='30'>{$entry['meta_value']}</textarea></td>\n\t</tr>";


		endforeach;

		?>
			</tbody>
		</table>
		<?php
	else :
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($){
		$("#cme-meta-box").hide();
	});
	</script>
	<?php
	endif;
	?>






</div>

	<?php



}

?>