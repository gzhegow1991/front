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


    public function fileExtensionGet() : string;

    public function directoryGet() : string;

    public function publicPathGet() : ?string;


    /**
     * @return array<string, Folder>
     */
    public function folders() : array;

    public function folderGet(string $alias) : Folder;

    public function folderByDirectoryGet(string $directory) : Folder;

    /**
     * @param Folder|array $folder
     *
     * @return static
     */
    public function folderAdd($folder);


    /**
     * @return array<string, Remote>
     */
    public function remotes() : array;

    public function remoteGet(string $alias) : Remote;

    /**
     * @param Remote|array $remote
     *
     * @return static
     */
    public function remoteAdd($remote);


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


    public function assetResolverLocalSet(?FrontAssetResolverLocalInterface $assetLocalResolver = null) : FrontAssetResolverLocalInterface;

    public function assetResolverRemoteSet(?FrontAssetResolverRemoteInterface $assetRemoteResolver = null) : FrontAssetResolverRemoteInterface;


    public function make($name, array $data = []) : PlatesTemplateInterface;

    public function render($name, array $data = []) : string;
}
