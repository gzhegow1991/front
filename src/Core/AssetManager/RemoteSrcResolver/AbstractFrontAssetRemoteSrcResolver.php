<?php

namespace Gzhegow\Front\Core\AssetManager\RemoteSrcResolver;

use Gzhegow\Front\Core\Store\FrontStore;


abstract class AbstractFrontAssetRemoteSrcResolver implements FrontAssetRemoteSrcResolverInterface
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
