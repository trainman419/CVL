<?php

session_start();

# TODO:
#  - auto thumbnails
#  - admin/upload page
#  - protect our guts with htaccess (.git, .htaccess, etc)
#  - turn off auto index pages

# Admin page
# Modes:
#  - Album overview; add new albums
#  - Album view: name/rename album, upload new photos
#
# TODO: login with cookies
# TODO: log out (destroy session)

# cofiguration variables
$pic_dir = 'pictures';
$title = 'Picture Admin Page';

# header and sidebars
include 'header.html';

# look for and validate login credentials
if( isset($_POST['name']) and isset($_POST['pass']) ) {
   $name = $_POST['name'];
   $pass = $_POST['pass'];

   $htpass = fopen(".htpasswd", "r");
   if( $htpass ) {
      while( false !== ($line = fgets($htpass)) ) {
         $array = explode(':', $line);

         # look for matching name
         if( $array[0] == $name ) {
            $crypt = chop($array[1]);

            # if name and crypted passwords match, set login
            if( crypt($pass, substr($crypt,0,CRYPT_SALT_LENGTH)) == $crypt ) {
               $_SESSION['login'] = $name;
            }
         }
      }
      fclose($htpass);
   }

   unset($name);
   unset($pass);
}
# done validating login

$login = false;
if( isset($_SESSION['login']) ) {
   $login = $_SESSION['login'];
}

if( $login ) {
   $here = "admin.php";

   # figure out if we're viewing an album and adjust
   $page = false;
   if( isset($_GET["page"] ) ) {
      if( file_exists("$pic_dir/".$_GET['page']) ) {
         $page = $_GET['page'];
         $pic_dir = "$pic_dir/$page";
         $here = "$here?page=$page";

         $title = "";
         if( file_exists("$pic_dir/.index.txt") and 
               false !== ($fh = fopen("$pic_dir/.index.txt", "r")) ) {
            $title = htmlentities(fgets($fh));
            fclose($fh);
         }

         # Update album title
         if( isset($_POST['title']) ) {
            $title = $_POST['title'];
            if( false !== ($fh = fopen("$pic_dir/.index.txt", "w")) ) {
               fwrite($fh, $title);
               fclose($fh);
            }
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

   # Add photo to album
   if( $page and isset($_FILES['newpic']) ) {
      $uploadpath = $_FILES['newpic']['tmp_name'];

      # get the original file extension
      $origname = $_FILES['newpic']['name'];
      $parts = explode(".", strtolower($origname));
      $ext = $parts[count($parts)-1];

      if( $ext == "jpg" or $ext = "jpeg" ) {
         $newname = $files[count($files)-1];
         $tmp = explode(".",$newname);
         $newname = $tmp[0];
         $tmp = sscanf($newname, "%d");
         $newname = $tmp[0] + 1;
         $newname = "$newname.jpg";
         move_uploaded_file($uploadpath, "$pic_dir/$newname");
         $img_path = escapeshellarg("$pic_dir/$newname");
         $thumb_path = escapeshellarg("$pic_dir/.thumbnails/$newname");
         exec("convert -resize 240x240 $img_path $thumb_path");
         $files[] = $newname;
      } else {
         print "File upload failed: wrong file extension. Please upload jpeg images<br/>\n";
      }
   }
}

?>
<div id="picture_content">
<h3><?
# page title here
if( $page and $login ) {
   print "<form action=\"$here\" method=\"post\">\n";
   print "<input type=\"text\" name=\"title\" size=\"80\" value=\"$title\"/>\n";
   print "<input type=\"submit\" value=\"update\"/>\n";
   print "</form>\n";
} else {
   print $title;
}
?></h3>
<div class='featurebox_center'>
<?

if( $login ) {
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
      # Upload for new picture
      # TODO: fix color scheme and formatting; better field labels
      ?>
         <h4>Upload Picture</h4>
         <?
      print "<form action=\"$here\" method=\"post\" enctype=\"multipart/form-data\">\n";
      ?>
      <input type="file" name="newpic" size="50"/>
      <input type="submit" value="upload"/>
      </form>
      (recommended size no larger than 800x800)
      <?
   } else {
      # TODO: add new album here
   }
} else {
   # not logged in; display login page
   ?>
      <h4>Please Log in</h4>
      <form action="admin.php" method="post">
      <table>
      <tr><td>Name:</td><td><input type="text" name="name"/></td></tr>
      <tr><td>Password:</td><td><input type="password" name="pass"/></td></tr>
      <tr><td></td><td><input type="submit" value="Log In"/></td></tr>
      </table>
      </form>
   <?
}


?>
</div>
</div>
<?
# footer
include 'footer.html';

?>
