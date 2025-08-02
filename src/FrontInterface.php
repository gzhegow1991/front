<?php

namespace Gzhegow\Front;

use Gzhegow\Front\Store\FrontStore;
use League\Plates\Template\Func as LeagueFunc;
use Gzhegow\Front\Core\Resolver\ResolverInterface;
use League\Plates\Template\Folders as LeagueFolders;
use League\Plates\Extension\ExtensionInterface as LeagueExtensionInterface;
use Gzhegow\Front\Package\League\Plates\Template\TemplateInterface as PlatesTemplateInterface;


interface FrontInterface
{
    public function getStore() : FrontStore;


    public function resolverGet() : ?ResolverInterface;

    public function resolverSet(?ResolverInterface $resolver) : ?ResolverInterface;


    public function directoryGet() : string;

    /**
     * @return static
     */
    public function directorySet($directory);


    public function fileExtensionGet() : string;

    /**
     * @return static
     */
    public function fileExtensionSet($fileExtension);


    public function folderGetAll() : LeagueFolders;

    /**
     * @return static
     */
    public function folderAdd($name, $directory, $fallback = false);

    /**
     * @return static
     */
    public function folderRemove($name);


    public function dataGet($template = null);

    /**
     * @return static
     */
    public function dataAdd(array $data, $templates = null);


    public function functionExists($name, ?LeagueFunc &$func = null) : bool;

    public function functionGet($name) : LeagueFunc;


    /**
     * @return static
     */
    public function functionRegister($name, $callback);

    /**
     * @return static
     */
    public function functionDrop($name);


    /**
     * @return static
     */
    public function extensionLoadAll(array $extensions = []);

    /**
     * @return static
     */
    public function extensionLoad(LeagueExtensionInterface $extension);


    public function templateExists($name) : bool;

    public function templatePath($name) : string;

    public function templateDir($name) : string;

    public function templateName($name) : string;


    public function make($name, array $data = []) : PlatesTemplateInterface;

    public function render($name, array $data = []) : string;


    public function langCurrentSet(?string $langCurrent) : ?string;

    public function langDefaultSet(?string $langDefault) : ?string;
}
