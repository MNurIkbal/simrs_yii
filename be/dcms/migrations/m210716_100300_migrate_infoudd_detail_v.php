<?php

use yii\db\Migration;

/**
 * Class m210716_100300_migrate_infoudd_detail_v
 */
class m210716_100300_migrate_infoudd_detail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
           $this->execute('DROP VIEW if exists "public"."infoudd_dosis_v";');

           $this->execute("
            CREATE VIEW \"public\".\"infoudd_dosis_v\" AS  SELECT udd_dosis_t.udd_dosis_id,
    udd_dosis_t.udd_detail_id,
    udd_dosis_t.obatalkes_id,
    obat.obatalkes_nama,
    udd_dosis_t.dosis,
    udd_detail_t.satuan_input,
    udd_detail_t.satuan_kecil,
    waktupemberian.waktu_pemberian,
    udd_dosis_t.jam_pemberian,
    ruteobat.is_oral,
    ruteobat.nama_rute AS rute,
    udd_detail_t.catatan_dokter
   FROM udd_dosis_t
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama,
            a.ruteobat_id
           FROM obatalkes_m a) obat ON udd_dosis_t.obatalkes_id = obat.obatalkes_id
     JOIN ( SELECT b.udd_detail_id,
            b.satuaninput_id,
            b.satuankecil_id,
            b.catatan_dokter,
            sat_input.satuanunit_nama AS satuan_input,
            sat_kecil.satuanunit_nama AS satuan_kecil
           FROM udd_detail_t b
             LEFT JOIN satuanunit_m sat_input ON b.satuaninput_id = sat_input.satuanunit_id
             LEFT JOIN satuanunit_m sat_kecil ON b.satuankecil_id = sat_kecil.satuanunit_id
          WHERE b.tgl_selesai IS NULL) udd_detail_t ON udd_dosis_t.udd_detail_id = udd_detail_t.udd_detail_id
     LEFT JOIN ( SELECT c.waktupemberian_id,
            c.waktu_pemberian
           FROM waktupemberian_m c) waktupemberian ON udd_dosis_t.waktupemberian_id = waktupemberian.waktupemberian_id
     LEFT JOIN ( SELECT d.ruteobat_id,
            d.nama_rute,
            d.is_oral
           FROM ruteobat_m d) ruteobat ON obat.ruteobat_id = ruteobat.ruteobat_id;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210716_100300_migrate_infoudd_detail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210716_100300_migrate_infoudd_detail_v cannot be reverted.\n";

        return false;
    }
    */
}
