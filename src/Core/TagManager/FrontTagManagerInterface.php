<?php

namespace Gzhegow\Front\Core\TagManager;

use Gzhegow\Front\FrontInterface;


interface FrontTagManagerInterface
{
    public function initialize(FrontInterface $front) : void;


    public function tag(string $tag, $content, ?array $attributes = null) : string;


    /**
     * @param string|string[] $content
     * @param string|true     $url
     */
    public function tagAHref($content, $url, ?string $title = null, ?array $attributes = null) : string;

    /**
     * @param string|string[] $content
     * @param string|true     $url
     */
    public function tagAButton($content, $url, ?string $title = null, ?array $attributes = null) : string;

    /**
     * @param string|true $url
     */
    public function tagImg($url, ?string $alt = null, ?array $attributes = null) : string;


    public function attributes(?array $attributes = null) : string;


    public function attrAlt($alt) : string;

    public function attrAltOrNull($alt) : ?string;

    public function attrTitle($title) : string;

    public function attrTitleOrNull($title) : ?string;


    public function linkHref($content, $url = true, ?string $title = null, ?array $attributes = null) : string;

    public function linkSeo($content, $url = true, ?string $title = null, ?array $attributes = null) : string;
}
