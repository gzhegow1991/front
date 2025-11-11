<?php

namespace Gzhegow\Front\Core\AssetManager\ResolverRemote;

use Gzhegow\Front\Core\Store\FrontStore;


abstract class AbstractFrontAssetResolverRemote implements FrontAssetResolverRemoteInterface
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
