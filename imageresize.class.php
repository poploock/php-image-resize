<?php

 /*
 * ImageResize Class.
 *
 * Copyright (c) 2014 Ghasem Paran <poploock@gmail.com>
 * http://khabargir.ir
 *
 * The MIT License (MIT)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * 1- The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 * 
 * 2- THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 
 * @package    ImageResize
 * @author     Ghasem Paran <poploock@gmail.com>
 * @copyright  2014 Ghasem Paran
 * @license    http://opensource.org/licenses/mit-license.php The MIT License
 * @link       https://github.com/poploock/ImageResize
 * @see        ImageResize
 * @version    1.0
 */

class ImageResize {
	
	private $file_name;
	
	private $new_width;
	
	private $new_height;
	
	private $quality;
	
	private $size;
	
	private $upload_patch;
	
	private $split_image;
	
	private $new_file;
	
	private $image;
	
	private $bg;
	
	private $file_prefix = "";
	
	private $file_suffix = "";
	
	// set default value
	function __construct() {
		$this->new_width = 400;
		$this->new_height = 300;
		$this->upload_patch = "";
		$this->quality = 100;
    }
	
	// clean all variable
	function __destruct() {
		unset($this->file_name,
			$this->new_width,
			$this->new_height,
			$this->quality,
			$this->size,
			$this->upload_patch,
			$this->split_image,
			$this->new_file,
			$this->image,
			$this->bg,
			$this->file_prefix);
	}
	
	// get private variable
	public function __get($property) {
		if (property_exists($this, $property)) {
			return $this->$property;
		}
	}
	
	// set value for private variable
	public function __set($key,$value) {
        $this->$key = $value;
    }
	
	// check upload_dir , new_file , file_name	
	public function check($action = null) {
		if(isset($action)) {
			if($action == "upload_dir") {
				// create directory if upload_dir not exists
				if (!file_exists($this->upload_patch)) {
					@mkdir($this->upload_patch, 0777, true);
				}
			}
			else if($action == "new_file") {
				// check file exist
				if(!file_exists($this->new_file)) {
					return true;
				}
				else {
					return false;
				}
			}
		}
		else {
			if(!isset($this->file_name)) {
				// show message if file name is empty
				die("Enter Source File.");
			}
		}
	}
	
	// Copy image into your source folder
	private function copyImage() {
		copy($this->file_name, $this->new_file);
	}
	
	// remove %5 , %25 , %20 from file name
	private function clean_file_name() {
		return str_replace('%5', '-', str_replace('%25', '-', str_replace('%20', '-', $this->split_image['filename'])));
	}
	
	// resize image
	public function save(){
		
		// split file name in 2 section <filename , file extension>
		$this->split_image = pathinfo($this->file_name);
		
		$this->check("upload_dir");
		
		// set file location and new file name
		$this->new_file = $this->upload_patch.$this->file_prefix.$this->clean_file_name().$this->file_suffix.".jpg";
		
		// if file not exist , copy image in upload patch
		if($this->check("new_file") == true) {
			$this->copyImage();
		}
		
		// get file size <width and height>
		$this->size = getimagesize($this->new_file);
		
		// check image width and height is not larger than the your desired size
		if($this->size[0] != $this->new_width || $this->size[1] != $this->new_height) {
			
			list($width, $height) = $this->size;
			
			// check file extension
			switch($this->split_image['extension']) {
				case 'png':
					// http://ir1.php.net/manual/en/function.imagecreatefrompng.php
					$this->image = imagecreatefrompng($this->file_name);
					break;
				case 'jpg':
					// http://ir1.php.net/manual/en/function.imagecreatefromjpeg.php
					$this->image = imagecreatefromjpeg($this->file_name);
					break;
				case 'gif':
					// http://ir1.php.net/manual/en/function.imagecreatefromgif.php
					$this->image = imagecreatefromgif($this->file_name);
					break;
				case 'wbmp ':
					// http://ir1.php.net/manual/en/function.imagecreatefromwbmp.php
					$this->image = imagecreatefromwbmp ($this->file_name);
					break;
			}
			
			// set new image width and height , more information in http://php.net/manual/en/function.imagecreatetruecolor.php
			$this->bg = imagecreatetruecolor($this->new_width, $this->new_height);
			
			// set white background  , more information in http://php.net/manual/en/function.imagefill.php
			// and http://php.net/manual/en/function.imagealphablending.php
			imagefill($this->bg, 0, 0, imagecolorallocate($this->bg, 255, 255, 255));
			imagealphablending($this->bg, TRUE);
			
			// Copy and resize part of an image with resampling  , more information in http://php.net/manual/en/function.imagecopyresampled.php
			imagecopyresampled($this->bg, $this->image, 0, 0, 0, 0, $this->new_width, $this->new_height, $width, $height);
			
			// save image in your directory  , more information in http://php.net/manual/en/function.imagejpeg.php
			imagejpeg($this->bg, $this->new_file , $this->quality);
		}
	}	
}

?>
