<?php

namespace Gzhegow\Front\Package\League\Plates\Template;

use Gzhegow\Lib\Lib;
use Gzhegow\Front\Front;
use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Core\Struct\Remote;
use Gzhegow\Front\Core\Store\FrontStore;
use Gzhegow\Lib\Exception\LogicException;
use Gzhegow\Front\Exception\RuntimeException;
use Gzhegow\Front\Package\League\Plates\Engine;
use League\Plates\Template\Template as LeagueTemplate;
use Gzhegow\Front\Core\TagManager\FrontTagManagerInterface;
use Gzhegow\Front\Core\AssetManager\FrontAssetManagerInterface;
use Gzhegow\Front\Package\League\Plates\EngineInterface as PlatesEngineInterface;


class Template extends LeagueTemplate implements TemplateInterface
{
    const SECTION_BRACES = [ '<!-- {{ __', '__ }} -->' ];

    const SECTION_CONTENT = 'content';
    const SECTION_CSS     = 'css';
    const SECTION_JS      = 'js';

    const LIST_SECTION = [
        self::SECTION_CONTENT => true,
        self::SECTION_CSS     => true,
        self::SECTION_JS      => true,
    ];


    /**
     * @var FrontAssetManagerInterface
     */
    protected $frontAssetManager;
    /**
     * @var FrontTagManagerInterface
     */
    protected $frontTagManager;

    /**
     * @var FrontStore
     */
    protected $frontStore;

    /**
     * @var Engine
     */
    protected $engine;


    /**
     * @var string
     */
    protected $pathResolved;

    /**
     * @var array
     */
    protected $sections = [
        self::SECTION_CONTENT => '',
        self::SECTION_CSS     => '',
        self::SECTION_JS      => '',
    ];
    /**
     * @var string
     */
    protected $css = [];
    /**
     * @var string
     */
    protected $js = [];


    public function __construct(
        FrontAssetManagerInterface $assetManager,
        FrontTagManagerInterface $tagManager,
        //
        FrontStore $store,
        //
        PlatesEngineInterface $engine,
        //
        $name
    )
    {
        $this->frontAssetManager = $assetManager;
        $this->frontTagManager = $tagManager;

        $this->frontStore = $store;

        parent::__construct($engine, $name);
    }


    public function name() : string
    {
        return $this->name->getName();
    }

    public function path() : string
    {
        /** @see parent::path() */

        if ( null !== $this->pathResolved ) {
            return $this->pathResolved;
        }

        $resolveTemplatePathInvokableObject = $this->engine->getResolveTemplatePath();

        try {
            $pathResolved = call_user_func_array(
                $resolveTemplatePathInvokableObject,
                [ $this->name ]
            );
        }
        catch ( \Throwable $e ) {
            throw new RuntimeException($e);
        }

        $fileRealpath = realpath($pathResolved);

        return $this->pathResolved = $fileRealpath;
    }

    public function relpath() : string
    {
        $theFs = Lib::php();

        $file = $this->path();
        $directory = $this->engine->getDirectory();

        $fileRelpath = $theFs->path_relative($file, $directory);

        return $fileRelpath;
    }

    public function dir() : string
    {
        $file = $this->path();

        $dirRealpath = realpath(dirname($file));

        return $dirRealpath;
    }


    public function folderRoot() : Folder
    {
        return $this->frontStore->foldersByAlias[Front::ROOT_FOLDER_ALIAS];
    }

    public function folder() : Folder
    {
        $directoryRealpath = $this->dir();

        $match = [];
        foreach ( $this->frontStore->folders as $folder ) {
            if ( ! $folder->hasPublicPath() ) {
                continue;
            }

            $folderDirectoryRealpath = $folder->getDirectory();

            if ( 0 === strpos($directoryRealpath, $folderDirectoryRealpath) ) {
                $match[$folderDirectoryRealpath] = $folder;
            }
        }

        if ( [] === $match ) {
            throw new RuntimeException(
                [ 'The `directory` is outside of all defined `folders`', $this ]
            );
        }

        uksort(
            $match,
            static function ($a, $b) {
                return strlen($a) - strlen($b);
            }
        );

        $folder = end($match);

        return $folder;
    }


