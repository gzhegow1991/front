<?php

namespace Gzhegow\Front\Core\Resolver;

use Gzhegow\Lib\Lib;
use League\Plates\Template\Name;
use Gzhegow\Front\Exception\Runtime\TemplateNotFoundException;


class FrontI18nResolver extends AbstractFrontResolver
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

        $templatePathAbsoluteMain = "{$dirname}/{$basename}";

        $pathes = [];

        $hasLangCurrent = false;
        $hasLangDefault = false;
        if (false
            || ($hasLangCurrent = (null !== $this->store->langCurrent))
            || ($hasLangDefault = (null !== $this->store->langDefault))
        ) {
            if ($hasLangCurrent) {
                $langCurrent = $this->store->langCurrent;

                $absolutePathNew = "{$dirname}/{$langCurrent}/{$basename}";
                $relativePathNew = $theFs->path_relative($absolutePathNew, $folderPathAbsolute);

                $pathes[ $absolutePathNew ] = $relativePathNew;
            }

            if ($hasLangDefault) {
                $langDefault = $this->store->langDefault;

                $absolutePathNew = "{$dirname}/{$langDefault}/{$basename}";
                $relativePathNew = $theFs->path_relative($absolutePathNew, $folderPathAbsolute);

                $pathes[ $absolutePathNew ] = $relativePathNew;
            }
        }

        $absolutePathNew = $templatePathAbsoluteMain;
        $relativePathNew = $theFs->path_relative($absolutePathNew, $folderPathAbsolute);

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
