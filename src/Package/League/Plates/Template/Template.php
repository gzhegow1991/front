<?php

namespace Gzhegow\Front\Package\League\Plates\Template;

use Gzhegow\Lib\Lib;
use Gzhegow\Front\Store\FrontStore;
use League\Plates\Exception\TemplateNotFound;
use Gzhegow\Front\Exception\RuntimeException;
use Gzhegow\Front\Package\League\Plates\Engine;
use League\Plates\Template\Template as LeagueTemplate;
use Gzhegow\Front\Core\TagManager\FrontTagManagerInterface;


class Template extends LeagueTemplate implements TemplateInterface
{
    /**
     * @var FrontTagManagerInterface
     */
    protected $tagManager;

    /**
     * @var FrontStore
     */
    protected $store;

    /**
     * @var Engine
     */
    protected $engine;


    public function __construct(
        FrontTagManagerInterface $tagManager,
        //
        FrontStore $store,
        //
        Engine $engine,
        //
        $name
    )
    {
        $this->tagManager = $tagManager;

        $this->store = $store;

        parent::__construct($engine, $name);
    }


    public function name() : string
    {
        return $this->name->getName();
    }

    public function path() : string
    {
        try {
            return ($this->engine->getResolveTemplatePath())($this->name);
        }
        catch ( TemplateNotFound $e ) {
            return $e->paths()[ 0 ];
        }
    }

    public function dir() : string
    {
        return dirname($this->path());
    }


    public function fetch($name, ?array $data = null) : string
    {
        $dataTotal = $data ?? [];
        $dataTotal = $dataTotal + $this->data;

        $template = $this->engine->make($name, $dataTotal);
        $template->sections = $this->sections;

        try {
            $html = $template->render();
        }
        catch ( \Throwable $e ) {
            throw new RuntimeException($e);
        }

        return $html;
    }

    public function render(?array $data = null) : string
    {
        $dataBefore = $this->data($data);

        try {
            ob_start();

            /**
             * @noinspection PhpMethodParametersCountMismatchInspection
             */
            (function () {
                extract($this->data);

                include func_get_arg(0);
            })($this->path());

            $content = ob_get_clean();
        }
        catch ( \Throwable $e ) {
            ob_end_clean();

            $eMessage = $e->getMessage();

            $templateName = $this->name->getName();

            if (! $this->store->isDebug) {
                $content = "[ ERROR: {$templateName} / {$eMessage} ]";

            } else {
                $linesDebug = [];
                $linesDebug[] = "[ ERROR: {$templateName} ]";
                $linesDebug[] = $e->getMessage();
                $linesDebug[] = '';
                $linesDebug[] = $e->getFile() . ': ' . $e->getLine();

                $linesDebug = array_merge(
                    $linesDebug,
                    Lib::debugThrowabler()->getThrowableTraceLines($e)
                );

                $content = implode("\n", $linesDebug);
            }
        }

        $content = trim($content);

        $lines = explode("\n", $content);
        $lines = array_map('rtrim', $lines);
        foreach ( $lines as $i => $l ) {
            if ('' === $l) {
                unset($lines[ $i ]);
            }
        }

        $content = implode("\n", $lines) . "\n";

        if (isset($this->layoutName)) {
            $layout = $this->engine->make($this->layoutName);

            $layout->sections = []
                + [ 'content' => $content ]
                + $this->sections;

            $content = $layout->render($this->layoutData + $this->data);

            $content = trim($content);
        }

        $this->data = $dataBefore;

        return $content;
    }


    public function getData() : array
    {
        return $this->data;
    }

    /**
     * @return static
     */
    public function setData(?array $data = null)
    {
        $this->data = [];

        if (null !== $data) {
            $this->data = $data;
        }

        return $this;
    }

    /**
     * @see parent::data()
     */
    public function data(?array $data = null) : array
    {
        if (null !== $data) {
            $this->data = array_replace(
                $this->data,
                $data
            );
        }

        return $this->data;
    }


    public function content() : string
    {
        return $this->sections[ 'content' ] ?? '';
    }


    public function getTagManager() : FrontTagManagerInterface
    {
        return $this->tagManager;
    }

    public function tag(string $tag, $content, ?array $attributes = null) : string
    {
        $html = $this->tagManager->tag($tag, $content, $attributes);

        return $html;
    }

    public function tagAttributes(?array $attributes = null) : string
    {
        $html = $this->tagManager->attributes($attributes);

        return $html;
    }

    public function tagAttributeValueAlt($alt) : string
    {
        $html = $this->tagManager->attributeValueAlt($alt);

        return $html;
    }

    public function tagAttributeValueAltOrNull($alt) : ?string
    {
        $html = $this->tagManager->attributeValueAltOrNull($alt);

        return $html;
    }

    public function tagAttributeValueTitle($title) : string
    {
        $html = $this->tagManager->attributeValueTitle($title);

        return $html;
    }

    public function tagAttributeValueTitleOrNull($title) : ?string
    {
        $html = $this->tagManager->attributeValueTitleOrNull($title);

        return $html;
    }

    public function tagLinkSeo($content, ?string $url, ?string $title = null, ?array $attributes = null) : string
    {
        $html = $this->tagManager->linkSeo($content, $url, $title, $attributes);

        return $html;
    }

    public function tagLinkHref($content, ?string $url, ?string $title = null, ?array $attributes = null) : string
    {
        $html = $this->tagManager->linkHref($content, $url, $title, $attributes);

        return $html;
    }
}
