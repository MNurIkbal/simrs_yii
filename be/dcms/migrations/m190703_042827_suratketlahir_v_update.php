<?php

use yii\db\Migration;

/**
 * Class m190703_042827_suratketlahir_v_update
 */
class m190703_042827_suratketlahir_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
   DROP VIEW if  exists public.suratketlahir_v;
        ');

          $this->execute('
   CREATE OR REPLACE VIEW public.suratketlahir_v AS 
 SELECT suratketlahir_t.suratketlahir_id,
    suratketlahir_t.pendaftaran_id,
    suratketlahir_t.ibu_nama,
    suratketlahir_t.ibu_ktp,
    suratketlahir_t.ibu_alamat,
    suratketlahir_t.ibu_pekerjaan,
    suratketlahir_t.ibu_golongandarah,
    suratketlahir_t.ayah_nama,
    suratketlahir_t.ayah_ktp,
    suratketlahir_t.ayah_alamat,
    pekerjaan_m.pekerjaan_nama AS ayah_pekerjaan,
    fgetnamalookup(suratketlahir_t.ayah_golongandarah_id) AS ayah_golongandarah,
    fgetnamalookup(suratketlahir_t.hari_lahir) AS hari_lahir,
    suratketlahir_t.tgl_lahir,
    suratketlahir_t.jam_lahir,
    suratketlahir_t.bb_lahir,
    suratketlahir_t.panjang_lahir,
    suratketlahir_t.kelahiran,
    suratketlahir_t.anakke,
    fgetnamalookup(suratketlahir_t.golongan_darah) AS golongandarah_bayi,
    pegawai_m.nama_pegawai AS dokter_dpjp,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin_bayi
   FROM suratketlahir_t
     JOIN pasien_m ON suratketlahir_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pekerjaan_m ON suratketlahir_t.ayah_pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN pegawai_m ON suratketlahir_t.dokterdpjp_id = pegawai_m.pegawai_id
  WHERE suratketlahir_t.is_deleted = false AND suratketlahir_t.is_active = true;
        ');

           $this->execute('
   ALTER TABLE public.suratketlahir_v
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190703_042827_suratketlahir_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190703_042827_suratketlahir_v_update cannot be reverted.\n";

        return false;
    }
    */
}
