<?php

namespace Gzhegow\Front\Core\AssetManager\RemoteResolver;

use Gzhegow\Front\Core\Store\FrontStore;
use Gzhegow\Front\Core\Struct\Remote;


interface FrontAssetRemoteResolverInterface
{
    /**
     * @return static
     */
    public function setStore(FrontStore $store);


    /**
     * @return array{
     *     key: string,
     *     remote: Remote,
     *     src: string,
     *     version: string,
     *     uri: string,
     * }
     */
    public function resolve(
        string $key,
        ?Remote $remoteCurrent = null
    ) : array;
}
