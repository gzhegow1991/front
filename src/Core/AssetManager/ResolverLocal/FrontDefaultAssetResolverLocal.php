<?php

namespace Gzhegow\Front\Core\AssetManager\ResolverLocal;

use Gzhegow\Lib\Lib;
use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Exception\RuntimeException;
use Gzhegow\Front\Package\League\Plates\Template\Template;


class FrontDefaultAssetResolverLocal extends AbstractFrontAssetResolverLocal
{
    /**
     * @return array{
     *     input: string,
     *     folder: Folder,
     *     realpath: string,
     *     src: string,
     * }
     */
    public function resolve(string $input, Template $template) : array
    {
        $theFs = Lib::fs();
        $thePhp = Lib::php();
        $theType = Lib::type();

        $inputNormalized = $theType->path_normalized($input)->orThrow();

        if ( '/' === $inputNormalized[0] ) {
            $folderRoot = $template->folderRoot();

            if ( ! $folderRoot->hasPublicPath($folderRootPublicPath) ) {
                throw new RuntimeException(
                    [ 'The `folderRoot` has no `publicPath`', $folderRoot ]
                );
            }

            $srcFolder = $folderRoot;
            $srcFolderRealpath = $folderRoot->getDirectory();
            $srcFolderPublicPath = $folderRoot->getPublicPath();

        } else {
            $split = explode('::', $inputNormalized, 2);

            if ( count($split) > 1 ) {
                [ $folderAlias, $inputNormalized ] = $split;

                if ( ! isset($this->frontStore->foldersByAlias[$folderAlias]) ) {
                    throw new RuntimeException(
                        [ 'The `folder` is not found by alias: ' . $folderAlias, $folderAlias ]
                    );
                }

                $folder = $this->frontStore->foldersByAlias[$folderAlias];

                if ( ! $folder->hasPublicPath($folderPublicPath) ) {
                    throw new RuntimeException(
                        [ 'The `folder` has no `publicPath`', $folder ]
                    );
                }

                $srcFolder = $this->frontStore->foldersByAlias[$folderAlias];
                $srcFolderRealpath = $srcFolder->getDirectory();
                $srcFolderPublicPath = $folderPublicPath;

            } else {
                $directoryCurrent = $template->dir();
                $directoryCurrentRealpath = $theType->dirpath_realpath($directoryCurrent)->orThrow();

                $folderCurrent = $template->folder();

                if ( ! $folderCurrent->hasPublicPath($folderCurrentPublicPath) ) {
                    throw new RuntimeException(
                        [ 'The `folderCurrent` has no `publicPath`', $folderCurrent ]
                    );
                }

                $srcFolder = $folderCurrent;
                $srcFolderRealpath = $folderCurrent->getDirectory();
                $srcFolderPublicPath = $folderCurrent->getPublicPath();

                $srcRealSubpath = str_replace($srcFolderRealpath, '', $directoryCurrentRealpath);
                $srcPublicSubpath = $thePhp->path_normalize($srcRealSubpath);

                $srcFolderRealpath = $theFs->path_join([ $srcFolderRealpath, $srcRealSubpath ]);
                $srcFolderPublicPath = $thePhp->path_join([ $srcFolderPublicPath, $srcPublicSubpath ]);
            }
        }

        $srcInput = $inputNormalized;
        $srcFile = $theFs->path_join([ $srcFolderRealpath, $srcInput ]);

        $srcInputDir = dirname($srcInput);

        $pi = $thePhp->pathinfo(
            $srcFile,
            null, null,
            0
            | _PHP_PATHINFO_DIRNAME
            | _PHP_PATHINFO_EXTENSIONS
            | _PHP_PATHINFO_FNAME
        );
        $srcFileDir = $pi['dirname'];
        $srcFileFname = $pi['fname'];
        $srcFileExtensions = $pi['extensions'];

        $srcFileList = [];

        $srcRealpathNew = null;
        $srcNew = null;

        $map = $this->frontStore->assetExtensionsMap[$srcFileExtensions] ?? [];
        if ( [] !== $map ) {
            foreach ( $map as $extTo => $bool ) {
                $srcInputCurrent = $thePhp->path_join([ $srcInputDir, "{$srcFileFname}.{$extTo}" ]);
                $srcFileCurrent = $thePhp->path_join([ $srcFileDir, "{$srcFileFname}.{$extTo}" ]);

                $srcFileList[$srcFileCurrent] = true;

                $srcRealpathCurrent = realpath($srcFileCurrent);
                if ( false !== $srcRealpathCurrent ) {
                    $srcRealpathNew = $srcRealpathCurrent;
                    $srcNew = $thePhp->path_join([ $srcFolderPublicPath, $srcInputCurrent ]);

                    break;
                }
            }
        }

        if ( null === $srcNew ) {
            $srcInputCurrent = $srcInput;
            $srcFileCurrent = $srcFile;

            $srcFileList[$srcFile] = true;

            $srcRealpathCurrent = realpath($srcFileCurrent);
            if ( false !== $srcRealpathCurrent ) {
                $srcRealpathNew = $srcRealpathCurrent;
                $srcNew = $thePhp->path_join([ $srcFolderPublicPath, $srcInputCurrent ]);
            }
        }

        if ( false === $srcRealpathNew ) {
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

        $resolved = [
            'input'    => $inputNormalized,
            'folder'   => $srcFolder,
            'realpath' => $srcRealpathNew,
            'src'      => $srcNew,
        ];

        return $resolved;
    }
}
