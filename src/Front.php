<?php

namespace Gzhegow\Front;

use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Core\Struct\Remote;
use Gzhegow\Front\Core\Store\FrontStore;
use Gzhegow\Front\Core\TemplateResolver\FrontTemplateResolverInterface;
use Gzhegow\Front\Package\League\Plates\EngineInterface as PlatesEngineInterface;
use Gzhegow\Front\Core\AssetManager\LocalResolver\FrontAssetLocalResolverInterface;
use Gzhegow\Front\Core\AssetManager\RemoteResolver\FrontAssetRemoteResolverInterface;
use Gzhegow\Front\Package\League\Plates\Template\TemplateInterface as PlatesTemplateInterface;


class Front
{
    const ROOT_FOLDER_ALIAS = '@root';


    public static function getEngine() : PlatesEngineInterface
    {
        return static::$facade->getEngine();
    }


    public static function getStore() : FrontStore
    {
        return static::$facade->getStore();
    }


    public static function directoryGet() : string
    {
        return static::$facade->directoryGet();
    }

    public static function fileExtensionGet() : string
    {
        return static::$facade->fileExtensionGet();
    }

    public static function publicPathGet() : ?string
    {
        return static::$facade->publicPathGet();
    }


    public static function getFolders() : array
    {
        return static::$facade->getFolders();
    }

    public static function getFolder(int $id) : Folder
    {
        return static::$facade->getFolder($id);
    }

    public static function getFolderByAlias(string $alias) : Folder
    {
        return static::$facade->getFolderByAlias($alias);
    }

    public static function getFolderByDirectory(string $directory) : Folder
    {
        return static::$facade->getFolderByDirectory($directory);
    }

    public static function folderAdd($folder) : int
    {
        return static::$facade->folderAdd($folder);
    }


    public static function getRemotes() : array
    {
        return static::$facade->getRemotes();
    }

    public static function getRemote(int $id) : Remote
    {
        return static::$facade->getRemote($id);
    }

    public static function getRemoteByAlias(string $alias) : Remote
    {
        return static::$facade->getRemoteByAlias($alias);
    }

    public static function remoteAdd($remote) : int
    {
        return static::$facade->remoteAdd($remote);
    }


    public static function dataGet($template = null)
    {
        return static::$facade->dataGet($template);
    }

    public static function dataAdd(array $data, $templates = null)
    {
        return static::$facade->dataAdd($data, $templates);
    }


    public static function templateResolver($templateResolver) : ?FrontTemplateResolverInterface
    {
        return static::$facade->templateResolver($templateResolver);
    }


    public static function templateExists($name) : bool
    {
        return static::$facade->templateExists($name);
    }

    public static function templateName($name) : string
    {
        return static::$facade->templateName($name);
    }

    public static function templateDir($name) : string
    {
        return static::$facade->templateDir($name);
    }

    public static function templateFolder($name) : Folder
    {
        return static::$facade->templateFolder($name);
    }

    public static function templatePath($name) : string
    {
        return static::$facade->templatePath($name);
    }

    public static function templateRelpath($name) : string
    {
        return static::$facade->templateRelpath($name);
    }


    public static function templateLangCurrentSet($langCurrent) : ?string
    {
        return static::$facade->templateLangCurrent($langCurrent);
    }

    public static function templateLangDefaultSet($langDefault) : ?string
    {
        return static::$facade->templateLangDefault($langDefault);
    }


    public static function make($name, array $data = []) : PlatesTemplateInterface
    {
        return static::$facade->make($name, $data);
    }

    public static function render($name, array $data = []) : string
    {
        return static::$facade->render($name, $data);
    }


    /**
     * @param callable|false|null $fnTemplateGetItem
     */
    public static function fnTemplateGetItem($fnTemplateGetItem = null) : ?callable
    {
        return static::$facade->fnTemplateGetItem($fnTemplateGetItem);
    }

    /**
     * @param callable|false|null $fnTemplateCatchError
     */
    public static function fnTemplateCatchError($fnTemplateCatchError = null) : ?callable
    {
        return static::$facade->fnTemplateCatchError($fnTemplateCatchError);
    }


    public static function assetLocalResolver($assetLocalResolver) : ?FrontAssetLocalResolverInterface
    {
        return static::$facade->assetLocalResolver($assetLocalResolver);
    }

    public static function assetRemoteResolver($assetRemoteResolver) : ?FrontAssetRemoteResolverInterface
    {
        return static::$facade->assetRemoteResolver($assetRemoteResolver);
    }


    public static function setFacade(?FrontInterface $facade) : ?FrontInterface
    {
        $last = static::$facade;

        static::$facade = $facade;

        return $last;
    }

    /**
     * @var FrontInterface
     */
    protected static $facade;
}
