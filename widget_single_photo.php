<?php
/*
Plugin Name: Single Photo Widget
Plugin URI: http://www.vjcatkick.com/?page_id=3860
Description: Display single photo on your sidebar.
Version: 0.0.3
Author: V.J.Catkick
Author URI: http://www.vjcatkick.com/
*/

/*
0.0.3 - fix, compatibility. with this update, <ul> tag had been added. if you need to adjust position of frame, use following CSS code.
---
#widget_single_photo ul {
margin-left: 0px !important;
}
---
also, i did changed option string for compatibility with other my widgets so you must re-enter options to display it.
*/


/*
License: GPL
Compatibility: WordPress 2.5 with Widget-plugin.

Installation:
Place the widget_single_photo folder in your /wp-content/plugins/ directory
and activate through the administration panel, and then go to the widget panel and
drag it to where you would like to have it!
*/

/*  Copyright V.J.Catkick - http://www.vjcatkick.com/

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
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


/* Changelog
* Wed Nov 26 2008 - v0.0.1
- Initial release
* Thur Nov 27 2008 - v0.0.2
- some fix
* Tue Dec 30 2008 - v0.0.3
- compatibility fix
*/


function widget_single_photo_init() {
	if ( !function_exists('register_sidebar_widget') )
		return;

	function widget_single_photo( $args ) {
		extract($args);

function _wp_get_first_entry( $request ) {
	$buff = file_get_contents( $request );

	if( $buff <> FALSE ) {
			$sPos = strpos( $buff, '<div class="post' );
			$buff = substr( $buff, $sPos, strlen( $buff ) );	
			$sPos = strpos( $buff, '<div class="post', 1 );
			if( $sPos <> FALSE ) $buff = substr( $buff, 0, $sPos );

			$sPos = strpos( $buff, '<div class="entry">' );
			$ePos = strpos( $buff, '<p class="postmetadata">' );
			$buff = substr( $buff, $sPos, $ePos - $sPos );
	} /* if */
	return $buff;
} /* _wp_get_first_entry() */

function _get_first_image_tag( $request ) {
	$buff = $request;
	$ret = FALSE;
	if( $buff ) {
			$sPos = strpos( $buff, '<img' );
			if( $sPos <> FALSE ) {
				$ePos = strpos( $buff, '/>', $sPos ) + 2;
				$ret = substr( $buff, $sPos, $ePos - $sPos );
			} /* if */
	} /* if */
	return $ret;
} /* _get_first_image_tag() */


		$options = get_option('widget_single_photo');
		$title = $options['single_photo_title'];
		$srcurl = $options['single_photo_src_url'];
		$linkURL = $options['single_photo_lnk_url'];
//		$blc = $options['single_photo_src_blc'];
$blc = '"';
		$targetwidth =  $options['single_photo_dst_width'];
		$hasImgFrame = $options['single_photo_dst_hasframe'];

		$output = '<div id="widget_single_photo"><ul>';

		// section main logic from here 

	$r = _wp_get_first_entry( $srcurl );
	$buff =  _get_first_image_tag( $r );

	if( $buff <> FALSE ) {	// if image tag found
		$removeItems = array( 'width', 'height' );
		$imgInfo = array();

		foreach( $removeItems as $rmv) {
			$ePos = strpos( $buff, $rmv );
			if( $ePos <> FALSE ) {
				$f = substr( $buff, 0, $ePos );
				$e = substr( $buff, $ePos );
				$ePos = strpos( $e, $blc );
				$e = substr( $e, $ePos + 1 );
				$ePos = strpos( $e, $blc );
				$imgInfo[] = substr ( $e, 0, $ePos );
				$e = substr( $e, $ePos + 1 );
				$buff = $f. $e;
			} /* if */
		} /* foreach */

		$isVertical = $imgInfo[1] > $imgInfo[0];
		$buff = substr( $buff,  0, -2 );
		$theOff = $hasImgFrame ? 10 : 0;

		$ret = '<div id="photof" style="position:relative; width:' . $targetwidth . 'px; height:' . $targetwidth . 'px; " >';

		if( $isVertical ) {
			$theTop = $theOff;
			$theLeft = floor( ( $targetwidth - ( $targetwidth - $theTop * 2 ) * $imgInfo[0] / $imgInfo[1] ) / 2  );
			$buff .= 'height="'. ( $targetwidth - ($theOff * 2))  . '" style="position: absolute; left:' . $theLeft . 'px; top: ' . $theTop . 'px;" />';
			if( $hasImgFrame ) $ret .= '<img src="'.get_option('siteurl').'/wp-content/plugins/single-photo/imageback_v.jpg" width="' . $targetwidth . '" style="position: absolute; left:0; top: 0;" >';
		} else {
			$theLeft = $theOff;
			$theTop = floor( ( $targetwidth - ( $targetwidth - $theLeft * 2 ) * $imgInfo[1] / $imgInfo[0] ) / 2  );
			$buff .= 'width="'. ( $targetwidth - ($theOff * 2))  . '" style="position: absolute; left:' . $theLeft . 'px; top: ' . $theTop . 'px; " />';
			if( $hasImgFrame ) $ret .= '<img src="'.get_option('siteurl').'/wp-content/plugins/single-photo/imageback_h.jpg" width="' . $targetwidth . '" style="position: absolute; left:0; top: 0;" >';
		} /* if else */
		$buff = '<a href="' . $linkURL . '" target="_blank">' . $buff . '</a>';

		$ret = $ret . $buff . '</div>';
	} else {
		$ret = '<a href="' . $linkURL . '" target="_blank"><img src="'.get_option('siteurl').'/wp-content/plugins/single-photo/imageback_h.jpg" width="' . $targetwidth . '" style="" ></a>';
	} /* if else */

		// These lines generate the output

		$output .= $ret;
		$output .= '</ul></div>';
		echo $before_widget . $before_title . $title . $after_title;
		echo $output;
		echo $after_widget;
	} /* widget_single_photo() */

	function widget_single_photo_control() {
		$options = $newoptions = get_option('widget_single_photo');
		if ( $_POST["single_photo_src_submit"] ) {
			$newoptions['single_photo_title'] = strip_tags(stripslashes($_POST["single_photo_title"]));
			$newoptions['single_photo_src_url'] = strip_tags(stripslashes($_POST["single_photo_src_url"]));
//			$newoptions['single_photo_src_blc'] = strip_tags(stripslashes($_POST["single_photo_src_blc"]));
			$newoptions['single_photo_lnk_url'] = strip_tags(stripslashes($_POST["single_photo_lnk_url"]));
			$newoptions['single_photo_dst_width'] = (int) $_POST["single_photo_dst_width"];
			$newoptions['single_photo_dst_hasframe'] = (int) $_POST["single_photo_dst_hasframe"];
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_single_photo', $options);
		}

		// those are default value
//		if ( !$options['single_photo_src_blc'] ) $options['single_photo_src_blc'] = '"';
		if ( !$options['single_photo_dst_width'] ) $options['single_photo_dst_width'] = 190;
//		if ( !$options['single_photo_dst_hasframe'] ) $options['single_photo_dst_hasframe'] = 1;

		$single_photo_dst_width = $options['single_photo_dst_width'];
		$single_photo_dst_hasframe = $options['single_photo_dst_hasframe'];
		$single_photo_lnk_url = htmlspecialchars($options['single_photo_lnk_url'], ENT_QUOTES);
		$single_photo_src_blc = htmlspecialchars($options['single_photo_src_blc'], ENT_QUOTES);

		$title = htmlspecialchars($options['single_photo_title'], ENT_QUOTES);
		$srcurl = htmlspecialchars($options['single_photo_src_url'], ENT_QUOTES);
?>

	    <?php _e('Title:'); ?> <input style="width: 170px;" id="single_photo_title" name="single_photo_title" type="text" value="<?php echo $title; ?>" /><br />
		<?php _e('Source:'); ?> <input style="width: 170px;" id="single_photo_src_url" name="single_photo_src_url" type="text" value="<?php echo $srcurl; ?>" /><br />
        <?php _e('Width:'); ?> <input style="width: 75px;" id="single_photo_dst_width" name="single_photo_dst_width" type="text" value="<?php echo $single_photo_dst_width; ?>" /><br />

        <?php _e('Frame:'); ?> <input id="single_photo_dst_hasframe" name="single_photo_dst_hasframe" type="radio" value="1" <?php if( $single_photo_dst_hasframe == 1) { echo "checked";}  ?>/>use frame 
		<input id="single_photo_dst_hasframe" name="single_photo_dst_hasframe" type="radio" value="0" <?php if( $single_photo_dst_hasframe == 0) { echo "checked";}  ?>/>no frame (use CSS)<br />

		<?php _e('Link:'); ?> <input style="width: 170px;" id="single_photo_lnk_url" name="single_photo_lnk_url" type="text" value="<?php echo $single_photo_lnk_url; ?>" /><br />
  	    <input type="hidden" id="single_photo_src_submit" name="single_photo_src_submit" value="1" />

<?php
	} /* widget_single_photo_control() */

	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	register_sidebar_widget('Single Photo', 'widget_single_photo');
	register_widget_control('Single Photo', 'widget_single_photo_control' );
} /* widget_single_photo_init() */

// Run our code later in case this loads prior to any required plugins.
add_action('plugins_loaded', 'widget_single_photo_init');

?>