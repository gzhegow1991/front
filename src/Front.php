<?php

namespace Gzhegow\Front;

use Gzhegow\Front\Store\FrontStore;
use League\Plates\Template\Func as LeagueFunc;
use League\Plates\Template\Folders as LeagueFolders;
use League\Plates\Extension\ExtensionInterface as LeagueExtensionInterface;
use League\Plates\Template\ResolveTemplatePath as LeagueResolveTemplatePath;
use Gzhegow\Front\Package\League\Plates\Template\TemplateInterface as PlatesTemplateInterface;


class Front
{
    public function getStore() : FrontStore
    {
        return static::$facade->getStore();
    }


    public function resolverGet() : LeagueResolveTemplatePath
    {
        return static::$facade->resolverGet();
    }

    /**
     * @return FrontInterface
     */
    public function resolverSet(LeagueResolveTemplatePath $resolver)
    {
        return static::$facade->resolverSet($resolver);
    }


    public function directoryGet() : string
    {
        return static::$facade->directoryGet();
    }

    /**
     * @return FrontInterface
     */
    public function directorySet($directory)
    {
        return static::$facade->directorySet($directory);
    }


    public function fileExtensionGet() : string
    {
        return static::$facade->fileExtensionGet();
    }

    /**
     * @return FrontInterface
     */
    public function fileExtensionSet($fileExtension)
    {
        return static::$facade->fileExtensionSet($fileExtension);
    }


    public function folderGetAll() : LeagueFolders
    {
        return static::$facade->folderGetAll();
    }

    /**
     * @return FrontInterface
     */
    public function folderAdd($name, $directory, $fallback = false)
    {
        return static::$facade->folderAdd($name, $directory, $fallback);
    }

    /**
     * @return FrontInterface
     */
    public function folderRemove($name)
    {
        return static::$facade->folderRemove($name);
    }


    public function dataGet($template = null)
    {
        return static::$facade->dataGet($template);
    }

    /**
     * @return FrontInterface
     */
    public function dataAdd(array $data, $templates = null)
    {
        return static::$facade->dataAdd($data, $templates);
    }


    public function functionExists($name, ?LeagueFunc &$func = null) : bool
    {
        return static::$facade->functionExists($name, $func);
    }

    public function functionGet($name) : LeagueFunc
    {
        return static::$facade->functionGet($name);
    }


    /**
     * @return FrontInterface
     */
    public function functionRegister($name, $callback)
    {
        return static::$facade->functionRegister($name, $callback);
    }

    /**
     * @return FrontInterface
     */
    public function functionDrop($name)
    {
        return static::$facade->functionDrop($name);
    }


    /**
     * @return FrontInterface
     */
    public function extensionLoadAll(array $extensions = [])
    {
        return static::$facade->extensionLoadAll($extensions);
    }

    /**
     * @return FrontInterface
     */
    public function extensionLoad(LeagueExtensionInterface $extension)
    {
        return static::$facade->extensionLoad($extension);
    }


    public function templateExists($name) : bool
    {
        return static::$facade->templateExists($name);
    }

    public function templatePath($name) : string
    {
        return static::$facade->templatePath($name);
    }


    public function make($name, array $data = []) : PlatesTemplateInterface
    {
        return static::$facade->make($name, $data);
    }

    public function render($name, array $data = []) : string
    {
        return static::$facade->make($name, $data);
    }


    public function langCurrentSet(?string $langCurrent) : ?string
    {
        return static::$facade->langCurrentSet($langCurrent);
    }

    public function langDefaultSet(?string $langDefault) : ?string
    {
        return static::$facade->langDefaultSet($langDefault);
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
