<?php namespace App\Libs;

use Carbon\Carbon;

class Crypt{

    static function mc_decrypt($decrypt) {
        $mc_key = 'yGfJrzEVfDmtbWZS';
        $decoded = self::base64_urlsafe_decode($decrypt);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $mc_key, trim($decoded), MCRYPT_MODE_ECB, $iv));
        $ary_decrypted = explode("ex", rtrim( self::pkcs5_unpad($decrypted) ));
        if(count($ary_decrypted) < 2 || !is_numeric($ary_decrypted[1])){
            return false;
        }
        $now_time = Carbon::now();
        if($ary_decrypted[1] + 10 < $now_time->timestamp){
            return false; 
        }
        return $ary_decrypted[0];
    }

    static function base64_urlsafe_decode($val) {
	$val = str_replace(array('-','_'), array('+', '/'), $val);
	return base64_decode($val);
    }

    // PKCS5Padding
    // 埋められたバイト値を除く
    static function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text)) return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
        return substr($text, 0, -1 * $pad);
    }

}
