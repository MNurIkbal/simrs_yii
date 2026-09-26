<?php

namespace app\modules\v1\models;

use Yii;

use yii\helpers\ArrayHelper;
use \Doco\components\DocoConstants;
use app\modules\v1\models\PendaftaranOnline;

/**
 * This is the model class for table "infojadwaldokter_v".
 */
class InfoJadwalDokterView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infojadwaldokter_v';
    }

    public function getJadwalDokterIdByWaktu($pegawai_id, $ruangan_id, $date = null, $waktu = null)
    {
        $date = empty($date) ? date('Y-m-d') : $date;
        $waktu = empty($waktu) ? date('H:i:s') : $waktu;
        $hari = DocoConstants::$look_hari[date('N', strtotime($date))];

        $result = InfoJadwalDokterView::find()->select([
            'jadwaldokter_id'
        ])->where([
            'pegawai_id' => $pegawai_id,
            'ruangan_id' => $ruangan_id,
            'hari_jadwalbuka' => $hari,
        ])->andWhere(['<=','waktu_mulai', $waktu])
        ->andWhere(['>=','waktu_selesai', $waktu])
        ->asArray()->one();

        return $result;
    }

    public function getEstimasiByJadwal($jadwaldokter_id, $tgl_antrian, $is_bpjs = false, $inmilisecond = true, $pendaftaran_ol_id=null)
    {
        if(empty($jadwaldokter_id)){
            return null;
        }
        $estimasi_pendaftaranol = null;
        
        if(!empty($pendaftaran_ol_id)){
            $pendaftaranol = PendaftaranOnline::find()->select(['jadwaldokter_id', 'estimasidilayani'])
                                ->where(['pendaftaranol_id' => $pendaftaran_ol_id])->asArray()->one();
            if (!empty($pendaftaranol) && $jadwaldokter_id == $pendaftaranol['jadwaldokter_id']) {
                $estimasi_pendaftaranol = $pendaftaranol['estimasidilayani'];
            }
        }
        
        if(isset($estimasi_pendaftaranol) && !empty($estimasi_pendaftaranol)){
            return ($inmilisecond) ? $estimasi_pendaftaranol : $estimasi_pendaftaranol/1000;
        }
        $query = "
            SELECT
                jadwaldokter_mulai,
                jadwaldokter_tutup,
                kuota_bpjs_offline,
                kuota_bpjs_online,
                kuota_nonbpjs_offline,
                kuota_nonbpjs_online,
                jumlah_loaddokter
            FROM jadwaldokter_m
            WHERE jadwaldokter_id = :jadwaldokter_id
        ";

        $dataJadwal = Yii::$app->db->createCommand($query)->bindValue(':jadwaldokter_id',$jadwaldokter_id)->queryOne();

        $dataAntrian = Yii::$app->db->createCommand("
            SELECT 
                count(antrian_id) filter (where is_online is true and (groupcarabayar_id=418 or carabayar_id = 6)) as jumlah_antrian_bpjs_online,
                count(antrian_id) filter (where is_online is false and (groupcarabayar_id=418 or carabayar_id = 6)) as jumlah_antrian_bpjs_offline,
                count(antrian_id) filter (where is_online is true and (groupcarabayar_id<>418 or carabayar_id <> 6)) as jumlah_antrian_nonbpjs_online,  
                count(antrian_id) filter (where is_online is false and (groupcarabayar_id<>418 or carabayar_id <> 6)) as jumlah_antrian_nonbpjs_offline
            FROM antrian_t 
            WHERE jenisantrian_id = :jenisantrian_id
            AND antrian_t.jadwaldokter_id = :jadwaldokter_id
            AND tgl_antrian::date = :tgl_antrian
            AND antrian_t.is_deleted is false 
        ")
        ->bindValue(':jenisantrian_id',DocoConstants::VAR_JA_P)
        ->bindValue(':jadwaldokter_id',$jadwaldokter_id)
        ->bindValue(':tgl_antrian',$tgl_antrian);
        $dataAntrian = $dataAntrian->queryOne();

        $kuota_bpjs_offline = ArrayHelper::getValue($dataJadwal,'kuota_bpjs_offline',0);
        $kuota_bpjs_online= ArrayHelper::getValue($dataJadwal,'kuota_bpjs_online',0);
        $kuota_nonbpjs_offline= ArrayHelper::getValue($dataJadwal,'kuota_nonbpjs_offline',0);
        $kuota_nonbpjs_online= ArrayHelper::getValue($dataJadwal,'kuota_nonbpjs_online',0);
        $kuotajkn = $kuota_bpjs_online + $kuota_bpjs_offline;
        $kuotanonjkn = $kuota_nonbpjs_offline + $kuota_nonbpjs_online;
        $jumlah_antrian_bpjs_online = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_bpjs_online',0);
        $jumlah_antrian_bpjs_offline = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_bpjs_offline',0);
        $jumlah_antrian_nonbpjs_online = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_nonbpjs_online',0);
        $jumlah_antrian_nonbpjs_offline = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_nonbpjs_offline',0);
        $sisakuotanonjkn = $kuotanonjkn - ($jumlah_antrian_nonbpjs_online + $jumlah_antrian_nonbpjs_offline);
        $sisakuotajkn = $kuotajkn - ($jumlah_antrian_bpjs_online + $jumlah_antrian_bpjs_offline);

        $jam_mulai = !empty($dataJadwal['jadwaldokter_mulai']) 
                        ? date('H:i', strtotime($dataJadwal['jadwaldokter_mulai'])) : '00:00';
        $jam_tutup = !empty($dataJadwal['jadwaldokter_tutup']) 
                        ? date('H:i', strtotime($dataJadwal['jadwaldokter_tutup'])) : '00:00';

        $tglestimasi = date('Y-m-d', strtotime($tgl_antrian));
        $jamestimasi = date('H:i:s', strtotime($jam_mulai));
        if($is_bpjs){
            $totalAntrian = $kuotajkn;
            $sisaAntrian = $sisakuotajkn;
        }else{
            $totalAntrian = $kuotanonjkn;
            $sisaAntrian = $sisakuotanonjkn;
        }
        
        $totalAntrianPoli = $kuotajkn + $kuotanonjkn;
        $sisaAntrianPoli = $sisakuotajkn + $sisakuotanonjkn;

        $jumlah_loaddokter = ArrayHelper::getValue($dataJadwal,'jumlah_loaddokter');
        $spm = isset($jumlah_loaddokter) && !empty($jumlah_loaddokter) ? $jumlah_loaddokter : 6;
        $waktuestimasi = $tglestimasi . " " . $jamestimasi;
        $noUrut = ($totalAntrian - $sisaAntrian);
        $noUrutPoli = ($totalAntrianPoli - $sisaAntrianPoli);

        /**
         * jika waktu ambil lebih dari waktu mulai praktek
         * maka
         *  jika timeslot yg sudah diambil < waktu ambil antrian
         *  maka
         *      ambil timeslot setelah jam ambil
         * jika tidak 
         *      jam mulai + (spm * nourut)
         */
        $jamSekarang = date('H:i:s');
        $tglSekarang = date('Y-m-d');
        if(strtotime($tglestimasi) > strtotime($tglSekarang)){
            $tglJamSekarang = $tglSekarang . ' ' . $jamSekarang;
        }else{
            $tglJamSekarang = $tglestimasi . ' ' . $jamSekarang;
        }
        $waktuestimasi = $tglestimasi . ' ' . $jamestimasi;
        $urutanAntrianDiambil = $noUrut>0 ? $noUrut-1 : $noUrut;
        $currentTimeSlot = (date_create($waktuestimasi)->getTimestamp()) + (
            (($spm * $urutanAntrianDiambil) * 60)
        );

        $timestampsecond = (date_create($waktuestimasi)->getTimestamp()) + (
            (($spm * $noUrutPoli) * 60)
        );

        if(strtotime($tglJamSekarang) > strtotime($waktuestimasi)){
            if($currentTimeSlot < strtotime($tglJamSekarang)){
                $interval = date_diff(date_create($waktuestimasi),date_create($tglJamSekarang));
                Yii::warning($spm,'spm');
                $urutanSekarang = ceil(((($interval->h * 60)+$interval->i) / $spm));

                $timestampsecond = (date_create($waktuestimasi)->getTimestamp()) + (
                    (($spm * $urutanSekarang) * 60)
                );
                
                $dataAntrianPoli = Yii::$app->db->createCommand("
                    SELECT 
                        count(antrian_id) as total_antrian
                    FROM antrian_t 
                    WHERE jenisantrian_id = :jenisantrian_id
                    AND antrian_t.jadwaldokter_id = :jadwaldokter_id
                    AND tgl_antrian::date = :tgl_antrian
                    AND estimasidilayani >= :estimasidilayani
                    AND antrian_t.is_deleted is false 
                ")
                ->bindValue(':jenisantrian_id',DocoConstants::VAR_JA_P)
                ->bindValue(':jadwaldokter_id',$jadwaldokter_id)
                ->bindValue(':tgl_antrian', $tgl_antrian)
                ->bindValue(':estimasidilayani', $timestampsecond * 1000);
                $dataAntrianPoli = $dataAntrianPoli->queryOne();
                
                if (ArrayHelper::getValue($dataAntrianPoli, 'total_antrian', 0) > 0) {
                    $urutanSekarang += ArrayHelper::getValue($dataAntrianPoli, 'total_antrian') ;
                    // recalculate timestamp dengan urutan terbaru
                    $timestampsecond = (date_create($waktuestimasi)->getTimestamp()) + (
                        (($spm * $urutanSekarang) * 60)
                    );
                }
            }
        }

        return ($inmilisecond) ? $timestampsecond * 1000 : $timestampsecond;
    }
}
