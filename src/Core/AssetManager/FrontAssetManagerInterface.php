<?php

namespace Gzhegow\Front\Core\AssetManager;

use Gzhegow\Front\FrontInterface;
use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Core\Struct\Remote;
use Gzhegow\Front\Core\AssetManager\LocalSrcResolver\FrontAssetLocalSrcResolverInterface;
use Gzhegow\Front\Core\AssetManager\RemoteSrcResolver\FrontAssetRemoteSrcResolverInterface;


interface FrontAssetManagerInterface
{
    public function initialize(FrontInterface $front) : void;


    /**
     * @param FrontAssetLocalSrcResolverInterface|false|null $localSrcResolver
     */
    public function localSrcResolver($localSrcResolver) : ?FrontAssetLocalSrcResolverInterface;

    public function localSrc(string $src, ?Folder $folderRoot = null, ?Folder $folderCurrent = null, ?string $directoryCurrent = null) : string;


    /**
     * @param FrontAssetRemoteSrcResolverInterface|false|null $remoteSrcResolver
     */
    public function remoteSrcResolver($remoteSrcResolver) : ?FrontAssetRemoteSrcResolverInterface;

    public function remoteSrc(string $src, ?Remote $remoteCurrent = null) : string;
}
