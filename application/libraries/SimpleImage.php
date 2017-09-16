<?php
/*
* File: SimpleImage.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 08/11/06
* Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details:
* http://www.gnu.org/licenses/gpl.html
*
*/

class SimpleImage {

   var $image;
   var $image_type;
   var $optons = array(
       '728_90' => array(
           'description_font_size' => 18,
           'profile_image_width' => 90,
           'profile_image_height' => 90,
           'profile_x' => 0,
           'profile_y' => 0,
           'agent_image_width' => 80,
           'agent_image_height' => 27,
           'agency_image_width' => 130,
           'agency_image_height' => 46,
           'agent_image_inline' => true,
           'agency_y' => 38,// desc font size + 2*mergin
           'agency_x' => 342, //middle
           'agent_y' => 47,
           'agent_x' => 472, //agency_x + agency_image_width
           'description_y' => 28, //desc font size + margin
           'font_size' => 20,
           'number_font_size' => 18,
           'url_font_size' => 11,
           'middle' => 342,
           'vertical' => 0,
           'margin' => 10,
           'x' => 100, // prof pic width + margin
           'y' => 30, //vertical + font size + margin
           'centre' => false
       ),
       '468_60' => array(
           'description_font_size' => 15,
           'profile_image_width' => 60,
           'profile_image_height' => 60,
           'profile_x' => 0,
           'profile_y' => 0,
           'agent_image_width' => 53,
           'agent_image_height' => 20,
           'agency_image_width' => 83,
           'agency_image_height' => 30,
           'agent_image_inline' => true,
           'agency_y' => 26,// desc font size + 2*mergin
           'agency_x' => 222, //middle
           'agent_y' => 31,
           'agent_x' => 305, //agency_x + agency_image_width
           'description_y' => 21, //desc font size + margin
           'font_size' => 14,
           'number_font_size' => 12,
           'url_font_size' => 10,
           'middle' => 222,
           'vertical' => 0,
           'margin' => 5,
           'x' => 65, // prof pic width + margin
           'y' => 19, //vertical + font size + margin
           'centre' => false
       ),
       '300_250' => array(
           'description_font_size' => 18,
           'profile_image_width' => 90,
           'profile_image_height' => 90,
           'profile_x' => 0,
           'profile_y' => 0,
           'agent_image_width' => 80,
           'agent_image_height' => 27,
           'agency_image_width' => 130,
           'agency_image_height' => 46,
           'agent_image_inline' => false,
           'agency_y' => 128,// description_y  + mergin
           'agency_x' => 85, //(300 - agency_image_width)/2
           'agent_y' => 160, //agency_y + agent_image_height +margin // hardcode chi texavorvum
           'agent_x' => 110, // (300 - agent_image_width)/2
           'description_y' => 118, // profile_image_height + margin + desc font size
           'font_size' => 18,
           'number_font_size' => 16,
           'url_font_size' => 12,
           'middle' => 0,
           'vertical' => 0,
           'margin' => 10,
           'x' => 100, // prof pic width + margin
           'y' => 28, //vertical + font size + margin
           'centre' => false
       ),
       '160_600' => array(
           'description_font_size' => 18,
           'profile_image_width' => 90,
           'profile_image_height' => 90,
           'profile_x' => 35, //(160 - profile_image_width)/2
           'profile_y' => 0,
           'agent_image_width' => 80,
           'agent_image_height' => 27,
           'agency_image_width' => 130,
           'agency_image_height' => 46,
           'agent_image_inline' => false,
           'agency_y' => 455,// agent_y - mergin - agent image height
           'agency_x' => 15, //(160 - agency_image_width)/2
           'agent_y' => 509, // 600 - 3*agent_image_height - margin
           'agent_x' => 40, // (160 - agent_image_width)/2
           'description_y' => 300, //
           'font_size' => 16,
           'number_font_size' => 14,
           'url_font_size' => 9,
           'middle' => 0,
           'vertical' => 90, // prof image height
           'margin' => 10,
           'x' => 10, //
           'y' => 120, //vertical + font size + margin
           'centre' => true

       ),
   );

