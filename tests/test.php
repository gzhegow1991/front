<?php

// > настраиваем PHP
\Gzhegow\Lib\Lib::entrypoint()
    ->useAllRecommended($lock = false)
    //
    ->setDirRoot(__DIR__ . '/..')
    //
    ->useAll($lock = true)
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
        // > устанавливаем режим DEBUG, чтобы в HTML выводились пути к шаблонам
        $config->isDebug = true;
        //
        // > устанавливаем формат файла шаблона
        $config->fileExtension = 'phtml';
        //
        // > устанавливаем папку для шаблонов
        $config->directory = __DIR__ . '/html';
        //
        // > устанавливаем путь для ассетов, который будет добавлен, если в ->assetLocalSrc() использовать путь, начинающийся со `/`
        // > в начале работы инструмент добавляет Folder с именем `@root`, папкой `->directory` и внешним путём `->publicPath`
        $config->publicPath = '/html';
        //
        // > добавляем ещё папки, которые можно использовать для поиска шаблонов, а также при выведении ссылок на ассеты внутри них
        $config->folders = [
            // \Gzhegow\Front\Core\Struct\Folder::fromArray([ '@html', __DIR__ . '/html', '/html' ])->orThrow(),
        ];
        //
        // > добавляем внешние хранилища навроде CDN для выведения внешних ассетов
        $config->remotes = [
            // \Gzhegow\Front\Core\Struct\Remote::fromArray([ '@cdn', 'https://cdn.site.com' ])->orThrow(),
        ];
        //
        // > устанавливаем языки, чтобы resolver с их поддержкой мог искать шаблоны в языковых подпапках
        $config->templateLangCurrent = 'ru';
        $config->templateLangDefault = 'en';
        //
        // > можно задать расширения для поиска более подходящего файла - например, если изображения минифицируются вручную
        $config->assetExtensionsMap = [
            'gif'       => [
                'min.gif.webp' => true,
                'gif.webp'     => true,
                'min.gif'      => true,
                'gif'          => true,
            ],
            'gif.webp'  => [
                'min.gif.webp' => true,
                'gif.webp'     => true,
                'min.gif'      => true,
                'gif'          => true,
            ],
            //
            'jpg'       => [
                'min.jpg.webp' => true,
                'jpg.webp'     => true,
                'min.jpg'      => true,
                'jpg'          => true,
            ],
            'jpg.webp'  => [
                'min.jpg.webp' => true,
                'jpg.webp'     => true,
                'min.jpg'      => true,
                'jpg'          => true,
            ],
            //
            'jpeg'      => [
                'min.jpeg.webp' => true,
                'jpeg.webp'     => true,
                'min.jpeg'      => true,
                'jpeg'          => true,
            ],
            'jpeg.webp' => [
                'min.jpeg.webp' => true,
                'jpeg.webp'     => true,
                'min.jpeg'      => true,
                'jpeg'          => true,
            ],
            //
            'png'       => [
                'min.png.webp' => true,
                'png.webp'     => true,
                'min.png'      => true,
                'png'          => true,
            ],
            'png.webp'  => [
                'min.png.webp' => true,
                'png.webp'     => true,
                'min.png'      => true,
                'png'          => true,
            ],
            //
            'svg'       => [
                'min.svg' => true,
                'svg'     => true,
            ],
        ];
        //
        // > можно задать версию для локальных ассетов, чтобы использовать filemtime(realpath) для локальных, установите TRUE
        // $config->assetLocalVersion = true;
        $config->assetLocalVersion = '1.0.0';
        //
        // > можно задать версию для внешних ассетов, чтобы использовать filemtime для локальных, установите TRUE
        $config->assetRemoteVersion = '1.0.0';
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
$front->folderAdd([ $alias = '@html', $directory = __DIR__ . '/html', $publicPath = '/html' ]);
// $front->folderAdd([ '@blocks', __DIR__ . '/html/blocks', '/html/blocks', $publicPath = null ]);
// $front->folderAdd([ '@layouts', __DIR__ . '/html/layouts', '/html/layouts', $publicPath = null ]);
// $front->folderAdd([ '@modals', __DIR__ . '/html/modals', '/html/modals', $publicPath = null ]);
// $front->folderAdd([ '@pages', __DIR__ . '/html/pages', '/html/pages', $publicPath = null ]);

// > можно добавить `templateResolver`, чтобы, например, подключить языковые шаблоны или искать шаблон в нескольких папках
$front->templateResolverSet(new \Gzhegow\Front\Core\TemplateResolver\FrontI18nTemplateResolver());
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

        return $data[$name] ?? null;
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

// > можно добавить `assetLocalResolver`, чтобы, проверять несколько файлов перед выводом src и/или добавлять параметр версии
$front->assetResolverLocalSet(new \Gzhegow\Front\Core\AssetManager\ResolverLocal\FrontDefaultAssetResolverLocal());

// > можно добавить `assetRemoteResolver`, чтобы, проверять несколько файлов перед выводом src и/или добавлять параметр версии
$front->assetResolverRemoteSet(new \Gzhegow\Front\Core\AssetManager\ResolverRemote\FrontDefaultAssetResolverRemote());

// > создаем фасад, если удобно пользоваться статикой
\Gzhegow\Front\Front::setFacade($front);



// >>> ТЕСТЫ

// > TEST
// > так можно отрисовать шаблон с его содержимым
$fn = function () use ($ffn, $front) {
    $ffn->print('TEST 1');
    echo "\n";

    $beforeLangDefault = $front->templateLangDefaultSet(false);
    $beforeLangCurrent = $front->templateLangCurrentSet(false);

    $ffn->print($front->render('@html::pages/demo/page.demo.phtml'));

    $front->templateLangCurrentSet($beforeLangCurrent);
    $front->templateLangDefaultSet($beforeLangDefault);
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
    <img\n
        alt=\"Cat | Application\" title=\"Cat | Application\"\n
        src=\"/html/blocks/demo/img/cat-300x300.png?v=1.0.0\"\n
    />\n
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

    $beforeLangDefault = $front->templateLangDefaultSet(false);
    $beforeLangCurrent = $front->templateLangCurrentSet(false);

    $front->templateLangDefaultSet('ru');

    $front->templateLangCurrentSet('en'); // > будет использован `en`
    $ffn->print($front->render('@html::pages/demo/page.demo'));
    echo "\n";

    $front->templateLangCurrentSet('ru'); // > будет использован `ru`, совпадает с `default`
    $ffn->print($front->render('@html::pages/demo/page.demo'));
    echo "\n";

    $front->templateLangCurrentSet('unknown'); // > будет использован `default`
    $ffn->print($front->render('@html::pages/demo/page.demo'));

    $front->templateLangCurrentSet($beforeLangCurrent);
    $front->templateLangDefaultSet($beforeLangDefault);
};
$test = $ffn->test($fn);
$test->expectStdout('
"TEST 2"

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
');
$test->run();
