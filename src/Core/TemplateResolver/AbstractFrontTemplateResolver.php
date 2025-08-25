<?php

namespace Gzhegow\Front\Core\TemplateResolver;

use Gzhegow\Front\Core\Store\FrontStore;


/**
 * @see ResolveTemplatePathOriginal
 */
abstract class AbstractFrontTemplateResolver implements FrontTemplateResolverInterface
{
    /**
     * @var FrontStore
     */
    protected $frontStore;


    public function setStore(FrontStore $store)
    {
        $this->frontStore = $store;

        return $this;
    }
}