   function load($filename) {
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
         $this->image = imagecreatefrompng($filename);
      }
   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image,$filename);
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image,$filename);
      }
      if( $permissions != null) {
         chmod($filename,$permissions);
      }
   }
   function output($image_type=IMAGETYPE_JPEG) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image);
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image);
      }
   }
   function getWidth() {
      return imagesx($this->image);
   }
   function getHeight() {
      return imagesy($this->image);
   }
   function resizeToHeight($height) {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100;
      $this->resize($width,$height);
   }
   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;
   }
   function createImageBySize($width,$height,$image_data) {

       $options = $this->optons[$width.'_'.$height];
       $path = 'v2/images/affiliation/';
       $x = $options['x'];
       $y = $options['y'];

//       if($height<=90) {
//           $middle_with_margin = $options['middle'] + $options['margin'];
//           $middle_with_margin_2 = $middle_with_margin + $options['agency_image_width'];
//           $vertical_for_affiliation = $options['description_font_size'] + 2*$options['margin'];
//       } else {
//           $middle_with_margin = $options['middle'] + $options['margin'];
//           $middle_with_margin_2 = $middle_with_margin;
//           $vertical_for_affiliation = 180;
//       }

       if($image_data['bg_'.$width.'_'.$height]) {
           $this->load($image_data['bg_'.$width.'_'.$height]);
           $im = $this->image;
       } else {
           $im = imagecreatetruecolor($width, $height);
           $hex = '#'.$image_data['background_color'];
           list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");

           // sets background
           $bg_color = imagecolorallocate($im, $r, $g, $b);
           imagefill($im, 0, 0, $bg_color);
       }

       //$profile_im = imagecreatefromjpeg("v2/images/affiliation/profile.jpg");
       if($width == 468) {

           $this->load($image_data['profile_image']);
           $this->resize($options['profile_image_width'], $options['profile_image_width']);
           $profile_im = $this->image;
           foreach($image_data['agent_affiliation'] as $key => $agent_image) {
               $agent_affiliation_im = imagecreatefrompng($path.$options['agent_image_width'].'_'.$options['agent_image_height'].'_'.$agent_image);
               if($options['agent_image_inline']){

                   $agent_x = $options['agent_x'] + $key*$options['agent_image_width'] + $key*$options['margin'];
                   imagecopy($im, $agent_affiliation_im, $agent_x, $options['agent_y'], 0, 0, $options['agent_image_width'], $options['agent_image_height']);
               } else {

                   $agent_y = $options['agent_y'] + $key * $options['agent_image_height'] + $options['margin'];
                   imagecopy($im, $agent_affiliation_im, $options['agent_x'], $agent_y, 0, 0, $options['agent_image_width'], $options['agent_image_height']);
               }
           }
           $agency_affiliation_im = imagecreatefrompng($path.$options['agency_image_width'].'_'.$options['agency_image_height'].'_'.$image_data['agency_affiliation']);
       } else {
           $this->load($image_data['profile_image']);
           $profile_im = $this->image;

           foreach($image_data['agent_affiliation'] as $key => $agent_image) {
               $agent_affiliation_im = imagecreatefrompng($path.$agent_image);
               if($options['agent_image_inline']){
                   $agent_x = $options['agent_x'] + $key*$options['agent_image_width'] + $options['margin'];
                   imagecopy($im, $agent_affiliation_im, $agent_x, $options['agent_y'], 0, 0, $options['agent_image_width'], $options['agent_image_height']);
               } else {
                   $agent_y = $options['agent_y'] + $key * $options['agent_image_height'] + $options['margin'];
                   imagecopy($im, $agent_affiliation_im, $options['agent_x'], $agent_y, 0, 0, $options['agent_image_width'], $options['agent_image_height']);
               }
           }
           $agency_affiliation_im = imagecreatefrompng($path.$image_data['agency_affiliation']);
       }
       //$profile_im = imagecreatefromjpeg("v2/images/affiliation/profile.jpg");


      //imagefill($im, 0, 0, $white);

       imagecopy($im, $profile_im, $options['profile_x'], $options['profile_y'], 0, 0, $options['profile_image_width'], $options['profile_image_height']);
       imagecopy($im, $agency_affiliation_im, $options['agency_x'], $options['agency_y'], 0, 0, $options['agency_image_width'], $options['agency_image_height']);

//      if($image_data['background_images[]']){}
       $text_color_hex =  '#'.$image_data['text_color'];

       $fontname = 'v2/fonts/Capriola-Regular.ttf';
       $fontname = 'v2/fonts/'.$image_data['font'].'.ttf';
       //$fontname = 'v2/fonts/proximanova-light-webfont.ttf';

       $image_name = md5(microtime().$width.$height);
       $file = "uploads/tmp/".$image_name.".png";
      // define the base image that we lay our text on
      //$im = imagecreatefromjpeg("pass.jpg");

      // setup the text colours
       list($r, $g, $b) = sscanf($text_color_hex, "#%02x%02x%02x");

       $text_color = imagecolorallocate($im, $r, $g, $b);
       $destination_url = preg_replace('#^https?://#', '', rtrim($image_data['destination_url'],'/'));

      // center the text in our image - returns the x value
       if($options['centre']) {
           $x = $this->centerText($image_data['agent_name'], $image_data['name_font_size'], $width, $fontname);
       }
       imagettftext($im, $image_data['name_font_size'], 0, $x, $y, $text_color, $fontname, $image_data['agent_name']);
       imagettftext($im, $image_data['name_font_size'], 0, $x, $y+1, $text_color, $fontname, $image_data['agent_name']);
       imagettftext($im, $image_data['name_font_size'], 0, $x+1, $y, $text_color, $fontname, $image_data['agent_name']);

       if($options['centre']) {
           $x = $this->centerText($image_data['agent_phone'], $image_data['phone_font_size'], $width, $fontname);
       }
       imagettftext($im, $image_data['phone_font_size'], 0, $x, $y+$y-$options['vertical'], $text_color, $fontname, $image_data['agent_phone']);

       if($options['centre']) {
           $x = $this->centerText($destination_url, $image_data['url_font_size'], $width, $fontname);
       }
       imagettftext($im, $image_data['url_font_size'], 0, $x, $y+$y+$image_data['url_font_size']+$options['margin']-$options['vertical'], $text_color, $fontname, $destination_url);

       $x = $this->centerText($image_data['description'], $image_data['description_font_size'], $width-$options['middle'], $fontname);
       //var_dump($x, $width); exit;
       if($x<0){
           $texts = $this->makeTextBlock($image_data['description'], $fontname, $image_data['description_font_size'], $width-$options['middle']);
           $line_height = $options['description_y'];
           foreach($texts as $key => $text){
               $description_y = $options['description_y'] + $key*$image_data['description_font_size'] + $key*4;
               $x = $this->centerText($text, $image_data['description_font_size'], $width-$options['middle'], $fontname);
               imagettftext($im, $image_data['description_font_size'], 0, $x+$options['middle'], $description_y, $text_color, $fontname, $text);

           }
       } else {
           imagettftext($im, $image_data['description_font_size'], 0, $x+$options['middle'], $options['description_y'], $text_color, $fontname, $image_data['description']);
       }
       $text = $this->makeTextBlock($image_data['description'], $fontname, $image_data['description_font_size'], $width-$options['middle']);
       //var_dump($text);
       imagettftext($im, $image_data['description_font_size'], 0, $x+$options['middle'], $options['description_y'], $text_color, $fontname, $text);

       imagepng($im, $file);
       imagedestroy($im);

       return $file;
   }
    function centerText($string, $font_size, $width, $fontname){

        //$fontname = 'v2/fonts/'.$font.'.ttf';

        $dimensions = imagettfbbox($font_size, 0, $fontname, $string);

        return ceil(($width - $dimensions[4]) / 2);
    }

    function makeTextBlock($text, $fontfile, $fontsize, $width)
    {
        $words = explode(' ', $text);
        //var_dump($words);
        $lines = array($words[0]);
        $currentLine = 0;
        for($i = 1; $i < count($words); $i++)
        {
            $lineSize = imagettfbbox($fontsize, 0, $fontfile, $lines[$currentLine] . ' ' . $words[$i]);
            if($lineSize[2] - $lineSize[0] < $width)
            {
                $lines[$currentLine] .= ' ' . $words[$i];
            }
            else
            {
                $currentLine++;
                $lines[$currentLine] = $words[$i];
            }
        }
        return $lines;
        return implode("\n", $lines);
    }
}
?>

