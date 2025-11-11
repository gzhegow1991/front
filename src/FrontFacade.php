<?php

namespace Gzhegow\Front;

use Gzhegow\Lib\Lib;
use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Core\Struct\Remote;
use Gzhegow\Front\Core\Store\FrontStore;
use Gzhegow\Front\Core\Config\FrontConfig;
use Gzhegow\Front\Exception\LogicException;
use Gzhegow\Front\Exception\RuntimeException;
use Gzhegow\Front\Core\TagManager\FrontTagManagerInterface;
use Gzhegow\Front\Core\AssetManager\FrontAssetManagerInterface;
use Gzhegow\Front\Package\League\Plates\Template\TemplateInterface;
use Gzhegow\Front\Core\TemplateResolver\FrontDefaultTemplateResolver;
use Gzhegow\Front\Core\TemplateResolver\FrontTemplateResolverInterface;
use Gzhegow\Front\Package\League\Plates\EngineInterface as PlatesEngineInterface;
use Gzhegow\Front\Core\AssetManager\ResolverLocal\FrontDefaultAssetResolverLocal;
use Gzhegow\Front\Core\AssetManager\ResolverLocal\FrontAssetResolverLocalInterface;
use Gzhegow\Front\Core\AssetManager\ResolverRemote\FrontDefaultAssetResolverRemote;
use Gzhegow\Front\Core\AssetManager\ResolverRemote\FrontAssetResolverRemoteInterface;
use Gzhegow\Front\Package\League\Plates\Template\TemplateInterface as PlatesTemplateInterface;
use Gzhegow\Front\Package\League\Plates\Template\ResolveTemplatePath\TemplateResolverResolveTemplatePath;


class FrontFacade implements FrontInterface
{
    /**
     * @var FrontFactoryInterface
     */
    protected $factory;

    /**
     * @var FrontAssetManagerInterface
     */
    protected $assetManager;
    /**
     * @var FrontTagManagerInterface
     */
    protected $tagManager;

    /**
     * @var FrontConfig
     */
    protected $config;

    /**
     * @var FrontStore
     */
    protected $store;

    /**
     * @var PlatesEngineInterface
     */
    protected $engine;

    /**
     * @var FrontTemplateResolverInterface
     */
    protected $templateResolver;


    public function __construct(
        FrontFactoryInterface $factory,
        //
        FrontAssetManagerInterface $assetManager,
        FrontTagManagerInterface $tagManager,
        //
        FrontConfig $config,
        //
        ?FrontTemplateResolverInterface $templateResolver = null,
        ?FrontAssetResolverLocalInterface $assetLocalResolver = null,
        ?FrontAssetResolverRemoteInterface $assetRemoteResolver = null
    )
    {
        $templateResolver = $templateResolver ?? new FrontDefaultTemplateResolver();
        $assetLocalResolver = $assetLocalResolver ?? new FrontDefaultAssetResolverLocal();
        $assetRemoteResolver = $assetRemoteResolver ?? new FrontDefaultAssetResolverRemote();

        $this->factory = $factory;

        $this->assetManager = $assetManager;
        $this->tagManager = $tagManager;

        $this->config = $config;
        $this->config->validate();

        $directory = $this->config->directory;
        $fileExtension = $this->config->fileExtension;
        $publicPath = $this->config->publicPath;

        $this->store = $this->factory->newStore();
        $this->store->isDebug = $this->config->isDebug;
        $this->store->directory = $directory;
        $this->store->fileExtension = $fileExtension;
        $this->store->publicPath = $publicPath;
        $this->store->templateLangCurrent = $this->config->templateLangCurrent;
        $this->store->templateLangDefault = $this->config->templateLangDefault;
        $this->store->appNameShort = $this->config->tagAppNameShort;
        $this->store->appNameFull = $this->config->tagAppNameFull;
        $this->store->assetVersion = $this->config->assetVersion;
        $this->store->assetExtensionsMap = $this->config->assetExtensionsMap;

        $this->engine = $this->factory->newPlatesEngine(
            $this->assetManager,
            $this->tagManager,
            //
            $this->store,
            //
            $directory,
            $fileExtension
        );

        $this->initialize();

        $this->templateResolverSet($templateResolver);
        $this->assetResolverLocalSet($assetLocalResolver);
        $this->assetResolverRemoteSet($assetRemoteResolver);

        $folderRoot = Folder::fromArray([
            'alias'       => Front::ROOT_FOLDER_ALIAS,
            'directory'   => $this->config->directory,
            'public_path' => $this->config->publicPath,
        ])->orThrow();

        $this->folderAdd($folderRoot);

        foreach ( $this->config->folders as $folder ) {
            $this->folderAdd($folder);
        }

        foreach ( $this->config->remotes as $remote ) {
            $this->remoteAdd($remote);
        }
    }

