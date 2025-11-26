<?php

namespace Gzhegow\Front\Core\AssetManager\ResolverRemote;

use Gzhegow\Lib\Lib;
use Gzhegow\Front\Core\Struct\Remote;
use Gzhegow\Front\Exception\RuntimeException;
use Gzhegow\Front\Package\League\Plates\Template\Template;


class FrontDefaultAssetResolverRemote extends AbstractFrontAssetResolverRemote
{
    /**
     * @return array{
     *     input: string,
     *     remote: Remote,
     *     src: string,
     * }
     */
    public function resolve(string $input, Template $template) : array
    {
        $thePhp = Lib::php();
        $theType = Lib::type();

        $inputNormalized = $theType->path_normalized($input)->orThrow();

        $split = explode('::', $inputNormalized, 2);

        if ( count($split) < 1 ) {
            throw new RuntimeException(
                [ 'The `input` should contain `@remote::` prefix', $inputNormalized ]
            );
        }

        [ $remoteAlias, $inputNormalized ] = $split + [ '', '' ];

        if ( ! isset($this->frontStore->remotesByAlias[$remoteAlias]) ) {
            throw new RuntimeException(
                [ 'The `remote` is not found by alias: ' . $remoteAlias, $this->frontStore ]
            );
        }

        $srcRemote = $this->frontStore->remotesByAlias[$remoteAlias];

        $remotePath = $srcRemote->getRemotePath();

        $src = $thePhp->path_join([ $remotePath, $inputNormalized ]);

        $resolved = [
            'input'  => $inputNormalized,
            'remote' => $srcRemote,
            'src'    => $src,
        ];

        return $resolved;
    }
}
