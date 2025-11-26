<?php

namespace Gzhegow\Front;

use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Core\Struct\Remote;
use Gzhegow\Front\Core\Store\FrontStore;
use Gzhegow\Front\Core\TemplateResolver\FrontTemplateResolverInterface;
use Gzhegow\Front\Package\League\Plates\EngineInterface as PlatesEngineInterface;
use Gzhegow\Front\Core\AssetManager\ResolverLocal\FrontAssetResolverLocalInterface;
use Gzhegow\Front\Core\AssetManager\ResolverRemote\FrontAssetResolverRemoteInterface;
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


    public static function fileExtensionGet() : string
    {
        return static::$facade->fileExtensionGet();
    }


    public static function directoryGet() : string
    {
        return static::$facade->directoryGet();
    }

    public static function publicPathGet() : ?string
    {
        return static::$facade->publicPathGet();
    }


    /**
     * @return array<string, Folder>
     */
    public static function folders() : array
    {
        return static::$facade->folders();
    }

    public static function folderGet(string $alias) : Folder
    {
        return static::$facade->folderGet($alias);
    }

    public static function folderByDirectoryGet(string $directory) : Folder
    {
        return static::$facade->folderByDirectoryGet($directory);
    }

    /**
     * @param Folder|array $folder
     *
     * @return FrontInterface
     */
    public static function folderAdd($folder)
    {
        return static::$facade->folderAdd($folder);
    }


    /**
     * @return array<string, Remote>
     */
    public static function remotes() : array
    {
        return static::$facade->remotes();
    }

    public static function remoteGet(string $alias) : Remote
    {
        return static::$facade->remoteGet($alias);
    }

    /**
     * @param Remote|array $remote
     *
     * @return FrontInterface
     */
    public static function remoteAdd($remote)
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


    public static function templateResolverSet($templateResolver) : ?FrontTemplateResolverInterface
    {
        return static::$facade->templateResolverSet($templateResolver);
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

    public static function templatePath($name) : string
    {
        return static::$facade->templatePath($name);
    }

    public static function templateRelpath($name) : string
    {
        return static::$facade->templateRelpath($name);
    }

    public static function templateFolder($name) : Folder
    {
        return static::$facade->templateFolder($name);
    }


    public static function templateLangCurrentSet($langCurrent) : ?string
    {
        return static::$facade->templateLangCurrentSet($langCurrent);
    }

    public static function templateLangDefaultSet($langDefault) : ?string
    {
        return static::$facade->templateLangDefaultSet($langDefault);
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


    public static function assetResolverLocalSet($assetResolverLocal) : ?FrontAssetResolverLocalInterface
    {
        return static::$facade->assetResolverLocalSet($assetResolverLocal);
    }

    public static function assetResolverRemoteSet($assetResolverRemote) : ?FrontAssetResolverRemoteInterface
    {
        return static::$facade->assetResolverRemoteSet($assetResolverRemote);
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
