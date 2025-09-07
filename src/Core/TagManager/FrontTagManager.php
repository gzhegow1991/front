<?php

namespace Gzhegow\Front\Core\TagManager;

use Gzhegow\Lib\Lib;
use Gzhegow\Front\FrontInterface;
use Gzhegow\Front\Core\Store\FrontStore;


class FrontTagManager implements FrontTagManagerInterface
{
    /**
     * @var FrontStore
     */
    protected $frontStore;

    /**
     * @var array<string, bool>
     */
    protected $linkSeoTimes = [];


    public function initialize(FrontInterface $front) : void
    {
        $this->frontStore = $front->getStore();
    }


    public function tag(string $tag, $content, array $attributes = []) : string
    {
        $theType = Lib::type();

        $tagString = $theType->string_not_empty($tag)->orThrow();

        $lines = is_array($content)
            ? $content
            : ($content ? [ $content ] : []);

        $attributesArray = $attributes ?? [];

        $attributesArray['alt'] = $attributesArray['alt'] ?? '';
        $attributesArray['title'] = $attributesArray['title'] ?? $attributesArray['alt'] ?? '';

        $attributesArray['alt'] = ('' === $attributesArray['alt']) ? false : $this->attrAlt($attributesArray['alt']);
        $attributesArray['title'] = ('' === $attributesArray['title']) ? false : $this->attrTitle($attributesArray['title']);

        $htmlContent = implode("\n", $lines);
        $htmlContent = trim($htmlContent);

        $htmlAttributes = $this->attributes($attributesArray);

        return ""
            . "<{$tagString} {$htmlAttributes}>"
            /****/ . $htmlContent
            . "</{$tagString}>";
    }

    public function tagShort(string $tag, array $attributes = []) : string
    {
        $theType = Lib::type();

        $tagString = $theType->string_not_empty($tag)->orThrow();

        $attributesArray = $attributes ?? [];

        $attributesArray['alt'] = $attributesArray['alt'] ?? '';
        $attributesArray['title'] = $attributesArray['title'] ?? $attributesArray['alt'] ?? '';

        $attributesArray['alt'] = ('' === $attributesArray['alt']) ? false : $this->attrAlt($attributesArray['alt']);
        $attributesArray['title'] = ('' === $attributesArray['title']) ? false : $this->attrTitle($attributesArray['title']);

        $htmlAttributes = $this->attributes($attributesArray);

        return "<{$tagString} {$htmlAttributes} />";
    }


