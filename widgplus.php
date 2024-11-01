<?php
/*
 Plugin Name: Widgplus
 Plugin URI: http://www.widgplus.com/
 Description: WidgPlus allows you to show your latest Google Plus posts on your blog, Therefore its a great opportunity to get in touch with your visitors!
 Author: Widgplus
 Version: 1.01
 Author URI: http://www.widgplus.com/

 ==
 Copyright 2011 - present date  Widgplus

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

//************************************  create plugin cleanup function and admin page
 
register_uninstall_hook(__FILE__, 'widgplus_delete_plugin_options');
add_action('admin_init', 'widgplus_init' );
add_action('admin_menu', 'widgplus_add_options_page');

function widgplus_delete_plugin_options() {
	delete_option('widgplus_options');
}

register_activation_hook(__FILE__, 'widgplus_create_plugin_options');

function widgplus_create_plugin_options() {
	if (!get_option('widgplus_options')) : 
		$options['textcolor'] = 'FFFFFF';
		$options['bgcolor'] = '333333';
		$options['contentbgcolor'] = '555555';
		$options['linkcolor'] = '4D90FE';
		$options['width'] = '282';
		$options['height'] = '362';
		add_option('widgplus_options', $options);
	endif;
}

function widgplus_init(){
	 wp_register_style( 'widgplus_admin_style', plugins_url('/styles/adminstyle.css', __FILE__) );
	 wp_register_style( 'widgplus_jqui_style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/themes/flick/jquery-ui.css');
}

function widgplus_add_options_page() {
	$optpage = add_submenu_page( 'options-general.php', 'Widgplus Settings', 'Google+', 'manage_options', __FILE__, 'widgplus_render_form' );
	add_action( 'admin_print_styles-' . $optpage, 'widgplus_admin_styles' );
}

function widgplus_admin_styles() {
       
       wp_enqueue_style( 'widgplus_admin_style' );
	   wp_enqueue_style( 'widgplus_jqui_style' );
   }
if (isset($_GET['page']) and substr($_GET['page'], -12) == 'widgplus.php') :
add_action('admin_head', 'widgplus_admin_javascript');
add_action('admin_enqueue_scripts', 'widgplus_enqueue_admin_deps');

function widgplus_enqueue_admin_deps() {
wp_register_script('jqfilter', plugin_dir_url(__FILE__).'/js/filter.js' , array('jquery'));
wp_enqueue_script('jqfilter');
wp_register_script('mcolor', plugin_dir_url(__FILE__).'/js/mcolor.js' , array('jquery'));
wp_enqueue_script('mcolor');
wp_enqueue_script('jquery-ui-dialog');
}


function widgplus_admin_javascript() {

?>
<script type="text/javascript" >
jQuery(document).ready(function($) {
		$('#widgplusSave').live('click', function() {
			if ($('#widgplusGoogleID').val() == '') {
				alert('Google+ ID is required');
				return;
				}
			var data = {
				action : 'widgplus_update_options',
				<?php $widgplusupdateoptionsnonce = wp_create_nonce('widgplus_update_options_nonce'); ?>
				security : '<?php echo $widgplusupdateoptionsnonce; ?>',
				plusid : $('#widgplusGoogleID').val(),
				textcolor : $('#widgplusTextColor').val().substring(1),
				bgcolor : $('#widgplusBackgroundColor').val().substring(1),
				contentbgcolor : $('#widgplusContentBackgroundColor').val().substring(1),
				linkcolor : $('#widgplusLinkColor').val().substring(1),
				width : $('#widgplusWidth').val(),
				height: $('#widgplusHeight').val()
			}
		jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) { 
			$('#widgplussettingspreview').html(response);
			});
		});
		$('#showWidgplusHelp').live('click', function() {
			$('#findmyid').dialog({
			height: 300,
			width: 450,
			modal: true
			});
		});
});
</script>
<?php
}
endif;

function widgplus_render_form() {
	?>
	<div class="wrap">
		
		<!-- Display Plugin Icon, Header, and Description -->
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Widgplus Settings</h2>
		<p>Configure your Google+ options below. Click Save to preview your changes.</p>
		<!-- Beginning of the Plugin Options Form -->
		<form method="post" class="iform" action="options.php">
			<?php settings_fields('widgplus_plugin_options'); ?>
			<?php $options = get_option('widgplus_options'); ?>
			<ul>
			<li class="iheader">Google+ Widget Settings</li>
			<li><label for="GoogleID">Google+ ID - <a id="showWidgplusHelp" style="cursor:pointer;">Where do I find this?</a></label><input class="itext" type="text" name="GoogleID" id="widgplusGoogleID" value="<?php echo $options['plusid']; ?>" /></li>
			<li><label for="TextColor">Text Color</label><input class="icolor" type="color" data-hex="true" name="TextColor" id="widgplusTextColor" value="#<?php echo $options['textcolor']; ?>" /></li>
			<li><label for="BackgroundColor">Background Color</label><input class="icolor" type="color" data-hex="true" name="BackgroundColor" id="widgplusBackgroundColor" value="#<?php echo $options['bgcolor']; ?>" /></li>
			<li><label for="ContentBackgroundColor">Content Background Color</label><input class="icolor" type="color" data-hex="true" name="ContentBackgroundColor" id="widgplusContentBackgroundColor" value="#<?php echo $options['contentbgcolor']; ?>" /></li>
			<li><label for="LinkColor">Link Color</label><input class="icolor" type="color" data-hex="true" name="LinkColor" id="widgplusLinkColor" value="#<?php echo $options['linkcolor']; ?>" /></li>
			<li><label for="Width">Width(px)</label><input class="itext" type="text" name="Width" id="widgplusWidth" value="<?php echo $options['width']; ?>" /></li>
			<li><label for="Height">Height(px)</label><input class="itext" type="text" name="Height" id="widgplusHeight" value="<?php echo $options['height']; ?>" /></li>
			<li><label>&nbsp;</label><input type="button" class="ibutton" onclick="sendForm()" name="Save" id="widgplusSave" value="Save" /></li>
			<li class="iheader">Preview</li>
			</ul>
				<div style="display:none;" id="findmyid">
					<h2>How do I find my Google+ ID?</h2>
					Go to your <a href="http://plus.google.com/me" target="_blank">Google+ profile</a> <br /> 
					<br /> 
					<img src="<?php echo plugin_dir_url(__FILE__).'/img/helpimg.png'; ?>" />
					<br /> 
					The highlighted part of the URL is your Google+ ID<br /> 
					<br /> 
				</div>
		</form>
<br clear="all" /> 
	<div style="text-align:center;" id="widgplussettingspreview">
		<?php if (isset($options['plusid']) and $options['plusid'] != '') : ?><iframe class="widgpluswidget" src="http://widgplus.com/main/plusactivity.php?plusid=<?php echo $options['plusid']; ?>&textcolor=<?php echo $options['textcolor']; ?>&bg=<?php echo $options['bgcolor']; ?>&contentbg=<?php echo $options['contentbgcolor']; ?>&linkcolor=<?php echo $options['linkcolor']; ?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:<?php echo $options['width']; ?>px; height:<?php echo $options['height']; ?>px;"></iframe>
		<?php else : ?><p>Please add a Google+ ID above to see the preview of your G+ Widget.</p>
		<?php endif; ?>
	</div>
	</div>
	<?php
	
}


add_action('wp_ajax_widgplus_update_options', 'widgplus_update_options');

function widgplus_update_options() {
check_ajax_referer('widgplus_update_options_nonce', 'security');
$defaults['textcolor'] = 'FFFFFF';
$defaults['bgcolor'] = '333333';
$defaults['contentbgcolor'] = '555555';
$defaults['linkcolor'] = '4D90FE';
$defaults['width'] = '282';
$defaults['height'] = '362';
$option = get_option('widgplus_options');
foreach ($_POST as $key => $postvar) : 
if (!empty($postvar) and $postvar != '') :
$option[$key] = $postvar;
else:
$option[$key] = $defaults[$key];
endif;
endforeach;
update_option('widgplus_options', $option); 
$previewoptions = get_option('widgplus_options');
?>
<iframe class="widgpluswidget" src="http://widgplus.com/main/plusactivity.php?plusid=<?php echo $previewoptions['plusid']; ?>&textcolor=<?php echo $previewoptions['textcolor']; ?>&bg=<?php echo $previewoptions['bgcolor']; ?>&contentbg=<?php echo $previewoptions['contentbgcolor']; ?>&linkcolor=<?php echo $previewoptions['linkcolor']; ?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:<?php echo $previewoptions['width']; ?>px; height:<?php echo $previewoptions['height']; ?>px;"></iframe>
<?php
die();
}

//************************************  Create widget

class widgplusWidget extends WP_Widget {
	/** constructor */
	function widgplusWidget() {
		$widgpluswidgetparams = array('description' => 'Displays your G+ feed on your sidebar.');
		parent::WP_Widget( 'widgpluswidget', $name = 'Google Plus Widget', $widgpluswidgetparams);
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title; $widgplusoptions = get_option('widgplus_options'); 
			if (isset($widgplusoptions['plusid']) and $widgplusoptions['plusid'] != '') : ?>
		<br /><iframe class="widgpluswidget" src="http://widgplus.com/main/plusactivity.php?plusid=<?php echo $widgplusoptions['plusid']; ?>&textcolor=<?php echo $widgplusoptions['textcolor']; ?>&bg=<?php echo $widgplusoptions['bgcolor']; ?>&contentbg=<?php echo $widgplusoptions['contentbgcolor']; ?>&linkcolor=<?php echo $widgplusoptions['linkcolor']; ?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:<?php echo $widgplusoptions['width']; ?>px; height:<?php echo $widgplusoptions['height']; ?>px;"></iframe><br /> 
			<?php else : ?><br clear="all" /> <span style="font-size:18px;font-weight:bold;">Widgplus Notice</span><br clear="all" /> <p>No Google+ ID is set. Please add one under Settings->Google+ in your WP Admin.</p>
			<?php endif; ?>
		<?php echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['feedname'] = strip_tags($new_instance['feedname']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Display Title:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		<br clear="all" /> <br clear="all" /> 
		</p>
		<?php 
	}

} 

add_action( 'widgets_init', create_function( '', 'return register_widget("widgplusWidget");' ) );
