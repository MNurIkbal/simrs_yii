<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "reseptur_t".
 *
 * @property int $reseptur_id
 * @property int $pasienadmisi_id
 * @property int $ruangan_id
 * @property int $pasien_id
 * @property int $pegawai_id
 * @property int $pendaftaran_id
 * @property int $penjualanresep_id
 * @property string $tglreseptur
 * @property string $noresep
 * @property int $ruanganreseptur_id
 * @property int $status_reseptur lookup_type='status_reseptur'
 * @property int $antrian_id
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property int $instruksi_id
 * @property bool $is_hamil
 * @property int $berat_badan
 * @property int $tinggi_badan
 * @property string $luas_tubuh
 * @property int $diagnosa_id
 * @property string $catatan
 *
 * @property PenjualanresepT[] $penjualanresepTs
 * @property ResepturdetailT[] $resepturdetailTs
 */

class Reseptur extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reseptur_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasienadmisi_id', 'ruangan_id', 'pasien_id', 'pegawai_id', 'pendaftaran_id', 'penjualanresep_id', 'ruanganreseptur_id', 'status_reseptur', 'antrian_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'instruksi_id', 'berat_badan', 'tinggi_badan', 'diagnosa_id','catatan'], 'default', 'value' => null],
            [['pasienadmisi_id', 'ruangan_id', 'pasien_id', 'pegawai_id', 'pendaftaran_id', 'penjualanresep_id', 'ruanganreseptur_id', 'status_reseptur', 'antrian_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'instruksi_id', 'diagnosa_id',
                'hasil_resep_kronis_id',
            ], 'integer'],
            [['ruangan_id', 'pasien_id', 'pegawai_id', 'pendaftaran_id', 'tglreseptur', 'ruanganreseptur_id'], 'required'],
            [['tglreseptur', 'created_date', 'last_modified_date', 'deleted_date','berat_badan', 'tinggi_badan','catatan'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active', 'is_hamil'], 'boolean'],
            [['is_deleted'],'default', 'value' => false],
            [['is_active'],'default', 'value' => true],
            [['noresep'], 'string', 'max' => 50],
            [['luas_tubuh'], 'string', 'max' => 100]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reseptur_id' => 'Reseptur ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'ruangan_id' => 'Ruangan ID',
            'pasien_id' => 'Pasien ID',
            'pegawai_id' => 'Pegawai ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'penjualanresep_id' => 'Penjualanresep ID',
            'tglreseptur' => 'Tglreseptur',
            'noresep' => 'Noresep',
            'ruanganreseptur_id' => 'Ruanganreseptur ID',
            'status_reseptur' => 'Status Reseptur',
            'antrian_id' => 'Antrian ID',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'instruksi_id' => 'Instruksi ID',
            'is_hamil' => 'Is Hamil',
            'berat_badan' => 'Berat Badan',
            'tinggi_badan' => 'Tinggi Badan',
            'luas_tubuh' => 'Luas Tubuh',
            'diagnosa_id' => 'Diagnosa ID',
            'hasil_resep_kronis_id' => 'Hasil Generate Resep Kronis',
            'catatan' => 'Catatan'
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualanresepTs()
    {
        return $this->hasMany(PenjualanresepT::className(), ['reseptur_id' => 'reseptur_id']);
    }

    public function getResepturData($column, $value)
    {
        $column = $column == 'reseptur_id' ? 'rt.reseptur_id' : 'pt.penjualanresep_id';
        $sql = 'SELECT 
                    \'reseptur\'::text AS jenis, rt.reseptur_id, rt.penjualanresep_id, rt.noresep as no_reseptur, rt.status_reseptur as status_reseptur_id,
                    rt.pasien_id, rt.pasienadmisi_id, rt.pegawai_id,
                    pt.noresep AS noresep,  pt.biayaadministrasi, pt.jenispenjualan, pt.noresep as no_resep, pt.iter,
                    rm.ruangan_nama AS ruangan_reseptur,
                    im.instalasi_nama AS instalasi_reseptur,
                    pm.nama_pegawai,
                    CASE
                        WHEN rm.instalasi_id = 1 THEN pasienmorbiditas_t.diagnosa_pasien ->> \'text\'::text
                        WHEN rm.instalasi_id = 2 THEN cppt_rd.diagnosa_utama
                        WHEN rm.instalasi_id = 3 THEN cppt_rd.diagnosa_utama
                        ELSE NULL::text
                    END AS diagnosa_text,
                    dm.diagnosa_id,
                    concat(dm.diagnosa_kode, \'-\', dm.diagnosa_nama) AS diagnosa_nama,
                    rt.tinggi_badan,
                    rt.berat_badan,
                    pt.hasil_resep_kronis_id,
                    pt.resep_kronis_asal_id,
                    pt.reseptur_kronis_asal_id,
                    rt.hasil_resep_kronis_id AS hasil_reseptur_kronis,
                    COALESCE(pt.antrian_id, rt.antrian_id) AS antrian_id
                FROM reseptur_t rt
                LEFT JOIN penjualanresep_t pt ON pt.reseptur_id = rt.reseptur_id
                LEFT JOIN pendaftaran_t pt2 ON pt2.pendaftaran_id = rt.pendaftaran_id
                LEFT JOIN ruangan_m rm ON rm.ruangan_id = rt.ruanganreseptur_id
                LEFT JOIN instalasi_m im ON im.instalasi_id = rm.instalasi_id
                LEFT JOIN pegawai_m pm ON pm.pegawai_id = rt.pegawai_id
                LEFT JOIN diagnosa_m dm ON dm.diagnosa_id = rt.diagnosa_id
                LEFT JOIN pasienmorbiditas_t ON pt2.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false AND pasienmorbiditas_t.kelompokdiagnosa_id = 2
                LEFT JOIN ( SELECT instruksi_t.instruksi_id,
                        cppt_t.cppt_id,
                        cppt_t.pendaftaran_id,
                        cppt_t.a_diag_utama ->> \'text\'::text AS diagnosa_utama
                    FROM instruksi_t
                        JOIN cppt_t ON instruksi_t.cppt_id = cppt_t.cppt_id AND cppt_t.is_deleted = false AND cppt_t.is_active = true
                    WHERE instruksi_t.is_deleted = false AND instruksi_t.is_active = true) cppt_rd ON pt2.pendaftaran_id = cppt_rd.pendaftaran_id AND rt.instruksi_id = cppt_rd.instruksi_id';
       
        $where = " where $column = $value";

        $query = Yii::$app->db->createCommand($sql.$where);

        return $query->queryOne();
    }
}
