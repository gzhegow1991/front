<?php

namespace Gzhegow\Front\Core\AssetManager;

use Gzhegow\Lib\Lib;
use Gzhegow\Front\FrontInterface;
use Gzhegow\Front\Core\Store\FrontStore;
use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Core\Struct\Remote;
use Gzhegow\Front\Exception\RuntimeException;
use Gzhegow\Front\Core\AssetManager\LocalSrcResolver\FrontAssetLocalSrcResolverInterface;
use Gzhegow\Front\Core\AssetManager\RemoteSrcResolver\FrontAssetRemoteSrcResolverInterface;


class FrontAssetManager implements FrontAssetManagerInterface
{
    /**
     * @var FrontStore
     */
    protected $frontStore;

    /**
     * @var FrontAssetLocalSrcResolverInterface
     */
    protected $localSrcResolver;
    /**
     * @var FrontAssetRemoteSrcResolverInterface
     */
    protected $remoteSrcResolver;


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
        if (! isset($this->frontStore->folders[ $id ])) {
            throw new RuntimeException(
                [ 'The `id` is missing: ' . $id, $id ]
            );
        }

        return $this->frontStore->folders[ $id ];
    }

    public function getFolderByAlias(string $alias) : Folder
    {
        $theType = Lib::type();

        $aliasString = $theType->string_not_empty($alias)->orThrow();

        if (! isset($this->frontStore->foldersByAlias[ $aliasString ])) {
            throw new RuntimeException(
                [ 'The `alias` is missing: ' . $alias, $alias ]
            );
        }

        $folder = $this->frontStore->foldersByAlias[ $aliasString ];

        return $folder;
    }

    public function getFolderByDirectory(string $directory) : Folder
    {
        $theType = Lib::type();

        $directoryRealpath = $theType->dirpath_realpath($directory)->orThrow();

        if (! isset($this->frontStore->foldersByDirectory[ $directoryRealpath ])) {
            throw new RuntimeException(
                [ 'The `directory` is missing: ' . $directory, $directory ]
            );
        }

        $folder = $this->frontStore->foldersByDirectory[ $directoryRealpath ];

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
        if (! isset($this->frontStore->remotes[ $id ])) {
            throw new RuntimeException(
                [ 'The `id` is missing: ' . $id, $id ]
            );
        }

        return $this->frontStore->remotes[ $id ];
    }

    public function getRemoteByAlias(string $alias) : Remote
    {
        $theType = Lib::type();

        $aliasString = $theType->string_not_empty($alias)->orThrow();

        if (! isset($this->frontStore->remotesByAlias[ $aliasString ])) {
            throw new RuntimeException(
                [ 'The `alias` is missing: ' . $alias, $alias ]
            );
        }

        $remote = $this->frontStore->remotesByAlias[ $aliasString ];

        return $remote;
    }


    /**
     * @param FrontAssetLocalSrcResolverInterface|false|null $localSrcResolver
     */
    public function localSrcResolver($localSrcResolver) : ?FrontAssetLocalSrcResolverInterface
    {
        $last = $this->localSrcResolver;

        if (null !== $localSrcResolver) {
            if (false === $localSrcResolver) {
                $localSrcResolver = null;

            } else {
                $localSrcResolver->setStore($this->frontStore);
            }
        }

        $this->localSrcResolver = $localSrcResolver;

        return $last;
    }

    public function localSrc(string $src, ?Folder $folderRoot = null, ?Folder $folderCurrent = null, ?string $directoryCurrent = null) : string
    {
        if (null === $this->localSrcResolver) {
            return $src;
        }

        return $this->localSrcResolver->resolve($src, $folderRoot, $folderCurrent, $directoryCurrent);
    }


    /**
     * @param FrontAssetRemoteSrcResolverInterface|false|null $remoteSrcResolver
     */
    public function remoteSrcResolver($remoteSrcResolver) : ?FrontAssetRemoteSrcResolverInterface
    {
        $last = $this->remoteSrcResolver;

        if (null !== $remoteSrcResolver) {
            if (false === $remoteSrcResolver) {
                $remoteSrcResolver = null;

            } else {
                $remoteSrcResolver->setStore($this->frontStore);
            }
        }

        $this->remoteSrcResolver = $remoteSrcResolver;

        return $last;
    }

    public function remoteSrc(string $src, ?Remote $remoteCurrent = null) : string
    {
        if (null === $this->remoteSrcResolver) {
            return $src;
        }

        return $this->remoteSrcResolver->resolve($src, $remoteCurrent);
    }
}
