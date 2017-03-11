<?php
/**
 * Author: Terry Lin (terrylinooo)
 * Original Golang version: https://github.com/localvar/zhuyin/blob/master/zhuyin.go
 * URL: https://github.com/DictPedia/ZhuyinPinyin/
 * License: MIT
 *
 * 此PHP 版與 Golang 原版的差別是捨棄字串陣列，分別讀取個別字元進去陣列以避免可能的 UTF-8字元問題
 * 原始邏輯已經處理的很好了，所以直接移植為 PHP 版本。只加了一個函式 splitString 來處理個別 UTF-8 字元
 * 
 */
 
namespace DictPedia; 

class ZhuyinPinyin {

    var $map_z2p;
    var $map_p2z;
    var $pinyin_tones;
    var $zhyuin_tones;

    /**
     * ZhuPin constructor.
     */
    public function __construct()
    {
        $this->map_p2z = json_decode(
            // Consonant
            '{
            "b": "ㄅ", "p": "ㄆ", "m": "ㄇ", "f": "ㄈ",
            "d": "ㄉ", "t": "ㄊ", "n": "ㄋ", "l": "ㄌ",
            "g": "ㄍ", "k": "ㄎ", "h": "ㄏ",
            "j": "ㄐ", "q": "ㄑ", "x": "ㄒ",
            "zh": "ㄓ", "ch": "ㄔ", "sh": "ㄕ", "r": "ㄖ",
            "z": "ㄗ", "c": "ㄘ", "s": "ㄙ",
            "y": "一", "w": "ㄨ",
            ' .
            // Rhymes
            // 'ue' is same as 've', for typo
            '
            "a": "ㄚ", "o": "ㄛ", "e": "ㄜ",
            "i": "一", "u": "ㄨ", "v": "ㄩ",
            "ai": "ㄞ", "ei": "ㄟ", "ui": "ㄨㄟ",
            "ao": "ㄠ", "ou": "ㄡ", "iu": "一ㄡ",
            "an": "ㄢ", "en": "ㄣ", "in": "一ㄣ",
            "ang": "ㄤ", "eng": "ㄥ", "ing": "一ㄥ",
            "ong": "ㄨㄥ", "ie": "一ㄝ", "er": "ㄦ",
            "ue": "ㄩㄝ", "ve": "ㄩㄝ",
            "un": "ㄨㄣ", "vn": "ㄩㄣ", "ia": "一ㄚ",
            "ua": "ㄨㄚ", "uan": "ㄨㄢ", "van": "ㄩㄢ",
            "uai": "ㄨㄞ", "uo": "ㄨㄛ", "iong": "ㄩㄥ",
            "iang": "一ㄤ", "uang": "ㄨㄤ", "ian": "一ㄢ",
            "iao": "一ㄠ"
            }', true);

        $this->map_z2p = json_decode(
            // Consonant
            '{
            "ㄅ": "b", "ㄆ": "p", "ㄇ": "m", "ㄈ": "f",
            "ㄉ": "d", "ㄊ": "t", "ㄋ": "n", "ㄌ": "l",
            "ㄍ": "g", "ㄎ": "k", "ㄏ": "h",
            "ㄐ": "j", "ㄑ": "q", "ㄒ": "x",
            "ㄓ": "zh", "ㄔ": "ch", "ㄕ": "sh", "ㄖ": "r",
            "ㄗ": "z", "ㄘ": "c", "ㄙ": "s",
            ' .
            // Rhymes
            '
            "ㄚ": "a", "ㄛ": "o", "ㄜ": "e", "ㄝ": "e",
            "一": "i", "ㄨ": "u", "ㄩ": "v",
            "ㄞ": "ai", "ㄟ": "ei", "ㄦ": "er",
            "ㄠ": "ao", "ㄡ": "ou",
            "ㄢ": "an", "ㄣ": "en",
            "ㄤ": "ang", "ㄥ": "eng",

            "ㄨㄥ": "ong", "一ㄝ": "ie",
            "一ㄡ": "iu", "一ㄣ": "in", "一ㄥ": "ing",
            "ㄩㄝ": "ve",
            "ㄨㄣ": "un", "ㄩㄣ": "vn", "一ㄚ": "ia",
            "ㄨㄚ": "ua", "ㄨㄢ": "uan", "ㄩㄢ": "van",
            "ㄨㄞ": "uai", "ㄨㄛ": "uo", "ㄩㄥ": "iong",
            "一ㄤ": "iang", "ㄨㄤ": "uang", "一ㄢ": "ian",
            "一ㄠ": "iao", "ㄨㄟ": "ui"
            }', true);

        // 'y' and 'w' is not included because '一' and 'ㄨ' are already
        // mapped to 'i' and 'u'
        // "一": "y", "ㄨ": "w",

        $this->pinyin_tones = [
            ["a", "ā", "á", "ǎ", "à"],
            ["o", "ō", "ó", "ǒ", "ò"],
            ["e", "ē", "é", "ě", "è"],
            ["i", "ī", "í", "ǐ", "ì"],
            ["u", "ū", "ú", "ǔ", "ù"],
            ["ü", "ǖ", "ǘ", "ǚ", "ǜ"]
        ];

        $this->zhuyin_tones = [
            "˙",
            "",
            "ˊ",
            "ˇ",
            "ˋ"
        ];
    }

