<?php

namespace Gzhegow\Front;


use Gzhegow\Front\Store\FrontStore;
use Gzhegow\Front\Core\TagManager\FrontTagManagerInterface;
use Gzhegow\Front\Package\League\Plates\EngineInterface as PlatesEngineInterface;
use Gzhegow\Front\Package\League\Plates\Template\TemplateInterface as PlatesTemplateInterface;


interface FrontFactoryInterface
{
    public function newStore() : FrontStore;


    public function newPlatesEngine(
        FrontTagManagerInterface $tagManager,
        //
        FrontStore $store,
        //
        string $directory,
        ?string $fileExtension = null
    ) : PlatesEngineInterface;

    public function newPlatesTemplate(
        FrontTagManagerInterface $tagManager,
        //
        FrontStore $store,
        //
        PlatesEngineInterface $plates,
        //
        $name
    ) : PlatesTemplateInterface;
}
