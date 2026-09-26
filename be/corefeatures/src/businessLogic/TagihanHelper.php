<?php

/**
 * @author: [Dede Herdiana][dede.herdiana@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace SirsCore\businessLogic;

use Yii;
use Doco\models\kasir\PenjualanResep;
use Doco\models\kasir\InfoPenjualanResepView;

class TagihanHelper
{
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

    public static function getBiayaAdmin($id,$penjaminId, $kelasPelayananId, $total, $admisiId){
        $cacheKasir = Yii::$app->kasirCache;
        $biayaAdm = 0;
        $biayaAdminReseptur = (new self)->getAdminreseptur($id); 
        $biayaJPK = (new self)->getJPK($id); 
        $total = $total - $biayaJPK;
        $confSistem = $cacheKasir::getKonfigSistem();
        $admTindakanId = !empty($confSistem['adm_tindakan_id']) ? $confSistem['adm_tindakan_id'] : null;
        $persenAdm = !empty($confSistem['adm_persen']) ? $confSistem['adm_persen'] : 0;

        $qAdmRi = [];
        if(!empty($admisiId)){
            $biayaAdmin = $total;
            if (!empty($admTindakanId)) {
                $qAdmRi = Yii::$app->db->createCommand("
                    SELECT 
                        harga_tariftindakan as tarif
                    FROM totaltarifnaikkelas_fn(:penjamin_id, :kelaspelayanan_id, 'pelayanan')
                    WHERE daftartindakan_id = :daftartindakan_id")
                ->bindParam(':penjamin_id', $penjaminId)
                ->bindParam(':kelaspelayanan_id', $kelasPelayananId)
                ->bindParam(':daftartindakan_id', $admTindakanId)
                ->queryOne();
            }
            $biayaAdm = ($persenAdm / 100) * $biayaAdmin;
            $biayaAdm += $biayaAdminReseptur;
            $maxAdm = !empty($qAdmRi['tarif']) ? (float) $qAdmRi['tarif'] : 0;
            if (!empty($maxAdm) && $biayaAdm > $maxAdm && $maxAdm != 0) {
                $biayaAdm = $maxAdm;
            }
        }else{
            $biayaAdm = $biayaAdminReseptur;
        }
        return $biayaAdm;
    }

    public function getAdminreseptur($cond){
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
                "status_pembayaran" => "BELUM LUNAS"
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