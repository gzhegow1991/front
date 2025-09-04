<?php

namespace Gzhegow\Front\Core\AssetManager\LocalResolver;

use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Core\Store\FrontStore;


interface FrontAssetLocalResolverInterface
{
    /**
     * @return static
     */
    public function setStore(FrontStore $store);


    /**
     * @return array{
     *     key: string,
     *     folder: Folder,
     *     realpath: string,
     *     src: string,
     *     version: string,
     *     uri: string,
     * }
     */
    public function resolve(
        string $key,
        ?string $directoryCurrent = null,
        ?Folder $folderRoot = null, ?Folder $folderCurrent = null
    ) : array;
}
