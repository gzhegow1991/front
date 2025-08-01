<?php

namespace Gzhegow\Front;

use Gzhegow\Front\Store\FrontStore;
use Gzhegow\Front\Core\TagManager\FrontTagManagerInterface;
use Gzhegow\Front\Package\League\Plates\Engine as PlatesEngine;
use Gzhegow\Front\Package\League\Plates\Template\Template as PlatesTemplate;
use Gzhegow\Front\Package\League\Plates\EngineInterface as PlatesEngineInterface;
use Gzhegow\Front\Package\League\Plates\Template\TemplateInterface as PlatesTemplateInterface;


class FrontFactory implements FrontFactoryInterface
{
    public function newStore() : FrontStore
    {
        return new FrontStore();
    }


    public function newPlatesEngine(
        FrontTagManagerInterface $tagManager,
        //
        FrontStore $store,
        //
        string $directory,
        ?string $fileExtension = null
    ) : PlatesEngineInterface
    {
        return new PlatesEngine(
            $this,
            //
            $tagManager,
            //
            $store,
            //
            $directory, $fileExtension
        );
    }

    public function newPlatesTemplate(
        FrontTagManagerInterface $tagManager,
        //
        FrontStore $store,
        //
        PlatesEngineInterface $plates,
        //
        $name
    ) : PlatesTemplateInterface
    {
        return new PlatesTemplate(
            $tagManager,
            //
            $store,
            //
            $plates,
            //
            $name
        );
    }
}
