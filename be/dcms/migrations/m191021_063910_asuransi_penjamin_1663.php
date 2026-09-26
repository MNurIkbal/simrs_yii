<?php

use yii\db\Migration;

/**
 * Class m191021_063910_asuransi_penjamin_1663
 */
class m191021_063910_asuransi_penjamin_1663 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        $this->execute('DROP VIEW if exists public.pegawaiakses_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.pegawaiakses_v AS 
 SELECT pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai,
    ruanganpemakai_k.ruangan_id,
    ruangan_m.instalasi_id,
    ruangan_m.ruangan_nama
   FROM pegawai_m
     JOIN loginpemakai_k ON pegawai_m.pegawai_id = loginpemakai_k.pegawai_id AND loginpemakai_k.is_deleted = false
     JOIN ruanganpemakai_k ON loginpemakai_k.loginpemakai_id = ruanganpemakai_k.loginpemakai_id AND ruanganpemakai_k.is_deleted = false
     JOIN ruangan_m ON ruanganpemakai_k.ruangan_id = ruangan_m.ruangan_id;
");

        $this->execute('ALTER TABLE public.pegawaiakses_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.laporanklaiminacbg_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.laporanklaiminacbg_v AS 
 SELECT
        CASE
            WHEN klaiminacbg_t.instalasi_id = 3 THEN 'RI'::text
            ELSE 'RJ'::text
        END AS tipe,
    klaiminacbg_t.instalasi_id,
    klaiminacbg_t.klaiminacbg_id,
    klaimgroup_t.klaimgroup_id,
    klaiminacbg_t.tgl_masuk,
    klaiminacbg_t.tgl_keluar,
    klaimgroup_t.created_date AS tgl_group,
    klaiminacbg_t.no_sep,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    klaiminacbg_t.diagnosa_primer,
    klaiminacbg_t.diagnosa_sekunder,
    klaimgroup_t.spesial_procedure,
    klaiminacbg_t.total_tarifrs,
    klaimgroup_t.group_nama,
    klaimgroup_t.total,
    klaiminacbg_t.is_terkirim,
        CASE
            WHEN klaiminacbg_t.is_terkirim = false THEN '-'::text
            ELSE 'Terkirim'::text
        END AS status_kirim,
    loginpemakai_k.nama_pemakai AS username,
    pegawai_m.nama_pegawai AS petugas,
    pendaftaran.penjamin_id,
    pendaftaran.penjamin_nama,
    klaiminacbg_t.carapulang_id AS cara_pulang,
    klaiminacbg_t.tarif AS jenis_tarif,
    klaiminacbg_t.jenis_kelasrawat
   FROM klaiminacbg_t
     JOIN pasien_m ON klaiminacbg_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama
           FROM pendaftaran_t
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id) pendaftaran ON klaiminacbg_t.pendaftaran_id = pendaftaran.pendaftaran_id
     LEFT JOIN klaimgroup_t ON klaiminacbg_t.klaimgroup_id = klaimgroup_t.klaimgroup_id
     LEFT JOIN loginpemakai_k ON klaimgroup_t.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
  WHERE klaiminacbg_t.is_deleted = false
  ORDER BY klaiminacbg_t.klaiminacbg_id DESC;");

        $this->execute('ALTER TABLE public.laporanklaiminacbg_v
  OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191021_063910_asuransi_penjamin_1663 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191021_063910_asuransi_penjamin_1663 cannot be reverted.\n";

        return false;
    }
    */
}
