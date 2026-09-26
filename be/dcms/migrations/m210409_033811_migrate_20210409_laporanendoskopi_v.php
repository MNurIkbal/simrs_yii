<?php

use yii\db\Migration;

/**
 * Class m210409_033811_migrate_20210409_laporanendoskopi_v
 */
class m210409_033811_migrate_20210409_laporanendoskopi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('DROP VIEW if exists "public"."laporanendoskopi_v";');

    $this->execute("
        CREATE VIEW \"public\".\"laporanendoskopi_v\" AS  SELECT laporanendoskopi_r.laporanedoskopi_id,
    laporanendoskopi_r.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    laporanendoskopi_r.pasienmasukpenunjang_id,
    pasien_m.no_rekam_medik AS no_rm,
    pasien_m.nama_pasien,
    laporanendoskopi_r.simptoms,
    laporanendoskopi_r.pre_diagnosis,
    laporanendoskopi_r.pre_diagnosis_sekunder,
    laporanendoskopi_r.indications_examinations,
    laporanendoskopi_r.instrument,
    laporanendoskopi_r.pre_medications,
    laporanendoskopi_r.procedure_performed,
    laporanendoskopi_r.findings,
    laporanendoskopi_r.sampling,
    laporanendoskopi_r.endoscopic_diagnosis,
    laporanendoskopi_r.endoscopic_diagnosis_sekunder,
    laporanendoskopi_r.recommendations,
    laporanendoskopi_r.additional_photo,
    dok_operator.nama_pegawai AS dokter_operator,
    spesialis_m.spesialis_nama AS spesialis
   FROM laporanendoskopi_r
     JOIN pendaftaran_t ON laporanendoskopi_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN inpostoperasi_t ON laporanendoskopi_r.pasienmasukpenunjang_id = inpostoperasi_t.pasienmasukpenunjang_id
     LEFT JOIN pegawai_m dok_operator ON inpostoperasi_t.dokterbedah_id = dok_operator.pegawai_id
     LEFT JOIN spesialis_m ON dok_operator.spesialis_id = spesialis_m.spesialis_id
  WHERE laporanendoskopi_r.is_deleted = false;");
    
    $this->execute('ALTER TABLE "public"."laporanendoskopi_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210409_033811_migrate_20210409_laporanendoskopi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210409_033811_migrate_20210409_laporanendoskopi_v cannot be reverted.\n";

        return false;
    }
    */
}
