<?php

namespace Gzhegow\Front\Core\AssetManager\LocalSrcResolver;

use Gzhegow\Front\Core\Store\FrontStore;
use Gzhegow\Front\Core\Struct\Folder;


interface FrontAssetLocalSrcResolverInterface
{
    /**
     * @return static
     */
    public function setStore(FrontStore $store);


    public function resolve(string $src, ?Folder $folderRoot = null, ?Folder $folderCurrent = null, ?string $directoryCurrent = null) : string;
}