    /**
     * @template-covariant T of mixed
     *
     * @param class-string<T>|null $classT
     *
     * @return T
     */
    public function get(string $name, ?string $classT = null)
    {
        $fnGetItem = $this->engine->fnTemplateGetItem();

        if ( null === $fnGetItem ) {
            $theType = Lib::type();

            $item = $theType->key_exists($name, $this->data)->orThrow();

        } else {
            $item = call_user_func_array(
                $fnGetItem,
                [ $name, $classT, $this ]
            );
        }

        return $item;
    }


    /**
     * @see parent::render()
     */
    public function render(?array $data = null) : string
    {
        $dataBefore = $this->data($data);

        ob_start();

        try {
            /**
             * @noinspection PhpMethodParametersCountMismatchInspection
             */
            (function () {
                extract($this->data);

                include func_get_arg(0);
            })(
                $this->path()
            );

            $content = ob_get_clean();
        }
        catch ( \Throwable $e ) {
            $content = ob_get_clean();

            $fnCatchError = $this->engine->fnTemplateCatchError();

            if ( null !== $fnCatchError ) {

                try {
                    $content = call_user_func_array(
                        $fnCatchError,
                        [ $e, $content, $this ]
                    );
                }
                catch ( \Throwable $e ) {
                    throw new RuntimeException($e);
                }

            } else {
                throw new RuntimeException($e);
            }
        }

        if ( [] !== $this->sections ) {
            foreach ( array_keys($this->sections) as $name ) {
                $sectionPlaceholder = static::SECTION_BRACES[0] . $name . static::SECTION_BRACES[1];
                $sectionContent = $this->sections[$name] ?? '';

                $content = str_replace(
                    $sectionPlaceholder,
                    $sectionContent,
                    $content
                );
            }
        }

        $content = trim($content);

        $lines = explode("\n", $content);
        $lines = array_map('rtrim', $lines);
        foreach ( $lines as $i => $l ) {
            if ( '' === $l ) {
                unset($lines[$i]);
            }
        }

        if ( $this->frontStore->isDebug ) {
            $relpath = $this->relpath();

            $lines = array_merge(
                [
                    '',
                    "<!-- [ >>> {$relpath} ] -->",
                ],
                $lines,
                [
                    "<!-- [ <<< {$relpath} ] -->",
                    '',
                ],
            );
        }

        $content = implode("\n", $lines) . "\n";

        if ( isset($this->layoutName) ) {
            $layout = $this->engine->make($this->layoutName);

            $layout->sections = []
                + [ 'content' => $content ]
                + $this->sections;

            $content = $layout->render($this->layoutData + $this->data);

            $content = trim($content);
        }

        $this->data = $dataBefore;

        return $content;
    }

    /**
     * @see parent::fetch()
     */
    public function fetch($name, ?array $data = null) : string
    {
        $dataTotal = $data ?? [];
        $dataTotal = $dataTotal + $this->data;

        $template = $this->engine->make($name, $dataTotal);
        $template->sections =& $this->sections;
        $template->css =& $this->css;
        $template->js =& $this->js;

        try {
            $html = $template->render();
        }
        catch ( \Throwable $e ) {
            throw new RuntimeException($e);
        }

        return $html;
    }


    /**
     * @deprecated
     */
    public function start($name)
    {
        $this->_sectionStart($name);

        return $this;
    }

    /**
     * @deprecated
     */
    public function push($name)
    {
        $this->_sectionPush($name);

        return $this;
    }

    /**
     * @deprecated
     */
    public function unshift($name)
    {
        $this->_sectionUnshift($name);

        return $this;
    }

    /**
     * @deprecated
     */
    public function stop()
    {
        $this->_sectionEnd();

        return $this;
    }

    /**
     * @deprecated
     */
    public function end()
    {
        $this->_sectionEnd();

        return $this;
    }

