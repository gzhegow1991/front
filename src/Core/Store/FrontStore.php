<?php

namespace Gzhegow\Front\Core\Store;

use Gzhegow\Front\Core\Struct\Folder;
use Gzhegow\Front\Core\Struct\Remote;


class FrontStore
{
    public $isDebug = false;

    /**
     * @var string
     */
    public $directory;
    /**
     * @var string
     */
    public $fileExtension;
    /**
     * @var string|null
     */
    public $publicPath;

    /**
     * @var Folder[]
     */
    public $folders = [];
    /**
     * @var array<string, Folder>
     */
    public $foldersByDirectory = [];
    /**
     * @var array<string, Folder>
     */
    public $foldersByAlias = [];

    /**
     * @var Remote[]
     */
    public $remotes = [];
    /**
     * @var array<string, Remote>
     */
    public $remotesByAlias = [];

    /**
     * @var string|null
     */
    public $templateLangCurrent;
    /**
     * @var string|null
     */
    public $templateLangDefault;

    /**
     * @var string
     */
    public $appNameShort;
    /**
     * @var string
     */
    public $appNameFull;

    /**
     * @var string|null
     */
    public $assetVersion;
    /**
     * @var array<string, array<string, bool>>
     */
    public $assetExtensionsMap = [];
}
