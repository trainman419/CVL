<?php

# TODO:
#  - auto thumbnails
#  - admin/upload page

# Admin page
# Modes:
#  - Album overview; add new albums
#  - Album view: name/rename album, upload new photos

# cofiguration variables
$pic_dir = 'pictures';
$title = 'Picture Admin Page';

# header and sidebars
include 'header.html';

# figure out if we're viewing an album and adjust
$page = false;
if( isset($_GET["page"] ) ) {
   if( file_exists("$pic_dir/".$_GET['page']) ) {
      $page = $_GET['page'];
      $pic_dir = "$pic_dir/$page";

      $title = "";
      if( file_exists("$pic_dir/.index.txt") and 
               false !== ($fh = fopen("$pic_dir/.index.txt", "r")) ) {
         $title = htmlentities(fgets($fh));
         fclose($fh);
      }
   }
}

# open our directory and get a listing
$files = array();

if( $dir = opendir($pic_dir) ) {

   while( false !== ($file = readdir($dir))) {
      if( ! preg_match('/^\./', $file) ) {
         $files[] = $file;
      }
   }

   closedir($dir);
}

# code here
?>
<div id="picture_content">
<h3><?
   # page title here
   if( $page ) {
      print "<form action=\"admin.php\" method=\"post\">\n";
      print "<input type=\"text\" name=\"title\" value=\"$title\"/>\n";
      print "<input type=\"submit\" value=\"update\"/>\n";
      print "</form>\n";
   } else {
      print $title;
   }
?></h3>
   <div class='featurebox_center'>
   <?
   if( $page ) {
      print "<table width=\"100%\"><tr>\n";
   }

   # display here
   foreach($files as $file) {
      if( $page ) {
         print "</tr><tr>\n";
         print "<td width=\"33%\">";
         print "<img alt=\"\" src=\"$pic_dir/.thumbnails/$file\"/>";
         print "</td>\n";
      } else {
         $name = $file;
         if( file_exists("$pic_dir/$file/.index.txt") and 
               ($fh = fopen("$pic_dir/$file/.index.txt", "r") ) ) {
            $name = htmlentities(fgets($fh));
            fclose($fh);
         }
         print "<h2><a href=\"admin.php?page=$file\">$name</h2>\n";
      }
   }

   if( $page ) {
      print "</tr></table>\n";
      # TODO: upload new picture here
      print "<form action=\"admin.php\" method=\"post\" enctype=\"multipart/form-data\">\n";
      print "<input type=\"file\" name=\"newpic\" size=\"50\"/>\n";
      print "<input type=\"submit\" value=\"upload\"/>\n";
      print "</form>\n";
   } else {
      # TODO: add new album here
   }


?>
</div>
</div>
<?
# footer
include 'footer.html';

?>
