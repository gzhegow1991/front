<?php

namespace Gzhegow\Front\Core\Resolver;

use League\Plates\Template\Name;


class CallableResolver extends AbstractResolver
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
        try {
            $argsNew = array_merge(
                [ $name ],
                $this->fnResolveArgs
            );

            $path = call_user_func_array($this->fnResolve, $argsNew);

            return $path;
        }
        catch ( \Throwable $e ) {
            throw $e;
        }
    }
}
