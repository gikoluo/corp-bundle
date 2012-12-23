<?php

namespace Giko\CorpBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Giko\CorpBundle\Image\Image;
use Giko\CorpBundle\Image\Downloader;
use Giko\CorpBundle\Io\File;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function urlAction(Request $request, $url) {
        $localfile = '';
        return "";
    }
    public function indexAction(Request $request, $size, $source)
    {
        if ($size) {
            $both_sizes = explode("x", $size);
            if (count($both_sizes) == 2) {
                $size_string = $both_sizes [0] . "x" . $both_sizes [1];
            } else if (!count($both_sizes) == 1) {
                $size_string = $size . "x" . $size;
            }
        } else {
            $size_string = 'original';
        }
        
        if($size_string == 'original') {
            $sendPath = $source;
        }
        else {
            $image = new Image($source);
            list($width, $height) = explode('x',$size_string);
            $image->resize($width, $height);
            $destination = sprintf("%s/%s/%s", 'cache', $size_string, $source);
            $image->save($destination);
            $sendPath = $destination;
        }
        
        
        $sendPath = realpath($sendPath);
        
        $path_parts = pathinfo($sendPath);
        
        $content = file_get_contents($sendPath);
        $response = new Response($content);
        $response->headers->set('Content-Type', sprintf('image/%s', $path_parts['extension']));
        //$response->headers->set('Content-Disposition', 'attachment; filename="' . $path_parts['basename'] . '"');
        $response->headers->set('Content-Length', filesize($sendPath));
        return $response;
    }
    
    public function cleanAction() {
        
    }
}