    public function section($name, $default = null)
    {
        if ( ! array_key_exists($name, $this->sections) ) {
            $this->sections[$name] = $default;
        }

        return static::SECTION_BRACES[0] . $name . static::SECTION_BRACES[1];
    }

    public function sectionStart($name) : TemplateInterface
    {
        if ( array_key_exists($name, $this->sections) ) {
            throw new RuntimeException(
                [ 'The section with `name` is already present, use `push()/unshift()` instead', $name ]
            );
        }

        $this->_sectionStart($name);

        return $this;
    }

    public function sectionPush($name) : TemplateInterface
    {
        $this->_sectionPush($name);

        return $this;
    }

    public function sectionUnshift($name) : TemplateInterface
    {
        $this->_sectionUnshift($name);

        return $this;
    }

    public function sectionEnd() : TemplateInterface
    {
        $this->_sectionEnd();

        return $this;
    }

    protected function _sectionStart($name)
    {
        if ( isset(static::LIST_SECTION[$name]) ) {
            throw new LogicException(
                [
                    ''
                    . 'The section names is reserved: '
                    . '[ ' . implode(' ][ ', array_keys(static::LIST_SECTION)) . ' ]',
                    //
                    $name,
                ]
            );
        }

        if ( $this->sectionName ) {
            throw new LogicException(
                [ 'You cannot nest sections within other sections.', $name, $this->sectionName ]
            );
        }

        $this->sectionName = $name;

        ob_start();

        return $this;
    }

    protected function _sectionPush($name)
    {
        parent::push($name);

        return $this;
    }

    protected function _sectionUnshift($name)
    {
        parent::unshift($name);

        return $this;
    }

    protected function _sectionEnd()
    {
        parent::stop();

        return $this;
    }


    public function getData() : array
    {
        return $this->data;
    }

    /**
     * @return static
     */
    public function setData(?array $data = null)
    {
        $this->data = [];

        if ( null !== $data ) {
            $this->data = $data;
        }

        return $this;
    }

    /**
     * @see parent::data()
     */
    public function data(?array $data = null) : array
    {
        if ( null !== $data ) {
            $this->data = array_replace(
                $this->data,
                $data
            );
        }

        return $this->data;
    }


    public function content() : string
    {
        return $this->sections['content'];
    }


    public function css(string $src, array $attributes = [])
    {
        $assetLocalUri = $this->assetLocalUri($src);

        if ( ! isset($this->css[$assetLocalUri]) ) {
            $htmlAttributes = $this->tagAttributes($attributes);

            $this->sections[static::SECTION_CSS] .= "<link rel=\"stylesheet\" href=\"{$assetLocalUri}\" {$htmlAttributes} />\n";

            $this->css[$assetLocalUri] = true;
        }

        return $this;
    }

    public function cssRemote(string $src, array $attributes = [])
    {
        $assetRemoteUri = $this->assetRemoteUri($src);

        if ( ! isset($this->css[$assetRemoteUri]) ) {
            $htmlAttributes = $this->tagAttributes($attributes);

            $this->sections[static::SECTION_CSS] .= "<link rel=\"stylesheet\" href=\"{$assetRemoteUri}\" {$htmlAttributes} />\n";

            $this->css[$assetRemoteUri] = true;
        }

        return $this;
    }

    public function js(string $src, array $attributes = [])
    {
        $assetLocalUri = $this->assetLocalUri($src);

        if ( ! isset($this->js[$assetLocalUri]) ) {
            $htmlAttributes = $this->tagAttributes($attributes);

            $this->sections[static::SECTION_JS] .= "<script src=\"{$assetLocalUri}\" {$htmlAttributes}></script>\n";

            $this->js[$assetLocalUri] = true;
        }

        return $this;
    }

