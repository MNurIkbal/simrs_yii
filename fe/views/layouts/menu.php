<?php
    $array_menu = [
      "module_id" => "89",
      "module_name" => "module antrian",
      "menu" => [
            [
              "menu_name" => "Master",
              "menu_id" => "12",
              'menu_icon' => "fa fa-book",
              "chield" => [
                [
                  "chield_name" => "Master A",
                  "chield_id" => "1",
                  "menu_icon" => "fa fa-book",
                  "chield" => [
                    [
                      "chield_name" => "Master A",
                      "chield_id" => "chield ID"
                    ]
                  ]
                ],
                [
                  "chield_name" => "Master B",
                  "chield_id" => "1",
                  "menu_icon" => "fa fa-book",
                  "chield" => [
                    [
                      "chield_name" => "Master A",
                      "chield_id" => "chield ID"
                    ]
                  ]
                ],
                [
                  "chield_name" => "Master C",
                  "chield_id" => "1",
                  "menu_icon" => "fa fa-book",
                  "chield" => [
                    [
                      "chield_name" => "Master A",
                      "chield_id" => "chield ID"
                    ]
                  ]
                ],
                [
                  "chield_name" => "Master D",
                  "chield_id" => "1",
                  "menu_icon" => "fa fa-book",
                  "chield" => [
                    [
                      "chield_name" => "Master A",
                      "chield_id" => "chield ID"
                    ]
                  ]
                ],
              ]
            ],
            [
              "menu_name" => "Transaksi",
              "menu_id" => "13",
              'menu_icon' => "fa fa-book",
              "chield" => [
                [
                  "chield_name" => "Master A",
                  "chield_id" => "chield ID",
                  "chield" => [
                    [
                      "chield_name" => "Master A",
                      "chield_id" => "chield ID"
                    ]
                  ]
                ],
                [
                  "chield_name" => "Master A",
                  "chield_id" => "chield ID"
                ],
                [
                  "chield_name" => "Master A",
                  "chield_id" => "chield ID"
                ],
                [
                  "chield_name" => "Master A",
                  "chield_id" => "chield ID"
                ]
              ]
            ]

          ]
    ]
?>


    <?php foreach ($array_menu['menu'] as $key => $value): ?>
        <li class="dropdown mega-menu mega-menu-wide">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="<?= $value['menu_icon'] ?> position-left" aria-hidden="true"></i> 
                <?= $value['menu_name'] ?> 
                <span class="caret"></span>
            </a>
            <div class="dropdown-menu dropdown-content">
                <div class="dropdown-content-body">
                    <div class="row">
                        <?php foreach ($value["chield"] as $k => $v): ?>
                            <div class="col-md-3">
                                <span class="menu-heading underlined"><?= $v["chield_name"] ?></span>
                                <ul class="menu-list">
                                    <li>
                                        <a href="#"><i class="icon-pencil3"></i> Form components</a>
                                        <ul>
                                            <li><a href="form_inputs_basic.html">Basic inputs</a></li>
                                            <li><a href="form_checkboxes_radios.html">Checkboxes &amp; radios</a></li>
                                            <li><a href="form_input_groups.html">Input groups</a></li>
                                            <li><a href="form_controls_extended.html">Extended controls</a></li>
                                            <li><a href="form_floating_labels.html">Floating labels</a></li>
                                            <li>
                                                <a href="#">Selects</a>
                                                <ul>
                                                    <li><a href="form_select2.html">Select2 selects</a></li>
                                                    <li><a href="form_multiselect.html">Bootstrap multiselect</a></li>
                                                    <li><a href="form_select_box_it.html">SelectBoxIt selects</a></li>
                                                    <li><a href="form_bootstrap_select.html">Bootstrap selects</a></li>
                                                </ul>
                                            </li>
                                            <li><a href="form_tag_inputs.html">Tag inputs</a></li>
                                            <li><a href="form_dual_listboxes.html">Dual Listboxes</a></li>
                                            <li><a href="form_editable.html">Editable forms</a></li>
                                            <li><a href="form_validation.html">Validation</a></li>
                                            <li><a href="form_inputs_grid.html">Inputs grid</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="#"><i class="icon-file-css"></i> JSON forms</a>
                                        <ul>
                                            <li><a href="alpaca_basic.html">Basic inputs</a></li>
                                            <li><a href="alpaca_advanced.html">Advanced inputs</a></li>
                                            <li><a href="alpaca_controls.html">Controls</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="#"><i class="icon-footprint"></i> Wizards</a>
                                        <ul>
                                            <li><a href="wizard_steps.html">Steps wizard</a></li>
                                            <li><a href="wizard_form.html">Form wizard</a></li>
                                            <li><a href="wizard_stepy.html">Stepy wizard</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="#"><i class="icon-spell-check"></i> Editors</a>
                                        <ul>
                                            <li><a href="editor_summernote.html">Summernote editor</a></li>
                                            <li><a href="editor_ckeditor.html">CKEditor</a></li>
                                            <li><a href="editor_wysihtml5.html">WYSIHTML5 editor</a></li>
                                            <li><a href="editor_code.html">Code editor</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="#"><i class="icon-select2"></i> Pickers</a>
                                        <ul>
                                            <li><a href="picker_date.html">Date &amp; time pickers</a></li>
                                            <li><a href="picker_color.html">Color pickers</a></li>
                                            <li><a href="picker_location.html">Location pickers</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="#"><i class="icon-insert-template"></i> Form layouts</a>
                                        <ul>
                                            <li><a href="form_layout_vertical.html">Vertical form</a></li>
                                            <li><a href="form_layout_horizontal.html">Horizontal form</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </li>

    <?php endforeach ?>
</li>