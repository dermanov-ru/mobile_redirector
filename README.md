# Описание
С помощью данного класса можно настроить редирект на мобильный поддомен для конкретных разделов или страниц сайта.
Это особенно полезно, когда мобильная версия содержит урезанный функционал и сделать один редирект для всего сайта невозможно.

Данная реализация заточена под битрикс, ее легко можно изменить.

Класс получает текущий роут и ищет подходящее правило для редиректа. 
Если правило нашлось и пользователь зашел с мобильного устройства - происходит редирект.
Если правило нашлось и пользователь зашел с компьютера - не происходит редирект, но добавляется мета-тег `alternative`
```
Asset::getInstance()->addString('<link rel="alternate" href="' . $mobileRout . '" media="only screen and (max-width: 767px)"/>');
```

### Примеры редиректов
```
site.ru/ --> m.site.ru/
site.ru/cart/ --> m.site.ru/cart/
site.ru/catalog/product-1/ --> m.site.ru/catalog/product-1/
```

# Использование

```php
// bitrix/php_interface/s1/init.php

/*
 * Точечно перенапраляем мобильных пользователей на мобильную версию.
 * Тк не все разделы сайта поддерживают мобильную версию.
 * Нужно задать роуты, для которых существует мобильная версия и запустить обработку запроса.
 * */
$mobileRedirector = new \Dermanov\MobileRedirector("https://m.site.ru");
$mobileRedirector
    ->addStrictRout("/")
    ->addStrictRout("/cart/")
    ->addPatternRout("/catalog/")
    ->addPatternRout("/collections/")
    ->addStrictRout("/content/wholesale/")
    ->handleRequest($APPLICATION->GetCurPage());
```