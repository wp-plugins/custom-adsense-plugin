<?php
/*
Plugin Name: Custom Adsense Plugin
Plugin URI: http://www.google.com
Description: Plugin creates inactive post and then activates it on the page load of wordpress (any page).
Version: 1.2
Author: Umakant Patil
Contributers: Ravi Penna
Author URI: http://www.google.com
*/

$thisisatest = null;
$thisisanothertest = null;
$thisisthethirdtest = null;

function gm_injectjs() {
	//global $adurl;
	$ptemplate =  get_option('expertpost_options_posthtml');	
	$pouter = get_option('expertpost_options_classname');
	
	// noentries found
	if($ptemplate === false || $pouter === false){
		return false;
	}
	// get default user
	$user_info = get_userdata(1);	

	// Checking if we are on front page or on home page.
	if ( is_front_page() ||  is_home() ){
		$adurl = get_option('expertpost_options_homeposturl');
		if(strlen($adurl) <= 0  || $adurl === false){
			return false;
		}	
		// Put template, username and class name inot page DOM.
		echo '<script type="text/javascript">';
		echo 'var ptype = "home";';
		echo 'var post_username = "'.$user_info->user_login.'";   ';
		echo 'var ptemplate = \''.$ptemplate.'\';   ';
		echo 'var pouter = \''.$pouter.'\';  ';
		echo '</script>';
		
		// Call creative in the hidden div
		echo '<div id="gm_hiddenpost" style="display:none;"><script type="text/javascript" src="'.$adurl.'">';
		echo '</script></div>';
		
		// Load JS file which is loacated in plugin directory.
		$plugin_dir = '/wp-content/plugins';
		if ( defined( 'PLUGINDIR' ) ) {
			$plugin_dir = '/' . PLUGINDIR;
		}
		echo '<script src="'.get_option('siteurl').$plugin_dir.'/wp-glam/expertplugin.js"></script>';

	} else if(is_single()){
		// If the post/article is single
		$adurl = get_option('expertpost_options_singleposturl');
		if(strlen($adurl) <= 0 || $adurl === false){
			return false;
		}
		// Put template, username and class name inot page DOM.
		echo '<script type="text/javascript">';
		echo 'var ptype = "single";';
		echo 'var post_username = "'.$user_info->user_login.'";   ';
		echo 'var ptemplate = \''.$ptemplate.'\';   ';
		echo 'var pouter = \''.$pouter.'\';  ';
		echo '</script>';
		
		// Call creative in the hidden div
		echo '<div id="gm_hiddenpost" style="display:none;"><script type="text/javascript" src="'.$adurl.'">';
		echo '</script></div>';
		
		// Load JS file which is loacated in plugin directory.
		$plugin_dir = '/wp-content/plugins';
		if ( defined( 'PLUGINDIR' ) ) {
			$plugin_dir = '/' . PLUGINDIR;
		}
		echo '<script src="'.get_option('siteurl').$plugin_dir.'/wp-glam/expertplugin.js"></script>';
		
	}
}
/**
  Call gm_injectjs function when footer is loaded.
 */
add_action( 'wp_footer', 'gm_injectjs', 4, 1);


/** 
  admin settings start here..
 */

function expertpost_setting_classname() {
	$options = get_option('expertpost_options_classname');
	echo "<input type='text' id='expertpost_postclassname' name='expertpost_options_classname' style='width:400px;' value='{$options}' />";
} 
function expertpost_setting_posthtml() {
	$options = get_option('expertpost_options_posthtml');
	echo "<textarea style='width:400px;height:100px' id='expertpost_posthtml'  name='expertpost_options_posthtml'>{$options}</textarea>";
} 
function expertpost_setting_homeposturl(){
	$options = get_option('expertpost_options_homeposturl');
	echo "<input type='text' style='width:400px; id='expertpost_homeposturl'  name='expertpost_options_homeposturl' value='{$options}' />";
}
function expertpost_setting_singleposturl(){
	$options = get_option('expertpost_options_singleposturl');
	echo "<input type='text'  style='width:400px; id='expertpost_singleposturl'  name='expertpost_options_singleposturl' value='{$options}' />";	
}
 // validate our options
function expertpost_options_validate($input) {
	$input = str_replace("\n", "", $input);
	$input = str_replace("\r", "", $input);
	return trim($input);
	/*
	$newinput['text_string_1'] = trim($input['text_string_1']);
	if(!preg_match('/^[a-z0-9]{32}$/i', $newinput['text_string_1'])) {
		$newinput['text_string_1'] = '';
	}
	return $newinput;
	*/
}

