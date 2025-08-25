<?php

namespace Gzhegow\Front\Package\League\Plates\Template;

use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Core\Struct\Remote;
use Gzhegow\Front\Core\TagManager\FrontTagManagerInterface;
use Gzhegow\Front\Core\AssetManager\FrontAssetManagerInterface;


interface TemplateInterface
{
    public function name() : string;

    public function dir() : string;

    public function folder() : Folder;

    public function path() : string;

    public function relpath() : string;


    /**
     * @template-covariant T of mixed
     *
     * @param class-string<T>|null $classT
     *
     * @return T
     */
    public function get(string $name, ?string $classT = null);


    public function render(?array $data = null) : string;

    public function fetch($name, ?array $data = null) : string;


    public function getData() : array;

    /**
     * @return static
     */
    public function setData(?array $data = null);

    public function data(?array $data = null) : array;


    public function content() : string;


    public function getAssetManager() : FrontAssetManagerInterface;

    public function assetLocalSrc(string $src, ?Folder $folderRoot = null, ?Folder $folderCurrent = null, ?string $directoryCurrent = null) : string;

    public function assetRemoteSrc(string $src, ?Remote $remoteCurrent = null) : string;


    public function getTagManager() : FrontTagManagerInterface;

    public function tag(string $tag, $content, ?array $attributes = null) : string;

    public function tagAttributes(?array $attributes = null) : string;

    public function tagAttrAlt($alt) : string;

    public function tagAttrAltOrNull($alt) : ?string;

    public function tagAttrTitle($title) : string;

    public function tagAttrTitleOrNull($title) : ?string;

    public function tagLinkSeo($content, ?string $url, ?string $title = null, ?array $attributes = null) : string;

    public function tagLinkHref($content, ?string $url, ?string $title = null, ?array $attributes = null) : string;
}
