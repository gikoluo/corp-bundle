<?php
namespace Giko\CorpBundle\Image;

class Corper {
	// Initialize variables;
	protected $imgSrc, $myImage, $cropHeight, $cropWidth, $x, $y, $thumb, $dif, $imageRoot;

	// Watermark
	private $watermark = WATERMARK;
	
	function __construct($imageRoot='cache') {
	    $this->$imageRoot = $imageRoot;
	}

	/**
	 * Stage 2: Read the image and check if it is present on our cache folder. If so we'll just use the cached version. Take in account that even if you supply
	 * an image on an external source it will not check the image itself but rather the link, thus, no external connection is made.
	 *
	 * Also check what type of file we're working with. Different files, different methods.
	 *
	 * @param $image The image that it's to crop&scale
	 * @return nothing
	 */
	function setImage($image) {

		// Your Image
		$this->imgSrc = $image;
		// Getting the image dimensions
		list($width, $height) = getimagesize($this->imgSrc);
		// Check what file we're working with
		if ($this->getExtension($this->imgSrc) == 'png') {
			//create image png
			$this->myImage = imagecreatefrompng($this->imgSrc) or die("Error: Cannot find image!");
			imagealphablending($this->myImage, true); // setting alpha blending on
			imagesavealpha($this->myImage, true); // save alphablending setting (important)
		} elseif ($this->getExtension($this->imgSrc) == 'jpg' || $this->getExtension($this->imgSrc) == 'jpeg' || $this->getExtension($this->imgSrc) == 'jpe') {
			//create image jpeg
			$this->myImage = imagecreatefromjpeg($this->imgSrc) or die("Error: Cannot find image!");
		} elseif ($this->getExtension($this->imgSrc) == 'gif') {
			//create image gif
			$this->myImage = imagecreatefromgif($this->imgSrc) or die("Error: Cannot find image!");
		} elseif ($this->getExtension($this->imgSrc) == 'bmp') {
			//create image gif
			$this->myImage = ImageCreateFromBmp($this->imgSrc) or die("Error: Cannot find image!");
		}

		// Find biggest length
		if ($width > $height)
			$biggestSide = $width;
		else
			$biggestSide = $height;

		// This will zoom in to 50% zoom (crop!)
		$cropPercent = 1;
		// Get the size that you submitted for resize on the URL
		$both_sizes = explode("x", $_GET['size']);

		// Check if it was submited something like 50x50 and not only 50 (wich is also supported)
		if (!empty($_GET['size'])) {
			if (count($both_sizes) == 2) {
				if ($width > $height) {
					// Apply the cropping formula
					$this->cropHeight = $biggestSide * (($both_sizes[1] * $cropPercent) / $both_sizes[0]);
					$this->cropWidth = $biggestSide * $cropPercent;
				} else {
					// Apply the cropping formula
					$this->cropHeight = $biggestSide * $cropPercent;
					$this->cropWidth = $biggestSide * (($both_sizes[0] * $cropPercent) / $both_sizes[1]);
				}
			} else {
				$this->cropHeight = $biggestSide * $cropPercent;
				$this->cropWidth = $biggestSide * $cropPercent;
			}
		} else {
			if ($width > $height) {
				// Apply the cropping formula
				$this->cropHeight = $biggestSide * ($height * $cropPercent) / $width;
				$this->cropWidth = $biggestSide * $cropPercent;
			} else {
				// Apply the cropping formula
				$this->cropHeight = $biggestSide * $cropPercent;
				$this->cropWidth = $biggestSide * ($width * $cropPercent) / $height;
			}
		}

		// Getting the top left coordinate
		$this->x = ($width - $this->cropWidth) / 2;
		$this->y = ($height - $this->cropHeight) / 2;

	}

	/**
	 * From a file get the extension
	 *
	 * @param $filename The filename
	 * @return string file extension
	 */
	function cache_url($filename) {
		global $size_string;

		$array = explode("/", $size_string);
		$tmp = $this->imageRoot;

		foreach ($array as $element) {
			if (!is_dir($tmp . '/' . $element)) {
				mkdir($tmp . '/' . $element);
			}
			$tmp = $tmp . '/' . $element;
		}

		return img_cache . '/' . $size_string . '/' . $filename;
	}

	/**
	 * From a file get the extension
	 *
	 * @param $filename The filename
	 * @return string file extension
	 */
	function getExtension($filename) {
		return $ext = strtolower(array_pop(explode('.', $filename)));
	}

