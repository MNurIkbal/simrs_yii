<?php 

namespace app\components\Services;

use Yii;

class AksesFormService
{
    protected $service; 
    protected $is_cek;
    protected $status_disabled;

    public function __construct()
    {
        $this->service = Yii::$app->docoRest->rm;
    }

    public function execute($pasien_id,$form)
    {
        $bodyAkses = Yii::$app->session->get('akses_form_'.$pasien_id);
    
        $is_cek = false;
        if (empty($bodyAkses)){
            $response = $this->service->get('inf-pencarian-pasien/cek-akses-form', [
                'query' => [
                    'pasien_id' => $pasien_id
                ]
            ]);
            $bodyAkses = json_decode($response->getBody(), true);
            Yii::$app->session->set('akses_form_'.$pasien_id,$bodyAkses);
        }
        
        if (!empty($bodyAkses['response'])){
            $bodyAkses = $bodyAkses['response']['akses'];
            if (!empty($bodyAkses)){
                foreach ($bodyAkses as $key => $value) {
                    if ($key == $form){
                        if ($value == true){
                            $is_cek = true;
                        }
                    }
                }
            }
        }
        return $is_cek;
    }
}
