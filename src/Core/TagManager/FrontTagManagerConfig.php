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


    protected function validation(array &$refContext = []) : bool
    {
        $t = Lib::parseThrow();

        $this->appNameShort = $this->appNameShort ?? $this->appNameFull;

        if (null !== $this->appNameFull) {
            $this->appNameFull = $t->string_not_empty($this->appNameFull);
        }

        if (null !== $this->appNameShort) {
            $this->appNameShort = $t->string_not_empty($this->appNameShort);
        }

        return true;
    }
}