    protected function initialize() : void
    {
        $this->assetManager->initialize($this);
        $this->tagManager->initialize($this);
    }


    public function getEngine() : PlatesEngineInterface
    {
        return $this->engine;
    }


    public function getStore() : FrontStore
    {
        return $this->store;
    }


    public function directoryGet() : string
    {
        return $this->store->directory;
    }

    public function fileExtensionGet() : string
    {
        return $this->store->fileExtension;
    }

    public function publicPathGet() : ?string
    {
        return $this->store->publicPath;
    }


    /**
     * @return Folder[]
     */
    public function folders() : array
    {
        return $this->store->folders;
    }

    public function folderGet(int $id) : Folder
    {
        if ( ! isset($this->store->folders[$id]) ) {
            throw new RuntimeException(
                [ 'The `id` is missing: ' . $id, $id ]
            );
        }

        return $this->store->folders[$id];
    }

    public function folderByAliasGet(string $alias) : Folder
    {
        $theType = Lib::type();

        $aliasString = $theType->string_not_empty($alias)->orThrow();

        if ( ! isset($this->store->foldersByAlias[$aliasString]) ) {
            throw new RuntimeException(
                [ 'The `alias` is missing: ' . $alias, $alias ]
            );
        }

        $folder = $this->store->foldersByAlias[$aliasString];

        return $folder;
    }

    public function folderByDirectoryGet(string $directory) : Folder
    {
        $theType = Lib::type();

        $directoryRealpath = $theType->dirpath_realpath($directory)->orThrow();

        if ( ! isset($this->store->foldersByDirectory[$directoryRealpath]) ) {
            throw new RuntimeException(
                [ 'The `directory` is missing: ' . $directory, $directory ]
            );
        }

        $folder = $this->store->foldersByDirectory[$directoryRealpath];

        return $folder;
    }

    /**
     * @param Folder|array $folder
     */
    public function folderAdd($folder) : int
    {
        $folderObject = Folder::from($folder)->orThrow();

        $folderRealpath = $folderObject->getDirectory();
        $folderAlias = $folderObject->getAlias();

        $i = array_key_last($this->store->folders);
        $i++;

        $this->store->folders[$i] = $folderObject;
        $this->store->foldersByAlias[$folderAlias] = $folderObject;
        $this->store->foldersByDirectory[$folderRealpath] = $folderObject;

        $this->engine->addFolder($folderAlias, $folderRealpath, $fallback = false);

        return $i;
    }


    /**
     * @return Remote[]
     */
    public function remotes() : array
    {
        return $this->store->remotes;
    }

    public function remoteGet(int $id) : Remote
    {
        if ( ! isset($this->store->remotes[$id]) ) {
            throw new RuntimeException(
                [ 'The `id` is missing: ' . $id, $id ]
            );
        }

        return $this->store->remotes[$id];
    }

    public function remoteByAliasGet(string $alias) : Remote
    {
        $theType = Lib::type();

        $aliasString = $theType->string_not_empty($alias)->orThrow();

        if ( ! isset($this->store->remotesByAlias[$aliasString]) ) {
            throw new RuntimeException(
                [ 'The `alias` is missing: ' . $alias, $alias ]
            );
        }

        $remote = $this->store->remotesByAlias[$aliasString];

        return $remote;
    }

