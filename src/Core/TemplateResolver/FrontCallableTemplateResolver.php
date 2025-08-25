<?php

namespace Gzhegow\Front\Core\TemplateResolver;

use League\Plates\Template\Name;


class FrontCallableTemplateResolver extends AbstractFrontTemplateResolver
{
    /**
     * @var \Closure
     */
    protected $fnResolve;
    /**
     * @var array
     */
    protected $fnResolveArgs = [];


    public function __construct(\Closure $fnResolve, array $fnResolveArgs = [])
    {
        $this->fnResolve = $fnResolve;
        $this->fnResolveArgs = $fnResolveArgs;
    }


    /**
     * @throws \Throwable
     */
    public function resolve(Name $name) : string
    {
        $argsNew = array_merge(
            [ $name ],
            $this->fnResolveArgs
        );

        $path = call_user_func_array($this->fnResolve, $argsNew);

        return $path;
    }
}
