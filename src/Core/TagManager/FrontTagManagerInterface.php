<?php

namespace Gzhegow\Front\Core\TagManager;

use Gzhegow\Front\FrontInterface;


interface FrontTagManagerInterface
{
    public function initialize(FrontInterface $front) : void;


    public function tag(string $tag, $content, array $attributes = []) : string;


    /**
     * @param string|string[] $content
     */
    public function tagAButton($content, string $url, $title = null, array $attributes = []) : string;

    /**
     * @param string|string[] $content
     */
    public function tagAHref($content, string $url, $title = null, array $attributes = []) : string;


    public function tagImg(string $src, $alt, array $attributes = []) : string;


    public function attributes(array $attributes = []) : string;


    public function attrAlt($alt) : string;

    public function attrAltOrNull($alt) : ?string;

    public function attrTitle($title) : string;

    public function attrTitleOrNull($title) : ?string;


    public function linkButton($content, string $url, $title = null, array $attributes = []) : string;

    public function linkHref($content, string $url, $title = null, array $attributes = []) : string;

    public function linkSeo($content, string $url, $title = null, array $attributes = []) : string;
}
