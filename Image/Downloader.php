<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Varien
 * @package    Varien_Io
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Giko\CorpBundle\Image;

/**
 * Filesystem client
 *
 * @category   Varien
 * @package    Varien_Io
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Downloader
{
    protected $url;
    protected $tmpFile;
    
    function __construct($url) {
        $this->url = $url;
        $pathinfo = pathinfo($url);
        
        $imgExtensions = array('jpg','jpeg','gif','png');
        if (!isset($pathinfo['extension']) || !in_array(strtolower($pathinfo['extension']), $imgExtensions)) {
            throw new \Exception('Invalid image file type.');
        }
        
        $this->tmpFile = tempnam("/tmp", "giko") . '.' . $path_parts['extension'];
        
        $handle = fopen($tmpfname, "w");
        fwrite($handle, "writing to tempfile");
        fclose($handle);
        
        
        ob_start();
        readfile($this->url);
        $file = ob_get_contents();
        ob_end_clean();
        $handle = fopen($this->tmpFile, "w");
        fclose($handle);
    }
    
    public function __destruct() {
        if($this->tmpFile) {
            unlink($this->tmpFile);
        }
    }
    
    public function getDownloadedFile() {
        return $this->tmpFile;
    }
    
}