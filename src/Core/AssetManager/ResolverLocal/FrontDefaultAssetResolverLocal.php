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
            $srcFolderPublicPath = $folderRootPublicPath;

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
                $srcFolderPublicPath = $folderCurrentPublicPath;

                $srcRealSubpath = str_replace($srcFolderRealpath, '', $directoryCurrentRealpath);
                $srcPublicSubpath = $thePhp->path_normalize($srcRealSubpath);

                $srcFolderRealpath = $theFs->path_join([ $srcFolderRealpath, $srcRealSubpath ]);
                $srcFolderPublicPath = $thePhp->path_join([ $srcFolderPublicPath, $srcPublicSubpath ]);
            }
        }

        $srcFile = $theFs->path_join([ $srcFolderRealpath, $inputNormalized ]);

        $pi = $thePhp->pathinfo(
            $srcFile,
            null, null,
            0
            | _PHP_PATHINFO_DIRNAME
            | _PHP_PATHINFO_EXTENSIONS
            | _PHP_PATHINFO_FNAME
        );
        $srcDir = $pi['dirname'];
        $srcFname = $pi['fname'];
        $srcExtensions = $pi['extensions'];

        $srcFileList = [];
        $srcRealpathNew = null;

        if ( isset($this->frontStore->assetExtensionsMap[$srcExtensions]) ) {
            foreach ( $this->frontStore->assetExtensionsMap[$srcExtensions] as $extTo => $bool ) {
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

        $src = $thePhp->path_join([ $srcFolderPublicPath, $inputNormalized ]);

        $resolved = [
            'key'      => $inputNormalized,
            'folder'   => $srcFolder,
            'realpath' => $srcRealpathNew,
            'src'      => $src,
        ];

        return $resolved;
    }
}
