<?php

namespace Gzhegow\Front\Package\League\Plates;

use League\Plates\Template\ResolveTemplatePath;
use Gzhegow\Front\Package\League\Plates\Template\TemplateInterface;


/**
 * @mixin Engine
 */
interface EngineInterface
{
    public function getResolveTemplatePath() : ResolveTemplatePath;

    /**
     * @return static
     */
    public function setResolveTemplatePath(ResolveTemplatePath $resolveTemplatePath);

    /**
     * @return static
     */
    public function unsetResolveTemplatePath();


    public function make($name, array $data = []) : TemplateInterface;

    public function render($name, array $data = []) : string;


    /**
     * @param callable|false|null $fnTemplateGetItem
     */
    public function fnTemplateGetItem($fnTemplateGetItem = null) : ?callable;

    /**
     * @param callable|false|null $fnTemplateCatchError
     */
    public function fnTemplateCatchError($fnTemplateCatchError = null) : ?callable;
}
