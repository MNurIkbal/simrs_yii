<?php

use yii\db\Migration;

/**
 * Class m220121_092714_improvment_penyesuian_view_udd_US2696
 */
class m220121_092714_improvment_penyesuian_view_udd_US2696 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infoudd_detail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infoudd_detail_v" AS  
            SELECT udd_detail_t.udd_detail_id,
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
                        WHEN (udd_detail_t.tgl_selesai < CURRENT_DATE) THEN true
                        ELSE false
                    END AS is_stoped,
                obatalkes_m.is_oral,
                udd_dosis_t.waktu_pemberian,
                udd_dosis_t.jam_pemberian,
                satuankonversi_m.satuankecil_id,
                udd_detail_t.instruksi_id,
                udd_t.status_udd AS status_udd_id,
                    CASE
                        WHEN (COALESCE(obatalkespasien.obatalkespasien_id, 0) > 0) THEN (udd_detail_t.qty - COALESCE(obatalkespasien.qty_oa, (0)::double precision))
                        ELSE (0)::double precision
                    END AS qty_retur
               FROM ((((((udd_detail_t
                 JOIN udd_t ON ((udd_detail_t.udd_id = udd_t.udd_id)))
                 JOIN ( SELECT a.obatalkes_id,
                        a.obatalkes_nama,
                        a.is_oral
                       FROM obatalkes_m a) obatalkes_m ON ((udd_detail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
                 LEFT JOIN ( SELECT b.satuankonversi_id,
                        b.satuanbesar_id,
                        sat_besar.satuanunit_nama AS satuan_besar,
                        b.satuankecil_id,
                        sat_kecil.satuanunit_nama AS satuan_kecil,
                        b.nilai_konversi
                       FROM ((satuankonversi_m b
                         JOIN satuanunit_m sat_besar ON ((b.satuanbesar_id = sat_besar.satuanunit_id)))
                         JOIN satuanunit_m sat_kecil ON ((b.satuankecil_id = sat_kecil.satuanunit_id)))
                      WHERE (b.is_deleted = false)) satuankonversi_m ON ((udd_detail_t.satuankonversi_id = satuankonversi_m.satuankonversi_id)))
                 LEFT JOIN ( SELECT c.signa_id,
                        c.signa_nama
                       FROM signaobat_m c) signa_m ON ((udd_detail_t.signa_id = signa_m.signa_id)))
                 LEFT JOIN ( SELECT d.udd_detail_id,
                        d.dosis,
                        string_agg(concat((waktupemberian_m.waktu_pemberian)::text, \' (\', d.keterangan, \')\'), \' - \'::text) AS waktu_pemberian,
                        string_agg((d.jam_pemberian)::text, \'-\'::text) AS jam_pemberian
                       FROM (udd_dosis_t d
                         JOIN waktupemberian_m ON ((d.waktupemberian_id = waktupemberian_m.waktupemberian_id)))
                      WHERE (d.is_deleted = false)
                      GROUP BY d.udd_detail_id, d.dosis) udd_dosis_t ON ((udd_detail_t.udd_detail_id = udd_dosis_t.udd_detail_id)))
                 LEFT JOIN ( SELECT obatalkespasien_t.udd_detail_id,
                        obatalkespasien_t.qty_oa,
                        obatalkespasien_t.obatalkes_id,
                        obatalkespasien_t.obatalkespasien_id
                       FROM obatalkespasien_t
                      WHERE (obatalkespasien_t.is_deleted IS FALSE)) obatalkespasien ON (((udd_detail_t.udd_detail_id = obatalkespasien.udd_detail_id) AND (udd_detail_t.obatalkes_id = obatalkespasien.obatalkes_id))));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220121_092714_improvment_penyesuian_view_udd_US2696 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220121_092714_improvment_penyesuian_view_udd_US2696 cannot be reverted.\n";

        return false;
    }
    */
}
