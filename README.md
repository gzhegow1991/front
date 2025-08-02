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
    function (\Gzhegow\Front\Core\Config\FrontConfig $config) use ($ffn) {
        // >>> шаблонизатор
        $config->isDebug = true;
        //
        // > устанавливаем папку и формат файла
        $config->directory = __DIR__ . '/../disc/html';
        $config->fileExtension = 'phtml';
        //
        // > можно установить собственные обработчики RESOLVE (подключить языки?), GET (подключить контейнер?) и CATCH (не бросать исключения?) для шаблонов
        $config->fnTemplateGet = function (string $name, \Gzhegow\Front\Package\League\Plates\Template\TemplateInterface $template) {
            $data = $template->getData();

            return $data[ $name ] ?? null;
        };
        $config->fnTemplateCatch = function (\Throwable $e, string $content, \Gzhegow\Front\Package\League\Plates\Template\TemplateInterface $template) {
            $eMessage = $e->getMessage();
            $templateName = $template->name();

            return $content . " [ ERROR : {$templateName} : {$eMessage} ]";
        };
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

// > можно добавить resolver, чтобы, например, подключить языковые шаблоны или искать шаблон в нескольких папках
$front->resolverSet(new \Gzhegow\Front\Core\Resolver\FrontI18nResolver());
// $front->resolverSet(new \Gzhegow\Front\Core\Resolver\DefaultResolver());
// $front->resolverSet(new \Gzhegow\Front\Core\Resolver\CallableResolver(
//     function (\League\Plates\Template\Name $name) { },
//     $fnArgs = [ 1, 2, 3 ]
// ));

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
    $ffn->print($front->render('html::pages/demo/page.demo.phtml'));
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

