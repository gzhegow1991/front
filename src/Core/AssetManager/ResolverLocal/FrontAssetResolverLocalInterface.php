<?php

namespace Gzhegow\Front\Core\AssetManager\ResolverLocal;

use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Core\Store\FrontStore;
use Gzhegow\Front\Package\League\Plates\Template\Template;


interface FrontAssetResolverLocalInterface
{
    /**
     * @return static
     */
    public function setStore(FrontStore $store);


    /**
     * @return array{
     *     input: string,
     *     folder: Folder,
     *     realpath: string,
     *     src: string,
     * }
     */
    public function resolve(string $input, Template $template) : array;
}
