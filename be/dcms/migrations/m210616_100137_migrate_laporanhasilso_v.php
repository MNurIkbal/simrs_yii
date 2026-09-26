<?php

use yii\db\Migration;

/**
 * Class m210616_100137_migrate_laporanhasilso_v
 */
class m210616_100137_migrate_laporanhasilso_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporanhasilso_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanhasilso_v\" AS  SELECT formulirstokopname_t.created_date AS tgl_form_so,
    stokopname_t.tglverifikasi AS tgl_validasi_so,
    peg_verif_so.nama_pegawai AS validasi_by,
    formulirstokopname_t.noformulir AS no_form_so,
    ruangan_m.ruangan_id,
    instalasi_m.instalasi_id,
    concat(instalasi_m.instalasi_nama, ' - ', ruangan_m.ruangan_nama) AS instalasi_ruangan,
    jenisobatalkes_m.jenisobatalkes_nama AS jenis_obatalkes,
    obatalkes_m.obatalkes_kode AS kode_obat,
    obatalkes_m.obatalkes_nama AS nama_obat,
    sat_kecil.satuanunit_nama AS satuan_kecil,
    stokopnamedetail_t.weighted_avg,
    formstokopname_t.volume_stok AS stok_sistem,
    stokopnamedetail_t.volume_fisik AS stok_fisik,
    stokopnamedetail_t.volume_sistem - stokopnamedetail_t.volume_fisik AS selisih,
    stokopnamedetail_t.weighted_avg * (stokopnamedetail_t.volume_sistem - stokopnamedetail_t.volume_fisik) AS total_harga_selisi
   FROM stokopname_t
     JOIN stokopnamedetail_t ON stokopname_t.stokopname_id = stokopnamedetail_t.stokopname_id AND stokopnamedetail_t.is_deleted = false
     JOIN formulirstokopname_t ON stokopname_t.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id
     JOIN obatalkes_m ON stokopnamedetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN formstokopname_t ON stokopnamedetail_t.stokopnamedetail_id = formstokopname_t.stokopnamedetail_id
     LEFT JOIN satuanunit_m sat_kecil ON obatalkes_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN pegawai_m peg_verif_so ON stokopname_t.pegawaiverifikasi_id = peg_verif_so.pegawai_id
     LEFT JOIN ruangan_m ON stokopname_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
  WHERE stokopname_t.is_deleted = false AND stokopname_t.is_active = true AND stokopname_t.is_verifikasi = true;
");
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210616_100137_migrate_laporanhasilso_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210616_100137_migrate_laporanhasilso_v cannot be reverted.\n";

        return false;
    }
    */
}
