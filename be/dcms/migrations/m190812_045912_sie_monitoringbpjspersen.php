<?php

use yii\db\Migration;

/**
 * Class m190812_045912_sie_monitoringbpjspersen
 */
class m190812_045912_sie_monitoringbpjspersen extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.sie_monitoringbpjspersen;');

        $this->execute("CREATE OR REPLACE VIEW public.sie_monitoringbpjspersen AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.tgl_pendaftaran,
    pasienpulang_t.tglpasienpulang,
    pasienadmisi_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    kelaspelayanan_m.urutankelas,
    pasien_m.no_rekam_medik,
    bpjs_t.nosep,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    bpjs_t.klsrawat AS hak_kelas,
    COALESCE(tagihan.sub_total, 0::double precision) AS tagihan_rs,
    COALESCE(monitorsetdiagnosa.total, 0::double precision) AS tarif_inacbg,
    (COALESCE(tagihan.sub_total, 0::double precision) * 100::double precision /
        CASE COALESCE(monitorsetdiagnosa.total, 1::double precision)
            WHEN 0 THEN 1::double precision
            ELSE COALESCE(monitorsetdiagnosa.total, 1::double precision)
        END)::numeric(15,2) AS persen,
        CASE
            WHEN (COALESCE(tagihan.sub_total, 0::double precision) * 100::double precision /
            CASE COALESCE(monitorsetdiagnosa.total, 1::double precision)
                WHEN 0 THEN 1::double precision
                ELSE COALESCE(monitorsetdiagnosa.total, 1::double precision)
            END)::numeric(15,2) >= 100::numeric THEN 'merah'::text
            WHEN (COALESCE(tagihan.sub_total, 0::double precision) * 100::double precision /
            CASE COALESCE(monitorsetdiagnosa.total, 1::double precision)
                WHEN 0 THEN 1::double precision
                ELSE COALESCE(monitorsetdiagnosa.total, 1::double precision)
            END)::numeric(15,2) < 75::numeric THEN 'hijau'::text
            ELSE 'kuning'::text
        END AS warna,
        CASE
            WHEN monitorsetdiagnosa.diag_utama_id IS NULL THEN 'BELUM DIMONITOR'::text
            ELSE 'SUDAH DIMONITOR'::text
        END AS status_monitor
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id AND carabayar_m.carabayar_id = 6
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN ( SELECT monitorsetdiagnosa_t.monitorsetdiagnosa_id,
            monitorsetdiagnosa_t.pendaftaran_id,
            monitorsetdiagnosa_t.pasienadmisi_id,
            monitorsetdiagnosa_t.diag_utama_id,
            diagnosa_m.diagnosa_kode,
            diagnosa_m.diagnosa_nama,
            monitorsetdiagnosa_t.diag_penyerta,
            monitorsetdiagnosa_t.diag_tindakan,
            monitorsetdiagnosa_t.total,
            monitorsetdiagnosa_t.is_dokter
           FROM monitorsetdiagnosa_t
             JOIN diagnosa_m ON monitorsetdiagnosa_t.diag_utama_id = diagnosa_m.diagnosa_id
          WHERE monitorsetdiagnosa_t.is_deleted = false) monitorsetdiagnosa ON pasienadmisi_t.pasienadmisi_id = monitorsetdiagnosa.pasienadmisi_id
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN ( SELECT x.pendaftaran_id,
            x.pasienadmisi_id,
            sum(x.sub_total) AS sub_total
           FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS sub_total
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
                  GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    pendaftaran_t_1.pasienadmisi_id,
                    sum(obatalkespasien_t.hargajual_oa) AS sub_total
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN obatalkespasien_t ON pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
                  GROUP BY pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id) x
          GROUP BY x.pendaftaran_id, x.pasienadmisi_id) tagihan ON pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = tagihan.pasienadmisi_id;
");

        $this->execute('ALTER TABLE public.sie_monitoringbpjspersen
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190812_045912_sie_monitoringbpjspersen cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190812_045912_sie_monitoringbpjspersen cannot be reverted.\n";

        return false;
    }
    */
}
