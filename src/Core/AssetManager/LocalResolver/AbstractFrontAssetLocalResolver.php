<?php

namespace Gzhegow\Front\Core\AssetManager\LocalResolver;

use Gzhegow\Front\Core\Store\FrontStore;


abstract class AbstractFrontAssetLocalResolver implements FrontAssetLocalResolverInterface
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
