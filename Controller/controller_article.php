<?php


class Controller_Article extends Controller
{
    public function  __construct()
    {
        $this->model = new Model_Article();
        $this->view = new View();
    }

    public function action_index()
    {
        parent::action_index(); // TODO: Change the autogenerated stub
    }

    public function action_article($id){
        $data = array();
        $data['list'] = $this->model->get_data();
        $data['article'] = $this->model->get_article($id);
        $this->view->generate('article_view.php', 'template_view.php', $data);
    }
}