    /**
     * @param Remote|array $remote
     */
    public function remoteAdd($remote) : int
    {
        $remoteObject = Remote::from($remote)->orThrow();

        $remoteAlias = $remoteObject->getAlias();

        $i = array_key_last($this->store->remotes);
        $i++;

        $this->store->remotes[$i] = $remoteObject;
        $this->store->remotesByAlias[$remoteAlias] = $remoteObject;

        return $i;
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


    /**
     * @param FrontTemplateResolverInterface|false|null $templateResolver
     */
    public function templateResolverSet($templateResolver = null) : ?FrontTemplateResolverInterface
    {
        $last = $this->templateResolver;

        if ( null !== $templateResolver ) {
            if ( false === $templateResolver ) {
                $this->engine->unsetResolveTemplatePath();

            } else {
                $templateResolver->setStore($this->store);

                $this->engine->setResolveTemplatePath(
                    new TemplateResolverResolveTemplatePath($templateResolver)
                );
            }
        }

        $this->templateResolver = $templateResolver;

        return $last;
    }


    public function templateExists($name) : bool
    {
        return $this->engine->exists($name);
    }

    public function templateName($name) : string
    {
        return $this->engine->make($name)->name();
    }

    public function templateDir($name) : string
    {
        return $this->engine->make($name)->dir();
    }

    public function templateFolder($name) : Folder
    {
        return $this->engine->make($name)->folder();
    }

    public function templatePath($name) : string
    {
        return $this->engine->make($name)->path();
    }

    public function templateRelpath($name) : string
    {
        return $this->engine->make($name)->relpath();
    }


    /**
     * @param string|false|null $langCurrent
     */
    public function templateLangCurrentSet($langCurrent) : ?string
    {
        $last = $this->store->templateLangCurrent;

        if ( null !== $langCurrent ) {
            if ( false === $langCurrent ) {
                $this->store->templateLangCurrent = null;

            } else {
                if ( '' === $langCurrent ) {
                    throw new LogicException(
                        [ 'The `langCurrent` should be non-empty string', $langCurrent ]
                    );
                }

                $this->store->templateLangCurrent = $langCurrent;
            }
        }

        return $last;
    }

    /**
     * @param string|false|null $langDefault
     */
    public function templateLangDefaultSet($langDefault) : ?string
    {
        $last = $this->store->templateLangDefault;

        if ( null !== $langDefault ) {
            if ( false === $langDefault ) {
                $this->store->templateLangDefault = null;

            } else {
                if ( '' === $langDefault ) {
                    throw new LogicException(
                        [ 'The `langDefault` should be non-empty string', $langDefault ]
                    );
                }

                $this->store->templateLangDefault = $langDefault;
            }
        }

        return $last;
    }


    /**
     * @param callable|false|null $fnTemplateGetItem
     */
    public function fnTemplateGetItem($fnTemplateGetItem = null) : ?callable
    {
        return $this->engine->fnTemplateGetItem($fnTemplateGetItem);
    }

    /**
     * @param callable|false|null $fnTemplateCatchError
     */
    public function fnTemplateCatchError($fnTemplateCatchError = null) : ?callable
    {
        return $this->engine->fnTemplateCatchError($fnTemplateCatchError);
    }


    /**
     * @param FrontAssetResolverLocalInterface|false|null $assetLocalResolver
     */
    public function assetResolverLocalSet($assetLocalResolver = null) : ?FrontAssetResolverLocalInterface
    {
        return $this->assetManager->resolverLocalSet($assetLocalResolver);
    }

    /**
     * @param FrontAssetResolverRemoteInterface|false|null $assetRemoteResolver
     */
    public function assetResolverRemoteSet($assetRemoteResolver = null) : ?FrontAssetResolverRemoteInterface
    {
        return $this->assetManager->resolverRemoteSet($assetRemoteResolver);
    }


    public function make($name, array $data = []) : PlatesTemplateInterface
    {
        return $name instanceof TemplateInterface
            ? $name
            : $this->engine->make($name, $data);
    }

    public function render($name, array $data = []) : string
    {
        return $name instanceof TemplateInterface
            ? $name->render($data)
            : $this->engine->render($name, $data);
    }
}
