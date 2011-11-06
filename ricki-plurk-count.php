<?php
/*
Plugin Name: Ricki Plurk Count
Plugin URI: http://developer.aniarc.org/ricki-plurk-count/
Description: Simple plurk sharing button with click counter. Usage: <strong>&lt;?php ricki_the_button(get_the_ID(), "shares", the_permalink()." (".get_the_title().")") ?&gt;</strong>
Author: <a href="http://developer.aniarc.org/ricki-plurk-count/"> Ricki P. </a>
Version: 1.1
*/

add_action('wp_ajax_ricki_plurk', 'ricki_plurk_callback');
add_action('wp_ajax_nopriv_ricki_plurk', 'ricki_plurk_callback');

function ricki_plurk_callback() {
	check_ajax_referer( 'ricki-plurk', 'nonce' );
	$post_id	= isset($_POST['id']) ? $_POST['id'] : die();
	
	$current_count = 1 + intval(get_post_meta($post_id , 'ricki_plurk_count', 'true' ));
	add_post_meta( $post_id, 'ricki_plurk_count', $current_count, true ) or 
		update_post_meta( $post_id, 'ricki_plurk_count', $current_count );	
		
	echo $current_count;
	die(); // this is required to return a proper result
}

add_action('wp_head', 'ricki_plurk_javascript');

function ricki_plurk_javascript() {
	$ajax_nonce = wp_create_nonce("ricki-plurk");
	?>
	<!-- http://developer.aniarc.org/ricki-plurk-count/ -->
    <script type="text/javascript" >
    function ricki_click(arg, post_id, post_quali, post_content){
		window.open("http://www.plurk.com/?qualifier=" + post_quali + "&status=" + post_content);
		jQuery.post(
			'/wp-admin/admin-ajax.php',
			{ action: 'ricki_plurk', id: post_id, nonce: '<?php echo $ajax_nonce; ?>' }, 
			function(response) {
				(document.all) ?
				arg.childNodes[0].childNodes[0].innerText = response :
				arg.childNodes[1].childNodes[1].innerText = response ;
			}
		);
    }
    </script>
	<?php
}

function ricki_the_button($ricki_id, $ricki_quali, $ricki_content) {
	$ricki_count = intval(get_post_meta( $ricki_id, 'ricki_plurk_count', 'true' ));
	?>
	<!-- http://developer.aniarc.org/ricki-plurk-count/ -->
	<div class="ricki-plurk-count" onclick="ricki_click(this, <?php echo $ricki_id;?>, '<?php echo $ricki_quali;?>', '<?php echo $ricki_content; ?>')" style="display: inline-block; padding-left: 3px; padding-right: 3px; cursor: pointer; cursor: hand;">
		<div style="background: no-repeat url(/wp-content/plugins/ricki-plurk-count/sprite.png) -255px -21px;  background-repeat-x: no-repeat;  background-repeat-y: no-repeat;  background-attachment: initial;  background-position-x: -255px;  background-position-y: -21px;  background-origin: initial;  background-clip: initial;  background-color: initial;  height: 35px;  width: 50px;  text-align: center;  margin: 3px;  overflow: hidden;  color: black;">
			<div style="padding-top: 6px; font-size: 16px; "><?php echo $ricki_count ?></div>
		</div>
		<img src="/wp-content/plugins/ricki-plurk-count/plurk.gif" style="padding-right: 10px">
	</div>
	<?php
} ?>