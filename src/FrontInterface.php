<?php

namespace Gzhegow\Front;

use Gzhegow\Front\Core\Struct\Remote;
use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Core\Store\FrontStore;
use Gzhegow\Front\Core\TemplateResolver\FrontTemplateResolverInterface;
use Gzhegow\Front\Package\League\Plates\EngineInterface as PlatesEngineInterface;
use Gzhegow\Front\Core\AssetManager\ResolverLocal\FrontAssetResolverLocalInterface;
use Gzhegow\Front\Core\AssetManager\ResolverRemote\FrontAssetResolverRemoteInterface;
use Gzhegow\Front\Package\League\Plates\Template\TemplateInterface as PlatesTemplateInterface;


interface FrontInterface
{
    public function getEngine() : PlatesEngineInterface;


    public function getStore() : FrontStore;


    public function directoryGet() : string;

    public function fileExtensionGet() : string;

    public function publicPathGet() : ?string;


    /**
     * @return Folder[]
     */
    public function folders() : array;

    public function folderGet(int $id) : Folder;

    public function folderByAliasGet(string $alias) : Folder;

    public function folderByDirectoryGet(string $directory) : Folder;

    /**
     * @param Folder|array $folder
     */
    public function folderAdd($folder) : int;


    /**
     * @return Remote[]
     */
    public function remotes() : array;

    public function remoteGet(int $id) : Remote;

    public function remoteByAliasGet(string $alias) : Remote;

    /**
     * @param Remote|array $remote
     */
    public function remoteAdd($remote) : int;


    public function dataGet($template = null);

    /**
     * @return static
     */
    public function dataAdd(array $data, $templates = null);


    /**
     * @param FrontTemplateResolverInterface|false|null $templateResolver
     */
    public function templateResolverSet($templateResolver = null) : ?FrontTemplateResolverInterface;


    public function templateExists($name) : bool;

    public function templateName($name) : string;

    public function templateDir($name) : string;

    public function templateFolder($name) : Folder;

    public function templatePath($name) : string;

    public function templateRelpath($name) : string;


    /**
     * @param string|false|null $langCurrent
     */
    public function templateLangCurrentSet($langCurrent) : ?string;

    /**
     * @param string|false|null $langDefault
     */
    public function templateLangDefaultSet($langDefault) : ?string;


    /**
     * @param callable|false|null $fnTemplateGetItem
     */
    public function fnTemplateGetItem($fnTemplateGetItem = null) : ?callable;

    /**
     * @param callable|false|null $fnTemplateCatchError
     */
    public function fnTemplateCatchError($fnTemplateCatchError = null) : ?callable;


    /**
     * @param FrontAssetResolverLocalInterface|false|null $assetLocalResolver
     */
    public function assetResolverLocalSet($assetLocalResolver = null) : ?FrontAssetResolverLocalInterface;

    /**
     * @param FrontAssetResolverRemoteInterface|false|null $assetRemoteResolver
     */
    public function assetResolverRemoteSet($assetRemoteResolver = null) : ?FrontAssetResolverRemoteInterface;


    public function make($name, array $data = []) : PlatesTemplateInterface;

    public function render($name, array $data = []) : string;
}
