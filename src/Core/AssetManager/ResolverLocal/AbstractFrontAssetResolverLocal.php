<?php

namespace Gzhegow\Front\Core\AssetManager\ResolverLocal;

use Gzhegow\Front\Core\Store\FrontStore;


abstract class AbstractFrontAssetResolverLocal implements FrontAssetResolverLocalInterface
{
    /**
     * @var FrontStore
     */
    protected $frontStore;


    /**
     * @return static
     */
    public function setStore(FrontStore $store)
    {
        $this->frontStore = $store;

        return $this;
    }
}