    /**
     * @param $c
     * @param $tone
     * @return mixed
     */
    public function getTonalMark($c, $tone)
    {
        if ($c == 'v') {
            $c = 'ü';
        }

        foreach ($this->pinyin_tones as $t) {
            if ($c == $t[0]) {
                return $t[$tone];
            }
        }
        throw new Exception('IMPOSSIBLE: should not run to here.');
    }

    /**
     * @param $s
     * @param $tone
     * @return mixed|string
     */
    public function toneRhymes($s, $tone)
    {
        $ss = $this->splitString($s);

        if (mb_strlen($s) == 1) {
            return $this->getTonalMark($ss[0], $tone);
        }

        $a = (string) $ss[0];
        $b = (string) $ss[1];

        if ($a == 'a' or (($a == 'o' or $a == 'e') and $b != 'a') or !$this->isRhymes($b)) {
            return $this->getTonalMark($a, $tone) . mb_substr($s, 1);
        }

        return $a . $this->getTonalMark($b, $tone) . mb_substr($s, 2);

    }

    /**
     * @param $b
     * @return bool
     *
     * return true if the input character is rhymes, otherwise return false
     */
    public function isRhymes($b)
    {
        return ($b == 'a' or $b == 'e' or $b == 'i' or $b == 'o' or $b == 'u' or $b == 'v');
    }

    /**
     * @param $b
     * @return bool
     *
     * return true if the input character is consonant, otherwise return false
     */
    public function isConsonant($b)
    {
        return ($b >= 'a' and $b <= 'z' and !$this->isRhymes($b));
    }

    /**
     * @param $s
     * @return array (string, string, int)
     *
     * split the input string into consonant, rhymes and tone
     * for example: 'zhang1' will be split to consonant 'zh', rhymes 'ang' and
     *              tone '1'
     * returns an empty rhymes in case an error
     */
    public function split($s)
    {
        $ss = $this->splitString($s);

        $i = 0;

        for (; $i < mb_strlen($s); $i++) {
            $c = $ss[$i];

            if (!$this->isConsonant($c)) {
                break;
            }
        }

        if ($i !== 0) {
            $consonant = mb_substr($s, 0, $i);
        } else {
            $consonant = $s[0];
        }

        for (; $i < mb_strlen($s); $i++) {
            $c = $ss[$i];

            if ($c < 'a' or $c > 'z') {
                break;
            }
        }

        $rhymes = substr($s, mb_strlen($consonant), $i - mb_strlen($consonant));

        // rhymes could not be empty, and the length of tone is at most 1
        if (mb_strlen($rhymes) == 0 or (mb_strlen($s) - $i > 2)) {
            return ['', '', 0];
        }

        if ($i < mb_strlen($s)) {
            $tone = str_replace('0', '', $ss[$i]);

            if ($tone < 0 or $tone > 4) {
                return ['', '', 0];
            }
        }

        return [$consonant, $rhymes, $tone];
    }

