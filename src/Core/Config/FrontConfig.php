<?php

namespace Gzhegow\Front\Core\Config;

use Gzhegow\Lib\Lib;
use Gzhegow\Lib\Config\AbstractConfig;
use Gzhegow\Front\Core\TagManager\FrontTagManagerConfig;


/**
 * @property bool                  $isDebug
 *
 * @property string                $directory
 * @property string|null           $fileExtension
 *
 * @property string                $langCurrent
 * @property string                $langDefault
 *
 * @property FrontTagManagerConfig $tagManager
 */
class FrontConfig extends AbstractConfig
{
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
     * @var string|null
     */
    protected $langCurrent;
    /**
     * @var string|null
     */
    protected $langDefault;

    /**
     * @var FrontTagManagerConfig
     */
    protected $tagManager;


    public function __construct()
    {
        $this->tagManager = new FrontTagManagerConfig();

        parent::__construct();
    }


    protected function validation(array &$refContext = []) : bool
    {
        $theParseThrow = Lib::parseThrow();

        $this->isDebug = (bool) $this->isDebug;

        $this->directory = $theParseThrow->dirpath_realpath($this->directory);

        if (null !== $this->fileExtension) {
            $this->fileExtension = $theParseThrow->string_not_empty($this->fileExtension);
        }

        if (null !== $this->langCurrent) {
            $this->langCurrent = $theParseThrow->string_not_empty($this->langCurrent);
        }

        if (null !== $this->langDefault) {
            $this->langDefault = $theParseThrow->string_not_empty($this->langDefault);
        }

        $status = true;
        $status &= $this->tagManager->validation($refContext);

        return $status;
    }
}
