# ZhuyinPinyin

A PHP library that deals with Zhuyin (注音) to Pinyin (漢語拼音), Pinyin to Zhuyin.

https://github.com/localvar/zhuyin (Golang version)

The original idea is from a Golang library written by **Bomin Zhang**, I recoding it with PHP and using on [DictPedia project](https://en.dictpedia.org).

For example, this library will translate **zhang1** to **zhāng** (pinyin) or **ㄓㄤ** (zhuyin), **zhāng** or **ㄓㄤ** decode to **zhang1**, and also support **pinyin to zhuyin** and **zhuyin to pinyin**.

這個函式庫的原始邏輯設計出自以 Golang 編寫，因為本人需要用在[字典百科](https://zh.dictpedia.org)專案中，但尋無以 PHP 版本實現的注音轉拼音、拼音轉注音的函式庫，所以我把它從 Golang 改寫成 PHP 版本。

 * 此PHP 版與 Golang 原版的差別是捨棄字串陣列，分別讀取個別字元進去陣列以避免可能的 UTF-8字元問題
 * 原始邏輯已經處理的很好了，所以直接移植為 PHP 版本。只加了一個函式 splitString 來處理個別 UTF-8 字元

## Install

```
composer require dictpedia/zhuyin-pinyin
```

## Usage

```php
$zh = new \DictPedia\ZhuyinPinyin();
```
Resutn empty string if the input string is invaild.

## API

###encodePinyin###
```php
echo $zh->encodePinyin('zhang1');

// result: zhāng
```

###decodePinyin###
```php
echo $zh->decodePinyin('zhāng');

// result: zhang1
```

###encodeZhuyin###
```php
echo $zh->encodeZhuyin('zhang1');

// result: ㄓㄤ
```

###decodeZhuyin###
```php
echo $zh->decodeZhuyin('ㄓㄤ');

// result: zhang1
```

###pinyinToZhuyin###
```php
echo $zh->pinyinToZhuyin('zhāng');

// result: ㄓㄤ
```

###zhuyinToPinyin###
```php
echo $zh->zhuyinToPinyin('ㄓㄤ');

// result: zhāng
``` 

## License

MIT

https://packagist.org/packages/dictpedia/zhuyin-pinyin
