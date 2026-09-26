<?php

namespace Doco\models\bpjs;

class ICare extends Bpjs {
    public function getUrlIcare($payload) {
        // $param = "wsihs/api/rs/validate";
        $url_icare = isset($this->url_icare) ? $this->url_icare : "https://apijkn-dev.bpjs-kesehatan.go.id/ihs_dev/api/rs/validate";
        $payload_json = is_array($payload) ? json_encode($payload) : json_encode([]);
        $data = self::curl($url_icare , $payload_json, self::getHeader(false), '');
        return self::out($data);
    }
}