	/**
	 * Add a watermark to the image
	 *
	 * @param $filename The filename
	 * @return string file extension
	 */
	function addWatermark(&$image) {
		global $imagem_encontrada;
		if (is_resource($image) && $imagem_encontrada) {
			if (!isset($_GET['size']) || empty($_GET['size'])) {
				$thumbSizex = $this->cropWidth;
				$thumbSizey = $this->cropHeight;
			} else {
				$thumbSizex = $thumbSizey = $_GET['size'];
				$both_sizes = explode("x", $_GET['size']);
				if (count($both_sizes) == 2) {
					$thumbSizex = $both_sizes[0];
					$thumbSizey = $both_sizes[1];
				}
			}
			$watermark = imagecreatefrompng($this->watermark) or die("Error: Cannot find watermark image!");
			list($watermark_width, $watermark_height, $type, $attr) = getimagesize($this->watermark);
			if ($thumbSizey >= $watermark_height * 1 && $thumbSizex >= $watermark_width * 1) {
				imagecopy($image, $watermark, $thumbSizex - WATERMARK_X, $thumbSizey - WATERMARK_Y, 0, 0, $watermark_width, $watermark_height);
			} else {
			}
			imagedestroy($watermark);
		} else {
			return false;
		}
	}

	/**
	 * For PNG files (and possibly GIF) add transparency filter
	 *
	 * @param $new_image
	 * @param $image_source
	 * @return nothing
	 */
	function setTransparency($new_image, $image_source) {
		$transparencyIndex = imagecolortransparent($image_source);
		$transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);

		if ($transparencyIndex >= 0) {
			$transparencyColor = imagecolorsforindex($image_source, $transparencyIndex);
		}

		$transparencyIndex = imagecolorallocate($new_image, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
		imagefill($new_image, 0, 0, $transparencyIndex);
		imagecolortransparent($new_image, $transparencyIndex);
	}

	/**
	 * Stage 3: Apply the changes and create image resource (new one).
	 *
	 * @return nothing
	 */
	function createThumb() {
		if (!isset($_GET['size']) || empty($_GET['size'])) {
			$thumbSizex = $this->cropWidth;
			$thumbSizey = $this->cropHeight;
		} else {
			$thumbSizex = $thumbSizey = $_GET['size'];
			$both_sizes = explode("x", $_GET['size']);
			if (count($both_sizes) == 2) {
				$thumbSizex = $both_sizes[0];
				$thumbSizey = $both_sizes[1];
			}
		}

		$this->thumb = imagecreatetruecolor($thumbSizex, $thumbSizey);
		$bg = imagecolorallocate($this->thumb, 255, 255, 255);
		imagefill($this->thumb, 0, 0, $bg);
		imagecopyresampled($this->thumb, $this->myImage, 0, 0, $this->x, $this->y, $thumbSizex, $thumbSizey, $this->cropWidth, $this->cropHeight);
		if (($this->getExtension($this->imgSrc) == 'png' || $this->getExtension($this->imgSrc) == 'gif') && isset($_GET['transparent']) && $_GET['transparent'] == 1) {
			$this->setTransparency($this->thumb, $this->myImage);
		}
	}

	/**
	 * Stage 4: Save image in cache and return the new image.
	 *
	 * @return nothing
	 */
	function renderImage() {
		global $size_string;

		$image_created = "";

		// Check if we should use watermark
		if (USE_WATERMARK)
			$this->addWatermark($this->thumb);

		if ($this->getExtension($this->imgSrc) == 'png') {
			header('Content-type: image/png');
			imagepng($this->thumb, null, PNG_IMAGE_QUALITY);
			/**
			 * Save image to the cache folder
			 */
			if (USE_CACHE)
				imagepng($this->thumb, $this->cache_url(basename($this->imgSrc)), 0);
		} elseif ($this->getExtension($this->imgSrc) == 'jpg' || $this->getExtension($this->imgSrc) == 'jpeg' || $this->getExtension($this->imgSrc) == 'jpe') {
			header('Content-type: image/jpeg');
			imagejpeg($this->thumb, null, IMAGE_QUALITY);
			/**
			 * Save image to the cache folder
			 */
			if (USE_CACHE)
				imagejpeg($this->thumb, $this->cache_url(basename($this->imgSrc)), IMAGE_QUALITY);
		} elseif ($this->getExtension($this->imgSrc) == 'gif') {
			header('Content-type: image/gif');
			imagegif($this->thumb);
			/**
			 * Save image to the cache folder
			 */
			if (USE_CACHE)
				imagegif($this->thumb, $this->cache_url(basename($this->imgSrc)));
		} elseif ($this->getExtension($this->imgSrc) == 'bmp') {
			header('Content-type: image/jpeg');
			imagejpeg($this->thumb, null, IMAGE_QUALITY);
			/**
			 * Save image to the cache folder
			 */
			if (USE_CACHE)
				imagejpeg($this->thumb, $this->cache_url(basename($this->imgSrc)), IMAGE_QUALITY);
		}
		imagedestroy($this->thumb);
	}
}
