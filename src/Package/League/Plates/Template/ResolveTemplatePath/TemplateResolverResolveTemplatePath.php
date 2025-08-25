<?php

namespace Gzhegow\Front\Package\League\Plates\Template\ResolveTemplatePath;

use League\Plates\Template\Name;
use League\Plates\Template\ResolveTemplatePath;
use Gzhegow\Front\Core\TemplateResolver\FrontTemplateResolverInterface;


class TemplateResolverResolveTemplatePath implements ResolveTemplatePath
{
    /**
     * @var FrontTemplateResolverInterface
     */
    protected $resolver;


    public function __construct(FrontTemplateResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }


    public function __invoke(Name $name) : string
    {
        return $this->resolver->resolve($name);
    }
}
