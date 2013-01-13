<?php
/*
Plugin Name: Tweet My Post
Plugin URI: http://ksg91.com/tweet-my-post/
Description: A WordPress Plugin which Tweets the new post with its title, link, Auther's twitter handle and a featured image from post.  
Version: 1.7.32
Author: Kishan Gor
Author URI: http://ksg91.com
License: GPL2

/////////////////////////////////////////////////////////////////////
    Copyright 2012  Kishan Gor  (email : ego@ksg91.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
//////////////////////////////////////////////////////////////////////
*/
add_action('admin_menu','add_tmp_page');
add_action('admin_init','reg_settings');
add_action('publish_post','tmp_ckeck_post');
add_action('publish_page','tmp_ckeck_post');
add_action('add_meta_boxes', 'tmp_metabox' );
add_action('admin_enqueue_scripts', 'tmp_head_resource');
add_action('publish_future_post','tmp_future_post');

//adds css and jquery plugin
function tmp_head_resource() {
  wp_register_style( 'tmp-style', plugin_dir_url( __FILE__ )."/tmp.css" );
  wp_enqueue_style( 'tmp-style' );
}

//adds tmp_metabox in New Post and Page page.
function tmp_metabox() {
    add_meta_box( 
        'tmp_metabox',
        'Tweet My Post',
        'tmp_metabox_html',
        'post',
        'side',
        'high' 
    );
    add_meta_box( 
        'tmp_metabox',
        'Tweet My Post',
        'tmp_metabox_html',
        'page',
        'side',
        'high'
    );
}

