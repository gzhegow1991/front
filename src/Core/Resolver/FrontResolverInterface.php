<?php

namespace Gzhegow\Front\Core\Resolver;

use League\Plates\Template\Name;
use Gzhegow\Front\Store\FrontStore;


interface FrontResolverInterface
{
    /**
     * @return static
     */
    public function setStore(FrontStore $store);


    public function resolve(Name $name) : string;
}
