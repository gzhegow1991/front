<?php

namespace Gzhegow\Front\Core\TagManager;

interface FrontTagManagerInterface
{
    public function tag(string $tag, $content, ?array $attributes = null) : string;


    public function attributes(?array $attributes = null) : string;

    public function attributeValueAlt($alt) : string;

    public function attributeValueAltOrNull($alt) : ?string;

    public function attributeValueTitle($title) : string;

    public function attributeValueTitleOrNull($title) : ?string;


    public function linkSeo($content, $url = true, ?string $title = null, ?array $attributes = null) : string;

    public function linkHref($content, $url = true, ?string $title = null, ?array $attributes = null) : string;
}
