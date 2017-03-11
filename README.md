# ZhuyinPinyin

A PHP library that deals with Zhuyin (注音) to Pinyin (漢語拼音), Pinyin to Zhuyin.

https://github.com/localvar/zhuyin (Golang version)

The original idea is from a Golang library written by **Bomin Zhang**, I recoding it with PHP and using on [DictPedia project](https://en.dictpedia.org).

For example, this library will translate **zhang1** to **zhāng** (pinyin) or **ㄓㄤ** (zhuyin), **zhāng** or **ㄓㄤ** decode to **zhang1**, and also support **pinyin to zhuyin** and **zhuyin to pinyin**.

這個函式庫的原始邏輯設計出自以 Golang 編寫，因為本人需要用在[字典百科](https://zh.dictpedia.org)專案中，但尋無以 PHP 版本實現的注音轉拼音、拼音轉注音的函式庫，所以我把它從 Golang 改寫成 PHP 版本。

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

###ecodePinyin###
```php
echo $zh->ecodePinyin('zhang1');

// result: ㄓㄤ
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