    /**
     * @param $consonant
     * @param $rhymes
     * @param $tone
     * @return string
     *
     * encodePinyin encode the input consonant, rhymes and tone into Pinyin
     * for example: encodePinyin("zh", "ang", 1) outputs 'zhāng'
     * return an empty string in case an error
     */
    private function _encodePinyin($consonant, $rhymes, $tone)
    {
        $arr_consonant = $this->splitString($consonant);
        $arr_rhymes    = $this->splitString($rhymes);

        //echo $consonant .'.'. $rhymes .'.'. $tone;
        // rhymes could not be empty and the maximum value of tone is 4
        if (mb_strlen($rhymes) == 0 or $tone > 4) {

            return '';
        }

        if (mb_strlen($consonant) > 0) {
            // is it an valid consonant?
            if (!$this->isConsonant($arr_consonant[0])) {

                return '';
            }

            $ok = $this->map_p2z[$consonant];

            if (!$ok) {
                return '';
            }
            unset($ok);

            // convert rhymes 'ü' to 'u' if consonant is 'j', 'q', 'x' or 'y'
            if ($arr_rhymes[0] == 'v') {
                $c = $arr_consonant[0];

                if ($c == 'j' or $c == 'q' or $c == 'x' or $c == 'y') {
                    $rhymes = 'u' . mb_substr($rhymes, 1);
                }
            }
        }

        // is it an valid rhymes?
        if (!$this->isRhymes($arr_rhymes[0])) {
            return '';
        }

        $ok = $this->map_p2z[$rhymes];

        if (!$ok) {
            return '';
        }
        unset($ok);

        // tone the rhymes and convert 'v' to 'ü'

        $rhymes = $this->toneRhymes($rhymes, $tone);

        if ($arr_rhymes[0] == 'v') {
            $rhymes = "ü" . mb_substr($rhymes, 1);
        }

        return $consonant . $rhymes;
    }


    /**
     * @param $s
     * @return string
     */
    public function encodePinyin($s) {
        // the special case
        if ($s == "e5") {
            return "ê";
        }

        $v = $this->split($s);

        return $this->_encodePinyin($v[0], $v[1], $v[2]);
    }


    /**
     * @param $s
     * @return array
     *
     * _decodeRhymes decode the input string into rhymes and tone
     * for example: decodeRhymes("āng") outputs 'ang' and 1
     * returns an empty rhymes in case an error
     */
    private function _decodeRhymes($s) {
        $tone = '';
        $rhymes = '';
        $s_splited = $this->splitString($s);

        foreach ($s_splited as $ch) {
            foreach ($this->pinyin_tones as $t) {
                for ($j = 1; $j < count($t); $j++) {
                    if ($ch == $t[$j]) {
                        $ch = $t[0];
                        if ($tone > 0) {
                            return ['', 0];
                        }
                        $tone = $j;
                    }
                }
            }
            if ($ch == 'ü') {
                $ch = 'v';
            }
            $rhymes = $rhymes . $ch;
        }
        return [$rhymes, $tone];
    }


    /**
     * @param $s
     * @return array
     *
     * _decodePinyin decode the input string into consonant, rhymes and tone
     * for example: decodePinyin("zhāng") outputs 'zh','ang' and 1
     * return an empty rhymes in case an error
     */
    private function _decodePinyin($s)
    {
        $consonant = '';
        $rhymes = '';

        $arr_s = $this->splitString($s);

        // split the input into consonant and rhymes(toned)
        for ($i = 0; $i < mb_strlen($s); $i++) {
            $c = $arr_s[$i];

            if (!$this->isConsonant($c)) {
                $consonant = mb_substr($s, 0, $i);
                $rhymes = mb_substr($s, $i);
                break;
            }
        }

        // is it an valid consonant?
        if (mb_strlen($consonant) > 0) {

            $ok = $this->map_p2z[$consonant];

            if (!$ok) {
                return ['', '', 0];
            }
            unset($ok);

        }

        // decode the toned rhymes into rhymes and tone
        $arr_decode_rhymes = $this->_decodeRhymes($rhymes);

        if (!empty($arr_decode_rhymes[0])) {
            $rhymes = $arr_decode_rhymes[0];
            $tone = $arr_decode_rhymes[1];
        } else {
            return ['', '', 0];
        }

        if (mb_strlen($rhymes) == 0) {
            return ['', '', 0];
        }

        if (mb_strlen($consonant) > 0 and $rhymes[0] == 'u') {
            $c = $consonant[0];

            if ($c == 'j' or $c == 'q' or $c == 'x' or $c == 'y') {
                $rhymes = 'v' . mb_substr($rhymes, 1);
            }
        }

        // is it an valid rhymes?
        $ok = $this->map_p2z[$rhymes];

        if (!$ok) {
            return ['', '', 0];
        }
        unset($ok);

        return [$consonant, $rhymes, $tone];
    }

