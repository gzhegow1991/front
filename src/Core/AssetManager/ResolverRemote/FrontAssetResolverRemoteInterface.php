<?php

namespace Gzhegow\Front\Core\AssetManager\ResolverRemote;

use Gzhegow\Front\Core\Struct\Remote;
use Gzhegow\Front\Core\Store\FrontStore;
use Gzhegow\Front\Package\League\Plates\Template\Template;


interface FrontAssetResolverRemoteInterface
{
    /**
     * @return static
     */
    public function setStore(FrontStore $store);


    /**
     * @return array{
     *     input: string,
     *     remote: Remote,
     *     src: string,
     * }
     */
    public function resolve(string $input, Template $template) : array;
}
