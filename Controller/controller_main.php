<?php


class Controller_Main extends Controller
{
    public function  __construct()
    {
        $this->model = new Model_Main();
        $this->view = new View();
    }

    public function action_index()
    {
        $data['list'] = $this->model->get_data();
        $data['article'] = $this->model->get_data();
        $this->view->generate('main_view.php', 'template_view.php', $data);

    }

    public function action_parse(){
        echo 'File Parsing';
        $this->view->generate('main_view.php', 'template_view.php');
    }

}