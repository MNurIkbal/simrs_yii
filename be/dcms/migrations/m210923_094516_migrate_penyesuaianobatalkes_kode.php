<?php

use yii\db\Migration;

/**
 * Class m210923_094516_migrate_penyesuaianobatalkes_kode
 */
class m210923_094516_migrate_penyesuaianobatalkes_kode extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopemakaianobatalkesdetail_v;');

        $this->execute("
            CREATE VIEW \"public\".\"infopemakaianobatalkesdetail_v\" AS  SELECT pemakaianobatdetail_t.pemakaianobatdetail_id,
    pemakaianobatdetail_t.pemakaianobat_id,
    pemakaianobat_t.tglpemakaianobat,
    pemakaianobat_t.ruangan_id,
    pemakaianobat_t.pegawai_id,
    pegawai_m.nama_pegawai,
    pemakaianobatdetail_t.obatalkes_id,
    obatalkes_m.obatalkes_namalain,
    pemakaianobatdetail_t.qty_satuanpakai,
    pemakaianobatdetail_t.satuankecil_id,
    satuan_kecil.satuanunit_nama AS satuankecil_nama,
    pemakaianobat_t.nopemakaian_obat,
    pemakaianobatdetail_t.jumlah_input,
    pemakaianobatdetail_t.satuanbesar_id,
    satuan_besar.satuanunit_nama AS satuanbesar_nama,
    pemakaianobat_t.keterangan_pemakaianobat,
    pemakaianobatdetail_t.ket_obatpakai,
    obatalkes_m.obatalkes_nama,
    obatalkes_m.obatalkes_kode
   FROM pemakaianobatdetail_t
     JOIN obatalkes_m ON pemakaianobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN pemakaianobat_t ON pemakaianobatdetail_t.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id
     JOIN pegawai_m ON pemakaianobat_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN satuanunit_m satuan_kecil ON pemakaianobatdetail_t.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN satuanunit_m satuan_besar ON pemakaianobatdetail_t.satuanbesar_id = satuan_besar.satuanunit_id
  WHERE pemakaianobatdetail_t.is_active = true AND pemakaianobatdetail_t.is_deleted = false;
");

        $this->execute('DROP VIEW if exists public.infopemusnahanobatdetail_v;');

        $this->execute("
            CREATE VIEW \"public\".\"infopemusnahanobatdetail_v\" AS  SELECT pemusnahanobatdetail_t.pemusnahanobat_id,
    pemusnahanobat_t.nopemusnahan,
    pemusnahanobat_t.tglpemusnahan,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    pegawai_mengetahui.nama_pegawai AS pegawai_mengetahui,
    pegawai_menyetujui.nama_pegawai AS pegawai_menyetujui,
    pegawai_pelaksana.nama_pegawai AS pegawai_pelaksana,
    obatalkes_m.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    pemusnahanobatdetail_t.tglkadaluarsa,
    pemusnahanobatdetail_t.jumlah AS stok,
    satuanunit_m.satuanunit_nama AS satuan_kecil,
    pemusnahanobatdetail_t.harganetto AS jumlah_harganetto,
    pemusnahanobatdetail_t.is_deleted,
    obatalkes_m.obatalkes_kode
   FROM pemusnahanobatdetail_t
     JOIN pemusnahanobat_t ON pemusnahanobatdetail_t.pemusnahanobat_id = pemusnahanobat_t.pemusnahanobat_id
     JOIN obatalkes_m ON pemusnahanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN satuanunit_m ON obatalkes_m.satuankecil_id = satuanunit_m.satuanunit_id
     LEFT JOIN ruangan_m ON pemusnahanobat_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pegawai_m pegawai_mengetahui ON pemusnahanobat_t.pegawaimengetahui_id = pegawai_mengetahui.pegawai_id
     LEFT JOIN pegawai_m pegawai_menyetujui ON pemusnahanobat_t.pegawaimenyetujui_id = pegawai_menyetujui.pegawai_id
     LEFT JOIN pegawai_m pegawai_pelaksana ON pemusnahanobat_t.pegawai_id = pegawai_pelaksana.pegawai_id
  WHERE pemusnahanobatdetail_t.is_active = true;");
       

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210923_094516_migrate_penyesuaianobatalkes_kode cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210923_094516_migrate_penyesuaianobatalkes_kode cannot be reverted.\n";

        return false;
    }
    */
}
