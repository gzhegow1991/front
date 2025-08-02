<?php

namespace Gzhegow\Front\Package\League\Plates\Template\ResolveTemplatePath;

use League\Plates\Template\Name;
use League\Plates\Template\ResolveTemplatePath;
use Gzhegow\Front\Core\Resolver\ResolverInterface;


class FrontResolveTemplatePath implements ResolveTemplatePath
{
    /**
     * @var ResolverInterface
     */
    protected $resolver;


    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }


    public function __invoke(Name $name) : string
    {
        return $this->resolver->resolve($name);
    }
}
