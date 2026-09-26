<?php

use yii\db\Migration;

/**
 * Class m230711_071819_migrate_RPP186_laporanpermintaanmakan_v
 */
class m230711_071819_migrate_RPP186_laporanpermintaanmakan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporanpermintaanmakan_v";');
        $this->execute("CREATE OR REPLACE VIEW public.laporanpermintaanmakan_v
        AS SELECT hit.tgl_permintaanmakan,
            hit.jenisdiet_id,
            hit.jenisdiet_nama,
            hit.makanandiet_id,
            hit.makanandiet_nama,
            count(hit.makanandiet_id) AS total,
            sum(hit.jumlah) AS jumlah,
            hit.riwayat_alergi,
            hit.diagnosa,
            hit.tanggal_lahir,
            hit.penjamin_nama,
            hit.jenis_kelamin
           FROM ( SELECT to_char(permintaanmakan_t.tgl_permintaanmakan, 'YYYY-MM-DD'::text) AS tgl_permintaanmakan,
                    permintaanmakandetail_t.jenisdiet_id,
                    jenisdiet_m.jenisdiet_nama,
                    permintaanmakandetail_t.makanandiet_id,
                    makanandiet_m.makanandiet_nama,
                    permintaanmakandetail_t.jumlah,
                    alergi.riwayat_alergi,
                    cppt_t.diagnosa,
                    pasien_m.tanggal_lahir,
                    penjamin_m.penjamin_nama,
                    look_jeniskelamin.lookup_name AS jenis_kelamin
                   FROM permintaanmakan_t
                     JOIN permintaanmakandetail_t ON permintaanmakan_t.permintaaanmakan_id = permintaanmakandetail_t.permintaanmakan_id
                     JOIN jenisdiet_m ON permintaanmakandetail_t.jenisdiet_id = jenisdiet_m.jenisdiet_id
                     JOIN makanandiet_m ON permintaanmakandetail_t.makanandiet_id = makanandiet_m.makanandiet_id
                     LEFT JOIN pasienadmisi_t ON permintaanmakan_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     LEFT JOIN ( SELECT x.pasien_id,
                            string_agg(x.riwayat_alergi, ', '::text) AS riwayat_alergi
                           FROM ( SELECT anamnesa_t.pasien_id,
                                    concat(
                                        CASE
                                            WHEN COALESCE(anamnesa_t.alergi_obat, ''::text) = ''::text THEN ''::text
                                            ELSE concat(anamnesa_t.alergi_obat, ', ')
                                        END,
                                        CASE
                                            WHEN COALESCE(anamnesa_t.alergi_lainnya, ''::text) = ''::text THEN ''::text
                                            ELSE concat(anamnesa_t.alergi_lainnya, ', ')
                                        END,
                                        CASE
                                            WHEN COALESCE(anamnesa_t.riwayat_alergiobat, ''::text) = ''::text THEN ''::text
                                            ELSE concat(anamnesa_t.riwayat_alergiobat, ', ')
                                        END,
                                        CASE
                                            WHEN COALESCE(anamnesa_t.alergi_makanan, ''::text) = ''::text THEN ''::text
                                            ELSE concat(anamnesa_t.alergi_makanan, ', ')
                                        END) AS riwayat_alergi
                                   FROM anamnesa_t
                                  WHERE COALESCE(anamnesa_t.alergi_obat, ''::text) <> ''::text OR COALESCE(anamnesa_t.alergi_lainnya, ''::text) <> ''::text OR COALESCE(anamnesa_t.riwayat_alergiobat, ''::text) <> ''::text OR COALESCE(anamnesa_t.alergi_makanan, ''::text) <> ''::text
                                UNION ALL
                                 SELECT pendaftaran_t.pasien_id,
                                    concat(
                                        CASE
                                            WHEN COALESCE(asesmenperawatrd_t.alergi_obat, ''::text) = ''::text THEN ''::text
                                            ELSE concat(asesmenperawatrd_t.alergi_obat, ', ')
                                        END,
                                        CASE
                                            WHEN COALESCE(asesmenperawatrd_t.alergi_lainnya, ''::text) = ''::text THEN ''::text
                                            ELSE concat(asesmenperawatrd_t.alergi_lainnya, ', ')
                                        END) AS riwayat_alergi
                                   FROM asesmenperawatrd_t
                                     JOIN ( SELECT a.pendaftaran_id,
                                            a.pasien_id
                                           FROM pendaftaran_t a) pendaftaran_t ON asesmenperawatrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                                  WHERE COALESCE(asesmenperawatrd_t.alergi_obat, ''::text) <> ''::text OR COALESCE(asesmenperawatrd_t.alergi_lainnya, ''::text) <> ''::text
                                UNION ALL
                                 SELECT pendaftaran_t.pasien_id,
                                    concat(
                                        CASE
                                            WHEN COALESCE(asesmenawal_t.additional_data::json ->> 'alergi_obat'::text, ''::text) = ''::text THEN ''::text
                                            ELSE concat(asesmenawal_t.additional_data::json ->> 'alergi_obat'::text, ', ')
                                        END,
                                        CASE
                                            WHEN COALESCE(asesmenawal_t.additional_data::json ->> 'alergi_lainnya'::text, ''::text) = ''::text THEN ''::text
                                            ELSE concat(asesmenawal_t.additional_data::json ->> 'alergi_lainnya'::text)
                                        END) AS riwayat_alergi
                                   FROM asesmenawal_t
                                     JOIN ( SELECT a.pendaftaran_id,
                                            a.pasien_id
                                           FROM pendaftaran_t a) pendaftaran_t ON asesmenawal_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                                  WHERE asesmenawal_t.additional_data IS NOT NULL) x
                          GROUP BY x.pasien_id) alergi ON pasienadmisi_t.pasien_id = alergi.pasien_id
                     LEFT JOIN ( SELECT a.pasienadmisi_id,
                            a.a_diag_utama ->> 'text'::text AS diagnosa
                           FROM cppt_t a
                          WHERE a.tgl_cppt = (( SELECT max(b.tgl_cppt) AS max
                                   FROM cppt_t b
                                  WHERE b.pasienadmisi_id = a.pasienadmisi_id AND b.is_deleted = false AND b.pasienadmisi_id IS NOT NULL)) AND a.is_deleted = false AND a.pasienadmisi_id IS NOT NULL AND (a.subject <> '-'::text OR a.object <> '-'::text OR (a.a_diag_utama ->> 'text'::text) <> '-'::text OR a.planning <> '-'::text)) cppt_t ON permintaanmakan_t.pasienadmisi_id = cppt_t.pasienadmisi_id
                     JOIN ( SELECT a.pasien_id,
                            a.tanggal_lahir,
                            a.jeniskelamin
                           FROM pasien_m a) pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
                     JOIN ( SELECT a.penjamin_id,
                            a.penjamin_nama
                           FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
                  WHERE permintaanmakan_t.status = 1
                  ORDER BY permintaanmakan_t.tgl_permintaanmakan) hit
          GROUP BY hit.tgl_permintaanmakan, hit.jenisdiet_id, hit.jenisdiet_nama, hit.makanandiet_id, hit.makanandiet_nama, hit.riwayat_alergi, hit.diagnosa, hit.tanggal_lahir, hit.penjamin_nama, hit.jenis_kelamin;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230711_071819_migrate_RPP186_laporanpermintaanmakan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230711_071819_migrate_RPP186_laporanpermintaanmakan_v cannot be reverted.\n";

        return false;
    }
    */
}
