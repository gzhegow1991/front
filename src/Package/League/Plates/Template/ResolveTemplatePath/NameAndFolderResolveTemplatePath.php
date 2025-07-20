<?php

namespace Gzhegow\Front\Package\League\Plates\Template\ResolveTemplatePath;

use Gzhegow\Lib\Lib;
use League\Plates\Template\Name;
use Gzhegow\Front\Exception\Runtime\TemplateNotFoundException;
use Gzhegow\Front\Package\League\Plates\Template\ResolveTemplatePath;


/**
 * @see ResolveTemplatePathOriginal
 */
class NameAndFolderResolveTemplatePath implements ResolveTemplatePath
{
    public function __invoke(Name $name) : string
    {
        $theFs = Lib::fs();
        $theStr = Lib::str();

        $fileExtension = $name->getEngine()->getFileExtension();

        $folderPathAbsolute = $name->getFolder()->getPath();

        $templatePathAbsolute = $name->getPath();

        $absolutePathNew = $templatePathAbsolute;
        $relativePathNew = $theFs->path_relative($absolutePathNew, $folderPathAbsolute);

        $pathes = [];
        $pathes[ $absolutePathNew ] = $relativePathNew;

        while ( null !== ($path = key($pathes)) ) {
            $basename = basename($path);
            $basename = $theStr->rcrop($basename, '.' . $fileExtension, false);

            $dirname = dirname($path);
            $path = "{$dirname}/{$basename}.{$fileExtension}";

            if (is_file($path)) {
                return $path;
            }

            next($pathes);
        }

        throw new TemplateNotFoundException(
            [
                ''
                . 'Templates not found: '
                . '[ ' . implode(' ][ ', $pathes) . ' ]',
                //
                $pathes,
            ]
        );
    }
}
