<?php

require_once root . 'include/libs/medoo/medoo.php';

class Controller extends medoo {

    public $_db;
    public $_data = array();

    public function __construct($options) {
        $this->_db = new medoo($options);
    }

    function view($Path, $data = NULL, $autoload_elements = FALSE) {
        if (isset($Path) && $Path) {
            $Path = make_path($Path);
            if (file_exists(root . "views/" . $Path)) {
                if (isset($data) && is_array($data) && !empty($data)) {
                    extract($data);
                }
                if (!empty($this->_data)) {
                    extract($this->_data);
                }
                if ($autoload_elements === TRUE) {
                    $this->view('elements/_header', $data);
                }
                include root . 'views/' . $Path;
                if ($autoload_elements === TRUE) {
                    $this->view('elements/_footer', $data);
                }
            } else {
                $this->show_404(TRUE);
            }
        } else {
            $this->show_404();
        }
    }

    function show_404($show_header_footer = FALSE) {
        if ($show_header_footer === TRUE) {
            $this->_data['title'] = '404 Not Found';
            $this->view("elements/_header");
        }
        $this->view("elements/404");
        if ($show_header_footer === TRUE) {
            $this->view("elements/_footer");
        }
    }

}
