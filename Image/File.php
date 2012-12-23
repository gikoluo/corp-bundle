<?php
namespace Giko\CorpBundle\Image;

use Imagine\Exception\Exception;
use Gaufrette\Adapter\mkdir;
class File {
	protected $base;
	protected $path;
	protected $image;

	function __construct($path, $base) {
		$this->base = $base;
		$this->path = $path;
		
	}

	protected function downloadImage($url, $filename = "") {
		
		
		$this->makeDir($filename);
		
		
	}
	
	private function makeDir($filename) {
	    $dirname = dirname($filename);
	    mkdir($dirname, 0777, true);
	}
}
