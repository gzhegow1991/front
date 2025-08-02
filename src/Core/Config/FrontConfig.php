<?php

namespace Gzhegow\Front\Core\Config;

use Gzhegow\Lib\Lib;
use Gzhegow\Lib\Config\AbstractConfig;
use Gzhegow\Front\Core\TagManager\FrontTagManagerConfig;


/**
 * @property FrontTagManagerConfig $tagManager
 *
 * @property bool                  $isDebug
 *
 * @property string                $directory
 * @property string|null           $fileExtension
 *
 * @property string                $fnTemplateGet
 * @property string                $fnTemplateCatch
 *
 * @property string                $langCurrent
 * @property string                $langDefault
 */
class FrontConfig extends AbstractConfig
{
    /**
     * @var FrontTagManagerConfig
     */
    protected $tagManager;

    /**
     * @var bool
     */
    protected $isDebug = false;

    /**
     * @var string
     */
    protected $directory;
    /**
     * @var string|null
     */
    protected $fileExtension;

    /**
     * @var \Closure
     */
    protected $fnTemplateGet;
    /**
     * @var \Closure
     */
    protected $fnTemplateCatch;

    /**
     * @var string|null
     */
    protected $langCurrent;
    /**
     * @var string|null
     */
    protected $langDefault;


    public function __construct()
    {
        $this->tagManager = new FrontTagManagerConfig();

        parent::__construct();
    }


    protected function validation(array $context = []) : bool
    {
        $theType = Lib::type();

        $this->isDebug = (bool) $this->isDebug;

        $this->directory = $theType->dirpath_realpath($this->directory)->orThrow();

        if (null !== $this->fileExtension) {
            $this->fileExtension = $theType->string_not_empty($this->fileExtension)->orThrow();
        }

        if (null !== $this->fnTemplateGet) {
            $this->fnTemplateGet = $theType->callable_object_closure($this->fnTemplateGet, null)->orThrow();
        }

        if (null !== $this->fnTemplateCatch) {
            $this->fnTemplateCatch = $theType->callable_object_closure($this->fnTemplateCatch, null)->orThrow();
        }

        if (null !== $this->langCurrent) {
            $this->langCurrent = $theType->string_not_empty($this->langCurrent)->orThrow();
        }

        if (null !== $this->langDefault) {
            $this->langDefault = $theType->string_not_empty($this->langDefault)->orThrow();
        }

        return true;
    }
}
