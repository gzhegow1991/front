<?php

namespace Gzhegow\Front\Core\Resolver;

use Gzhegow\Front\Store\FrontStore;


/**
 * @see ResolveTemplatePathOriginal
 */
abstract class AbstractResolver implements ResolverInterface
{
    /**
     * @var FrontStore
     */
    protected $store;

    public function setStore(FrontStore $store)
    {
        $this->store = $store;

        return $this;
    }
}
