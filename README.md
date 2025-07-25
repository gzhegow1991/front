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
    //
    ->useAll()
;


// > добавляем несколько функций для тестирования
$ffn = new class {
    function root() : string
    {
        return realpath(__DIR__ . '/..');
    }


    function values($separator = null, ...$values) : string
    {
        return \Gzhegow\Lib\Lib::debug()->values([], $separator, ...$values);
    }


    function print(...$values) : void
    {
        echo $this->values(' | ', ...$values) . PHP_EOL;
    }


    function test(\Closure $fn, array $args = []) : \Gzhegow\Lib\Modules\Test\Test
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);

        return \Gzhegow\Lib\Lib::test()->newTest()
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
    function (\Gzhegow\Front\Core\Config\FrontConfig $config) use ($ffn) {
        // >>> шаблонизатор
        $config->isDebug = true;
        //
        // > устанавливаем папку и формат файла
        $config->directory = __DIR__ . '/../disc/html';
        $config->fileExtension = 'phtml';
        //
        // > подключаем работу с языковыми шаблонами
        $config->langCurrent = 'ru';
        $config->langDefault = 'ru';

        // >>> менеджер тегов
        $config->tagManager->appNameFull = 'My Website | A personal Portfolio';
        $config->tagManager->appNameShort = 'My Website';
    }
);

// > создаем менеджер HTML-тегов
// > его задача создавать HTML теги для верстки в тех случаях, когда они управляются глобально и влияют на SEO
$tagManager = new \Gzhegow\Front\Core\TagManager\FrontTagManager($config->tagManager);

// > создаем роутер
$front = new \Gzhegow\Front\FrontFacade(
    $factory,
    //
    $tagManager,
    //
    $config
);

// > можно добавить папки в регистр, чтобы вызывать их напрямую через ->render('modals::{path}')
// > субъективно я предпочитаю использовать html::{путь}, не разделяя каждую папку отдельно
$front->folderAdd('html', __DIR__ . '/../disc/html');
// $plates->folderAdd('blocks', __DIR__ . '/../disc/html/blocks');
// $plates->folderAdd('layouts', __DIR__ . '/../disc/html/layouts');
// $plates->folderAdd('modals', __DIR__ . '/../disc/html/modals');
// $plates->folderAdd('pages', __DIR__ . '/../disc/html/pages');
// $plates->folderAdd('sections', __DIR__ . '/../disc/html/sections');

// > получаем экземпляр стора, который нужен для резольвера
// > стор хранит состояние шаблонизатора, и позволяет им делится с другими объектами
$store = $front->getStore();

// > создаем резольвер
// > его задача поиск шаблонов в папке до попытки их отрисовать, иначе исключение будет брошено на include $file
// > кроме того это позволяет подменять одни шаблоны на другие
// > после того, как синтаксис превратится в имя файла, например, пробовать найти языковой шаблон
$resolver = new \Gzhegow\Front\Package\League\Plates\Template\ResolveTemplatePath\LanguageNameAndFolderResolveTemplatePath($store);
$front->resolverSet($resolver);

// > создаем фасад, если удобно пользоваться статикой
\Gzhegow\Front\Front::setFacade($front);



// >>> ТЕСТЫ

// > TEST
// > так можно отрисовать шаблон с его содержимым
$fn = function () use ($ffn, $front) {
    $ffn->print('TEST 1');
    echo "\n";

    $before = $front->langDefaultSet('ru');

    $front->langCurrentSet('ru');
    $ffn->print($front->render('html::pages/demo/page.demo'));
    echo "\n";

    $front->langCurrentSet('en');
    $ffn->print($front->render('html::pages/demo/page.demo'));
    echo "\n";

    // > будет использован `default`, то есть `ru`
    $front->langCurrentSet('unknown');
    $ffn->print($front->render('html::pages/demo/page.demo'));

    $front->langDefaultSet($before);
};
$test = $ffn->test($fn);
$test->expectStdout('
"TEST 1"

"<div>Пример шаблона</div>\n
<div>\n
    <div>Пример блока</div>\n
</div>"

"<div>Demo Layout</div>\n
<div>\n
    <div>Demo Block</div>\n
</div>"

"<div>Пример шаблона</div>\n
<div>\n
    <div>Пример блока</div>\n
</div>"
');
$test->run();
```

