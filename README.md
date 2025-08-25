# Front

## Установить

```
composer require gzhegow/front
```

## Запустить тесты

```
php test.php
```

## Примеры и тесты

```php
<?php

// > настраиваем PHP
\Gzhegow\Lib\Lib::entrypoint()
    ->setDirRoot(__DIR__ . '/..')
    ->useAllRecommended()
;



// > добавляем несколько функций для тестирования
$ffn = new class {
    function root() : string
    {
        return realpath(__DIR__ . '/..');
    }


    function values($separator = null, ...$values) : string
    {
        return \Gzhegow\Lib\Lib::debug()->dump_values([], $separator, ...$values);
    }


    function print(...$values) : void
    {
        echo $this->values(' | ', ...$values) . PHP_EOL;
    }


    function test(\Closure $fn, array $args = []) : \Gzhegow\Lib\Modules\Test\TestCase
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);

        return \Gzhegow\Lib\Lib::test()->newTestCase()
            ->fn($fn, $args)
            ->trace($trace)
        ;
    }
};



// > сначала всегда фабрика
$factory = new \Gzhegow\Front\FrontFactory();

// > создаем конфигурацию
$config = new \Gzhegow\Front\Core\Config\FrontConfig();
$config->configure(
    static function (\Gzhegow\Front\Core\Config\FrontConfig $config) use ($ffn) {
        // >>> шаблонизатор
        $config->isDebug = true;
        //
        // > устанавливаем папку для шаблонов
        $config->directory = __DIR__ . '/disc/html';
        //
        // > устанавливаем формат файла шаблона
        $config->fileExtension = 'phtml';
        //
        // > устанавливаем путь для ассетов, который будет добавлен, если в ->localSrc() использовать путь, начинающийся со `/`
        $config->publicPath = '/disc/html';
        //
        // > добавляем папки, которые можно использовать для поиска шаблонов, и при выведении ассетов внутри них
        $config->folders = [
            \Gzhegow\Front\Core\Struct\Folder::fromArray([ '@disc', __DIR__ . '/disc', '/disc' ])->orThrow(),
        ];
        //
        // > добавляем внешние хранилища навроде CDN для выведения ассетов
        $config->remotes = [
            \Gzhegow\Front\Core\Struct\Remote::fromArray([ '@cdn', 'https://cdn.site.com' ])->orThrow(),
        ];
        //
        // > устанавливаем языки, чтобы resolver с их поддержкой мог искать шаблоны в языковых подпапках
        $config->templateLangCurrent = 'ru';
        $config->templateLangDefault = 'ru';
        //
        // > можно задать версию для ассетов, иначе для локальных будет использовано filemtime
        $config->assetVersion = '1.0.0';
        //
        // > можно задать расширения для проверки - например, если изображения минифицируются вручную
        $config->assetExtensionsMap = [
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
        //
        // > устанавливаем наименование приложения для менеджера тегов (генерация атрибутов title/alt)
        $config->tagAppNameShort = 'Application';
        $config->tagAppNameFull = 'MyApp | Application';
    }
);

// > создаем менеджер ассетов
// > его задача создавать ссылки на статические и внешние ресурсы для HTML-шаблонов
$assetManager = new \Gzhegow\Front\Core\AssetManager\FrontAssetManager();

// > создаем менеджер HTML-тегов
// > его задача создавать HTML теги для верстки в тех случаях, когда они управляются глобально и влияют на SEO
$tagManager = new \Gzhegow\Front\Core\TagManager\FrontTagManager();

// > создаем роутер
$front = new \Gzhegow\Front\FrontFacade(
    $factory,
    //
    $assetManager,
    $tagManager,
    //
    $config
);

// > можно добавить папки в регистр, чтобы вызывать их напрямую через ->render('@modals::{path}')
// > субъективно я предпочитаю использовать `@html::{путь}`, не разделяя каждую папку отдельно
$front->folderAdd([ $alias = '@html', $directory = __DIR__ . '/disc/html', $publicPath = '/disc/html' ]);
// $front->folderAdd([ '@blocks', __DIR__ . '/disc/html/blocks', '/disc/html/blocks', $publicPath = null ]);
// $front->folderAdd([ '@layouts', __DIR__ . '/disc/html/layouts', '/disc/html/layouts', $publicPath = null ]);
// $front->folderAdd([ '@modals', __DIR__ . '/disc/html/modals', '/disc/html/modals', $publicPath = null ]);
// $front->folderAdd([ '@pages', __DIR__ . '/disc/html/pages', '/disc/html/pages', $publicPath = null ]);
// $front->folderAdd([ '@sections', __DIR__ . '/disc/html/sections', '/disc/html/sections', $publicPath = null ]);

// > можно добавить `templateResolver`, чтобы, например, подключить языковые шаблоны или искать шаблон в нескольких папках
$front->templateResolver(new \Gzhegow\Front\Core\TemplateResolver\FrontI18nTemplateResolver());
// $front->templateResolverSet(new \Gzhegow\Front\Core\TemplateResolver\DefaultTemplateResolver());
// $front->templateResolverSet(new \Gzhegow\Front\Core\TemplateResolver\CallableTemplateResolver(
//     function (\League\Plates\Template\Name $name) { },
//     $fnArgs = [ 1, 2, 3 ]
// ));

// > можно установить собственные обработчик GET_ITEM (например, изнутри шаблонов позволит брать сервисы из контейнера)
$front->fnTemplateGetItem(
    static function (
        string $name, ?string $classT,
        \Gzhegow\Front\Package\League\Plates\Template\TemplateInterface $template
    ) {
        $data = $template->getData();

        return $data[ $name ] ?? null;
    }
);

// > можно установить собственные обработчики CATCH_ERROR (например, не бросать исключения, а заменять в строке шаблона ошибку на строку и просто логировать)
$front->fnTemplateCatchError(
    static function (
        \Throwable $e, string $content,
        \Gzhegow\Front\Package\League\Plates\Template\TemplateInterface $template
    ) {
        $eMessage = $e->getMessage();
        $templateName = $template->name();

        return $content . " [ ERROR : {$templateName} : {$eMessage} ]";
    }
);

// > можно добавить `assetLocalSrcResolver`, чтобы, проверять несколько файлов перед формированием src или добавлять параметр версии
$front->assetLocalSrcResolver(new \Gzhegow\Front\Core\AssetManager\LocalSrcResolver\FrontDefaultAssetLocalSrcResolver());

// > можно добавить `assetRemoteSrcResolver`, чтобы, проверять несколько файлов перед формированием src или добавлять параметр версии
$front->assetRemoteSrcResolver(new \Gzhegow\Front\Core\AssetManager\RemoteSrcResolver\FrontDefaultAssetRemoteSrcResolver());

// > создаем фасад, если удобно пользоваться статикой
\Gzhegow\Front\Front::setFacade($front);



// >>> ТЕСТЫ

// > TEST
// > так можно отрисовать шаблон с его содержимым
$fn = function () use ($ffn, $front) {
    $ffn->print('TEST 1');
    echo "\n";

    $beforeDefault = $front->templateLangDefault(false);
    $beforeCurrent = $front->templateLangCurrent(false);

    $ffn->print($front->render('@html::pages/demo/page.demo.phtml'));

    $front->templateLangCurrent($beforeCurrent);
    $front->templateLangDefault($beforeDefault);
};
$test = $ffn->test($fn);
$test->expectStdout('
"TEST 1"

"<!-- [ >>> layouts/demo/layout.demo.phtml ] -->\n
<div>Пример шаблона</div>\n
<div>\n
<!-- [ >>> pages/demo/page.demo.phtml ] -->\n
<!-- [ >>> blocks/demo/block.demo.phtml ] -->\n
<div>Пример блока</div>\n
<div>\n
    <img alt=\"Cat | Application\" src=\"/disc/html/blocks/demo/img/cat-300x300.png?v=1.0.0\" />\n
</div>\n
<!-- [ <<< blocks/demo/block.demo.phtml ] -->\n
<!-- [ <<< pages/demo/page.demo.phtml ] -->\n
</div>\n
<!-- [ <<< layouts/demo/layout.demo.phtml ] -->"
');
$test->run();

// > TEST
// > так можно отрисовать шаблон с его содержимым
$fn = function () use ($ffn, $front) {
    $ffn->print('TEST 2');
    echo "\n";

    $before = $front->templateLangDefault('ru');

    $front->templateLangCurrent('ru');
    $ffn->print($front->render('@html::pages/demo/page.demo.phtml'));
    echo "\n";

    $front->templateLangCurrent('en');
    $ffn->print($front->render('@html::pages/demo/page.demo'));
    echo "\n";

    // > будет использован `default`, то есть `ru`
    $front->templateLangCurrent('unknown');
    $ffn->print($front->render('@html::pages/demo/page.demo'));

    $front->templateLangDefault($before);
};
$test = $ffn->test($fn);
$test->expectStdout('
"TEST 2"

"<!-- [ >>> layouts/demo/ru/layout.demo.phtml ] -->\n
<div>Пример шаблона</div>\n
<div>\n
<!-- [ >>> pages/demo/page.demo.phtml ] -->\n
<!-- [ >>> blocks/demo/ru/block.demo.phtml ] -->\n
<div>Пример блока</div>\n
<!-- [ <<< blocks/demo/ru/block.demo.phtml ] -->\n
<!-- [ <<< pages/demo/page.demo.phtml ] -->\n
</div>\n
<!-- [ <<< layouts/demo/ru/layout.demo.phtml ] -->"

"<!-- [ >>> layouts/demo/en/layout.demo.phtml ] -->\n
<div>Demo Layout</div>\n
<div>\n
<!-- [ >>> pages/demo/page.demo.phtml ] -->\n
<!-- [ >>> blocks/demo/en/block.demo.phtml ] -->\n
<div>Demo Block</div>\n
<!-- [ <<< blocks/demo/en/block.demo.phtml ] -->\n
<!-- [ <<< pages/demo/page.demo.phtml ] -->\n
</div>\n
<!-- [ <<< layouts/demo/en/layout.demo.phtml ] -->"

"<!-- [ >>> layouts/demo/layout.demo.phtml ] -->\n
<div>Пример шаблона</div>\n
<div>\n
<!-- [ >>> pages/demo/page.demo.phtml ] -->\n
<!-- [ >>> blocks/demo/block.demo.phtml ] -->\n
<div>Пример блока</div>\n
<div>\n
    <img alt=\"Cat | Application\" src=\"/disc/html/blocks/demo/img/cat-300x300.png?v=1.0.0\" />\n
</div>\n
<!-- [ <<< blocks/demo/block.demo.phtml ] -->\n
<!-- [ <<< pages/demo/page.demo.phtml ] -->\n
</div>\n
<!-- [ <<< layouts/demo/layout.demo.phtml ] -->"
');
$test->run();
```

