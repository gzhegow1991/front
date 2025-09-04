<?php

namespace Gzhegow\Front\Core\AssetManager\LocalResolver;

use Gzhegow\Lib\Lib;
use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Exception\RuntimeException;


class FrontDefaultAssetLocalResolver extends AbstractFrontAssetLocalResolver
{
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
    ) : array
    {
        $theFs = Lib::fs();
        $thePhp = Lib::php();
        $theType = Lib::type();
        $theUrl = Lib::url();

        $keyNormalized = $theType->path_normalized($key)->orThrow();

        $srcFolder = null;
        $srcFolderRealpath = null;
        $srcFolderPublicPath = null;
        if ( '/' === $keyNormalized[0] ) {
            if ( null === $folderRoot ) {
                throw new RuntimeException(
                    [ 'The `folderRoot` is empty', $folderRoot ]
                );
            }

            if ( ! $folderRoot->hasPublicPath($folderRootPublicPath) ) {
                throw new RuntimeException(
                    [ 'The `folderRoot` has no `publicPath`', $folderRoot ]
                );
            }

            $srcFolder = $folderRoot;
            $srcFolderRealpath = $folderRoot->getDirectory();
            $srcFolderPublicPath = $folderRootPublicPath;

        } else {
            $split = explode('::', $keyNormalized, 2);

            if ( count($split) > 1 ) {
                [ $folderAlias, $keyNormalized ] = $split;

                if ( ! isset($this->frontStore->foldersByAlias[$folderAlias]) ) {
                    throw new RuntimeException(
                        [ 'The `folder` is not found by alias: ' . $folderAlias, $folderAlias ]
                    );
                }

                $folder = $this->frontStore->foldersByAlias[$folderAlias];

                if ( ! $folder->hasPublicPath($folderPublicPath) ) {
                    throw new RuntimeException(
                        [ 'The `folder` has no `publicPath`', $folderCurrent ]
                    );
                }

                $srcFolder = $this->frontStore->foldersByAlias[$folderAlias];
                $srcFolderRealpath = $srcFolder->getDirectory();
                $srcFolderPublicPath = $folderPublicPath;

            } else {
                if ( null === $folderCurrent ) {
                    throw new RuntimeException(
                        [ 'The `folderCurrent` is empty', $folderCurrent ]
                    );
                }

                if ( ! $folderCurrent->hasPublicPath($folderCurrentPublicPath) ) {
                    throw new RuntimeException(
                        [ 'The `folderCurrent` has no `publicPath`', $folderCurrent ]
                    );
                }

                $srcFolder = $folderCurrent;
                $srcFolderRealpath = $folderCurrent->getDirectory();
                $srcFolderPublicPath = $folderCurrentPublicPath;

                if ( null !== $directoryCurrent ) {
                    $directoryCurrentRealpath = $theType->dirpath_realpath($directoryCurrent)->orThrow();

                    if ( 0 !== strpos($directoryCurrentRealpath, $srcFolderRealpath) ) {
                        throw new RuntimeException(
                            [
                                'The `directoryCurrent` is outside `folderCurrent`',
                                //
                                $directoryCurrent,
                                $folderCurrent,
                            ]
                        );
                    }

                    $srcRealSubpath = str_replace($srcFolderRealpath, '', $directoryCurrentRealpath);
                    $srcPublicSubpath = $thePhp->path_normalize($srcRealSubpath);

                    $srcFolderRealpath = $theFs->path_join([ $srcFolderRealpath, $srcRealSubpath ]);
                    $srcFolderPublicPath = $thePhp->path_join([ $srcFolderPublicPath, $srcPublicSubpath ]);
                }
            }
        }

        $srcFile = $theFs->path_join([ $srcFolderRealpath, $keyNormalized ]);

        $pi = $thePhp->pathinfo(
            $srcFile,
            null, null,
            0
            | _PHP_PATHINFO_DIRNAME
            | _PHP_PATHINFO_EXTENSION
            | _PHP_PATHINFO_FNAME
        );
        $srcDir = $pi['dirname'];
        $srcExtension = $pi['extension'];
        $srcFname = $pi['fname'];

        $srcFileList = [];
        $srcRealpathNew = null;

        if ( isset($this->frontStore->assetExtensionsMap[$srcExtension]) ) {
            foreach ( $this->frontStore->assetExtensionsMap[$srcExtension] as $extTo => $bool ) {
                $srcFileExtTo = $thePhp->path_join([ $srcDir, "{$srcFname}.{$extTo}" ]);
                $srcFileList[$srcFileExtTo] = true;

                $srcRealpathExtTo = realpath($srcFileExtTo);
                if ( false === $srcRealpathExtTo ) {
                    continue;
                }

                $srcRealpathNew = $srcRealpathExtTo;

                break;
            }
        }

        if ( null === $srcRealpathNew ) {
            $srcFileList[$srcFile] = true;

            $srcRealpath = realpath($srcFile);
            if ( false === $srcRealpath ) {
                throw new RuntimeException(
                    [
                        ''
                        . 'The `file` is not found: '
                        . '[ ' . implode(' ][ ', array_keys($srcFileList)) . ' ]',
                        //
                        $srcFileList,
                    ]
                );
            }

            $srcRealpathNew = $srcRealpath;
        }

        $src = $thePhp->path_join([ $srcFolderPublicPath, $keyNormalized ]);
        $srcVersion = null
            ?? $this->frontStore->assetVersion
            ?? filemtime($srcRealpathNew)
            ?: null;

        $srcUri = $src;
        if ( null !== $srcVersion ) {
            $srcUri = $theUrl->uri($src, [ 'v' => $srcVersion ]);
        }

        $resolved = [
            'key'      => $keyNormalized,
            'folder'   => $srcFolder,
            'realpath' => $srcRealpathNew,
            'src'      => $src,
            'version'  => $srcVersion,
            'uri'      => $srcUri,
        ];

        return $resolved;
    }
}
