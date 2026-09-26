<?php

use yii\db\Migration;

/**
 * Class m210715_052726_migrate_improve_udd
 */
class m210715_052726_migrate_improve_udd extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
           $this->execute('DROP VIEW if exists "public"."infoudd_detail_v";');

           $this->execute("
            CREATE VIEW \"public\".\"infoudd_detail_v\" AS  SELECT udd_detail_t.udd_detail_id,
    udd_detail_t.udd_id,
    udd_detail_t.tgl_mulai,
    udd_detail_t.tgl_selesai,
    udd_detail_t.obatalkes_id,
    obatalkes_m.obatalkes_nama AS obat,
    satuankonversi_m.satuan_besar,
    satuankonversi_m.satuan_kecil,
    signa_m.signa_nama AS signa,
    udd_detail_t.qty,
    udd_detail_t.catatan_dokter,
    udd_detail_t.catatan_farmasi,
    udd_dosis_t.dosis,
        CASE
            WHEN udd_detail_t.tgl_selesai < CURRENT_DATE THEN true
            ELSE false
        END AS is_stoped,
    obatalkes_m.is_oral,
    udd_dosis_t.waktu_pemberian,
    udd_dosis_t.jam_pemberian
   FROM udd_detail_t
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama,
            a.is_oral
           FROM obatalkes_m a) obatalkes_m ON udd_detail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT b.satuankonversi_id,
            b.satuanbesar_id,
            sat_besar.satuanunit_nama AS satuan_besar,
            b.satuankecil_id,
            sat_kecil.satuanunit_nama AS satuan_kecil,
            b.nilai_konversi
           FROM satuankonversi_m b
             JOIN satuanunit_m sat_besar ON b.satuanbesar_id = sat_besar.satuanunit_id
             JOIN satuanunit_m sat_kecil ON b.satuankecil_id = sat_kecil.satuanunit_id
          WHERE b.is_deleted = false) satuankonversi_m ON udd_detail_t.satuankonversi_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN ( SELECT c.signa_id,
            c.signa_nama
           FROM signaobat_m c) signa_m ON udd_detail_t.signa_id = signa_m.signa_id
     LEFT JOIN ( SELECT d.udd_detail_id,
            d.dosis,
            string_agg(waktupemberian_m.waktu_pemberian::text, '-'::text) AS waktu_pemberian,
            string_agg(d.jam_pemberian::text, '-'::text) AS jam_pemberian
           FROM udd_dosis_t d
             JOIN waktupemberian_m ON d.waktupemberian_id = waktupemberian_m.waktupemberian_id
          WHERE d.is_deleted = false
          GROUP BY d.udd_detail_id, d.dosis) udd_dosis_t ON udd_detail_t.udd_detail_id = udd_dosis_t.udd_detail_id;");


    $this->execute('ALTER TABLE "public"."ruteobat_m" ADD COLUMN if not exists "is_oral" bool DEFAULT false;');


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
             LEFT JOIN satuanunit_m sat_kecil ON b.satuankecil_id = sat_kecil.satuanunit_id) udd_detail_t ON udd_dosis_t.udd_detail_id = udd_detail_t.udd_detail_id
     LEFT JOIN ( SELECT c.waktupemberian_id,
            c.waktu_pemberian
           FROM waktupemberian_m c) waktupemberian ON udd_dosis_t.waktupemberian_id = waktupemberian.waktupemberian_id
     LEFT JOIN ( SELECT d.ruteobat_id,
            d.nama_rute,
            d.is_oral
           FROM ruteobat_m d) ruteobat ON obat.ruteobat_id = ruteobat.ruteobat_id;");

           $this->execute('ALTER TABLE "public"."infoudd_dosis_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210715_052726_migrate_improve_udd cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210715_052726_migrate_improve_udd cannot be reverted.\n";

        return false;
    }
    */
}
