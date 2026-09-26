<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-04-17 13:34:37
 * @Last Modified by:   afil
 * @Last Modified time: 2018-04-17 13:35:22
 * @Description: 
 */

namespace app\modules\v1\models;

use Yii;
use Doco\components\DocoConstants;

/**
 * This is the model class for table "antrian_v".
 *
 * @property int $antrian_id
 * @property string $no_antrian
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property int $pendaftaran_id
 * @property int $layarantrian_id
 * @property string $layarantrian_nama
 * @property int $loket_id
 * @property string $loket_nama
 * @property double $panggilan_ke
 * @property string $tgl_antrian
 * @property int $status_antrian
 * @property string $stat_antrian
 * @property int $status_pasien
 * @property string $stat_pasien
 */
class AntrianView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'antrian_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['antrian_id', 'pasien_id', 'ruangan_id', 'carabayar_id', 'penjamin_id', 'pendaftaran_id', 'layarantrian_id', 'loket_id', 'status_antrian', 'status_pasien'], 'default', 'value' => null],
            [['antrian_id', 'pasien_id', 'ruangan_id', 'carabayar_id', 'penjamin_id', 'pendaftaran_id', 'layarantrian_id', 'loket_id', 'status_antrian', 'status_pasien'], 'integer'],
            [['panggilan_ke'], 'number'],
            [['tgl_antrian'], 'safe'],
            [['stat_antrian'], 'string'],
            [['no_antrian'], 'string', 'max' => 6],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'ruangan_nama', 'carabayar_nama', 'penjamin_nama', 'loket_nama'], 'string', 'max' => 50],
            [['layarantrian_nama'], 'string', 'max' => 100],
            [['stat_pasien'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'antrian_id' => 'Antrian ID',
            'no_antrian' => 'No Antrian',
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'pendaftaran_id' => 'Pendaftaran ID',
            'layarantrian_id' => 'Layarantrian ID',
            'layarantrian_nama' => 'Layarantrian Nama',
            'loket_id' => 'Loket ID',
            'loket_nama' => 'Loket Nama',
            'panggilan_ke' => 'Panggilan Ke',
            'tgl_antrian' => 'Tgl Antrian',
            'status_antrian' => 'Status Antrian',
            'stat_antrian' => 'Stat Antrian',
            'status_pasien' => 'Status Pasien',
            'stat_pasien' => 'Stat Pasien',
        ];
    }

    public static function getAntrianFarmasi($ruangan_id)
    {
        $query = "
        SELECT 
            antrian_t.antrian_id,
            antrian_t.no_antrian,
            antrian_t.pasien_id,
            antrian_t.ruangan_id,
            antrian_t.carabayar_id,
            antrian_t.penjamin_id,
            antrian_t.pendaftaran_id,
            antrian_t.layarantrian_id,
            antrian_t.loket_id,
            antrian_t.panggilan_ke,
            antrian_t.status_antrian,
            CASE antrian_t.status_antrian
                WHEN 0 THEN 'Belum Panggil'::text
                WHEN 1 THEN 'Panggil'::text
                WHEN  2 THEN 'Lewati'::text
                ELSE 'Batal'::text
            END AS stat_antrian,
            antrian_t.status_pasien,
            fgetnamalookup(antrian_t.status_pasien) AS stat_pasien,
            CASE 
                WHEN resep.count_racikan >= 1 then 1
                WHEN resep.count_freetext >= 1 then 1
                ELSE 2 
            END AS racikan_id,
            CASE 
                WHEN resep.count_racikan >= 1 then 'Racikan'
                WHEN resep.count_freetext >= 1 then 'Racikan'
                ELSE 'Non Racikan' 
            END AS racikan_nama,
            fgetnamalookup(antrian_t.groupcarabayar_id) AS namagroupcarabayar,
            antrian_t.jenisantrian_id,
            antrian_t.is_online,
            antrian_t.fungsiantrian_id,
            fgetnamalookup(antrian_t.fungsiantrian_id) AS fungsi_nama,
            antrian_t.antrian_farmasi,
            CASE
                WHEN fgetnamalookup(antrian_t.antrian_farmasi) IS NULL THEN 'Belum Proses'::text::character varying
                ELSE fgetnamalookup(antrian_t.antrian_farmasi)
            END AS stat_antrian_farmasi,
            CASE
                WHEN antrian_t.antrian_farmasi = 584 THEN 'Siap Ambil'::text
                WHEN antrian_t.antrian_farmasi = 585 THEN 'Selesai'::text
                WHEN antrian_t.antrian_farmasi = 586 THEN 'Selesai'::text
                ELSE 'Proses'::text
            END AS stat_proses_antrian_farmasi,
            antrian_t.is_appointment,
            antrian_t.panggil_flag,
            antrian_t.jenisantriandetail_id,
            CASE
                WHEN resep.status_reseptur::text = 'Diserahkan'::text THEN 'Diserahkan'::text::character varying
                WHEN antrian_t.panggilan_ke > 1::double precision THEN 'Siap Diserahkan'::text::character varying
                ELSE resep.status_reseptur
            END AS status_reseptur,
            resep.is_cetak_etiket,
            date(resep.tgl_cetak_etiket) as tgl_cetak_etiket,
            date(antrian_t.tgl_antrian) as tgl_antrian
        FROM antrian_t   
        LEFT JOIN (
            SELECT reseptur_t.antrian_id,at2.tgl_antrian, reseptur_t.tgl_cetak_etiket,reseptur_t.is_cetak_etiket ,
            CASE
                WHEN penjualanresep_t.penjualanresep_id IS NULL THEN 'Belum Proses'::character varying
                WHEN penjualanresep_t.status_reseptur = 347 THEN 'Dalam Proses'::character varying
                ELSE fgetnamalookup(reseptur_t.status_reseptur)
            END AS status_reseptur,
            (
                SELECT 
                count(a.racikan_id) FILTER (WHERE a.racikan_id = 1) AS count_obat_racikan
                FROM resepturdetail_t a
                WHERE a.reseptur_id IS NOT NULL AND a.is_deleted = false
                AND a.reseptur_id =reseptur_t.reseptur_id 
                GROUP BY a.reseptur_id
            ) as count_racikan,
            (
                SELECT count(reseptur_id) AS ct_racikan
                FROM resepturracikan_t a
                WHERE a.reseptur_id = reseptur_t.reseptur_id 
                GROUP BY a.reseptur_id
            ) as count_freetext
            FROM reseptur_t 
            LEFT JOIN antrian_t at2 on at2.antrian_id = reseptur_t.antrian_id 
            LEFT JOIN penjualanresep_t on penjualanresep_t.penjualanresep_id = reseptur_t.penjualanresep_id 
            UNION ALL
            SELECT penjualanresep_t.antrian_id ,at2.tgl_antrian,tgl_cetak_etiket ,is_cetak_etiket,
            CASE
                WHEN penjualanresep_t.status_reseptur = 347 THEN 'Dalam Proses'::character varying
                ELSE fgetnamalookup(penjualanresep_t.status_reseptur)
            END AS status_reseptur,
            (
                SELECT 
                count(a.racikan_id) FILTER (WHERE a.racikan_id = 1) AS count_obat_racikan
                FROM obatalkespasien_t a
                WHERE a.penjualanresep_id IS NOT NULL AND a.is_deleted = false
                AND a.penjualanresep_id =penjualanresep_t.penjualanresep_id 
                GROUP BY a.penjualanresep_id
            ) as count_racikan,
            0 as count_freetext
            FROM penjualanresep_t 
            LEFT JOIN antrian_t at2 on at2.antrian_id = penjualanresep_t.antrian_id 
        ) resep on resep.antrian_id = antrian_t.antrian_id 
        ";

        $konfigEtiket = Yii::$app->db->createCommand("SELECT konfig_display_antrian_farmasi_etiket FROM konfigsystem_k limit 1")->queryScalar();

        if(isset($konfigEtiket) && $konfigEtiket == TRUE){
            $andWhere = "WHERE tgl_cetak_etiket::date = CURRENT_DATE ";
            $andWhere .= "AND resep.is_cetak_etiket IS TRUE ";
            $order = " ORDER BY tgl_cetak_etiket ASC";
        }else{
            $andWhere = "WHERE resep.tgl_antrian::date = CURRENT_DATE ";
            $order = " ORDER BY resep.tgl_antrian ASC";
        }
        $andWhere .= "AND is_online IS FALSE ";
        $andWhere .= "AND jenisantrian_id = ".DocoConstants::VAR_JA_F;
        if(isset($ruangan_id) && !empty($ruangan_id)){
            $andWhere .= " AND ruangan_id = ".$ruangan_id;
        }

        $query .= $andWhere.$order;
        return Yii::$app->db->createCommand($query)->queryAll();
    }
}
