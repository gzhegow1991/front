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

    /**
     * @return array{
     *     key: string,
     *     folder: Folder,
     *     realpath: string,
     *     src: string,
     *     version: string,
     *     uri: string,
     * }
     */
    public function assetLocal(
        string $key,
        ?string $directoryCurrent = null,
        ?Folder $folderRoot = null, ?Folder $folderCurrent = null
    ) : array;

    /**
     * @return array{
     *     key: string,
     *     remote: Remote,
     *     src: string,
     *     version: string,
     *     uri: string,
     * }
     */
    public function assetRemote(
        string $key,
        ?Remote $remoteCurrent = null
    ) : array;

    public function assetLocalUri(
        string $key,
        ?string $directoryCurrent = null,
        ?Folder $folderRoot = null, ?Folder $folderCurrent = null
    ) : string;

    public function assetRemoteUri(
        string $key,
        ?Remote $remoteCurrent = null
    ) : string;


    public function getTagManager() : FrontTagManagerInterface;

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

    public function tagAttributes(array $attributes = []) : string;

    public function tagAttrAlt($alt) : string;

    public function tagAttrAltOrNull($alt) : ?string;

    public function tagAttrTitle($title) : string;

    public function tagAttrTitleOrNull($title) : ?string;

    public function tagLinkButton($content, string $url, $title = null, array $attributes = []) : string;

    public function tagLinkHref($content, string $url, $title = null, array $attributes = []) : string;

    public function tagLinkSeo($content, string $url, $title = null, array $attributes = []) : string;
}
