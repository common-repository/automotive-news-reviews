<?php
/*
Plugin Name: Automotive News
Plugin URI: http://2levelsabove.com/wordpress-automotive-news-plug-in/
Description: Automotive news widget that displays the latest news & reviews from <a href="http://www.alltherides.com/">www.alltherides.com</a> covering the latest in automobiles. Great for automotive related blogs & sites.
Version: 0.5
Author: Scott Faisal
Author URI: http://alltherides.com/
*/

/*	
	This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

	Copyright 2008, 2 Levels Above LLC.
	
*/

/*
	Changes
	- 0.1, initial version
	- 0.2.1, removed empty <p> if no title is present, updated FAQ
	- 0.3, added ability to choose badge emblem
	- 0.4, three more badges images
	- 0.5, fixed image URL
*/

$pics=array(
"No Image"=>"none",
"Thin ATR Banner 150*21"=>"http://alltherides.com/images/badges/alltherides-com.png",
"Wide ATR logo 113*90"=>"http://alltherides.com/images/badges/logo2.gif",
"Sexy Car 1 300*98"=>"http://alltherides.com/images/badges/sexy_car1.jpg",
"Sexy Car 2 300*151"=>"http://alltherides.com/images/badges/sexy_car2.jpg"



);

function automotive_news_widget_getRSS($url, $numitems = '5', $before='<li class="automotive-news">', $after='</li>') {
	if(!is_null($url)) {
		require_once(ABSPATH. "wp-includes/rss-functions.php");
		$rss = fetch_rss($url);
		if($rss) {
			foreach(array_slice($rss->items,0,$numitems) as $item) {
				echo "$before<a title=\"".$item['title']."\" href=\"".$item['link']."\">".$item['title']."</a>$after";
			}
		} else {
			echo "There was an error processing the Automotive News feed. Please check your sidebar widget configuration.";
		}
	} else {
		echo "An error occured! No RSS url was specified. Please check your sidebar widget configuration.";
	}
}

function automotive_news_widget_activate() {
	$default_options = array(
		'title'=>'Automotive News',
		'rssfeed'=>'http://alltherides.com/articles-news/feed/',
		'badge'=>'http://alltherides.com/images/badges/alltherides-com.png',
		'numitems'=>'10',
		'beforebadge' => '',
		'afterbadge' => ''
	);
	$options = get_option('automotive_news_widget');
	
	// set default options
	if (!is_array($options)) {
		update_option('automotive_news_widget', $default_options);
	} else {
		foreach ($options as $i => $value) {
			$default_options[$i] = $options[$i];
		}
		update_option('automotive_news_widget', $default_options);
	}
}

function automotive_news_widget_init() {
	if (!function_exists('register_sidebar_widget')) {
		return;
	}
	
	function automotive_news_widget($args) {
		// extract options
		extract($args);
		$options = get_option('automotive_news_widget');
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$rssfeed = htmlspecialchars_decode($options['rssfeed']);
		$badge = htmlspecialchars_decode($options['badge']);
		$numitems = htmlspecialchars_decode($options['numitems'], ENT_QUOTES);
		$beforebadge = htmlspecialchars_decode($options['beforebadge'], ENT_QUOTES);
		$afterbadge = htmlspecialchars_decode($options['afterbadge'], ENT_QUOTES);
		
		// print widget
		echo $beforebadge;
		?>
		<div class="automotive-news-widget">
        
        <?php if ($badge!="none"){?>
        
		<p class='automotive-news-badge'><a target="_blank" href="http://alltherides.com" title="used cars for sale"><img src="<?php echo $badge; ?>" alt="Free Car Classifieds" title="Free Car Classifieds" border="0"  /></a></p>
        
        <?php  } ?>
        
		<?php if($title != '') :?>
		<p class='automotive-news-feed-title'><?php echo $title; ?></p>
		<?php endif; ?>
		<ul class="automotive-news-feed">
		<?php automotive_news_widget_getRSS($rssfeed,$numitems); ?>
		</ul>
		</div>
		<?php
		echo $afterbadge;
	}
	
	function automotive_news_widget_control() {
		$options = get_option('automotive_news_widget');
			
		if ( $_POST['automotive_news_widget-submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['automotive_news_widget-title']));
			$options['badge'] = stripslashes($_POST['automotive_news_widget-pic-url']);
			$options['numitems'] = stripslashes($_POST['automotive_news_widget-rss-items']);
			$options['beforebadge'] = stripslashes($_POST['automotive_news_widget-beforebadge']);
			$options['afterbadge'] = stripslashes($_POST['automotive_news_widget-afterbadge']);
			update_option('automotive_news_widget', $options);
		}
		
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$rssfeed = htmlspecialchars($options['rssfeed'], ENT_QUOTES);
		$badge = htmlspecialchars_decode($options['badge']);
		$numitems = htmlspecialchars($options['numitems'], ENT_QUOTES);
		$beforebadge = htmlspecialchars($options['beforebadge'], ENT_QUOTES);
		$afterbadge = htmlspecialchars($options['afterbadge'], ENT_QUOTES);
		
?>
<p style="text-align:left;">
<label for="automotive_news_widget-title"><?php echo __('Automotive News RSS Feed Title:'); ?></label>
<input style="width: 200px;" id="automotive_news_widget-title" name="automotive_news_widget-title" type="text" value="<?php echo $title; ?>" />
<br/><label for="automotive_news_widget-pic-url"><?php _e('Choose a badge (<a href="http://www.alltherides.com/images/badges/"  target="_blank">see all</a>):'); ?></label>
<select id="automotive_news_widget-pic-url" name="automotive_news_widget-pic-url">
<?php 
	global $pics;
	foreach($pics as $picname=>$picurl) {
		echo '<option value="'.$picurl.'" '. ( $picurl == $badge ? "selected='selected'" : '' ) .' >'.$picname.'</option>';
	}
?>
</select>
<br/>
<br/><label for="automotive_news_widget-rss-items"><?php _e('How many items would you like to display?'); ?></label>
<select id="automotive_news_widget-rss-items" name="automotive_news_widget-rss-items">
<?php
	for ( $i = 1; $i <= 20; ++$i ) {
		echo "<option value='$i' " . ( $numitems == $i ? "selected='selected'" : '' ) . ">$i</option>";
	}
?>
</select>
<br/><label for="automotive_news_widget-beforebadge"><?php echo __('Optional HTML/text before widget:'); ?></label>
<input style="width: 200px;" id="automotive_news_widget-beforebadge" name="automotive_news_widget-beforebadge" type="text" value="<?php echo $beforebadge; ?>" />
<br/><label for="automotive_news_widget-afterbadge"><?php echo __('Optional HTML/text after widget:'); ?></label>
<input style="width: 200px;" id="automotive_news_widget-afterbadge" name="automotive_news_widget-afterbadge" type="text" value="<?php echo $afterbadge; ?>" />
</p>
<input type="hidden" id="automotive_news_widget-submit" name="automotive_news_widget-submit" value="1" />
<?php
	}
	register_sidebar_widget(array('Automotive News', 'widgets'), 'automotive_news_widget');
	register_widget_control(array('Automotive News', 'widgets'), 'automotive_news_widget_control');
}

function automotive_news_widget_deactivate() {
	delete_option('automotive_news_widget');
}

// activation
register_activation_hook(__FILE__, 'automotive_news_widget_activate');
// initialization
add_action('plugins_loaded', 'automotive_news_widget_init');
// deactivation
register_deactivation_hook( __FILE__, 'automotive_news_widget_deactivate' );
?>