//HTML code for TMP metabox Code
function tmp_metabox_html($post_id) {
  $postStatus=get_post_status($post_id);
  // checkbox for meta
  echo '<div id="tmp-preview"></div>';
  echo '<span class="tmpit"><input type="checkbox" name="tmpChkbox"'.( 
    ($postStatus=="publish")?'':' checked="checked" ').'value="1" id="tmpChkbox" />
    <label for="tmpChkbox" style="font-size:large;">&nbsp; &nbsp; Tweet This Post?</label></span>';
  echo '<br /><br /><span class="tmpit"><input type="checkbox" name="tmpShrtlnk" checked="checked" value="1" id="tmpShrtlnk" />
    <label for="tmpShrtlnk" style="font-size:large;">&nbsp; &nbsp; Use Shortlink?</label></span>';
  echo '<br /><br /><span class="tmpit"><input type="checkbox" name="useFtrImg" checked="checked" value="1" id="useFtrImg" />
        <label for="useFtrImg" style="font-size:large;">&nbsp; &nbsp; Use Featured Image?</label></span><br />';
  echo '<div id="ftrImgSec">';
  echo '<img id="ftrImg" src="'.plugin_dir_url( __FILE__ ).'bird.png" height=100 width=100 />';
  echo '<img src="'.plugin_dir_url( __FILE__ ).'prev.png" id="tmpPrev" title="Previous Image" /> 
        <img src="'.plugin_dir_url( __FILE__ ).'next.png" id="tmpNext" title="Next Image" />
        <img src="'.plugin_dir_url( __FILE__ ).'refresh.png" id="tmpRefresh" title="Refetch New Images" />
        <br /> &nbsp; <span id="imgInfo"></span>';
  echo '</div>';
  echo '<input type="hidden" name="imgLnk" value="'.plugin_dir_url( __FILE__ ).'bird.png" id="hidFld" />';
  
  //js for Div
  echo '<script type="text/javascript">
		$=jQuery.noConflict();
      var imgs=new Array("'.plugin_dir_url( __FILE__ ).'bird.png");
      var count=1,curPos=0;
      $(document).ready(function(){
        getImages();
		$("#tmp-preview").hide();
		if($("#useFtrImg").attr("checked")!="checked")
          $("#ftrImgSec").hide("slow");
        $("#title").live("keyup",function(){
			$("#tmp-preview").show();
          $("#tmp-preview").html(getTweetPreview());
        });
        $("#tmpRefresh").click(function(e){
          $("#tmpRefresh").attr("src","'.plugin_dir_url( __FILE__ ).'loading.gif");
          getImages();
          $("#tmp-preview").html(getTweetPreview);
          e.preventDefault();
        });
        $("#tmpNext").click(function(e){
          nextPrevImg("next");
          updateImgInfo();
          $("#tmp-preview").html(getTweetPreview);
          e.preventDefault();
        });
        $("#tmpPrev").click(function(e){
          nextPrevImg("prev");
          updateImgInfo();
          $("#tmp-preview").html(getTweetPreview);
          e.preventDefault();
        });
        $("#useFtrImg").live("change",function(e){
          $("#ftrImgSec").toggle("slow");
          $("#tmp-preview").html(getTweetPreview);
        });
      });
      ';
      ?>$(document).ready(function(){
            var shortLink = '<?php echo get_option("useShortLinkOpt"); ?>';
            var ftrdImg = '<?php echo get_option("useFtrdImgOpt"); ?>';
            //alert(shortLink);
            if(shortLink == "yes"){
              $('#tmpShrtlnk').attr("checked","checked");
            }
            else if(shortLink == "no"){
                
              $('#tmpShrtlnk').attr("checked",false);
            }
            else{
                $('#tmpShrtlnk').attr("checked","checked");
            }
            if(ftrdImg == "yes"){
              $('#useFtrImg').attr("checked","checked");
            }
            else if(ftrdImg == "no"){
                
              $('#useFtrImg').attr("checked",false);
            }
            else{
                
                $('#useFtrImg').attr("checked","checked");
            }       
            $('#tmpShrtlnk').change();
            $('#useFtrImg').change();
          });
      <?php
  echo 'function getTweetPreview()
        {
          var format="'.getTweetFormat().'";
          var title=$("#title").val();
          var link="'.get_permalink($post_id).'";
          var preview=format.replace("[t]",title);
          preview=preview.replace("[l]",link);
          if($("#useFtrImg").attr("checked")=="checked")
            preview=preview+" "+($("#hidFld").attr("value"));
          return preview; 
        }
        </script>';
  echo '<script type="text/javascript">
        function getImages()
        {
          var preUrl=$("#post-preview").attr("href");
          $.get(preUrl, function(data) {
            var m=data.match(/https?:\/\/([a-zA-Z0-9\.\/\-\_\%\&\=])*\.(jpg|png|gif|jpeg)/gi);
            imgs=$.unique(m);
            count=imgs.push("'.plugin_dir_url( __FILE__ ).'bird.png");
			curPos=0;
            $("#ftrImg").attr("src",m[0]);
            $("#hidFld").attr("value",m[0]);
            $("#tmpRefresh").attr("src","'.plugin_dir_url( __FILE__ ).'refresh.png");
            updateImgInfo();
          });
        }
        function nextPrevImg(action)
        {
          if(action=="next")
          {
            if(curPos==(count-1))
              curPos=0;
            else
              curPos++;
            $("#ftrImg").attr("src",imgs[curPos]);
          }
          else
          {
            if(curPos==0)
              curPos=count-1;
            else
              curPos--;
            $("#ftrImg").attr("src",imgs[curPos]);
          }
          $("#hidFld").attr("value",imgs[curPos]);
        }
        function updateImgInfo()
        {
          $("#hidFld").attr("value",imgs[curPos]);
          $("#imgInfo").html("Images "+(curPos+1)+"/"+count);
        }
        </script>';
}

//Build Pattern for JS
function getTweetFormat()
{
  global $current_user;
  get_currentuserinfo();
  if(get_option("custom-mode")==1)
    $format=get_option("custom-format");
  else
    $format='\"[t]\" - [l] [o]by[/o] [h]';
  if(get_option("ID-".$current_user->ID)==NULL){
     $format=str_replace("[h]","",$format);
     $format=preg_replace("`(\[o\])(.*)(\[/o\])`","", $format);
  }
  else {
    $format=str_replace("[h]",'@'.get_option("ID-".$current_user->ID),$format);
    $format=str_replace("[o]","",$format);
    $format=str_replace("[/o]","",$format);
  }
  
  return $format;
}

//Checks if post is to be tweeted  
function tmp_ckeck_post( $postID ) {
  // Check permissions
  if ( 'page' == $_POST['post_type'] ) 
  {
    if ( !current_user_can( 'edit_page', $post_id ) )
        return $postID;
  }
  else
  {
    if ( !current_user_can( 'edit_post', $post_id ) )
        return $postID;
  }
  $tmpit=$_POST['tmpChkbox'];
  $tmpShrtlnk=$_POST['tmpShrtlnk'];
  $useFtrImg=$_POST['useFtrImg'];
  $imgLnk=$_POST['imgLnk'];
  //tweet if checkbox selected
  if($tmpit==1)
  {
    if($useFtrImg==1){
      tmp_tweet_it($postID,$tmpShrtlnk,$imgLnk);
      
    }
    else
      tmp_tweet_it($postID,$tmpShrtlnk);
  }
  return $postID;

}

