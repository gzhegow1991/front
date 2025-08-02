<?php

namespace Gzhegow\Front\Package\League\Plates\Template\ResolveTemplatePath;

use League\Plates\Template\Name;
use League\Plates\Template\ResolveTemplatePath;
use Gzhegow\Front\Core\Resolver\FrontResolverInterface;


class FrontResolveTemplatePath implements ResolveTemplatePath
{
    /**
     * @var FrontResolverInterface
     */
    protected $resolver;


    public function __construct(FrontResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }


    public function __invoke(Name $name) : string
    {
        return $this->resolver->resolve($name);
    }
}
