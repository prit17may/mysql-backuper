<?php

require_once root.'include/libs/medoo/medoo.php';

class controller extends medoo
{
    public $_db;
    public $_data = [];

    public function __construct($options)
    {
        $this->_db = new medoo($options);
    }

    public function view($Path, $data = null, $autoload_elements = false)
    {
        if (isset($Path) && $Path) {
            $Path = make_path($Path);
            if (file_exists(root.'views/'.$Path)) {
                if (isset($data) && is_array($data) && !empty($data)) {
                    extract($data);
                }
                if (!empty($this->_data)) {
                    extract($this->_data);
                }
                if ($autoload_elements === true) {
                    $this->view('elements/_header', $data);
                }
                include root.'views/'.$Path;
                if ($autoload_elements === true) {
                    $this->view('elements/_footer', $data);
                }
            } else {
                $this->show_404();
            }
        } else {
            $this->show_404();
        }
    }

    public function show_404($show_header_footer = false)
    {
        if ($show_header_footer === true) {
            $this->_data['title'] = '404 Not Found';
            $this->view('elements/_header');
        }
        $this->view('elements/404');
        if ($show_header_footer === true) {
            $this->view('elements/_footer');
        }
    }
}