// Function to be called for future/schedual post
function tmp_future_post( $post_id )
{
  tmp_tweet_it($post_id,1);
}


//Function for activation hook
function tmp_activate()
{
  add_option("twitter-consumer-key","");
  add_option("twitter-consumer-secret","");
  add_option("twitter-access-token","");
  add_option("twitter-access-secret","");
  add_option("debug-mode","0");
  add_option("debug-data","");
  add_option("custom-mode",0);
  add_option("custom-format","'[t]'[o]- by[/o] [h] - [l]");
  add_option("useFtrdImgOpt","yes");
  add_option("useShortLinkOpt","yes");
}

//Sends Post to Twitter
function tmp_tweet_it($postID,$tmpShrtlnk,$imgLnk=null)
{
  require_once 'lib/EpiCurl.php';
  require_once 'lib/EpiOAuth.php';
  require_once 'lib/EpiTwitter.php';
  $twitterObj = new EpiTwitter(get_option("twitter-consumer-key"), 
    get_option("twitter-consumer-secret"),get_option("twitter-access-token"),
    get_option("twitter-access-secret"));
  $tweet=buildTMPTweet($postID,$tmpShrtlnk,$imgLnk);
  try{
    $update_status = $twitterObj->post_statusesUpdate(array('status' => $tweet ));
    $res=$update_status->response;
  }
  catch(Exception $e){
    if($e->getMessage()==''){
      $res['error']='Unknown Error. Please contact developer if you keep getting this error.';
    }
    else {
      $error=json_decode($e->getMessage(),true);
      $res['error']=$error['errors'][0]['message'];
    }
  }
  if(get_option("debug-mode")==1)
    addLog($res);
  return $postID;
}

//Logs response From Twitter
function addLog($res)
{
  $data=get_option("debug-data");
  if($data==NULL)
     $data=array();
     $subData['logtime']=date(DATE_ATOM,time());
  if(!isset($res['error']))
  {
    $subData['TEXT']=$res['text'];
    $subData['SOURCE']=$res['source'];
    $subData['CREATED_AT']=$res['created_at'];
  }
  else
    $subData['ERROR']=$res['error'];
  array_unshift($data,$subData);
  update_option("debug-data",$data);
}

//Builds Tweet to be send
function buildTMPTweet($postID,$tmpShrtlnk,$imgLnk)
{
  if(get_option("custom-mode")==1)
    return getCustomTweet($postID,$tmpShrtlnk,$imgLnk);
  $post=get_post($postID);
  $author=get_option("ID-".$post->post_author);
  $link="Hello";
  if($tmpShrtlnk==1)
    $link=wp_get_shortlink($postID);
  else
    $link=get_permalink($postID);
  $tweet=$author;
  if($author=="") {
    $title=$post->post_title;
    if($imgLnk!=null)
    {
      if(strlen($title)>93){
        $title.=substr($title,0,93);
        $title.="...";
      }
    }
    else
    {
      if(strlen($title)>114){
        $title.=substr($title,0,110);
        $title.="...";
      }
    }
    $tweet="\"".$post->post_title."\" - ".$link;
    if($imgLnk!=null)
      $tweet.=" ".$imgLnk;
  }
  else {
    $len=strlen(" by @".$author);
    $title=$post->post_title;
    if($imgLnk!=NULL){
      if(strlen($title)>(116-$len-20)){
        $title=substr($title,0,(110-$len-20));
        $title.="...";
      }
      $tweet="\"".$title."\" - ". $link." by @".$author;
    }
    else {
      if(strlen($title)>(116-$len)){
        $title=substr($title,0,(110-$len));
        $title.="...";
      }
      $tweet="\"".$title."\" - ". $link." by @".$author;
    }
    if($imgLnk!=null)
      $tweet.=" ".$imgLnk;
  }
  return $tweet;
}

