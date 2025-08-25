<?php

namespace Gzhegow\Front\Core\AssetManager\RemoteSrcResolver;

use Gzhegow\Front\Core\Store\FrontStore;
use Gzhegow\Front\Core\Struct\Remote;


interface FrontAssetRemoteSrcResolverInterface
{
    /**
     * @return static
     */
    public function setStore(FrontStore $store);


    public function resolve(string $src, ?Remote $remoteCurrent = null) : string;
}