function plugin_section_text() {
	echo '<p>Here you can change the HTML of the Expert post and Class name(s) assigned to the div.</p>';
} 
function plugin_section2_text(){
	echo '<p>Leave the field blank if you don\'t want to show the post on respective places</p>';
}
function expertpost_admin_init(){
	register_setting( 'expertpost_options', 'expertpost_options_classname', 'expertpost_options_validate' );
	register_setting( 'expertpost_options', 'expertpost_options_posthtml', 'expertpost_options_validate' );
	register_setting( 'expertpost_options', 'expertpost_options_homeposturl', 'expertpost_options_validate' );
	register_setting( 'expertpost_options', 'expertpost_options_singleposturl', 'expertpost_options_validate' );


	add_settings_section('plugin_main', 'Main Settings', 'plugin_section_text', 'expertpost');
	add_settings_field('expertpost_postclassname', 'Post class names', 'expertpost_setting_classname', 'expertpost', 'plugin_main');
	add_settings_field('expertpost_posthtml', 'HTML Code for post', 'expertpost_setting_posthtml', 'expertpost', 'plugin_main');

	add_settings_section('plugin_url', 'Creative URL', 'plugin_section2_text', 'expertpost');
	add_settings_field('expertpost_homeposturl', 'Home Page Post URL', 'expertpost_setting_homeposturl', 'expertpost', 'plugin_url');
	add_settings_field('expertpost_singleposturl', 'Single Page Post URL', 'expertpost_setting_singleposturl', 'expertpost', 'plugin_url');
}

function expertpost_options_page() {
	?>
	<div>
	<h2>Expert Post settings</h2>
	<form action="options.php" method="post">
	<?php settings_fields('expertpost_options'); ?>
	<?php do_settings_sections('expertpost'); ?>
	<br />
	<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
	</form></div>
	<br />
	<br />
	<div>
	<h2>How it works?</h2>
	<p>
	<ol>
	<li><strong>Post class names</strong>:<br />
	It is the one or more classes for the outer <i>div</i> of the post inside which title, post content is rendered.
	</li>
	<li><strong>HTML Code for post</strong>:<br />
	Its the HTML(DOM) structure the way Expert post is rendered. Default is: <br /><br />
	<pre>
&lt;h2 class="entry-title"&gt;
	&lt;a id="gm_title"&gt;&lt;/a&gt;
&lt;/h2&gt;
&lt;div class="entry-meta"&gt;
	&lt;span class="meta-prep meta-prep-author"&gt;Posted on&lt;/span&gt;
	&lt;a&gt;&lt;span class="entry-date" style="text-decoration:underline;cursor:pointer;"&gt;August 27, 2010&lt;/span&gt;&lt;/a&gt;
	&lt;span class="meta-sep"&gt;by&lt;/span&gt;
	&lt;span class="author vcard"&gt;&lt;a class="url fn n" style="text-decoration:underline;cursor:pointer;" id="post_username"&gt;&lt;/a&gt;&lt;/span&gt;
&lt;/div&gt;
&lt;div class="entry-content" id="gm_desc"&gt;
&lt;/div&gt;
	</pre>
	<br />
	If you look at the code you will see there are some ID's are given to few tags, which helps in injecting the expert post content into it.<br />
	If you change the classes or styling or If you change HTML structure remember to put the back those ID's at there respective places.
	</li>
	<li><strong>Home Page Post URL</strong><br />
	Its the url using which ad creative is called into the home/font page. If you dnt want to show Expert post on home/front page then remove the URL i.e. keep that field blank.
	</li>
	<li><strong>Single Page Post URL</strong><br />
	Its the url using which ad creative is called into the single post(article) page above, comments section. If you dnt want to show Expert post on that page then remove the URL (i.e. keep that field blank).
	</li>
	</ol>
	</p>
	</div>
	<?php
}

function add_glam_settings() {
	add_options_page('Glam: Expert Post', 'Expert Post', 'manage_options', 'expertpost', 'expertpost_options_page');
}


add_action('admin_init', 'expertpost_admin_init');
add_action('admin_menu', 'add_glam_settings');


