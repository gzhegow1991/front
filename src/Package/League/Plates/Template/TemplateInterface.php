<?php

namespace Gzhegow\Front\Package\League\Plates\Template;

use Gzhegow\Front\Core\TagManager\FrontTagManagerInterface;


interface TemplateInterface
{
    public function name() : string;

    public function path() : string;

    public function dir() : string;


    /**
     * @template-covariant T of mixed
     *
     * @param class-string<T>|null $classT
     *
     * @return T
     */
    public function get(string $name, ?string $classT = null);


    public function fetch($name, ?array $data = null) : string;

    public function render(?array $data = null) : string;


    public function getData() : array;

    /**
     * @return static
     */
    public function setData(?array $data = null);

    public function data(?array $data = null) : array;


    public function content() : string;


    public function getTagManager() : FrontTagManagerInterface;

    public function tag(string $tag, $content, ?array $attributes = null) : string;

    public function tagAttributes(?array $attributes = null) : string;

    public function tagAttributeValueAlt($alt) : string;

    public function tagAttributeValueAltOrNull($alt) : ?string;

    public function tagAttributeValueTitle($title) : string;

    public function tagAttributeValueTitleOrNull($title) : ?string;

    public function tagLinkSeo($content, ?string $url, ?string $title = null, ?array $attributes = null) : string;

    public function tagLinkHref($content, ?string $url, ?string $title = null, ?array $attributes = null) : string;
}
