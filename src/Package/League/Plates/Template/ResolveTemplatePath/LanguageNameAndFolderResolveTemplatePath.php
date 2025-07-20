<?php

namespace Gzhegow\Front\Package\League\Plates\Template\ResolveTemplatePath;

use Gzhegow\Lib\Lib;
use League\Plates\Template\Name;
use Gzhegow\Front\Store\FrontStore;
use Gzhegow\Front\Exception\Runtime\TemplateNotFoundException;
use Gzhegow\Front\Package\League\Plates\Template\ResolveTemplatePath;


class LanguageNameAndFolderResolveTemplatePath implements ResolveTemplatePath
{
    /**
     * @var FrontStore
     */
    protected $store;


    public function __construct(FrontStore $store)
    {
        $this->store = $store;
    }


    public function __invoke(Name $name) : string
    {
        $theFs = Lib::fs();
        $theStr = Lib::str();

        $fileExtension = $name->getEngine()->getFileExtension();

        $folderPathAbsolute = $name->getFolder()->getPath();

        $templatePathAbsoluteMain = $name->getPath();

        $pathes = [];

        $hasLangCurrent = false;
        $hasLangDefault = false;
        if (false
            || ($hasLangCurrent = (null !== $this->store->langCurrent))
            || ($hasLangDefault = (null !== $this->store->langDefault))
        ) {
            $pi = $theFs->pathinfo($templatePathAbsoluteMain);

            $dirname = $pi[ 'dirname' ];
            $basename = $pi[ 'basename' ];

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