/**
	Actions to be done when plugin is activated and deactivated.
 **/
 function glam_start_expert(){
 	$options1 = get_option('expertpost_options_classname');
	$options2 = get_option('expertpost_options_posthtml');
	$options3 = get_option('expertpost_options_homeposturl');
	$options4 = get_option('expertpost_options_singleposturl');
	if($options1 === false){
		add_option("expertpost_options_classname", 'post-1000000 type-normal hentry category-uncategorized', '', 'yes'); 
	}
	if($options2 === false){
		add_option("expertpost_options_posthtml", '<h2 class="entry-title"><a id="gm_title"></a></h2> <div class="entry-meta"><span class="meta-prep meta-prep-author">Posted on</span> <a><span class="entry-date" style="text-decoration:underline;cursor:pointer;">August 27, 2010</span></a> <span class="meta-sep">by</span> <span class="author vcard"><a class="url fn n" style="text-decoration:underline;cursor:pointer;" id="post_username"></a></span></div><div class="entry-content" id="gm_desc"></div>', '', 'yes'); 
	}
	if($options3 === false){
		add_option("expertpost_options_homeposturl", 'http://www35.glam.com/gad/glamadapt_jsrv.act?;flg=73;;zone=/;nt=g;cc=us;ec=ron;p=0;p=1;al=042690;al=044787g;al=044839;al=044872;al=324022381;al=attp;cl=042689;cl=046456;ec=tb;ia=s;kv=glamron;kv=style;pec=b;vec=st;vpec=st;atf=u;pfl=0;dt=b;!c=hagl;!c=hagn;pl=h;pt=0;afid=318973508;dsid=618808;ep=yes;adtype=expert;proddeploy=test;;tt=i;u=b022116bgep1dtezs56,f0fu2sa,g100021;sz=300x250;tile=1;ord=3310524812862980.5;;afid=318973508;dsid=618808;url=rziq3d;seq=1;ux=f-fu2sa,tid-1,pid-16bgep1dtezs56,aid-2,i-1,g-73,1,;_glt=-330:4:16:10:40:313:2010:8:26;a_tz=330;_g_cv=2;http://www35.glam.com/gad/glamadapt_jsrv.act?;flg=73;;zone=/;nt=g;cc=us;ec=ron;p=0;p=1;al=042690;al=044787g;al=044839;al=044872;al=324022381;al=attp;cl=042689;cl=046456;ec=tb;ia=s;kv=glamron;kv=style;pec=b;vec=st;vpec=st;atf=u;pfl=0;dt=b;!c=hagl;!c=hagn;pl=h;pt=0;afid=318973508;dsid=618808;ep=yes;proddeploy=test;;tt=i;u=b022116bgep1dtezs56,f0fu2sa,g100021;sz=300x250;tile=1;ord=3310524812862980.5;;afid=318973508;dsid=618808;url=rziq3d;seq=1;ux=f-fu2sa,tid-1,pid-16bgep1dtezs56,aid-2,i-1,g-73,1,;_glt=-330:4:16:10:40:313:2010:8:26;a_tz=330;_g_cv=2;', '', 'yes'); 
	}
	if($options4 === false){
		add_option("expertpost_options_singleposturl", 'http://www35.glam.com/gad/glamadapt_jsrv.act?;flg=73;;zone=/;nt=g;cc=us;ec=ron;p=0;p=1;al=042690;al=044787g;al=044839;al=044872;al=324022381;al=attp;cl=042689;cl=046456;ec=tb;ia=s;kv=glamron;kv=style;pec=b;vec=st;vpec=st;atf=u;pfl=0;dt=b;!c=hagl;!c=hagn;pl=h;pt=0;afid=318973508;dsid=618808;ep=yes;adtype=expert;proddeploy=test;;tt=i;u=b022116bgep1dtezs56,f0fu2sa,g100021;sz=300x250;tile=1;ord=3310524812862980.5;;afid=318973508;dsid=618808;url=rziq3d;seq=1;ux=f-fu2sa,tid-1,pid-16bgep1dtezs56,aid-2,i-1,g-73,1,;_glt=-330:4:16:10:40:313:2010:8:26;a_tz=330;_g_cv=2;http://www35.glam.com/gad/glamadapt_jsrv.act?;flg=73;;zone=/;nt=g;cc=us;ec=ron;p=0;p=1;al=042690;al=044787g;al=044839;al=044872;al=324022381;al=attp;cl=042689;cl=046456;ec=tb;ia=s;kv=glamron;kv=style;pec=b;vec=st;vpec=st;atf=u;pfl=0;dt=b;!c=hagl;!c=hagn;pl=h;pt=0;afid=318973508;dsid=618808;ep=yes;proddeploy=test;;tt=i;u=b022116bgep1dtezs56,f0fu2sa,g100021;sz=300x250;tile=1;ord=3310524812862980.5;;afid=318973508;dsid=618808;url=rziq3d;seq=1;ux=f-fu2sa,tid-1,pid-16bgep1dtezs56,aid-2,i-1,g-73,1,;_glt=-330:4:16:10:40:313:2010:8:26;a_tz=330;_g_cv=2;', '', 'yes'); 
	}
 }
 //
function glam_stop_expert(){
 	$options1 = get_option('expertpost_options_classname');
	$options2 = get_option('expertpost_options_posthtml');
	$options3 = get_option('expertpost_options_homeposturl');
	$options4 = get_option('expertpost_options_singleposturl');
	if($options1 !== false){
		delete_option("expertpost_options_classname"); 
	}
	if($options2 !== false){
		delete_option("expertpost_options_posthtml"); 
	}
	if($options3 !== false){
		delete_option("expertpost_options_homeposturl"); 
	}
	if($options3 !== false){
		delete_option("expertpost_options_singleposturl"); 
	}
 }
register_activation_hook(__FILE__, 'glam_start_expert'); 
register_deactivation_hook(__FILE__, 'glam_stop_expert'); 

?>