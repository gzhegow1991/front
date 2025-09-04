<?php

namespace Gzhegow\Front\Core\AssetManager;

use Gzhegow\Front\FrontInterface;
use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Core\Struct\Remote;
use Gzhegow\Front\Core\AssetManager\LocalResolver\FrontAssetLocalResolverInterface;
use Gzhegow\Front\Core\AssetManager\RemoteResolver\FrontAssetRemoteResolverInterface;


interface FrontAssetManagerInterface
{
    public function initialize(FrontInterface $front) : void;


    /**
     * @param FrontAssetLocalResolverInterface|false|null $localResolver
     */
    public function localResolver($localResolver) : ?FrontAssetLocalResolverInterface;

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
    public function resolveLocal(
        string $key,
        ?string $directoryCurrent = null,
        ?Folder $folderRoot = null, ?Folder $folderCurrent = null
    ) : array;


    /**
     * @param FrontAssetRemoteResolverInterface|false|null $remoteResolver
     */
    public function remoteResolver($remoteResolver) : ?FrontAssetRemoteResolverInterface;

    /**
     * @return array{
     *     key: string,
     *     remote: Remote,
     *     src: string,
     *     version: string,
     *     uri: string,
     * }
     */
    public function resolveRemote(
        string $key,
        ?Remote $remoteCurrent = null
    ) : array;
}
