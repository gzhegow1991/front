<?php

namespace Gzhegow\Front\Core\AssetManager\RemoteResolver;

use Gzhegow\Lib\Lib;
use Gzhegow\Front\Core\Struct\Remote;
use Gzhegow\Front\Exception\RuntimeException;


class FrontDefaultAssetRemoteResolver extends AbstractFrontAssetRemoteResolver
{
    /**
     * @return array{
     *     key: string,
     *     remote: Remote,
     *     src: string,
     *     version: string,
     *     uri: string,
     * }
     */
    public function resolve(
        string $key,
        ?Remote $remoteCurrent = null
    ) : array
    {
        $thePhp = Lib::php();
        $theType = Lib::type();

        $keyNormalized = $theType->path_normalized($key)->orThrow();

        $split = explode('::', $keyNormalized, 2) + [ '', '' ];

        $srcRemote = null;
        if ( count($split) > 1 ) {
            [ $remoteAlias, $keyNormalized ] = $split;

            if ( ! isset($this->frontStore->remotesByAlias[$remoteAlias]) ) {
                throw new RuntimeException(
                    [ 'The `remote` is not found by alias: ' . $remoteAlias, $remoteAlias ]
                );
            }

            $srcRemote = $this->frontStore->remotesByAlias[$remoteAlias];

        } else {
            if ( null === $remoteCurrent ) {
                throw new RuntimeException(
                    [ 'The `remote` is empty', $remoteCurrent ]
                );
            }

            $srcRemote = $remoteCurrent;
        }

        $remotePath = $srcRemote->getRemotePath();

        $src = $thePhp->path_join([ $remotePath, $keyNormalized ]);
        $srcVersion = $this->frontStore->assetVersion;

        $srcUri = $src;
        if ( null !== $srcVersion ) {
            $theUrl = Lib::url();

            $srcUri = $theUrl->uri($src, [ 'v' => $srcVersion ]);
        }

        $resolved = [
            'key'     => $keyNormalized,
            'remote'  => $srcRemote,
            'src'     => $src,
            'version' => $srcVersion,
            'uri'     => $srcUri,
        ];

        return $resolved;
    }
}
