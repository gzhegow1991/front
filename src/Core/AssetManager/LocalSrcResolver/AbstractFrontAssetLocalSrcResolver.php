<?php

namespace Gzhegow\Front\Core\AssetManager\LocalSrcResolver;

use Gzhegow\Front\Core\Store\FrontStore;


abstract class AbstractFrontAssetLocalSrcResolver implements FrontAssetLocalSrcResolverInterface
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