//Builds Tweet according to custom format
function getCustomTweet($postID,$tmpShrtlnk,$imgLnk)
{
  $post=get_post($postID);
  $title=$post->post_title;
  $format=get_option("custom-format");
  $author=get_option("ID-".$post->post_author);
  if($tmpShrtlnk==1)
    $link=wp_get_shortlink($postID);
  else
    $link=get_permalink($postID);
  if($author!=NULL)
  {
    $tweet=str_replace("[h]","@".$author,$format);
    $tweet=str_replace("[o]","",$tweet);
    $tweet=str_replace("[/o]","",$tweet);
    $len=strlen($tweet);
    $tweet=str_replace("[l]",$link,$tweet);
    if($imgLnk!=NULL){
      if($len+37>140)
        return str_replace("[t]","",$tweet)." ".$imgLnk;
      if($len+20+strlen($title)<118)
        return str_replace("[t]",$title,$tweet)." ".$imgLnk;
      $title=substr($title,0,95-$len);
      $tweet=str_replace("[t]",$title."...",$tweet)." ".$imgLnk;
    }
    else{
      if($len+17>140)
        return str_replace("[t]","",$tweet);
      if($len+strlen($title)<118)
        return str_replace("[t]",$title,$tweet);
      $title=substr($title,0,111-$len);
      $tweet=str_replace("[t]",$title."...",$tweet);
    }
    return $tweet;
  }
  else
  {
    $tweet=str_replace("[h]","",$format);
    $tweet=preg_replace("`(\[o\])(.*)(\[/o\])`","", $tweet);
    $len=strlen($tweet);
    $tweet=str_replace("[l]",$link,$tweet);
    if($imgLnk!=NULL){
      if($len+17>140)
        return str_replace("[t]","",$tweet);
      if($len+strlen($title)<118)
        return str_replace("[t]",$title,$tweet);
      $title=substr($title,0,111-$len);
      $tweet=str_replace("[t]",$title."...",$tweet)." ".$imgLnk;
    }
    else{
      if($len+17>140)
        return str_replace("[t]","",$tweet);
      if($len+strlen($title)<118)
        return str_replace("[t]",$title,$tweet);
      $title=substr($title,0,111-$len);
      $tweet=str_replace("[t]",$title."...",$tweet);
    }
    return $tweet;
  }
}

//register settings
function reg_settings()
{
  register_setting('tmp-option', 'twitter-consumer-key');
  register_setting('tmp-option', 'twitter-consumer-secret');
  register_setting('tmp-option', 'twitter-access-token');
  register_setting('tmp-option', 'twitter-access-secret');
  register_setting('tmp-option', 'debug-mode');
  register_setting('tmp-option', 'custom-mode');
  register_setting('tmp-option', 'custom-format');
  register_setting('tmp-option', 'useFtrdImgOpt');
  register_setting('tmp-option', 'useShortLinkOpt');
}

//TMP user page code
function tmp_user_page()
{
  global $current_user;
  get_currentuserinfo();
  add_option("ID-".$current_user->ID);
  if(isset($_POST['twitter']))
    update_option("ID-".$current_user->ID,$_POST['twitter']);
  //echo get_option($current_user->user_login);
  //echo $current_user->ID;
  echo "<div class=\"wrap\">";
  echo "<h2>Tweet My Post</h2>";
  echo "<form method=\"post\" action=\"?page=tmp_user_page\">";
  settings_fields( 'tmp-option' );
  //do_settings_fields('tmp-option');
  echo "<table class=\"form-table\">";
  echo "<tr valign=\"top\"><th scope=\"row\">Your Twitter Handle</th>";
  echo "<td>@<input type=\"text\" name=\"twitter\" value=\"".get_option("ID-".$current_user->ID)."\"/></td>";
  echo "</tr>";
  echo "</table><p class=\"submit\"><input type=\"submit\" class=\"button-primary\" value=\"Save Changes\" /></p></form></div>";
}

