<?php

namespace Gzhegow\Front\Core\AssetManager\RemoteResolver;

use Gzhegow\Front\Core\Store\FrontStore;


abstract class AbstractFrontAssetRemoteResolver implements FrontAssetRemoteResolverInterface
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
