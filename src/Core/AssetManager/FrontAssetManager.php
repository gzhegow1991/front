<?php

namespace Gzhegow\Front\Core\AssetManager;

use Gzhegow\Lib\Lib;
use Gzhegow\Front\FrontInterface;
use Gzhegow\Front\Core\Store\FrontStore;
use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Core\Struct\Remote;
use Gzhegow\Front\Exception\RuntimeException;
use Gzhegow\Front\Core\AssetManager\LocalResolver\FrontAssetLocalResolverInterface;
use Gzhegow\Front\Core\AssetManager\RemoteResolver\FrontAssetRemoteResolverInterface;


class FrontAssetManager implements FrontAssetManagerInterface
{
    /**
     * @var FrontStore
     */
    protected $frontStore;

    /**
     * @var FrontAssetLocalResolverInterface
     */
    protected $localResolver;
    /**
     * @var FrontAssetRemoteResolverInterface
     */
    protected $remoteResolver;

    /**
     * @var array<string, array>
     */
    protected $cacheLocal = [];
    /**
     * @var array<string, array>
     */
    protected $cacheRemote = [];


    public function initialize(FrontInterface $front) : void
    {
        $this->frontStore = $front->getStore();
    }


    public function directoryGet() : string
    {
        return $this->frontStore->directory;
    }

    public function fileExtensionGet() : string
    {
        return $this->frontStore->fileExtension;
    }

    public function publicPathGet() : ?string
    {
        return $this->frontStore->publicPath;
    }


    /**
     * @return Folder[]
     */
    public function getFolders() : array
    {
        return $this->frontStore->folders;
    }

    public function getFolder(int $id) : Folder
    {
        if ( ! isset($this->frontStore->folders[$id]) ) {
            throw new RuntimeException(
                [ 'The `id` is missing: ' . $id, $id ]
            );
        }

        return $this->frontStore->folders[$id];
    }

    public function getFolderByAlias(string $alias) : Folder
    {
        $theType = Lib::type();

        $aliasString = $theType->string_not_empty($alias)->orThrow();

        if ( ! isset($this->frontStore->foldersByAlias[$aliasString]) ) {
            throw new RuntimeException(
                [ 'The `alias` is missing: ' . $alias, $alias ]
            );
        }

        $folder = $this->frontStore->foldersByAlias[$aliasString];

        return $folder;
    }

    public function getFolderByDirectory(string $directory) : Folder
    {
        $theType = Lib::type();

        $directoryRealpath = $theType->dirpath_realpath($directory)->orThrow();

        if ( ! isset($this->frontStore->foldersByDirectory[$directoryRealpath]) ) {
            throw new RuntimeException(
                [ 'The `directory` is missing: ' . $directory, $directory ]
            );
        }

        $folder = $this->frontStore->foldersByDirectory[$directoryRealpath];

        return $folder;
    }


    /**
     * @return Remote[]
     */
    public function getRemotes() : array
    {
        return $this->frontStore->remotes;
    }

    public function getRemote(int $id) : Remote
    {
        if ( ! isset($this->frontStore->remotes[$id]) ) {
            throw new RuntimeException(
                [ 'The `id` is missing: ' . $id, $id ]
            );
        }

        return $this->frontStore->remotes[$id];
    }

    public function getRemoteByAlias(string $alias) : Remote
    {
        $theType = Lib::type();

        $aliasString = $theType->string_not_empty($alias)->orThrow();

        if ( ! isset($this->frontStore->remotesByAlias[$aliasString]) ) {
            throw new RuntimeException(
                [ 'The `alias` is missing: ' . $alias, $alias ]
            );
        }

        $remote = $this->frontStore->remotesByAlias[$aliasString];

        return $remote;
    }


    /**
     * @param FrontAssetLocalResolverInterface|false|null $localResolver
     */
    public function localResolver($localResolver) : ?FrontAssetLocalResolverInterface
    {
        $last = $this->localResolver;

        if ( null !== $localResolver ) {
            if ( false === $localResolver ) {
                $localResolver = null;

            } else {
                $localResolver->setStore($this->frontStore);
            }
        }

        $this->localResolver = $localResolver;

        return $last;
    }

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
    ) : array
    {
        if ( ! isset($this->cacheLocal[$key]) ) {
            if ( null === $this->localResolver ) {
                $src = $key;
                $srcVersion = $this->frontStore->assetVersion ?? null;
                $srcUri = $key;
                if ( null !== $srcVersion ) {
                    $theUrl = Lib::url();

                    $srcUri = $theUrl->uri($src, [ 'v' => $srcVersion ]);
                }

                $this->cacheLocal[$key] = [
                    'key'      => $key,
                    'folder'   => null,
                    'realpath' => null,
                    'src'      => $src,
                    'version'  => $srcVersion,
                    'uri'      => $srcUri,
                ];

            } else {
                $this->cacheLocal[$key] = $this->localResolver->resolve(
                    $key,
                    $directoryCurrent,
                    $folderRoot, $folderCurrent,
                );
            }
        }

        return $this->cacheLocal[$key];
    }


    /**
     * @param FrontAssetRemoteResolverInterface|false|null $remoteResolver
     */
    public function remoteResolver($remoteResolver) : ?FrontAssetRemoteResolverInterface
    {
        $last = $this->remoteResolver;

        if ( null !== $remoteResolver ) {
            if ( false === $remoteResolver ) {
                $remoteResolver = null;

            } else {
                $remoteResolver->setStore($this->frontStore);
            }
        }

        $this->remoteResolver = $remoteResolver;

        return $last;
    }

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
    ) : array
    {
        if ( ! isset($this->cacheRemote[$key]) ) {
            if ( null === $this->remoteResolver ) {
                $src = $key;
                $srcVersion = $this->frontStore->assetVersion ?? null;
                $srcUri = $key;
                if ( null !== $srcVersion ) {
                    $theUrl = Lib::url();

                    $srcUri = $theUrl->uri($src, [ 'v' => $srcVersion ]);
                }

                $this->cacheRemote[$key] = [
                    'key'      => $key,
                    'folder'   => null,
                    'realpath' => null,
                    'src'      => $src,
                    'version'  => $srcVersion,
                    'uri'      => $srcUri,
                ];

            } else {
                $this->cacheRemote[$key] = $this->remoteResolver->resolve(
                    $key,
                    $remoteCurrent
                );
            }
        }

        return $this->cacheRemote[$key];
    }
}