//TMP Twitter Settings Page
function tmp_api_page()
{
  echo "<div class=\"wrap\">";
  echo "<h2> Tweet My Post - Your Twitter API Keys and Access Tokens</h2>";
  echo "<h3>Instructions</h3>Go to <a href=\"https://dev.twitter.com/apps\" target=\"_blank\">
    https://dev.twitter.com/apps</a> , Login and click on <b>Create App</b>. 
    Then fill simple details and get following details from there. Don't forget to put read+write access permission.";
  echo "<h3>Rate the Plugin</h3>Please <a href=\"http://wordpress.org/extend/plugins/tweet-my-post/\">Rate The Plugin</a> and share with your friends if you find it useful. :) ";
  echo "<h3>Support</h3>";
  echo "For quick support &nbsp; ";
  echo '<a href="https://twitter.com/ksg91" class="twitter-follow-button" data-show-count="false" data-size="large">Follow @ksg91</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
  echo " and/or ";
  echo '<a href="https://twitter.com/Tweet_My_Post" class="twitter-follow-button" data-show-count="false" data-size="large">Follow @Tweet_My_Post</a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
  echo "<br />You can email me at <a href=\"mailto:ego@ksg91.com\">ego@ksg91.com</a>";
  echo "<h3>Settings</h3>"; 
  echo "<form method=\"post\" action=\"options.php\">";
  settings_fields( 'tmp-option' );
  //do_settings_fields('tmp-option');
  echo "<table class=\"form-table\">";
  echo "<tr valign=\"top\"><th scope=\"row\">Twitter Consumer Key:</th>";
  echo "<td><input type=\"text\" name=\"twitter-consumer-key\" value=\"".get_option("twitter-consumer-key")."\"/></td>";
  echo "</tr>";
  echo "<tr valign=\"top\"><th scope=\"row\">Twitter Consumer Secret:</th>";
  echo "<td><input type=\"text\" name=\"twitter-consumer-secret\" value=\"".get_option("twitter-consumer-secret")."\"/></td>";
  echo "</tr>";
  echo "<tr valign=\"top\"><th scope=\"row\">Twitter Access Token:</th>";
  echo "<td><input type=\"text\" name=\"twitter-access-token\" value=\"".get_option("twitter-access-token")."\"/></td>";
  echo "</tr>";
  echo "<tr valign=\"top\"><th scope=\"row\">Twitter Access Token Secret:</th>";
  echo "<td><input type=\"text\" name=\"twitter-access-secret\" value=\"".get_option("twitter-access-secret")."\"/></td>";
  echo "</tr>";
  echo "<tr valign=\"top\"><th scope=\"row\">Enable Debug Log:</th>";
  echo "<td><input type=\"checkbox\" name=\"debug-mode\" value=1 ".(get_option("debug-mode")==1?"checked=\"yes\"":"")." /></td>";
  echo "</tr>";
  echo "<tr valign=\"top\"><th scope=\"row\">Enable Custom Format:</th>";
  echo "<td><input type=\"checkbox\" name=\"custom-mode\" value=1 ".(get_option("custom-mode")==1?"checked=\"yes\"":"")." /></td>";
  echo "</tr>";
  echo "<tr valign=\"top\"><th scope=\"row\">Custom Format:</th>";
  echo "<td><input type=\"text\" name=\"custom-format\" value=\"".get_option("custom-format")."\" /></td>";
  echo "</tr>";
  
  echo '<tr>';
  echo '<th col=2><strong>Sidebar Default State</strong></th>';
  echo '</tr>';
  echo '<tr>';
  echo '<td><label for="idShortLink">Use Shortlink?</label></td>';
  echo '<td><input type="checkbox" name="useShortLinkOpt" value="yes" id="idShortLink" /></td>';
  echo '</tr>';
  echo '<tr>';
  echo '<td><label for="idUseFtrdImg">Use Featured Image?</label></td>';
  echo '<td><input type="checkbox" name="useFtrdImgOpt" value="yes" id="idUseFtrdImg" /></td>';
  echo '</tr>';
  
  echo "<tr><td colspan=2>";
  echo "<div style=\"border: 2px solid #CDCDCD;background-color:#DDDDDD;\">";
  echo "<b>Format Options:</b>";
  echo "<ul>";
  echo "<li><b>[t]</b> for post title &nbsp;</li><li><b>[h]</b> for user handle &nbsp;</li>";
  echo "<li><b>[l]</b> for link to post &nbsp;</li> ";
  echo "<li><b>[o]OPTIONAL_TEXT[/o]</b> Only includes OPTION_TEXT if Author's Twitter handle is set &nbsp;</li> </ul><br /><b>Example:</b><br />";
  echo "<b>Format:</b> '[t]' [o]posted by[/o] [h] at [l]<br />";
  echo "<b>Output:</b> 'Hello World!' posted by <a href=\"http://twitter.com/ksg91\">@ksg91</a> at <a href=\"http://localhost/wordpress/?p=1\">http://localhost/wordpress/?p=1</a>";
  echo "</div>";
  echo "</td></tr>";
  echo "</table><p class=\"submit\"><input type=\"submit\" class=\"button-primary\" value=\"Save Changes\" /></p>";
  echo "<div style='display:none;' id='idShortLinkDiv' ></div>";
  echo "<div style='display:none;' id='idFtrdImgDiv' ></div>";
  echo "</form></div>";
  ?>
      <script type="text/javascript">
      $=jQuery.noConflict();
        $(document).ready(function(){
          
          $('#idShortLink').change(function(){
            if($(this).attr("checked")=="checked" || $(this).attr("checked")==true ){
              $('#idShortLinkDiv').empty();
            }
            else{
              $('#idShortLinkDiv').html('<input type="hidden" name="useShortLinkOpt" value="no" />');
            }
          });
          
          $('#idUseFtrdImg').change(function(){
            if($(this).attr("checked")=="checked" || $(this).attr("checked")==true){
              $('#idFtrdImgDiv').empty();
            }
            else{
              $('#idFtrdImgDiv').html('<input type="hidden" name="useFtrdImgOpt" value="no" />');
            }
          });
          callChange();
        });  
        
      </script>
      <script type="text/javascript">
      function callChange(){
        //alert('alerted');
        var shortLink = '<?php echo get_option("useShortLinkOpt"); ?>';
        var ftrdImg = '<?php echo get_option("useFtrdImgOpt"); ?>';
        //alert(shortLink);
        if(shortLink == "yes"){
          $('#idShortLink').attr("checked","checked");
        }
        else if(shortLink == "no"){
            
          $('#idShortLink').attr("checked",false);
        }
        else{
            <?php 
              add_option('useShortLinkOpt','yes');
            ?>
            $('#idShortLink').attr("checked","checked");
        }
        if(ftrdImg == "yes"){
          $('#idUseFtrdImg').attr("checked","checked");
        }
        else if(ftrdImg == "no"){
            
          $('#idUseFtrdImg').attr("checked",false);
        }
        else{
            <?php 
              add_option('useShortLinkOpt','yes');
            ?>
            $('#idUseFtrdImg').attr("checked","checked");
        }       
        $('#idShortLink').change();
        $('#idUseFtrdImg').change();
      }
      </script>
      
  <?php
  
}

