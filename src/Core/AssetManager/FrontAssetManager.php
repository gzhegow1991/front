<?php

namespace Gzhegow\Front\Core\AssetManager;

use Gzhegow\Lib\Lib;
use Gzhegow\Front\FrontInterface;
use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Core\Struct\Remote;
use Gzhegow\Front\Core\Store\FrontStore;
use Gzhegow\Front\Exception\RuntimeException;
use Gzhegow\Front\Package\League\Plates\Template\Template;
use Gzhegow\Front\Core\AssetManager\ResolverLocal\FrontDefaultAssetResolverLocal;
use Gzhegow\Front\Core\AssetManager\ResolverLocal\FrontAssetResolverLocalInterface;
use Gzhegow\Front\Core\AssetManager\ResolverRemote\FrontDefaultAssetResolverRemote;
use Gzhegow\Front\Core\AssetManager\ResolverRemote\FrontAssetResolverRemoteInterface;


class FrontAssetManager implements FrontAssetManagerInterface
{
    /**
     * @var FrontStore
     */
    protected $frontStore;

    /**
     * @var FrontAssetResolverLocalInterface
     */
    protected $localResolver;
    /**
     * @var FrontAssetResolverRemoteInterface
     */
    protected $remoteResolver;

    /**
     * @var array<string, array>
     */
    protected $cacheMemoryLocal = [];
    /**
     * @var array<string, array>
     */
    protected $cacheMemoryRemote = [];


    public function __construct()
    {
        $this->localResolver = new FrontDefaultAssetResolverLocal();
        $this->remoteResolver = new FrontDefaultAssetResolverRemote();
    }

    public function initialize(FrontInterface $front) : void
    {
        $this->frontStore = $front->getStore();

        $this->localResolver->setStore($this->frontStore);
        $this->remoteResolver->setStore($this->frontStore);
    }


    public function resolverLocalSet(?FrontAssetResolverLocalInterface $resolverLocal) : FrontAssetResolverLocalInterface
    {
        $resolverLocal = $resolverLocal ?? new FrontDefaultAssetResolverLocal();

        $last = $this->localResolver;

        $resolverLocal->setStore($this->frontStore);

        $this->localResolver = $resolverLocal;

        return $last;
    }

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
    public function resolveLocal(string $input, Template $template) : array
    {
        $templatePath = $template->path();

        $cacheKey = "{$templatePath}\0{$input}";

        if ( ! isset($this->cacheMemoryLocal[$cacheKey]) ) {
            $resolved = $this->localResolver->resolve($input, $template);

            if ( null === $resolved['realpath'] ) {
                throw new RuntimeException(
                    [ 'Asset not found: ' . $input, $resolved ]
                );
            }

            $srcVersion = null;
            if ( true === $this->frontStore->assetLocalVersion ) {
                $srcVersion = filemtime($resolved['realpath']);

            } else {
                $theType = Lib::type();

                if ( $theType->string_not_empty($this->frontStore->assetLocalVersion)->isOk([ &$string ]) ) {
                    $srcVersion = $string;
                }
            }

            $src = $srcUri = $resolved['src'];

            if ( null !== $srcVersion ) {
                $theUrl = Lib::url();

                $srcUri = $theUrl->uri($src, [ 'v' => $srcVersion ]);
            }

            $resolved['version'] = $srcVersion;
            $resolved['uri'] = $srcUri;

            $this->cacheMemoryLocal[$cacheKey] = $resolved;
        }

        return $this->cacheMemoryLocal[$cacheKey];
    }


    public function resolverRemoteSet(?FrontAssetResolverRemoteInterface $resolverRemote) : FrontAssetResolverRemoteInterface
    {
        $resolverRemote = $resolverRemote ?? new FrontDefaultAssetResolverRemote();

        $last = $this->remoteResolver;

        $resolverRemote->setStore($this->frontStore);

        $this->remoteResolver = $resolverRemote;

        return $last;
    }

    /**
     * @return array{
     *     input: string,
     *     remote: Remote,
     *     src: string,
     *     version: string,
     *     uri: string,
     * }
     */
    public function resolveRemote(string $input, Template $template) : array
    {
        if ( ! isset($this->cacheMemoryRemote[$input]) ) {
            $theType = Lib::type();

            $resolved = $this->remoteResolver->resolve($input, $template);

            $srcVersion = null;
            if ( $theType->string_not_empty($this->frontStore->assetRemoteVersion)->isOk([ &$string ]) ) {
                $srcVersion = $string;
            }

            $src = $srcUri = $resolved['src'];

            if ( null !== $srcVersion ) {
                $theUrl = Lib::url();

                $srcUri = $theUrl->uri($src, [ 'v' => $srcVersion ]);
            }

            $resolved['version'] = $srcVersion;
            $resolved['uri'] = $srcUri;

            $this->cacheMemoryRemote[$input] = $resolved;
        }

        return $this->cacheMemoryRemote[$input];
    }
}
