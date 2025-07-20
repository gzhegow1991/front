<?php

namespace Gzhegow\Front\Core\TagManager;

use Gzhegow\Lib\Lib;


class FrontTagManager implements FrontTagManagerInterface
{
    /**
     * @var FrontTagManagerConfig
     */
    protected $config;

    /**
     * @var array<string, bool>
     */
    protected $linkSeoList = [];


    public function __construct(
        FrontTagManagerConfig $config
    )
    {
        $this->config = $config;
    }


    public function tag(string $tag, $content, ?array $attributes = null) : string
    {
        $tagString = Lib::parseThrow()->string_not_empty($tag);

        $lines = is_array($content)
            ? $content
            : ($content ? [ $content ] : []);

        $attributesArray = $attributes ?? [];
        $attributesArray[ 'alt' ] = $attributesArray[ 'alt' ] ?? null;
        $attributesArray[ 'title' ] = $attributesArray[ 'title' ] ?? $attributesArray[ 'alt' ] ?? null;
        $attributesArray[ 'alt' ] = $this->attributeValueAltOrNull($attributesArray[ 'alt' ]) ?? false;
        $attributesArray[ 'title' ] = $this->attributeValueTitleOrNull($attributesArray[ 'title' ]) ?? false;

        $htmlContent = implode("\n", $lines);
        $htmlContent = trim($htmlContent);

        $htmlAttributes = $this->attributes($attributesArray);

        return ""
            . "<{$tagString} {$htmlAttributes}>"
            /****/ . $htmlContent
            . "</{$tagString}>";
    }

    public function attributes(?array $attributes = null) : string
    {
        $attributesArray = $attributes ?? [];

        $content = [];

        foreach ( $attributesArray as $k => $v ) {
            $content[] = null
                ?? (($v === true) ? "{$k}=\"{$k}\"" : null)
                ?? (($v === false) ? "" : null)
                ?? (($v === '') ? "{$k}" : null)
                ?? "{$k}=\"{$v}\"";
        }

        $content = array_filter($content, 'strlen');

        $content = implode(' ', $content);

        return $content;
    }


    public function attributeValueAlt($alt) : string
    {
        return $this->attributeValueAltOrNull($alt);
    }

    public function attributeValueAltOrNull($alt) : ?string
    {
        if (! is_string($alt)) {
            return null;
        }

        if ('' === $alt) {
            return null;
        }

        $websiteAppName = $this->config->appNameShort;

        $altString = ($alt !== $websiteAppName)
            ? ($alt . ' / ' . $websiteAppName)
            : ($alt);

        return $altString;
    }


    public function attributeValueTitle($title) : string
    {
        return $this->attributeValueTitleOrNull($title);
    }

    public function attributeValueTitleOrNull($title) : ?string
    {
        if (! is_string($title)) {
            return null;
        }

        if (! strlen($title)) {
            return null;
        }

        $websiteAppName = $this->config->appNameShort;

        $title = ($title !== $websiteAppName)
            ? ($title . ' / ' . $websiteAppName)
            : ($title);

        return $title;
    }


    public function linkSeo($content, $url = true, ?string $title = null, ?array $attributes = null) : string
    {
        $theParseThrow = Lib::parseThrow();
        $theUrl = Lib::url();

        $attributesArray = $attributes ?? [];

        $linkString = $theParseThrow->link(
            $url, null, null,
            1,
            [ &$parseUrlResult ]
        );

        $serverHttpHost = $_SERVER[ 'HTTP_HOST' ] ?? null;

        $urlScheme = $parseUrlResult[ 'scheme' ] ?? null;
        $urlHost = $parseUrlResult[ 'host' ] ?? null;
        $urlFragment = $parseUrlResult[ 'fragment' ] ?? null;

        $isCustomScheme = ! in_array($urlScheme, [ 'http', 'https' ]);
        $isHostRemote = ($urlHost !== $serverHttpHost);
        $hasFragment = strlen($urlFragment);

        $urlString = $linkString;

        if ($isCustomScheme || $isHostRemote) {
            $attributesArray[ 'rel' ] = 'nofollow';
        }

        $isCurrentPage = false;
        if (! ($isCustomScheme || $isHostRemote || $hasFragment)) {
            $urlString = $theUrl->url($linkString);

            $isCurrentPage = ($urlString === $theUrl->url_current());
        }

        $isSecondInstance = isset($this->linkSeoList[ $urlString ]);

        if ($isSecondInstance) {
            $isCurrentPage = $this->linkSeoList[ $urlString ];

        } elseif ($isCurrentPage) {
            $this->linkSeoList[ $urlString ] = $isCurrentPage;
        }

        $html = ($isSecondInstance || $isCurrentPage)
            ? $this->linkClickableJavascript($content, $urlString, $title, $attributesArray)
            : $this->linkClickableHref($content, $urlString, $title, $attributesArray);

        return $html;
    }

    public function linkHref($content, $url = true, ?string $title = null, ?array $attributes = null) : string
    {
        $theParseThrow = Lib::parseThrow();
        $theUrl = Lib::url();

        $linkString = $theParseThrow->link(
            $url, null, null,
            1,
            [ &$parseUrlResult ]
        );

        $attributesArray = $attributes ?? [];

        $serverHttpHost = $_SERVER[ 'HTTP_HOST' ] ?? null;

        $urlScheme = $parseUrlResult[ 'scheme' ] ?? null;
        $urlHost = $parseUrlResult[ 'host' ] ?? null;
        $urlFragment = $parseUrlResult[ 'fragment' ] ?? null;

        $isCustomScheme = ! in_array($urlScheme, [ 'http', 'https' ]);
        $isHostRemote = ($urlHost !== $serverHttpHost);
        $hasFragment = strlen($urlFragment);

        $urlString = $linkString;

        if ($isCustomScheme || $isHostRemote) {
            $attributesArray[ 'rel' ] = 'nofollow';
        }

        if (! ($isCustomScheme || $isHostRemote || $hasFragment)) {
            $urlString = $theUrl->url($linkString);
        }

        $html = $this->linkClickableHref($content, $urlString, $title, $attributesArray);

        return $html;
    }


    /**
     * @param string|string[] $content
     * @param string          $url
     * @param string|null     $title
     * @param array|null      $attributes
     *
     * @return string
     */
    protected function linkClickableJavascript($content, $url = true, ?string $title = null, ?array $attributes = null) : string
    {
        $theUrl = Lib::url();

        $urlString = $theUrl->url($url);

        $attributesArray = $attributes ?? [];
        $attributesArray[ 'title' ] = $title;

        $attributeTarget = $attributesArray[ 'target' ] ?? null;
        $attributeOnclick = $attributeTarget === '_blank'
            ? "window.open('{$urlString}');"
            : "location.href='{$urlString}';";

        unset($attributesArray[ 'target' ]);
        $attributesArray[ 'onclick' ] = $attributeOnclick;

        $html = $this->tag('button', $content, []
            + [
                'type'  => 'button',
                'style' => 'display: inline-block; cursor: pointer;',
            ]
            + $attributesArray
        );

        return $html;
    }

    /**
     * @param string|string[] $content
     * @param string          $url
     * @param string|null     $title
     * @param array|null      $attributes
     *
     * @return string
     */
    protected function linkClickableHref($content, string $url, ?string $title = null, ?array $attributes = null) : string
    {
        $attributesArray = $attributes ?? [];
        $attributesArray[ 'href' ] = $url;
        $attributesArray[ 'title' ] = $title;

        $html = $this->tag('a', $content, $attributesArray);

        return $html;
    }
}
