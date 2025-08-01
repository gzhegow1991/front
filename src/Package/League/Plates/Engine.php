<?php

namespace Gzhegow\Front\Package\League\Plates;

use Gzhegow\Front\Store\FrontStore;
use League\Plates\Engine as LeagueEngine;
use Gzhegow\Front\FrontFactoryInterface;
use Gzhegow\Front\Exception\RuntimeException;
use League\Plates\Template\ResolveTemplatePath;
use Gzhegow\Front\Core\TagManager\FrontTagManagerInterface;
use Gzhegow\Front\Package\League\Plates\Template\TemplateInterface;
use League\Plates\Template\ResolveTemplatePath\NameAndFolderResolveTemplatePath;


class Engine extends LeagueEngine implements EngineInterface
{
    /**
     * @var FrontFactoryInterface
     */
    protected $factory;
    /**
     * @var FrontTagManagerInterface
     */
    protected $tagManager;
    /**
     * @var FrontStore
     */
    protected $store;

    /**
     * @var ResolveTemplatePath
     */
    protected $resolveTemplatePath;


    public function __construct(
        FrontFactoryInterface $factory,
        //
        FrontTagManagerInterface $tagManager,
        //
        FrontStore $store,
        //
        string $directory, ?string $fileExtension = null
    )
    {
        $fileExtension = $fileExtension ?? 'phtml';

        $this->factory = $factory;

        $this->tagManager = $tagManager;

        $this->store = $store;

        $this->resolveTemplatePath = new NameAndFolderResolveTemplatePath();

        parent::__construct($directory, $fileExtension);
    }


    public function getResolveTemplatePath() : ResolveTemplatePath
    {
        return $this->resolveTemplatePath;
    }

    /**
     * @return static
     */
    public function setResolveTemplatePath(ResolveTemplatePath $resolveTemplatePath)
    {
        $this->resolveTemplatePath = $resolveTemplatePath;

        return $this;
    }

    /**
     * @return static
     */
    public function unsetResolveTemplatePath()
    {
        $this->resolveTemplatePath = new NameAndFolderResolveTemplatePath();

        return $this;
    }


    public function make($name, array $data = []) : TemplateInterface
    {
        $template = $this->factory->newPlatesTemplate(
            $this->tagManager,
            //
            $this->store,
            //
            $this,
            //
            $name
        );

        $template->data($data);

        return $template;
    }

    public function render($name, array $data = []) : string
    {
        $template = $this->make($name, $data);

        try {
            $html = $template->render();
        }
        catch ( \Throwable $e ) {
            throw new RuntimeException($e);
        }

        return $html;
    }
}
