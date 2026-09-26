<?php

namespace app\modules\v1\services\Contracts;

interface BpjsInterface {

    public function historyPelayanan($param);

    /**
     * @method pencarian pasien dari data bpjs by param nama,tgllahir dan jenis kelamin
     * @param array $peserta
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function cariPasienBpjsByNama($peserta);

    /**
     * @method pencarian pasien dari data bpjs by mr
     * @param array $peserta
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function cariPasienBpjsByRm($peserta);

    /**
     * @method pencarian pasien dari data bpjs by nokartu
     * @param array $peserta
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function cariPasienBpjsByNokartu($peserta);

    /**
     * @method pencarian pasien dari data bpjs by NIK
     * @param array $peserta
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function cariPasienBpjsByNik($peserta);

        /**
     * @method pencarian data rujukan by nokartu
     * @param string $noka
     * @param string $type
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function cariRujukanByNoKartu($noka, $type);

    /**
     * @method pencarian data rujukan by nokartu
     * @param string $noka
     * @param string $type
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function cariListRujukanByNoKartu($noka, $type);
}