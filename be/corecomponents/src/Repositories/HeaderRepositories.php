<?php

/**
 * @author ali.padilah@docotel.com
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\Repositories;

use Yii;

class HeaderRepositories
{

    protected $iniFile;
    
    public function __construct() {
        $this->iniFile = @parse_ini_file('../../config/env/.api', true);
    }

    public function getHeaderEcoll()
    {
        $const_id = empty($this->iniFile['setting_ecoll']['const_id']) 
                        ? null 
                        : $this->iniFile['setting_ecoll']['const_id'];
        $secret_key = empty($this->iniFile['setting_ecoll']['secret_key']) 
                        ? null 
                        : $this->iniFile['setting_ecoll']['secret_key'];

        return [
            'd-const-id' => $const_id,
            'd-signature' => $this->generateCredentialSHA256($const_id,$secret_key)
        ];

    }


    protected function generateCredentialSHA256($const_id,$secret_key)
    {
        $signature = hash_hmac(
            'sha256', 
            $const_id . "&" . self::getTimestamp(), 
            $secret_key, 
            true
        );

        return base64_encode($signature);
    }

    protected function getTimestamp()
    {
        // Computes the timestamp
        date_default_timezone_set('UTC');
        return strtotime(date("Y-m-d H"));
    }
}
