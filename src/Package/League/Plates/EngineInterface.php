<?php

namespace Gzhegow\Front\Package\League\Plates;

use Gzhegow\Front\Package\League\Plates\Template\TemplateInterface;


/**
 * @mixin Engine
 */
interface EngineInterface
{
    public function make($name, array $data = []) : TemplateInterface;

    public function render($name, array $data = []) : string;
}
