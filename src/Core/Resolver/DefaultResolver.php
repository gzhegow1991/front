<?php

namespace Gzhegow\Front\Core\Resolver;

use Gzhegow\Lib\Lib;
use League\Plates\Template\Name;
use Gzhegow\Front\Exception\Runtime\TemplateNotFoundException;


class DefaultResolver extends AbstractResolver
{
    public function resolve(Name $name) : string
    {
        $theFs = Lib::fs();
        $theStr = Lib::str();

        $fileExtension = $name->getEngine()->getFileExtension();

        $folderPathAbsolute = $name->getFolder()->getPath();

        $templatePathAbsoluteMain = $name->getPath();

        $piTemplatePathAbsoluteMain = $theFs->pathinfo($templatePathAbsoluteMain);

        $dirname = $piTemplatePathAbsoluteMain[ 'dirname' ];

        $basename = $piTemplatePathAbsoluteMain[ 'basename' ];
        $basename = $theStr->rcrop($basename, '.' . $fileExtension, false);
        $basename .= '.' . $fileExtension;

        $absolutePathNew = "{$dirname}/{$basename}";
        $relativePathNew = $theFs->path_relative($absolutePathNew, $folderPathAbsolute);

        $pathes = [];
        $pathes[ $absolutePathNew ] = $relativePathNew;

        while ( null !== ($path = key($pathes)) ) {
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