    public function jsRemote(string $src, array $attributes = [])
    {
        $assetRemoteUri = $this->assetRemoteUri($src);

        if ( ! isset($this->js[$assetRemoteUri]) ) {
            $htmlAttributes = $this->tagAttributes($attributes);

            $this->sections[static::SECTION_JS] .= "<script src=\"{$assetRemoteUri}\" {$htmlAttributes}></script>\n";

            $this->js[$assetRemoteUri] = true;
        }

        return $this;
    }


    public function getAssetManager() : FrontAssetManagerInterface
    {
        return $this->frontAssetManager;
    }

    /**
     * @return array{
     *     input: string,
     *     folder: Folder,
     *     realpath: string,
     *     src: string,
     *     version: string,
     *     uri: string,
     * }
     */
    public function assetLocal(string $input) : array
    {
        $resolved = $this->frontAssetManager->resolveLocal($input, $this);

        return $resolved;
    }

    public function assetLocalRealpath(string $input) : string
    {
        $resolved = $this->assetLocal($input);

        return $resolved['realpath'];
    }

    public function assetLocalSrc(string $input) : string
    {
        $resolved = $this->assetLocal($input);

        return $resolved['src'];
    }

    public function assetLocalUri(string $input) : string
    {
        $resolved = $this->assetLocal($input);

        return $resolved['uri'];
    }


    /**
     * @return array{
     *     input: string,
     *     remote: Remote,
     *     src: string,
     *     version: string,
     *     uri: string,
     * }
     */
    public function assetRemote(string $input) : array
    {
        $resolved = $this->frontAssetManager->resolveRemote($input, $this);

        return $resolved;
    }

    public function assetRemoteSrc(string $input) : string
    {
        $resolved = $this->assetRemote($input);

        return $resolved['src'];
    }

    public function assetRemoteUri(string $input) : string
    {
        $resolved = $this->assetRemote($input);

        return $resolved['uri'];
    }


    public function getTagManager() : FrontTagManagerInterface
    {
        return $this->frontTagManager;
    }

    public function tag(string $tag, $content, array $attributes = []) : string
    {
        $html = $this->frontTagManager->tag($tag, $content, $attributes);

        return $html;
    }

    /**
     * @param string|string[] $content
     */
    public function tagAButton($content, string $url, $title = null, array $attributes = []) : string
    {
        $html = $this->frontTagManager->tagAButton($content, $url, $title, $attributes);

        return $html;
    }

    /**
     * @param string|string[] $content
     */
    public function tagAHref($content, string $url, $title = null, array $attributes = []) : string
    {
        $html = $this->frontTagManager->tagAHref($content, $url, $title, $attributes);

        return $html;
    }

    public function tagImg(string $src, $alt, array $attributes = []) : string
    {
        $html = $this->frontTagManager->tagImg($src, $alt, $attributes);

        return $html;
    }

    public function tagAttributes(array $attributes = []) : string
    {
        $html = $this->frontTagManager->attributes($attributes);

        return $html;
    }

    public function tagAttrAlt($alt) : string
    {
        $html = $this->frontTagManager->attrAlt($alt);

        return $html;
    }

    public function tagAttrAltOrNull($alt) : ?string
    {
        $html = $this->frontTagManager->attrAltOrNull($alt);

        return $html;
    }

    public function tagAttrTitle($title) : string
    {
        $html = $this->frontTagManager->attrTitle($title);

        return $html;
    }

    public function tagAttrTitleOrNull($title) : ?string
    {
        $html = $this->frontTagManager->attrTitleOrNull($title);

        return $html;
    }

    public function tagLinkButton($content, string $url, $title = null, array $attributes = []) : string
    {
        $html = $this->frontTagManager->linkButton($content, $url, $title, $attributes);

        return $html;
    }

    public function tagLinkHref($content, string $url, $title = null, array $attributes = []) : string
    {
        $html = $this->frontTagManager->linkHref($content, $url, $title, $attributes);

        return $html;
    }

    public function tagLinkSeo($content, string $url, $title = null, array $attributes = []) : string
    {
        $html = $this->frontTagManager->linkSeo($content, $url, $title, $attributes);

        return $html;
    }
}
