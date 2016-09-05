<?php
 
class AuthToken {
    private static $key = "секретный ключ";
    private static $iv = "должен быть каждый раз случайным, но для данного решения подойдёт просто секретный";
 
    private static function int2char($int) {
        $char = "";
        $hex = sprintf("%08x", $int);
        for ($i =  0; $i < 4; $i++) {
            $char .= chr(hexdec(substr($hex, $i * 2, 2)));
        }
        return $char;
    }
 
    private static function char2int($char) {
        $int =  0;
        $hex = "";
        for ($i =  0; $i < 4; $i++) {
            $hex .= sprintf("%02x", ord($char{$i}));
        }
        $int = hexdec($hex);
        return $int;
    }
 
    public static function create($id, $expire =  0, $mode =  0) {
        $id = intval($id);
        $expire = intval($expire);
        $mode = intval($mode);
        if ($id <  0 || $expire <  0 || $mode <  0) {
            return null;
        }
 
        $info = array();
        $info["id"] = $id;
        $info["time"] = time();
        $info["expire"] = $expire;
        $info["mode"] = $mode;
        $info["rnd"] = ceil(mt_rand( 0, 255));
        $info["sum"] = $info["time"] - $info["expire"] - $info["mode"] - $info["rnd"] - $info["id"];
        $info = self::int2char($info["id"]) . self::int2char($info["time"]) . self::int2char($info["expire"]) . chr($info["mode"]) . chr($info["rnd"]) . self::int2char($info["sum"]);
 
        $token = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(self::$key), $info, MCRYPT_MODE_OFB, md5(self::$iv));
        $tokenHex = "";
        $tokenLength = strlen($token);
        for ($i =  0; $i < $tokenLength; $i++) {
            $tokenHex .= sprintf("%02x", ord($token{$i}));
        }
        return $tokenHex;
    }
 
    public static function check($tokenHex, $mode = null) {
        $token = "";
        $tokenHexLength = strlen($tokenHex) / 2;
        for ($i =  0; $i < $tokenHexLength; $i++) {
            $token .= chr(hexdec(substr($tokenHex, $i * 2, 2)));
        }
        $info = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(self::$key), $token, MCRYPT_MODE_OFB, md5(self::$iv));
        if (strlen($info) == 18) {
            $info = array("id" => self::char2int(substr($info,  0, 4)), "time" => self::char2int(substr($info, 4, 4)), "expire" => self::char2int(substr($info, 8, 4)), "mode" => ord($info{12}), "rnd" => ord($info{13}), "sum" => self::char2int(substr($info, 14, 4)));
            if ($info["sum"] == $info["time"] - $info["expire"] - $info["mode"] - $info["rnd"] - $info["id"]) {
                if ($info["expire"] >  0) {
                    if ($info["expire"] + $info["time"] < time()) {
                        return false;
                    }
                }
                if ($info["mode"] >  0) {
                    if ($mode !== null) {
                        if ($info["mode"] != $mode) {
                            return false;
                        }
                    }
                }
                return $info["id"];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
 
?>
 

