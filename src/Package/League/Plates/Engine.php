<?php

namespace Gzhegow\Front\Package\League\Plates;

use Gzhegow\Front\Core\Store\FrontStore;
use League\Plates\Engine as LeagueEngine;
use Gzhegow\Front\FrontFactoryInterface;
use Gzhegow\Front\Exception\RuntimeException;
use League\Plates\Template\ResolveTemplatePath;
use Gzhegow\Front\Core\TagManager\FrontTagManagerInterface;
use Gzhegow\Front\Core\AssetManager\FrontAssetManagerInterface;
use Gzhegow\Front\Package\League\Plates\Template\TemplateInterface;
use Gzhegow\Front\Core\TemplateResolver\FrontTemplateResolverInterface;
use League\Plates\Template\ResolveTemplatePath\NameAndFolderResolveTemplatePath;
use Gzhegow\Front\Package\League\Plates\Template\ResolveTemplatePath\TemplateResolverResolveTemplatePath;


class Engine extends LeagueEngine implements EngineInterface
{
    /**
     * @var FrontFactoryInterface
     */
    protected $factory;

    /**
     * @var FrontAssetManagerInterface
     */
    protected $assetManager;
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

    /**
     * @var callable|null
     */
    protected $fnTemplateGetItem;
    /**
     * @var callable|null
     */
    protected $fnTemplateCatchError;


    public function __construct(
        FrontFactoryInterface $factory,
        //
        FrontAssetManagerInterface $assetManager,
        FrontTagManagerInterface $tagManager,
        //
        FrontStore $store,
        //
        string $directory, ?string $fileExtension = null
    )
    {
        $fileExtension = $fileExtension ?? 'phtml';

        $this->factory = $factory;

        $this->assetManager = $assetManager;
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
            $this->assetManager,
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


    /**
     * @param callable|false|null $fnTemplateGetItem
     */
    public function fnTemplateGetItem($fnTemplateGetItem = null) : ?callable
    {
        $last = $this->fnTemplateGetItem;

        if (null !== $fnTemplateGetItem) {
            if (false === $fnTemplateGetItem) {
                $fnTemplateGetItem = null;
            }
        }

        $this->fnTemplateGetItem = $fnTemplateGetItem;

        return $last;
    }

    /**
     * @param callable|false|null $fnTemplateCatchError
     */
    public function fnTemplateCatchError($fnTemplateCatchError = null) : ?callable
    {
        $last = $this->fnTemplateCatchError;

        if (null !== $fnTemplateCatchError) {
            if (false === $fnTemplateCatchError) {
                $fnTemplateCatchError = null;
            }
        }

        $this->fnTemplateCatchError = $fnTemplateCatchError;

        return $last;
    }
}
