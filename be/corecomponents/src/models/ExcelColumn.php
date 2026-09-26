<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\models;

class ExcelColumn {
    public $name;
    public $type;
    public $label;

    function __construct($name, $type, $label) {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;

        return [
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->type
        ];
    }
}