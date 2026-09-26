<?php

use yii\db\Migration;

/**
 * Class m191216_074953_infodistribusiobatalkes_v
 */
class m191216_074953_infodistribusiobatalkes_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infodistribusiobatalkes_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infodistribusiobatalkes_v AS 
 SELECT pesanobatalkes_t.pesanobatalkes_id,
    pesanobatalkes_t.tglpemesanan,
    pesanobatalkes_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    pesanobatalkes_t.nopemesanan,
    pesanobatalkes_t.ruanganpemesan_id,
    ruangpemesan.ruangan_id AS ruangan_pemesan_id,
    ruangpemesan.ruangan_nama AS ruangan_pemesan,
    instalasipesan.instalasi_id AS instalasi_pemesan_id,
    instalasipesan.instalasi_nama AS instalasi_pemesan,
    pesanobatalkes_t.mutasiobatruangan_id,
    pesanobatalkes_t.statuspesan,
    fgetnamalookup(pesanobatalkes_t.statuspesan::integer) AS status_pengiriman,
    pesanobatalkes_t.tglmintadikirim,
    pesanobatalkes_t.keterangan_pesan,
    mutasiobatruangan_t.nomutasioa,
    mutasiobatruangan_t.tglmutasioa,
    mutasiobatruangan_t.status_mutasi,
    fgetnamalookup(mutasiobatruangan_t.status_mutasi) AS status_penerimaan,
    terimamutasiobat_t.noterimamutasi,
    terimamutasiobat_t.tglterima,
    concat(instalasi_m.instalasi_nama, ' - ', ruangan_m.ruangan_nama) AS instalasi_ruangan,
        CASE
            WHEN pesanobatalkes_t.statuspesan::text = '398'::text THEN 'Belum Dikirim'::text
            WHEN mutasiobatruangan_t.status_mutasi = 401 THEN 'Sudah Dikirim'::text
            WHEN mutasiobatruangan_t.status_mutasi = 400 THEN 'Diterima'::text
            ELSE '-'::text
        END AS status_distribusi,
    concat(mutasiobatruangan_t.nomutasioa,
        CASE
            WHEN terimamutasiobat_t.noterimamutasi IS NULL THEN ''::text
            ELSE concat(',', terimamutasiobat_t.noterimamutasi)
        END) AS reference,
        CASE
            WHEN pesanobatalkes_t.mutasiobatruangan_id IS NULL THEN pesanobatalkes_t.statuspesan::integer
            WHEN pesanobatalkes_t.mutasiobatruangan_id IS NOT NULL THEN mutasiobatruangan_t.status_mutasi
            WHEN terimamutasiobat_t.mutasiobatruangan_id IS NOT NULL THEN mutasiobatruangan_t.status_mutasi
            ELSE NULL::integer
        END AS status_id
   FROM pesanobatalkes_t
     JOIN ruangan_m ON pesanobatalkes_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruangpemesan ON pesanobatalkes_t.ruanganpemesan_id = ruangpemesan.ruangan_id
     JOIN instalasi_m instalasipesan ON ruangpemesan.instalasi_id = instalasipesan.instalasi_id
     LEFT JOIN mutasiobatruangan_t ON pesanobatalkes_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id
     LEFT JOIN terimamutasiobat_t ON mutasiobatruangan_t.mutasiobatruangan_id = terimamutasiobat_t.mutasiobatruangan_id
  WHERE pesanobatalkes_t.is_active = true AND pesanobatalkes_t.is_deleted = false;
");

        $this->execute('ALTER TABLE public.infodistribusiobatalkes_v
  OWNER TO postgres;');

        

                
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191216_074953_infodistribusiobatalkes_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191216_074953_infodistribusiobatalkes_v cannot be reverted.\n";

        return false;
    }
    */
}
