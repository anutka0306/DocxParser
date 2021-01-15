<?php


class Model
{
    public $db;

    function __construct()
    {
        $this->db = new DB();$this->UID = $_SESSION['login'];

    }

    public function get_data(){
        return $this->db->getRows("SELECT * FROM `articles`");
    }

}