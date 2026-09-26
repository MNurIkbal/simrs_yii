<?php

use yii\db\Migration;

/**
 * Class m210105_081607_migrate_sy_20210105_view_pasienasuransi_v
 */
class m210105_081607_migrate_sy_20210105_view_pasienasuransi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.pasienasuransi_v;');
        $this->execute("CREATE VIEW \"public\".\"pasienasuransi_v\" AS
             SELECT pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pasien_m.jenisidentitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
    pasien_m.nama_bin,
    pasien_m.tempat_lahir,
    pasien_m.jeniskelamin,
    pasien_m.statusperkawinan,
    asuransipasien_m.carabayar_id,
    asuransipasien_m.penjamin_id,
    asuransipasien_m.nokartuasuransi,
    asuransipasien_m.namapemilikasuransi,
    asuransipasien_m.nomorpokokperusahaan,
    asuransipasien_m.namaperusahaan,
    asuransipasien_m.kelastanggunganasuransi_id,
    asuransipasien_m.tgl_konfirmasi,
    asuransipasien_m.asuransipasien_id,
    asuransipasien_m.masaberlakukartu,
    asuransipasien_m.namabagian,
    asuransipasien_m.noindukkaryawan,
    asuransipasien_m.jpkm,
    asuransipasien_m.nama_asuransi
   FROM (pasien_m
     JOIN asuransipasien_m ON (((pasien_m.pasien_id = asuransipasien_m.pasien_id) AND (asuransipasien_m.is_deleted = false))))
  WHERE ((pasien_m.is_active = true) AND (pasien_m.is_deleted = false))
            ;");
            $this->execute('ALTER TABLE public.pasienasuransi_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210105_081607_migrate_sy_20210105_view_pasienasuransi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210105_081607_migrate_sy_20210105_view_pasienasuransi_v cannot be reverted.\n";

        return false;
    }
    */
}
