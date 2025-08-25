<?php

namespace Gzhegow\Front\Core\AssetManager\LocalSrcResolver;

use Gzhegow\Lib\Lib;
use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Exception\RuntimeException;


class FrontDefaultAssetLocalSrcResolver extends AbstractFrontAssetLocalSrcResolver
{
    public function resolve(
        string $src,
        ?Folder $folderRoot = null,
        ?Folder $folderCurrent = null, ?string $directoryCurrent = null
    ) : string
    {
        $theFs = Lib::fs();
        $thePhp = Lib::php();
        $theType = Lib::type();
        $theUrl = Lib::url();

        $srcNormalized = $theType->path_normalized($src)->orThrow();

        $srcNormalized = ltrim($srcNormalized, '/');
        $srcNormalizedDirname = $thePhp->dirname($srcNormalized);

        if ('/' === $srcNormalized[ 0 ]) {
            if (null === $folderRoot) {
                throw new RuntimeException(
                    [ 'The `folderRoot` is empty', $folderRoot ]
                );
            }

            if (! $folderRoot->hasPublicPath($publicPathString)) {
                throw new RuntimeException(
                    [ 'The `folderRoot` has no `publicPath`', $folderRoot ]
                );
            }

            $directoryRealpath = $folderRoot->getDirectory();

        } else {
            $split = explode('::', $srcNormalized, 2);

            if (count($split) > 1) {
                [ $srcAlias, $srcNormalized ] = $split;

                [
                    $directoryRealpath,
                    $publicPathString,
                ] = $this->frontStore->foldersByAlias[ $srcAlias ];

            } else {
                if (null === $folderCurrent) {
                    throw new RuntimeException(
                        [ 'The `folderCurrent` is empty', $folderCurrent ]
                    );
                }

                if (! $folderCurrent->hasPublicPath($publicPathString)) {
                    throw new RuntimeException(
                        [ 'The `folderCurrent` has no `publicPath`', $folderCurrent ]
                    );
                }

                $directoryRealpath = $folderCurrent->getDirectory();

                if (null !== $directoryCurrent) {
                    $directoryCurrentRealpath = $theType->dirpath_realpath($directoryCurrent)->orThrow();

                    if (0 !== strpos($directoryCurrentRealpath, $directoryRealpath)) {
                        throw new RuntimeException(
                            [
                                'The `directoryCurrent` is outside `folderCurrent`',
                                //
                                $directoryCurrent,
                                $folderCurrent,
                            ]
                        );
                    }

                    $subDirectoryRealpath = str_replace(
                        $directoryRealpath, '',
                        $directoryCurrentRealpath
                    );

                    $subPublicPathString = $thePhp->path_normalize($subDirectoryRealpath);

                    $directoryRealpath = $theFs->path_join([ $directoryRealpath, $subDirectoryRealpath ]);
                    $publicPathString = $thePhp->path_join([ $publicPathString, $subPublicPathString ]);
                }
            }
        }

        $filePath = "{$directoryRealpath}/{$srcNormalized}";

        $pi = $thePhp->pathinfo($filePath);
        $fileDirname = $pi[ 'dirname' ];
        $fileExtension = $pi[ 'extension' ];
        $fileFname = $pi[ 'fname' ];

        $files = [];
        $filePathNew = null;
        $srcNormalizedNew = null;
        if (isset($this->frontStore->assetExtensionsMap[ $fileExtension ])) {
            foreach ( $this->frontStore->assetExtensionsMap[ $fileExtension ] as $extTo => $bool ) {
                $filePathCurrent = $theFs->path_join([ $fileDirname, "{$fileFname}.{$extTo}" ]);
                $files[ $filePathCurrent ] = true;

                if (is_file($filePathCurrent)) {
                    $filePathCurrent = realpath($filePathCurrent);

                    $filePathNew = $filePathCurrent;
                    $srcNormalizedNew = $thePhp->path_join([ $srcNormalizedDirname, "{$fileFname}.{$extTo}" ]);

                    break;
                }
            }
        }

        if (null === $filePathNew) {
            throw new RuntimeException(
                [
                    ''
                    . 'The `file` is not found: '
                    . '[ ' . implode(' ][ ', array_keys($files)) . ' ]',
                    //
                    $files,
                ]
            );
        }

        $srcPath = "{$publicPathString}/{$srcNormalizedNew}";
        $srcVersion = $this->frontStore->assetVersion ?? filemtime($filePathNew);

        $srcPath = $theUrl->uri($srcPath, [ 'v' => $srcVersion ]);

        return $srcPath;
    }
}
