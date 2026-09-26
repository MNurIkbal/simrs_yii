<?php 

namespace app\components\Services;

use Yii;

class SuratKematianService
{
    protected $service; 
    public function __construct()
    {
        $this->service = Yii::$app->docoRest->igd;
    }

    public function execute($pendaftaran_id, $ruangan_id)
    {
            $path = Yii::getAlias("@download") . "/surat_kematian.pdf";
            $response = $this->service->get('kesimpulan/cetak-pdf-surat-kematian', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'ruangan_id' => $ruangan_id
                ],
                'save_to' => $path,
            ]);
        
            return $path;
    }
}