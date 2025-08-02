<?php

namespace Gzhegow\Front\Store;

class FrontStore
{
    public $isDebug = false;

    /**
     * @var \Closure
     */
    public $fnTemplateGet;
    /**
     * @var \Closure
     */
    public $fnTemplateCatch;

    /**
     * @var string
     */
    public $langCurrent;
    /**
     * @var string
     */
    public $langDefault;
}
