<?php

# TODO:
#  - auto thumbnails
#  - admin/upload page

# cofiguration variables
$pic_dir = 'pics';
$title = 'Pictures of Past Coastal Valley Lines Shows';

# header and sidebars
include 'header.html';

# figure out if we're viewing an album and adjust
$page = false;
if( isset($_GET["page"] ) ) {
   if( file_exists("pics/".$_GET['page']) ) {
      $page = $_GET['page'];
      $pic_dir = "pics/".$page;

      $title = "Sorry, no title for this album yet";
      if( file_exists("$pic_dir/.index.txt") and 
               false !== ($fh = fopen("$pic_dir/.index.txt", "r")) ) {
         $title = htmlentities(fgets($fh));
         fclose($fh);
      }
   }
}

# figure out if we're viewing an album or an image
$img = false;
if( isset($_GET['img'])) {
   if( file_exists("$pic_dir/".$_GET['img']) ) {
      $img = $_GET['img'];
      $img_path = "$pic_dir/$img";
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
   print $title;
?></h3>
   <div class='featurebox_center'>
   <?
      # if we're displaying an image, just do that
      if( $img ) {

         print "<div id=\"img_nav\">";

         # It's a start. Still need to deal with resize/scaling issues
         print "<img alt=\"\" src=\"$img_path\"/><br/>\n";
         # TODO: next, previous, album links
         # TODO: CSS center

         # get our position in the array
         $pos = array_search($img, $files);


         # Link? to first image
         if( $pos > 0 ) {
            print "<a href=\"pics.php?page=$page&img=".$files[0]."\">";
         }
         print "&lt;&lt;First";
         if( $pos > 0 ) {
            print "</a>";
         }
         print " ";

         # Link? to previous image
         if( $pos > 0 ) {
            print "<a href=\"pics.php?page=$page&img=".$files[$pos-1]."\">";
         }
         print "&lt;Prev";
         if( $pos > 0 ) print "</a>";

         # Link to album
         print " <a href=\"pics.php?page=$page\">Album</a> ";

         # Link? to next
         $next = false;
         if( isset($files[$pos+1]) ) $next = $files[$pos+1];
         if( $next ) {
            print "<a href=\"pics.php?page=$page&img=$next\">";
         }
         print "Next&gt;";
         if( $next ) {
            print "</a>";
         }
         print " ";

         # Link? to last
         if( $next ) {
            print "<a href=\"pics.php?page=$page&img=".$files[count($files)-1]."\">";
         }
         print "Last&gt;&gt;";
         if( $next ) print "</a>";

         print "</div>\n";
      } else {
         $i = 0;
         if( $page ) {
            print "<table width=\"100%\"><tr>\n";
         }

         # display here
         foreach($files as $file) {
            if( $page ) {
               if( $i % 3 == 0 and $i != 0 ) {
                  print "</tr><tr>\n";
               }
               print "<td width=\"33%\">";
               print "<a href=\"pics.php?page=$page&img=$file\">";
               print "<img alt=\"\" src=\"$pic_dir/thumbnails/$file\"/>";
               print "</a></td>\n";
               $i++;
            } else {
               $name = $file;
               if( file_exists("$pic_dir/$file/.index.txt") and 
                     ($fh = fopen("$pic_dir/$file/.index.txt", "r") ) ) {
                  $name = htmlentities(fgets($fh));
                  fclose($fh);
               }
               print "<h2><a href=\"pics.php?page=$file\">$name</h2>\n";
            }
         }

         if( $page ) {
            print "</tr></table>\n";
         }

      }
?>
</div>
</div>
<?
# footer
include 'footer.html';

?>
