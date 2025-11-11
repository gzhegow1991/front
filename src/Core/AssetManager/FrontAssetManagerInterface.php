<?php

namespace Gzhegow\Front\Core\AssetManager;

use Gzhegow\Front\FrontInterface;
use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Core\Struct\Remote;
use Gzhegow\Front\Package\League\Plates\Template\Template;
use Gzhegow\Front\Core\AssetManager\ResolverLocal\FrontAssetResolverLocalInterface;
use Gzhegow\Front\Core\AssetManager\ResolverRemote\FrontAssetResolverRemoteInterface;


interface FrontAssetManagerInterface
{
    public function initialize(FrontInterface $front) : void;


    /**
     * @param FrontAssetResolverLocalInterface|false|null $resolverLocal
     */
    public function resolverLocalSet($resolverLocal) : ?FrontAssetResolverLocalInterface;

    /**
     * @return array{
     *     input: string,
     *     folder: Folder,
     *     realpath: string,
     *     src: string,
     *     version: string,
     *     uri: string,
     * }
     */
    public function resolveLocal(string $input, Template $template) : array;


    /**
     * @param FrontAssetResolverRemoteInterface|false|null $resolverRemote
     */
    public function resolverRemoteSet($resolverRemote) : ?FrontAssetResolverRemoteInterface;

    /**
     * @return array{
     *     input: string,
     *     remote: Remote,
     *     src: string,
     *     version: string,
     *     uri: string,
     * }
     */
    public function resolveRemote(string $input, Template $template) : array;
}
