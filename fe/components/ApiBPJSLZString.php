<?php

namespace app\components;

use Yii;

class ApiBPJSLZString
{

    /**
     * @param  string $key    key enkripsi: consid + conspwd + timestamp request (concatenate string)
     * @param  $string response
     * @return string
     */
    public function stringDecrypt($key, $string)
    {
        $encrypt_method = 'AES-256-CBC';
        // hash
        $key_hash = hex2bin(hash('sha256', $key));
  
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);

        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        return $output;
    }

    public function decompress($string)
    {
        return \LZCompressor\LZString::decompressFromEncodedURIComponent($string);
    }

    public function decryptWithDecompress($key, $string)
    {
        $decrypt = $this->decompress($this->stringDecrypt($key, $string));
        return $decrypt ? json_decode($decrypt, true) : [];
    }
}