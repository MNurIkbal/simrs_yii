<?php

use yii\db\Migration;

/**
 * Class m190715_071845_sie_10besardiagnosa
 */
class m190715_071845_sie_10besardiagnosa extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
          DROP VIEW IF exists public.sie_10besardiagnosa;
              ');


        $this->execute("
          CREATE OR REPLACE VIEW public.sie_10besardiagnosa AS 
 SELECT gabung.kode_diagnosa AS \"Kode Diagnosa\",
    gabung.nama_diagnosa AS \"Nama Diagnosa\",
    sum(gabung.jumlah) AS \"Jumlah\"
   FROM ( SELECT 'RJ'::text AS instalasi,
            x.diagnosa_kode AS kode_diagnosa,
            x.diagnosa_nama AS nama_diagnosa,
            count(*) AS jumlah,
            x.tahun_dignosa
           FROM ( SELECT pendaftaran_t.pendaftaran_id,
                    koreksidiagnosa_t.diagnosa_id,
                    fgetdiagnosa_kode(koreksidiagnosa_t.diagnosa_id) AS diagnosa_kode,
                    fgetdiagnosa_namalain(koreksidiagnosa_t.diagnosa_id) AS diagnosa_nama,
                    fgetpasien_jeniskelamin(koreksidiagnosa_t.pasien_id) AS jenis_kelamin_id,
                    fgetnamalookup(fgetpasien_jeniskelamin(koreksidiagnosa_t.pasien_id)) AS nama_jenis_kelamin,
                    to_char(koreksidiagnosa_t.tgl_koreksidiagnosa, 'MM'::text)::character varying AS bulan_diagnosa,
                    to_char(koreksidiagnosa_t.tgl_koreksidiagnosa, 'YYYY'::text)::character varying AS tahun_dignosa,
                    to_char(koreksidiagnosa_t.tgl_koreksidiagnosa, 'MM-YYYY'::text)::character varying AS bulan_tahun,
                    to_char(koreksidiagnosa_t.tgl_koreksidiagnosa, 'YYYY-MM-DD'::text)::date AS tgl_koreksidiagnosa,
                    koreksidiagnosa_t.is_diagnosa_baru,
                        CASE koreksidiagnosa_t.is_diagnosa_baru
                            WHEN true THEN 'Baru'::text
                            ELSE 'Lama'::text
                        END::character varying AS nama_kasus_diagnosa
                   FROM koreksidiagnosa_t
                     JOIN pendaftaran_t ON koreksidiagnosa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                  WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 2 AND koreksidiagnosa_t.pasienadmisi_id IS NULL AND koreksidiagnosa_t.is_deleted IS FALSE AND pendaftaran_t.instalasi_id = 1 AND to_char(koreksidiagnosa_t.tgl_koreksidiagnosa::timestamp with time zone, 'YYYY'::text) = date_part('year'::text, 'now'::text::date)::text) x
          WHERE x.is_diagnosa_baru = true
          GROUP BY x.diagnosa_kode, x.diagnosa_nama, x.bulan_diagnosa, x.tahun_dignosa
        UNION ALL
         SELECT 'RD'::text AS instalasi,
            x.diagnosa_kode AS kode_diagnosa,
            x.diagnosa_nama AS nama_diagnosa,
            count(*) AS jumlah,
            x.tahun_dignosa
           FROM ( SELECT pendaftaran_t.pendaftaran_id,
                    koreksidiagnosa_t.diagnosa_id,
                    fgetdiagnosa_kode(koreksidiagnosa_t.diagnosa_id) AS diagnosa_kode,
                    fgetdiagnosa_namalain(koreksidiagnosa_t.diagnosa_id) AS diagnosa_nama,
                    fgetpasien_jeniskelamin(koreksidiagnosa_t.pasien_id) AS jenis_kelamin_id,
                    fgetnamalookup(fgetpasien_jeniskelamin(koreksidiagnosa_t.pasien_id)) AS nama_jenis_kelamin,
                    to_char(koreksidiagnosa_t.tgl_koreksidiagnosa, 'MM'::text)::character varying AS bulan_diagnosa,
                    to_char(koreksidiagnosa_t.tgl_koreksidiagnosa, 'YYYY'::text)::character varying AS tahun_dignosa,
                    to_char(koreksidiagnosa_t.tgl_koreksidiagnosa, 'MM-YYYY'::text)::character varying AS bulan_tahun,
                    to_char(koreksidiagnosa_t.tgl_koreksidiagnosa, 'YYYY-MM-DD'::text)::date AS tgl_koreksidiagnosa,
                    koreksidiagnosa_t.is_diagnosa_baru,
                        CASE koreksidiagnosa_t.is_diagnosa_baru
                            WHEN true THEN 'Baru'::text
                            ELSE 'Lama'::text
                        END::character varying AS nama_kasus_diagnosa
                   FROM koreksidiagnosa_t
                     JOIN pendaftaran_t ON koreksidiagnosa_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                  WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 2 AND koreksidiagnosa_t.pasienadmisi_id IS NULL AND koreksidiagnosa_t.is_deleted IS FALSE AND pendaftaran_t.instalasi_id = 2 AND to_char(koreksidiagnosa_t.tgl_koreksidiagnosa::timestamp with time zone, 'YYYY'::text) = date_part('year'::text, 'now'::text::date)::text) x
          GROUP BY x.diagnosa_kode, x.diagnosa_nama, x.bulan_diagnosa, x.tahun_dignosa
        UNION ALL
         SELECT 'RI'::text AS instalasi,
            x.diagnosa_kode AS kode_diagnosa,
            x.diagnosa_nama AS nama_diagnosa,
            count(*) AS jumlah,
            x.tahun_dignosa
           FROM ( SELECT pasienadmisi_t.pendaftaran_id,
                    koreksidiagnosa_t.diagnosa_id,
                    fgetdiagnosa_kode(koreksidiagnosa_t.diagnosa_id) AS diagnosa_kode,
                    fgetdiagnosa_namalain(koreksidiagnosa_t.diagnosa_id) AS diagnosa_nama,
                    fgetpasien_jeniskelamin(koreksidiagnosa_t.pasien_id) AS jenis_kelamin_id,
                    fgetnamalookup(fgetpasien_jeniskelamin(koreksidiagnosa_t.pasien_id)) AS nama_jenis_kelamin,
                    to_char(koreksidiagnosa_t.tgl_koreksidiagnosa, 'MM'::text)::character varying AS bulan_diagnosa,
                    to_char(koreksidiagnosa_t.tgl_koreksidiagnosa, 'YYYY'::text)::character varying AS tahun_dignosa,
                    to_char(koreksidiagnosa_t.tgl_koreksidiagnosa, 'MM-YYYY'::text)::character varying AS bulan_tahun,
                    to_char(koreksidiagnosa_t.tgl_koreksidiagnosa, 'YYYY-MM-DD'::text)::date AS tgl_koreksidiagnosa,
                    koreksidiagnosa_t.is_diagnosa_baru,
                        CASE koreksidiagnosa_t.is_diagnosa_baru
                            WHEN true THEN 'Baru'::text
                            ELSE 'Lama'::text
                        END::character varying AS nama_kasus_diagnosa
                   FROM koreksidiagnosa_t
                     JOIN pasienadmisi_t ON koreksidiagnosa_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                  WHERE koreksidiagnosa_t.kelompokdiagnosa_id = 2 AND koreksidiagnosa_t.is_deleted IS FALSE) x
          GROUP BY x.diagnosa_kode, x.diagnosa_nama, x.bulan_diagnosa, x.tahun_dignosa) gabung
  GROUP BY gabung.kode_diagnosa, gabung.nama_diagnosa
  ORDER BY (sum(gabung.jumlah)) DESC
 LIMIT 10;
              ");


        $this->execute('
          ALTER TABLE public.sie_10besardiagnosa
  OWNER TO postgres;
              ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190715_071845_sie_10besardiagnosa cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190715_071845_sie_10besardiagnosa cannot be reverted.\n";

        return false;
    }
    */
}
