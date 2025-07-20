<?php

namespace Gzhegow\Front;

use Gzhegow\Front\Store\FrontStore;
use Gzhegow\Front\Core\Config\FrontConfig;
use Gzhegow\Front\Exception\LogicException;
use League\Plates\Template\Func as LeagueFunc;
use League\Plates\Template\Folders as LeagueFolders;
use Gzhegow\Front\Core\TagManager\FrontTagManagerInterface;
use League\Plates\Extension\ExtensionInterface as LeagueExtensionInterface;
use League\Plates\Template\ResolveTemplatePath as LeagueResolveTemplatePath;
use Gzhegow\Front\Package\League\Plates\EngineInterface as PlatesEngineInterface;
use Gzhegow\Front\Package\League\Plates\Template\TemplateInterface as PlatesTemplateInterface;


class FrontFacade implements FrontInterface
{
    /**
     * @var FrontFactoryInterface
     */
    protected $factory;

    /**
     * @var FrontTagManagerInterface
     */
    protected $tagManager;

    /**
     * @var FrontConfig
     */
    protected $config;

    /**
     * @var PlatesEngineInterface
     */
    protected $engine;

    /**
     * @var FrontStore
     */
    protected $store;


    public function __construct(
        FrontFactoryInterface $factory,
        //
        FrontTagManagerInterface $tagManager,
        //
        FrontConfig $config
    )
    {
        $this->factory = $factory;

        $this->tagManager = $tagManager;

        $this->config = $config;
        $this->config->validate();

        $this->store = $this->factory->newStore();
        $this->store->isDebug = $this->config->isDebug;
        $this->store->langCurrent = $this->config->langCurrent;
        $this->store->langDefault = $this->config->langDefault;

        $this->engine = $this->factory->newPlatesEngine(
            $this->tagManager,
            //
            $this->store,
            //
            $this->config->directory, $this->config->fileExtension
        );
    }


    public function getStore() : FrontStore
    {
        return $this->store;
    }


    public function resolverGet() : LeagueResolveTemplatePath
    {
        return $this->engine->getResolveTemplatePath();
    }

    /**
     * @return static
     */
    public function resolverSet(LeagueResolveTemplatePath $resolver)
    {
        $this->engine->setResolveTemplatePath($resolver);

        return $this;
    }


    public function directoryGet() : string
    {
        return $this->engine->getDirectory();
    }

    /**
     * @return static
     */
    public function directorySet($directory)
    {
        $this->engine->setDirectory($directory);

        return $this;
    }


    public function fileExtensionGet() : string
    {
        return $this->engine->getFileExtension();
    }

    /**
     * @return static
     */
    public function fileExtensionSet($fileExtension)
    {
        $this->engine->setFileExtension($fileExtension);

        return $this;
    }


    public function folderGetAll() : LeagueFolders
    {
        return $this->engine->getFolders();
    }

    /**
     * @return static
     */
    public function folderAdd($name, $directory, $fallback = false)
    {
        $this->engine->addFolder($name, $directory, $fallback);

        return $this;
    }

    /**
     * @return static
     */
    public function folderRemove($name)
    {
        $this->engine->removeFolder($name);

        return $this;
    }


    public function dataGet($template = null)
    {
        return $this->engine->getData($template);
    }

    /**
     * @return static
     */
    public function dataAdd(array $data, $templates = null)
    {
        $this->engine->addData($data, $templates);

        return $this;
    }


    public function functionExists($name, ?LeagueFunc &$func = null) : bool
    {
        $func = null;

        if ($this->engine->doesFunctionExist($name)) {
            $func = $this->engine->getFunction($name);

            return true;
        }

        return false;
    }

    public function functionGet($name) : LeagueFunc
    {
        return $this->engine->getFunction($name);
    }

    /**
     * @return static
     */
    public function functionRegister($name, $callback)
    {
        $this->engine->registerFunction($name, $callback);

        return $this;
    }

    /**
     * @return static
     */
    public function functionDrop($name)
    {
        $this->engine->dropFunction($name);

        return $this;
    }


    /**
     * @return static
     */
    public function extensionLoadAll(array $extensions = [])
    {
        $this->engine->loadExtensions($extensions);

        return $this;
    }

    /**
     * @return static
     */
    public function extensionLoad(LeagueExtensionInterface $extension)
    {
        $this->engine->loadExtension($extension);

        return $this;
    }


    public function templateExists($name) : bool
    {
        return $this->engine->exists($name);
    }

    public function templatePath($name) : string
    {
        return $this->engine->path($name);
    }


    public function make($name, array $data = []) : PlatesTemplateInterface
    {
        return $this->engine->make($name, $data);
    }

    public function render($name, array $data = []) : string
    {
        return $this->engine->render($name, $data);
    }


    public function langCurrentSet(?string $langCurrent) : ?string
    {
        $last = $this->store->langCurrent;

        if (null !== $langCurrent) {
            if ('' === $langCurrent) {
                throw new LogicException(
                    [ 'The `langCurrent` should be non-empty string', $langCurrent ]
                );
            }

            $this->store->langCurrent = $langCurrent;
        }

        return $last;
    }

    public function langDefaultSet(?string $langDefault) : ?string
    {
        $last = $this->store->langDefault;

        if (null !== $langDefault) {
            if ('' === $langDefault) {
                throw new LogicException(
                    [ 'The `langDefault` should be non-empty string', $langDefault ]
                );
            }

            $this->store->langDefault = $langDefault;
        }

        return $last;
    }
}