    /**
     * @param string|string[] $content
     */
    public function tagAButton($content, string $url, $title = null, array $attributes = []) : string
    {
        $theType = Lib::type();

        $uriString = $theType->uri($url)->orThrow();

        $attributesArray = $attributes ?? [];
        $attributesArray['title'] = $title;

        $attributeTarget = $attributesArray['target'] ?? null;
        $attributeOnclick = $attributeTarget === '_blank'
            ? "window.open('{$uriString}');"
            : "location.href='{$uriString}';";

        unset($attributesArray['target']);
        $attributesArray['onclick'] = $attributeOnclick;

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
     */
    public function tagAHref($content, string $url, $title = null, array $attributes = []) : string
    {
        $theType = Lib::type();

        $uriString = $theType->uri($url)->orThrow();

        $attributesArray = $attributes ?? [];
        $attributesArray['href'] = $uriString;
        $attributesArray['title'] = $title;

        $html = $this->tag('a', $content, $attributesArray);

        return $html;
    }

    public function tagImg(string $src, $alt, array $attributes = []) : string
    {
        $theType = Lib::type();

        $uriString = $theType->uri($src)->orThrow();

        $attributesArray = $attributes ?? [];
        $attributesArray['href'] = $uriString;
        $attributesArray['alt'] = $alt;

        $html = $this->tagShort('img', $attributesArray);

        return $html;
    }


    public function attributes(array $attributes = []) : string
    {
        if ( [] === $attributes ) {
            return '';
        }

        $content = [];

        foreach ( $attributes as $k => $v ) {
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


    public function attrAlt($alt, ?string $separator = null) : string
    {
        $separator = $separator ?? ' | ';

        $theType = Lib::type();

        $altString = $theType->string_not_empty($alt)->orThrow();

        $websiteAppName = $this->frontStore->appNameShort;

        if ( $altString !== $websiteAppName ) {
            $altString = "{$altString}{$separator}{$websiteAppName}";
        }

        return $altString;
    }

    public function attrAltOrNull($alt, ?string $separator = null) : ?string
    {
        $separator = $separator ?? ' | ';

        $theType = Lib::type();

        if ( null === $alt ) {
            return null;
        }

        if ( '' === $alt ) {
            return null;
        }

        if ( ! $theType->string_not_empty($alt)->isOk([ &$altString ]) ) {
            return null;
        }

        $websiteAppName = $this->frontStore->appNameShort;

        if ( $altString !== $websiteAppName ) {
            $altString = "{$altString}{$separator}{$websiteAppName}";
        }

        return $altString;
    }


    public function attrTitle($title, ?string $separator = null) : string
    {
        $separator = $separator ?? ' | ';

        $theType = Lib::type();

        $titleString = $theType->string_not_empty($title)->orThrow();

        $websiteAppName = $this->frontStore->appNameShort;

        if ( $titleString !== $websiteAppName ) {
            $titleString = "{$titleString}{$separator}{$websiteAppName}";
        }

        return $titleString;
    }

    public function attrTitleOrNull($title, ?string $separator = null) : ?string
    {
        $separator = $separator ?? ' | ';

        $theType = Lib::type();

        if ( null === $title ) {
            return null;
        }

        if ( '' === $title ) {
            return null;
        }

        if ( ! $theType->string_not_empty($title)->isOk([ &$titleString ]) ) {
            return null;
        }

        $websiteAppName = $this->frontStore->appNameShort;

        if ( $titleString !== $websiteAppName ) {
            $titleString = "{$titleString}{$separator}{$websiteAppName}";
        }

        return $titleString;
    }


    public function linkButton($content, string $url, $title = null, array $attributes = []) : string
    {
        $theType = Lib::type();

        $attributesArray = $attributes ?? [];

        $uriString = $theType->uri(
            $url, null, null,
            1, 1,
            [ &$parseUrlResult ]
        )->orThrow();

        $serverHttpHost = $_SERVER['HTTP_HOST'] ?? null;

        $urlScheme = ('' === $parseUrlResult['scheme']) ? null : $parseUrlResult['scheme'];
        $urlHost = ('' === $parseUrlResult['host']) ? null : $parseUrlResult['host'];

        $isCustomScheme = ! in_array($urlScheme, [ 'http', 'https' ]);
        $isHostRemote = ($serverHttpHost !== $urlHost);

        if ( $isCustomScheme || $isHostRemote ) {
            $attributesArray['rel'] = 'nofollow';
        }

        $html = $this->tagAButton($content, $uriString, $title, $attributesArray);

        return $html;
    }

    public function linkHref($content, string $url, $title = null, array $attributes = []) : string
    {
        $theType = Lib::type();

        $attributesArray = $attributes ?? [];

        $uriString = $theType->uri(
            $url, null, null,
            1, 1,
            [ &$parseUrlResult ]
        )->orThrow();

        $serverHttpHost = $_SERVER['HTTP_HOST'] ?? null;

        $urlScheme = ('' === $parseUrlResult['scheme']) ? null : $parseUrlResult['scheme'];
        $urlHost = ('' === $parseUrlResult['host']) ? null : $parseUrlResult['host'];

        $isCustomScheme = ! in_array($urlScheme, [ 'http', 'https' ]);
        $isHostRemote = ($serverHttpHost !== $urlHost);

        if ( $isCustomScheme || $isHostRemote ) {
            $attributesArray['rel'] = 'nofollow';
        }

        $html = $this->tagAHref($content, $uriString, $title, $attributesArray);

        return $html;
    }

    public function linkSeo($content, string $url, $title = null, array $attributes = []) : string
    {
        $theType = Lib::type();
        $theUrl = Lib::url();

        $attributesArray = $attributes ?? [];

        $uriString = $theType->uri(
            $url, null, null,
            1, 1,
            [ &$parseUrlResult ]
        )->orThrow();

        $serverHttpHost = $_SERVER['HTTP_HOST'] ?? null;

        $urlScheme = ('' === $parseUrlResult['scheme']) ? null : $parseUrlResult['scheme'];
        $urlHost = ('' === $parseUrlResult['host']) ? null : $parseUrlResult['host'];
        $urlFragment = ('' === $parseUrlResult['fragment']) ? null : $parseUrlResult['fragment'];

        $hasFragment = strlen($urlFragment);
        $isCustomScheme = ! in_array($urlScheme, [ 'http', 'https' ]);
        $isHostRemote = ($serverHttpHost !== $urlHost);

        if ( $isCustomScheme || $isHostRemote || $hasFragment ) {
            $isCurrentPage = false;

        } else {
            $urlCurrentString = $theUrl->url_current();

            $isCurrentPage = ($urlCurrentString === $uriString);
        }

        if ( $isCustomScheme || $isHostRemote ) {
            $attributesArray['rel'] = 'nofollow';

            $isSecondTime = false;

        } else {
            $isSecondTime = isset($this->linkSeoTimes[$uriString]);

            if ( ! $isSecondTime ) {
                $this->linkSeoTimes[$uriString] = true;
            }
        }

        $html = ($isSecondTime || $isCurrentPage)
            ? $this->tagAButton($content, $uriString, $title, $attributesArray)
            : $this->tagAHref($content, $uriString, $title, $attributesArray);

        return $html;
    }
}
