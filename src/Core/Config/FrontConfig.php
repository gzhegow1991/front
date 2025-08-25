<?php

namespace Gzhegow\Front\Core\Config;

use Gzhegow\Lib\Lib;
use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Core\Struct\Remote;
use Gzhegow\Lib\Config\AbstractConfig;


/**
 * @property bool                               $isDebug
 *
 * @property string                             $directory
 * @property string                             $fileExtension
 * @property string|null                        $publicPath
 *
 * @property Folder[]                           $folders
 * @property Remote[]                           $remotes
 *
 * @property string                             $langCurrent
 * @property string                             $langDefault
 *
 * @property string                             $appNameShort
 * @property string                             $appNameFull
 *
 * @property string|null                        $assetVersion
 * @property array<string, array<string, bool>> $assetExtensionsMap
 */
class FrontConfig extends AbstractConfig
{
    /**
     * @var bool
     */
    protected $isDebug = false;

    /**
     * @var string
     */
    protected $directory;
    /**
     * @var string
     */
    protected $fileExtension;
    /**
     * @var string|null
     */
    protected $publicPath;

    /**
     * @var Folder[]
     */
    protected $folders = [];
    /**
     * @var Remote[]
     */
    protected $remotes = [];

    /**
     * @var string|null
     */
    protected $langCurrent;
    /**
     * @var string|null
     */
    protected $langDefault;

    /**
     * @var string
     */
    protected $appNameShort = 'Application';
    /**
     * @var string
     */
    protected $appNameFull = 'MyApp | Application';

    /**
     * @var string|null
     */
    protected $assetVersion;
    /**
     * @var array<string, array<string, bool>>
     */
    protected $assetExtensionsMap = [
        'gif'  => [
            'min.gif' => true,
            'gif'     => true,
        ],
        'jpeg' => [
            'min.jpeg.webp' => true,
            'jpeg.webp'     => true,
            'min.jpeg'      => true,
            'jpeg'          => true,
        ],
        'jpg'  => [
            'min.jpg.webp' => true,
            'jpg.webp'     => true,
            'min.jpg'      => true,
            'jpg'          => true,
        ],
        'png'  => [
            'min.png.webp' => true,
            'png.webp'     => true,
            'min.png'      => true,
            'png'          => true,
        ],
    ];


    protected function validation(array $context = []) : bool
    {
        $theType = Lib::type();

        $this->isDebug = (bool) $this->isDebug;

        $this->directory = $theType->dirpath_realpath($this->directory)->orThrow();
        $this->fileExtension = $theType->string($this->fileExtension)->orThrow();

        if (null !== $this->publicPath) {
            $this->publicPath = $theType->path($this->publicPath)->orThrow();
        }

        foreach ( $this->folders as $i => $folder ) {
            $this->folders[ $i ] = Folder::from($folder)->orThrow();
        }

        foreach ( $this->remotes as $i => $remote ) {
            $this->remotes[ $i ] = Remote::from($remote)->orThrow();
        }

        if (null !== $this->langCurrent) {
            $this->langCurrent = $theType->string_not_empty($this->langCurrent)->orThrow();
        }

        if (null !== $this->langDefault) {
            $this->langDefault = $theType->string_not_empty($this->langDefault)->orThrow();
        }

        if (null !== $this->appNameShort) {
            $this->appNameShort = $theType->string_not_empty($this->appNameShort)->orThrow();
        }

        if (null !== $this->appNameFull) {
            $this->appNameFull = $theType->string_not_empty($this->appNameFull)->orThrow();
        }

        if (null !== $this->assetVersion) {
            $this->assetVersion = $theType->string_not_empty($this->assetVersion)->orThrow();
        }

        if ([] !== $this->assetExtensionsMap) {
            foreach ( $this->assetExtensionsMap as $extFrom => $extToArray ) {
                $theType->array_not_empty($extToArray)->orThrow();

                $theType->string_not_empty($extFrom)->orThrow();

                foreach ( $extToArray as $extTo => $bool ) {
                    $theType->string_not_empty($extTo)->orThrow();

                    $this->assetExtensionsMap[ $extFrom ][ $extTo ] = true;
                }
            }
        }

        return true;
    }
}
