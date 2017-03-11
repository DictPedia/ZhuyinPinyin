# ZhuyinPinyin

A PHP library that deals with Zhuyin (注音) to Pinyin (漢語拼音), Pinyin to Zhuyin.

https://github.com/localvar/zhuyin (Golang version)

The original idea is from a Golang library written by **Bomin Zhang**, I recoding it with PHP and using on [DictPedia project](https://en.dictpedia.org).

For example, this library will translate **zhang1** to **zhāng** (pinyin) or **ㄓㄤ** (zhuyin), **zhāng** or **ㄓㄤ** decode to **zhang1**, and also support **pinyin to zhuyin** and **zhuyin to pinyin**.

## Usage

* ecodePinyin: **zhang1** to **zhāng**
* decodePinyin: **zhāng** to **zhang1**
* encodeZhuyin: **zhang1** to **ㄓㄤ**
* decodeZhuyin: **ㄓㄤ** to **zhang1**
* pinyinToZhuyin: **zhāng** to **ㄓㄤ**
* zhuyinToPinyin: **ㄓㄤ** to **zhāng**

# 注音拼音互轉

這個函式庫的原始邏輯設計出自以 Golang 編寫，因為本人需要用在[字典百科](https://zh.dictpedia.org)專案中，但尋無以 PHP 版本實現的注音轉拼音、拼音轉注音的函式庫，所以我把它從 Golang 改寫成 PHP 版本。

主要功能是把**zhang1** 這樣的輸入轉換成**zhāng**（拼音）或**ㄓㄤ**（注音），也可以把**zhāng** 或**ㄓㄤ* * 轉換回**zhang1**，同時也支持**拼音** 和**注音** 之間的互轉。

## 使用方法

這個庫對外暴露了六個函數，每個函數都只有一個字符串型的輸入參數和一個字符串型的返回值，當輸入正確時，返迴轉換結果；如果輸入有錯誤，則返回空字符串。下面是這六個函數的簡介。

* ecodePinyin：把 **zhang1** 轉換成 **zhāng**
* decodePinyin：把 **zhāng** 轉換成 **zhang1**
* encodeZhuyin：把 **zhang1** 轉換成 **ㄓㄤ**
* decodeZhuyin：把 **ㄓㄤ** 轉換成 **zhang1**
* pinyinToZhuyin：把 **zhāng** 轉換成 **ㄓㄤ**
* zhuyinToPinyin：把 **ㄓㄤ** 轉換成 **zhāng**

## 授權許可

**MIT** 許可證發布。
