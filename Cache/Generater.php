<?php 
namespace Giko\CorpBundle\Cache;

class Generater
{
    protected $container;
    protected $rootPath;
    
    public function __construct(ContainerInterface $container, $rootPath)
    {
        $this->container = $container;
        $this->rootPath = $rootPath;
    }
    
    
}

