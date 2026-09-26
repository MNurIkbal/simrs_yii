<?php

use yii\db\Migration;

/**
 * Class m200916_102352_migrate_20200916_infomutasiobatalkes
 */
class m200916_102352_migrate_20200916_infomutasiobatalkes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infomutasiobatalkes_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infomutasiobatalkes_v\" AS  SELECT mutasiobatruangan_t.mutasiobatruangan_id,
    mutasiobatruangan_t.nomutasioa,
    mutasiobatruangan_t.tglmutasioa,
    mutasiobatruangan_t.pesanobatalkes_id,
    pesanobatalkes_t.nopemesanan,
    instalasi_tujuan.instalasi_id AS instalasi_tujuan_id,
    instalasi_tujuan.instalasi_nama,
    ruangan_tujuan.ruangan_id AS ruangan_tujuan_id,
    ruangan_tujuan.ruangan_nama,
    instalasi_m.instalasi_id AS instalasi_asal_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    ruangan_m.ruangan_id AS ruangan_asal_id,
    ruangan_m.ruangan_nama AS ruangan_asal,
    mutasiobatruangan_t.status_mutasi,
    fgetnamalookup(mutasiobatruangan_t.status_mutasi) AS statusmutasi,
    mutasiobatruangan_t.created_by,
    pegawai_mutasi.nama_pegawai AS pegawai_mutasi,
    mutasiobatruangan_t.pegawaimengetahui_id,
    pegawai_mengetahui.nama_pegawai AS pegawai_mengetahui,
    terimamutasiobat_t.pegawaimengetahui_id AS id_pegawai_mengetahui,
    terimamutasiobat_t.pegawaipenerima_id AS id_pegawai_penerima,
    pegawai_mengetahui_penerimaan.nama_pegawai AS nama_pegawai_mengetahui,
    pegawai_mutasi_penerimaan.nama_pegawai AS nama_pegawai_penerima,
    terimamutasiobat_t.terimamutasiobat_id,
    terimamutasiobat_t.tglterima AS tgl_terima,
    terimamutasiobat_t.noterimamutasi AS reference
   FROM (((((((((((mutasiobatruangan_t
     LEFT JOIN pesanobatalkes_t ON ((mutasiobatruangan_t.pesanobatalkes_id = pesanobatalkes_t.pesanobatalkes_id)))
     JOIN ruangan_m ON ((mutasiobatruangan_t.ruanganasal_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ruangan_tujuan ON ((mutasiobatruangan_t.ruangantujuan_id = ruangan_tujuan.ruangan_id)))
     JOIN instalasi_m instalasi_tujuan ON ((ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id)))
     LEFT JOIN pegawai_m pegawai_mengetahui ON ((mutasiobatruangan_t.pegawaimengetahui_id = pegawai_mengetahui.pegawai_id)))
     LEFT JOIN loginpemakai_k ON ((mutasiobatruangan_t.created_by = loginpemakai_k.loginpemakai_id)))
     LEFT JOIN pegawai_m pegawai_mutasi ON ((loginpemakai_k.pegawai_id = pegawai_mutasi.pegawai_id)))
     LEFT JOIN terimamutasiobat_t ON ((terimamutasiobat_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id)))
     LEFT JOIN pegawai_m pegawai_mengetahui_penerimaan ON ((terimamutasiobat_t.pegawaimengetahui_id = pegawai_mengetahui_penerimaan.pegawai_id)))
     LEFT JOIN pegawai_m pegawai_mutasi_penerimaan ON ((terimamutasiobat_t.pegawaipenerima_id = pegawai_mutasi_penerimaan.pegawai_id)))
  WHERE ((mutasiobatruangan_t.is_deleted = false) AND (mutasiobatruangan_t.is_active = true));");
        
        $this->execute('ALTER TABLE "public"."infomutasiobatalkes_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200916_102352_migrate_20200916_infomutasiobatalkes cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200916_102352_migrate_20200916_infomutasiobatalkes cannot be reverted.\n";

        return false;
    }
    */
}
