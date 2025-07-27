<?php

namespace Gzhegow\Front\Core\TagManager;

use Gzhegow\Lib\Lib;
use Gzhegow\Lib\Config\AbstractConfig;


/**
 * @property string $appNameFull
 * @property string $appNameShort
 */
class FrontTagManagerConfig extends AbstractConfig
{
    /**
     * @var string
     */
    protected $appNameFull;
    /**
     * @var string
     */
    protected $appNameShort;


    protected function validation(array $context = []) : bool
    {
        $theType = Lib::type();

        $this->appNameShort = $this->appNameShort ?? $this->appNameFull;

        if (null !== $this->appNameFull) {
            $this->appNameFull = $theType->string_not_empty($this->appNameFull)->orThrow();
        }

        if (null !== $this->appNameShort) {
            $this->appNameShort = $theType->string_not_empty($this->appNameShort)->orThrow();
        }

        return true;
    }
}
