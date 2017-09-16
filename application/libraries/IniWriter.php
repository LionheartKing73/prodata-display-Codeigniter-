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

class IniWriter
{
   /**
    * Writes an array configuration to a INI file.
    *
    * The array provided must be multidimensional, indexed by section names:
    *
    * ```
    * array(
    *     'Section 1' => array(
    *         'value1' => 'hello',
    *         'value2' => 'world',
    *     ),
    *     'Section 2' => array(
    *         'value3' => 'foo',
    *     )
    * );
    * ```
    *
    * @param string $filename
    * @param array $config
    * @param string $header Optional header to insert at the top of the file.
    * @throws IniWritingException
    */
   public function writeToFile($filename, array $config, $header = '')
   {
      $ini = $this->writeToString($config, $header);
      if($ini) {
         if (!file_put_contents($filename, $ini)) {
            return false;
         }
         return true;
      } else {
         return false;
      }

   }
   /**
    * Writes an array configuration to a INI string and returns it.
    *
    * The array provided must be multidimensional, indexed by section names:
    *
    * ```
    * array(
    *     'Section 1' => array(
    *         'value1' => 'hello',
    *         'value2' => 'world',
    *     ),
    *     'Section 2' => array(
    *         'value3' => 'foo',
    *     )
    * );
    * ```
    *
    * @param array $config
    * @param string $header Optional header to insert at the top of the file.
    * @return string
    * @throws IniWritingException
    */
   public function writeToString(array $config, $header = '')
   {
      $ini = $header;
      $sectionNames = array_keys($config);
      foreach ($sectionNames as $sectionName) {
         $section = $config[$sectionName];
         // no point in writing empty sections
         if (empty($section)) {
            continue;
         }
         if (! is_array($section)) {
            return false;
            //throw new Exception(sprintf("Section \"%s\" doesn't contain an array of values", $sectionName));
         }
         $ini .= "[$sectionName]\n";
         foreach ($section as $option => $value) {
            if (is_numeric($option)) {
               $option = $sectionName;
               $value = array($value);
            }
            if (is_array($value)) {
               foreach ($value as $currentValue) {
                  $ini .= $option . '[] = ' . $this->encodeValue($currentValue) . "\n";
               }
            } else {
               $ini .= $option . ' = ' . $this->encodeValue($value) . "\n";
            }
         }
         $ini .= "\n";
      }
      return $ini;
   }
   private function encodeValue($value)
   {
      if (is_bool($value)) {
         return (int) $value;
      }
      if (is_string($value)) {
         return "\"$value\"";
      }
      return $value;
   }
}