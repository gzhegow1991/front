<?php

namespace Gzhegow\Front;

use Gzhegow\Front\Core\Struct\Remote;
use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Core\Store\FrontStore;
use Gzhegow\Front\Core\TemplateResolver\FrontTemplateResolverInterface;
use Gzhegow\Front\Package\League\Plates\EngineInterface as PlatesEngineInterface;
use Gzhegow\Front\Core\AssetManager\LocalResolver\FrontAssetLocalResolverInterface;
use Gzhegow\Front\Core\AssetManager\RemoteResolver\FrontAssetRemoteResolverInterface;
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
    public function getFolders() : array;

    public function getFolder(int $id) : Folder;

    public function getFolderByAlias(string $alias) : Folder;

    public function getFolderByDirectory(string $directory) : Folder;

    /**
     * @param Folder|array $folder
     */
    public function folderAdd($folder) : int;


    /**
     * @return Remote[]
     */
    public function getRemotes() : array;

    public function getRemote(int $id) : Remote;

    public function getRemoteByAlias(string $alias) : Remote;

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
    public function templateResolver($templateResolver = null) : ?FrontTemplateResolverInterface;


    public function templateExists($name) : bool;

    public function templateName($name) : string;

    public function templateDir($name) : string;

    public function templateFolder($name) : Folder;

    public function templatePath($name) : string;

    public function templateRelpath($name) : string;


    /**
     * @param string|false|null $langCurrent
     */
    public function templateLangCurrent($langCurrent) : ?string;

    /**
     * @param string|false|null $langDefault
     */
    public function templateLangDefault($langDefault) : ?string;


    public function make($name, array $data = []) : PlatesTemplateInterface;

    public function render($name, array $data = []) : string;


    /**
     * @param callable|false|null $fnTemplateGetItem
     */
    public function fnTemplateGetItem($fnTemplateGetItem = null) : ?callable;

    /**
     * @param callable|false|null $fnTemplateCatchError
     */
    public function fnTemplateCatchError($fnTemplateCatchError = null) : ?callable;


    /**
     * @param FrontAssetLocalResolverInterface|false|null $assetLocalResolver
     */
    public function assetLocalResolver($assetLocalResolver = null) : ?FrontAssetLocalResolverInterface;

    /**
     * @param FrontAssetRemoteResolverInterface|false|null $assetRemoteSrcResolver
     */
    public function assetRemoteSrcResolver($assetRemoteSrcResolver = null) : ?FrontAssetRemoteResolverInterface;
}
