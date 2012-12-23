<?php

namespace Giko\CorpBundle\Image;

class Manager {
	public function getMediaPath($url, $size = 'origin') {
		$md5 = md5($url);
		$ext = strrchr(strtolower($url), ".");
		$filename = sprintf('%s/%s/%s/%s.%s', $size, substr($md5, 0, 2), substr($md5, 2, 2), $md5, $ext);
		echo $filename;
	}
}
