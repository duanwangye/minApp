<?php
/**
 * Created by PhpStorm.
 * User: Duanwangye
 * Date: 18/12/05
 * Company:财联集惠
 */

namespace app\api\controller;

class AES
{
    // CRYPTO_CIPHER_BLOCK_SIZE 32

    private $_secret_key = '47e3d3b9955980fb445bb6486d3b4a94';

    /*public function setKey($key) {
        $this->_secret_key = $key;
    }*/

    public function encode1($data) {
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_256,'',MCRYPT_MODE_CBC,'');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td),MCRYPT_RAND);
        mcrypt_generic_init($td,$this->_secret_key,$iv);
        $encrypted = mcrypt_generic($td,$data);
        mcrypt_generic_deinit($td);

        return bin2hex($iv . $encrypted);
    }



    public function decode1($data) {
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_256,'',MCRYPT_MODE_CBC,'');
        $iv = mb_substr($data,0,32,'latin1');
        mcrypt_generic_init($td,$this->_secret_key,$iv);
        $data = mb_substr($data,32,mb_strlen($data,'latin1'),'latin1');
        $data = mdecrypt_generic($td,$data);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return trim($data);
    }


    /**
     * [encrypt aes加密]
     * @param [type]     $input [要加密的数据]
     * @param [type]     $key [加密key]
     * @return [type]       [加密后的数据]
     */
    public function encrypt($input, $key)
    {
        $data = openssl_encrypt($input, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        //$data = openssl_encrypt($input, 'aes-256-cbc', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING);
        $data = bin2hex($data);
        return $data;
    }
    /**
     * [decrypt aes解密]
     * @param [type]     $sStr [要解密的数据]
     * @param [type]     $sKey [加密key]
     * @return [type]       [解密后的数据]
     */
    public function decrypt($sStr, $sKey)
    {
        $decrypted = openssl_decrypt(base64_decode($sStr), 'AES-128-ECB', $sKey, OPENSSL_RAW_DATA);
        return $decrypted;
    }

    public function pkcs5_pad($text,$blocksize){
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text.str_repeat(chr($pad),$pad);
    }

    public function encode($input,$key){
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_ECB);
        $input = $this->pkcs5_pad($input,$size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'',MCRYPT_MODE_ECB,'');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td),MCRYPT_RAND);
        mcrypt_generic_init($td,$key,$iv);
        $data = mcrypt_generic($td,$input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = strtoupper(bin2hex($data));
        return $data;
    }


    function decode($sStr, $sKey) {
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $sKey, hex2bin($sStr), MCRYPT_MODE_ECB);
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s - 1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    }
}