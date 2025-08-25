<?php

namespace Gzhegow\Front\Core\AssetManager\RemoteSrcResolver;

use Gzhegow\Lib\Lib;
use Gzhegow\Front\Core\Struct\Remote;
use Gzhegow\Front\Exception\RuntimeException;


class FrontDefaultAssetRemoteSrcResolver extends AbstractFrontAssetRemoteSrcResolver
{
    public function resolve(string $src, ?Remote $remoteCurrent = null) : string
    {
        $theType = Lib::type();

        $srcNormalized = $theType->path_normalized($src)->orThrow();

        $split = explode('::', $srcNormalized, 2) + [ '', '' ];

        if (count($split) > 1) {
            [ $srcAlias, $srcNormalized ] = $split;

            $remoteCurrent = $this->frontStore->remotesByAlias[ $srcAlias ];

        } else {
            if (null === $remoteCurrent) {
                throw new RuntimeException(
                    [ 'The `remote` is empty', $remoteCurrent ]
                );
            }
        }

        $remotePathString = $remoteCurrent->getRemotePath();

        $srcNormalized = ltrim($srcNormalized, '/');

        $srcPath = "{$remotePathString}/{$srcNormalized}";
        $srcVersion = $this->frontStore->assetVersion;

        if (null !== $srcVersion) {
            $theUrl = Lib::url();

            $srcPath = $theUrl->uri($srcPath, [ 'v' => $srcVersion ]);
        }

        return $srcPath;
    }
}