//TMP Log Page
function log_page()
{
  if($_GET['action']=="clearLog")
    update_option("debug-data",NULL);
  echo "<h2>Log Page</h2>";
  echo "<form><input type=\"button\" value=\"Clear Log\" onClick=\"window.location.href='admin.php?page=tmp_log_page&action=clearLog'\"></form>";
  $debug=get_option("debug-data");
  if($debug==NULL)
    return;
  foreach($debug as $val){
    echo "<div style=\"border-top:2px solid #DEDEDE;border-bottom:2px solid #DEDEDE;\">";
    if(is_array($val))
    {
      echo "<h3>[".$val['logtime']."]</h3>";
      unset($val['logtime']);
      foreach($val as $k=>$v)
      {
        if(isset($val['ERROR']))
          echo '<div class="error">';
        else
          echo '<div class="updated">';
        echo "<b>".strtoupper($k).":</b> ".$v."<br />";
        echo '</div>'; 
      }
    }
    else
      echo $val."<br />";
    echo "</div>";
  }
}

//function action for admin_menu hook to add pages
function add_tmp_page()
{
  add_users_page( "Tweet My Post", "Tweet My Post", 'level_1', "tmp_user_page", "tmp_user_page");
  add_menu_page( "Tweet My Post","Tweet My Post", 'level_8',"tmp_admin_page", 'tmp_api_page', plugin_dir_url( __FILE__ )."bird_small.png");
  add_submenu_page("tmp_admin_page", "Tweet My Post","TMP - Log ", 'level_8',"tmp_log_page", 'log_page');
}

//register activation/deactivation hook
register_activation_hook(__FILE__, 'tmp_activate' );
//register_deactivation_hook(__FILE__, 'tmp_deactivate' );
//load_plugin_textdomain('tweet-my-post', false, basename( dirname( __FILE__ ) ) . '/languages' );
?>