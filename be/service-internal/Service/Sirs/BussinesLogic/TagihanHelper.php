<?php

/**
 * @author: [Dede Herdiana][dede.herdiana@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */


namespace Integrasi\Service\Sirs\BussinesLogic;

use Yii;
use Doco\exceptions\ValidationException;
use Integrasi\Service\Sirs\Cache\Cache;
use Integrasi\Service\Sirs\Models\PenjualanResep;
use Integrasi\Service\Sirs\Models\InfoPenjualanResepView;

class TagihanHelper
{
    const STATUS_RESEPTUR = 'BELUM LUNAS';

    const JPK = "JPK";

    protected static $instance = null;

    protected static $returnArray = false;

    /**
     * [$biayaAdm description]
     * @var app\components\object\BiayaAdmInterface
     */
    public $biayaAdm;

    /** Get biaya admin untuk semua tarif tindakan**/
    /**
        * params total yang dikirim harus dikondisikan kurangi dulu dengan jasa pelayanan keperawatan!
        * step prosedur getBiayaAdmin
        * 1. cari biaya adm reseptur berdasarkan pendaftaran id jika pasien rawat inap atau penjualanresep_id jika reseptur (paramsnya jadi kondisi jika dia punya tipe pasien), 
        * 2. cari biaya adm pelayanan Ranap 
        * 3. akumulasi 1 dan 2 
        * 4. validasi nilai maksimal adm 
        * 5. Jika nilai akumulasi lebih dari nilai maksimal maka ambil nilai maks 
        * 6. Jika sebaliknya ambil nilai akumulasi 
    **/

    public static function getBiayaAdmin($id,$penjaminId, $kelasPelayananId, $total, $admisiId, $returnArray = false)
    {
        $biayaAdm = $maxAdm = 0;
        $biayaAdminReseptur = (new self)->getAdminreseptur($id); 
        $biayaJPK = (new self)->getJPK($id); 
        $total = $total - $biayaJPK;
        $confSistem = Cache::getKonfigSistem();
        $konfigTarif = Cache::getKonfigTarif();
        $admTindakanId = !empty($confSistem['adm_tindakan_id']) ? $confSistem['adm_tindakan_id'] : null;
        $persenAdm = !empty($confSistem['adm_persen']) ? $confSistem['adm_persen'] : 0;

        $qAdmRi = [];
        if (!empty($admisiId)) {
            $biayaAdmin = $total;
            if (!empty($admTindakanId)) {
                $ruangan_id = isset($konfigTarif['default_ruangan']) ? $konfigTarif['default_ruangan'] : 1; //default ruangan sirs-it
                $qAdmRi = Yii::$app->db->createCommand("
                    SELECT 
                        harga_tariftindakan as tarif
                    FROM tariftotalrs_fn(:ruangan_id, :penjamin_id, :kelaspelayanan_id, 'pelayanan', null)
                    WHERE daftartindakan_id = :daftartindakan_id")
                ->bindParam(':ruangan_id', $ruangan_id)
                ->bindParam(':penjamin_id', $penjaminId)
                ->bindParam(':kelaspelayanan_id', $kelasPelayananId)
                ->bindParam(':daftartindakan_id', $admTindakanId)
                ->queryOne();
            }
            $biayaAdm = ($persenAdm / 100) * $biayaAdmin;
            $biayaAdm += $biayaAdminReseptur;
            $maxAdm = !empty($qAdmRi['tarif']) ? (float) $qAdmRi['tarif'] : 0;
            if($maxAdm == 0) {
                $biayaAdm = 0;
            }
            else {
                if (!empty($maxAdm) && $biayaAdm > $maxAdm && $maxAdm != 0) {
                    $biayaAdm = $maxAdm;
                }
            }
        } else {
            $biayaAdm = $biayaAdminReseptur;
        }

        if (self::$returnArray) {
            return [
                'totalBiayaAdm' => $biayaAdm,
                'biayaResep' => $biayaAdminReseptur,
                'admPersen' => ($persenAdm/100),
                'maxAdm' => $maxAdm,
                'total_tagihan' => $total
            ];
        }

        return $biayaAdm;
    }

    public function getAdminreseptur($cond)
    {
        $biayaAdminReseptur = 0;
        if(is_array($cond)){
            $penjualanResep = PenjualanResep::find()->select([
                'pendaftaran_id',
                'biayaadministrasi',
            ])->andWhere($cond)->asArray()->one();
            if (!empty($penjualanResep)) {
                $biayaAdminReseptur = isset($penjualanResep['biayaadministrasi']) ? $penjualanResep['biayaadministrasi'] : 0;
            }
        }else{
            $id = $cond;
            $cond = [
                "pendaftaran_id" => $cond,
                "status_pembayaran" => self::STATUS_RESEPTUR
            ];
            $biayaAdminReseptur = InfoPenjualanResepView::find()->select([
                'pendaftaran_id',
                'sum (biayaadministrasi) as biaya_administrasi',
            ])
            ->andWhere($cond)
            ->groupBy('pendaftaran_id')
            ->asArray()->one();
            $biayaAdminReseptur = !empty($biayaAdminReseptur['biaya_administrasi']) ? $biayaAdminReseptur['biaya_administrasi'] : 0;
        }

        return $biayaAdminReseptur;
    }

    public function getJPK(){
        return 0;
    }
}