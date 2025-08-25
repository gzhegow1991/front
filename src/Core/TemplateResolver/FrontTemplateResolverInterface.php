<?php

namespace Gzhegow\Front\Core\TemplateResolver;

use League\Plates\Template\Name;
use Gzhegow\Front\Core\Store\FrontStore;


interface FrontTemplateResolverInterface
{
    /**
     * @return static
     */
    public function setStore(FrontStore $store);


    public function resolve(Name $name) : string;
}