    /**
     * @param $s
     * @return string
     */
    public function decodePinyin($s)
    {
        if ($s == "ê") {
            return "e5";
        }

        $arr_decode_pinyin = $this->_decodePinyin($s);

        if (!empty($arr_decode_pinyin[1])) {
            $consonant = $arr_decode_pinyin[0];
            $rhymes = $arr_decode_pinyin[1];
            $tone = $arr_decode_pinyin[2];
        } else {
            return '';
        }

        if (mb_strlen($rhymes) == 0) {
            return '';
        }

        return $consonant . $rhymes . $tone;
    }


    /**
     * @param $consonant
     * @param $rhymes
     * @param $tone
     * @return string
     */
    private function _encodeZhuyin($consonant, $rhymes, $tone)
    {
        if (mb_strlen($rhymes) == 0) {
            return '';
        }

        if ($rhymes[0] == 'u' and mb_strlen($consonant) > 0) {
            $c = $consonant[0];

            if ($c == 'j' or $c == 'q' or $c == 'x' or $c == 'y') {
                $rhymes = 'v' . mb_substr($rhymes, 1);
            }
        }

        // the special cases for 'Zheng3 Ti3 Ren4 Du2'

        if ($rhymes == 'i') {
            if ($consonant == 'zh' or $consonant == 'ch' or $consonant == 'sh' or
                $consonant == 'r'  or $consonant == 'z'  or $consonant == 'c'  or
                $consonant == 's'  or $consonant == 'y') {
                $rhymes = '';
            }
        } else if ($consonant == 'w') {
            if ($rhymes == 'u') {
                $consonant = '';
            }
        } else if ($consonant == 'y') {
            if ($rhymes == 'v'   or $rhymes == 'e'   or $rhymes == 've' or $rhymes == 'in' or
                $rhymes == 'van' or $rhymes == 'ing' or $rhymes == 'vn') {
                $consonant = '';
            }
        }

        // consonant must be valid
        if (mb_strlen($consonant) > 0) {

            $ok = $this->map_p2z[$consonant];

            if (!$ok) {
                return '';
            } else {
                $consonant = $ok;
            }
            unset($ok);
        }

        // rhymes must be valid
        if (mb_strlen($rhymes) > 0) {
            $ok = $this->map_p2z[$rhymes];

            if (!$ok) {
                return '';
            } else {
                $rhymes = $ok;
            }
            unset($ok);
        }

        return $consonant . $rhymes . $this->zhyuin_tones[$tone];
    }


    // encodeZhuyin encode the input string into Zhuyin
    // for example: encodeZhuyin("min2") outputs 'ㄇ一ㄣˊ'
    // return an empty string in case an error
    /**
     * @param $s
     * @return string
     */
    public function encodeZhuyin($s)
    {
        if ($s == 'e5') {
            return 'ㄝ';
        }

        $v = $this->split($s);

	    return $this->_encodeZhuyin($v[0], $v[1], $v[2]);
    }


