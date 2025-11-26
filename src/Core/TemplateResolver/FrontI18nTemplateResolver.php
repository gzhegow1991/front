<?php

namespace Gzhegow\Front\Core\TemplateResolver;

use Gzhegow\Lib\Lib;
use League\Plates\Template\Name;
use Gzhegow\Front\Exception\Runtime\TemplateNotFoundException;


class FrontI18nTemplateResolver extends AbstractFrontTemplateResolver
{
    public function resolve(Name $name) : string
    {
        $theFs = Lib::fs();
        $theStr = Lib::str();

        $fileExtension = $name->getEngine()->getFileExtension();

        $folderPathAbsolute = $name->getFolder()->getPath();

        $templatePathAbsoluteMain = $name->getPath();

        $piTemplatePathAbsoluteMain = $theFs->pathinfo($templatePathAbsoluteMain);

        $dirname = $piTemplatePathAbsoluteMain['dirname'];

        $basename = $piTemplatePathAbsoluteMain['basename'];
        $basename = $theStr->rcrop($basename, '.' . $fileExtension, false);
        $basename .= '.' . $fileExtension;

        $templatePathAbsoluteMain = $theFs->path_join([ $dirname, $basename ]);

        $pathes = [];

        $hasLangCurrent = (null !== $this->frontStore->templateLangCurrent);
        $hasLangDefault = (null !== $this->frontStore->templateLangDefault);
        if ( false
            || $hasLangCurrent
            || $hasLangDefault
        ) {
            if ( $hasLangCurrent ) {
                $langCurrent = $this->frontStore->templateLangCurrent;

                $absolutePathNew = $theFs->path_join([ $dirname, $langCurrent, $basename ]);
                $relativePathNew = $theFs->path_relative($absolutePathNew, $folderPathAbsolute);

                $pathes[$absolutePathNew] = $relativePathNew;
            }

            if ( $hasLangDefault ) {
                $langDefault = $this->frontStore->templateLangDefault;

                $absolutePathNew = $theFs->path_join([ $dirname, $langDefault, $basename ]);
                $relativePathNew = $theFs->path_relative($absolutePathNew, $folderPathAbsolute);

                $pathes[$absolutePathNew] = $relativePathNew;
            }
        }

        $absolutePathNew = $templatePathAbsoluteMain;
        $relativePathNew = $theFs->path_relative($absolutePathNew, $folderPathAbsolute);

        $pathes[$absolutePathNew] = $relativePathNew;

        while ( null !== ($path = key($pathes)) ) {
            if ( is_file($path) ) {
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
