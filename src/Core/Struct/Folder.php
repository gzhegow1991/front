<?php

namespace Gzhegow\Front\Core\Struct;

use Gzhegow\Lib\Lib;
use Gzhegow\Lib\Modules\Type\Ret;


class Folder
{
    /**
     * @var string
     */
    protected $alias;
    /**
     * @var string
     */
    protected $directory;
    /**
     * @var string
     */
    protected $publicPath;


    private function __construct()
    {
    }


    /**
     * @return static|Ret<static>
     */
    public static function from($from, ?array $fallback = null)
    {
        $ret = Ret::new();

        $instance = null
            ?? static::fromStatic($from)->orNull($ret)
            ?? static::fromArray($from)->orNull($ret);

        if ($ret->isFail()) {
            return Ret::throw($fallback, $ret);
        }

        return Ret::ok($fallback, $instance);
    }

    /**
     * @return static|Ret<static>
     */
    public static function fromStatic($from, ?array $fallback = null)
    {
        if ($from instanceof static) {
            return Ret::ok($fallback, $from);
        }

        return Ret::throw(
            $fallback,
            [ 'The `from` should be instance of: ' . static::class, $from ],
            [ __FILE__, __LINE__ ]
        );
    }

    /**
     * @return static|Ret<static>
     */
    public static function fromArray($from, ?array $fallback = null)
    {
        if (! is_array($from)) {
            return Ret::throw(
                $fallback,
                [ 'The `from` should be array: ' . static::class, $from ],
                [ __FILE__, __LINE__ ]
            );
        }

        $theType = Lib::type();

        $alias = $from[ 'alias' ] ?? $from[ 0 ];
        $directory = $from[ 'directory' ] ?? $from[ 1 ];
        $publicPath = $from[ 'public_path' ] ?? $from[ 2 ] ?? null;

        if (! $theType->string_not_empty($alias)->isOk([ &$aliasString, &$ret ])) {
            return Ret::throw(
                $fallback,
                $ret,
                [ __FILE__, __LINE__ ]
            );
        }

        if ('@' !== $aliasString[ 0 ]) {
            return Ret::throw(
                $fallback,
                [ 'The `from[alias]` should begin with sign `@`', $alias ],
                [ __FILE__, __LINE__ ]
            );
        }

        if (! $theType->dirpath_realpath($directory)->isOk([ &$directoryRealpath, &$ret ])) {
            return Ret::throw(
                $fallback,
                $ret,
                [ __FILE__, __LINE__ ]
            );
        }

        $publicPathString = null;
        if (null !== $publicPath) {
            if (! $theType->path($publicPath)->isOk([ &$publicPathString, &$ret ])) {
                return Ret::throw(
                    $fallback,
                    $ret,
                    [ __FILE__, __LINE__ ]
                );
            }

            if ('/' !== $publicPathString[ 0 ]) {
                return Ret::throw(
                    $fallback,
                    [ 'The `from[public_path]` should begin with sign `/`', $alias ],
                    [ __FILE__, __LINE__ ]
                );
            }

            $publicPathString = rtrim($publicPathString, '/');
        }

        $instance = new static();
        $instance->alias = $aliasString;
        $instance->directory = $directoryRealpath;
        $instance->publicPath = $publicPathString;

        return Ret::ok($fallback, $instance);
    }


    public function getAlias() : string
    {
        return $this->alias;
    }

    public function getDirectory() : string
    {
        return $this->directory;
    }


    public function hasPublicPath(?string &$publicPath = null) : bool
    {
        $publicPath = null;

        if (null !== $this->publicPath) {
            $publicPath = $this->publicPath;

            return true;
        }

        return false;
    }

    public function getPublicPath() : string
    {
        return $this->publicPath;
    }
}