    /**
     * @param $s
     * @return array
     *
     * decodeZhuyin decode the input string into consonant, rhymes and tone
     * for example: decodeZhuyin("ㄇ一ㄣˊ") outputs 'm','in' and 2
     * return an empty rhymes in case an error
     */
    private function _decodeZhuyin($s)
    {
	    $consonant = '';
        $rhymes = '';
	    $tone = 1;

	    // split input into consonant, rhymes and tone
        $s_splited = $this->splitString($s);

        $i = 0;
        foreach ($s_splited as $ch) {
            // if the character is consonant or rhymes
            if (!empty($this->map_z2p[$ch])) {

                $ok = $this->map_z2p[$ch];
                // if it is the 1st character and it is consonant
                if ($i == 0 and $this->isConsonant($ok[0])) {
                    $consonant = $ok;


                } else {
                    // add it to rhymes, note, rhymes is still Zhuyin
                    $rhymes = $rhymes . $ch;
                }

                $i++;
                continue;
            }

            if ($i == 0) {
                return ['', '', 0];
            }

            // try to find the tone

            $st = mb_substr($s, $i);

            $j = 0;
            foreach ($this->zhuyin_tones as $t) {
                if ($st == $t) {
                    $tone = $j;
                    break;
                }
                $j++;
            }
            //return ['', '', 0];
        }


        if (mb_strlen($rhymes) == 0) {
            // if it is 'Zheng3 Ti3 Ren4 Du2', the rhymes should be 'i'
            if ($consonant == 'zh' or $consonant == 'ch' or $consonant == 'sh' or
                $consonant == 'r'  or $consonant == 'z'  or $consonant == 'c'  or
                $consonant == 's') {
                $rhymes = 'i';
            }

            // rhymes will be empty if not 'Zheng3 Ti3 Ren4 Du2',
            // this is an error case, will be handled outside
            return [$consonant, $rhymes, $tone];
        }



        // is it an valid rhymes?
        $rhymes = $this->map_z2p[$rhymes];

        if (!$rhymes or !$this->isRhymes($rhymes[0])) {
            return ['', '', 0];
        }

        if (mb_strlen($consonant) == 0) {
            // first:  check if it is 'Zheng3 Ti3 Ren4 Du2',
            // second: remove leading 'u' and set consonant to 'w', or
            //         remove leading 'i' and set consonant to 'y',
            // last:   check for the very special case,
            //         'ong' need to be converted to 'weng'
            if ($rhymes == 'i'   or $rhymes == 'v'  or $rhymes == 'e'   or
                $rhymes == 've'  or $rhymes == 'in' or $rhymes == 'van' or
                $rhymes == 'ing' or $rhymes == 'vn') {
                $consonant = 'y';
            } else if ($rhymes == 'u') {
                $consonant = 'w';
            } else if ($rhymes[0] == 'u') {
                $consonant = 'w';
                $rhymes = substr($rhymes, 1);
            } else if ($rhymes[0] == 'i') {
                $consonant = 'y';
                $rhymes = substr($rhymes, 1);
            } else if ($rhymes == 'ong') {
                $consonant = 'w';
                $rhymes = 'eng';
            }
        }
        return [$consonant, $rhymes, $tone];
    }


    /**
     * @param $s
     * @return string
     *
     * decodePinyin decode the input Zhuyin
     * for example: decodeZhuyin("ㄇ一ㄣˊ") outputs 'min2'
     * return an empty string in case an error
     */
    public function decodeZhuyin($s) {
        if ($s == 'ㄝ') {
            return 'e5';
        }

        $arr_decode_zhuyin = $this->_decodeZhuyin($s);

        $consonant = $arr_decode_zhuyin[0];
        $rhymes    = $arr_decode_zhuyin[1];
        $tone      = $arr_decode_zhuyin[2];

        if (mb_strlen($rhymes) == 0) {
            return '';
        }
        return $consonant . $rhymes . $tone;
    }


    /**
     * @param $s
     * @return string
     *
     * pinyinToZhuyin converts the input Pinyin to Zhuyin
     * for example: zhāng  -->  ㄓㄤ
     */
    public function pinyinToZhuyin($s) {
        if ($s == 'ê') {
            return 'ㄝ';
        }

        $arr_decode_pinyin = $this->_decodePinyin($s);

        $consonant = $arr_decode_pinyin[0];
        $rhymes = $arr_decode_pinyin[1];
        $tone = $arr_decode_pinyin[2];

        return $this->_encodeZhuyin($consonant, $rhymes, $tone);
    }


    /**
     * @param $s
     * @return string
     *
     * zhuyinToPinyin converts the input Zhuyin to Pinyin
     * for example: ㄓㄤ  -->  zhāng
     */
    public function zhuyinToPinyin($s) {
        if ($s == 'ㄝ') {
            return 'ê';
        }
        $arr_decode_zhuyin = $this->_decodeZhuyin($s);

        $consonant = $arr_decode_zhuyin[0];
        $rhymes = $arr_decode_zhuyin[1];
        $tone = $arr_decode_zhuyin[2];

        return $this->_encodePinyin($consonant, $rhymes, $tone);
    }


    private function splitString($str) {
        return preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
    }

}