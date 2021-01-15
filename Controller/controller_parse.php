<?php


class Controller_Parse
{
    public $errors = array();
    public $messages = array();

public function  __construct()
    {
        $this->model = new Model_Parse();
        $this->view = new View();
        $this->host = 'http://'.$_SERVER['HTTP_HOST'].'/';
    }

public function action_index()
    {
        $doc = new Docx_reader();
        $destination_dir = $this->loadFile();

        if(empty($this->errors)) {
            $doc->setFile($destination_dir);

            if (!$doc->get_errors()) {
                $html = $doc->to_html($destination_dir);
                //$plain_text = $doc->to_plain_text();
                try {
                    $this->model->add_or_update($html);
                    header('Location: http://' . $_SERVER['HTTP_HOST']);
                } catch (PDOException $e) {
                    echo $e;
                }

            } else {
                $this->errors[] = implode(', ', $doc->get_errors());
            }
        }else{
            $_SESSION['errors'] = $this->errors;
            header('Location: http://' . $_SERVER['HTTP_HOST']);
        }

    }


    private function loadFile(){
        ini_set('upload_max_filesize', '1M');

        if ($_SERVER['REQUEST_METHOD'] == "POST" ) {

            $file_extension = $this->get_extension($_FILES['inputFile']['name']);

            if($file_extension != 'docx'){
                $this->errors[] = 'Неверный формат файла! Загрузите файл расширения .docx';
            }

            if ($_FILES['inputFile']['error'] == UPLOAD_ERR_OK && $_FILES['inputFile']['type'] == 'application/octet-stream' && empty($this->errors)) {
                $destination_dir =  './text_doc/'.$_FILES['inputFile']['name'];

                if(file_exists($destination_dir)){
                    unlink($destination_dir);
                }
                if(move_uploaded_file($_FILES['inputFile']['tmp_name'], $destination_dir )) {
                    return $destination_dir;
                }else{
                    $this->errors[]= 'Файл не был загружен';
                    return;
                }
            }else{
                switch ($_FILES['inputFile']['error']) {
                    case UPLOAD_ERR_FORM_SIZE:
                    case UPLOAD_ERR_INI_SIZE:
                        $this->errors[] = 'Превышен максимально допустимый размер файла';
                        brake;
                    case UPLOAD_ERR_NO_FILE:
                        $this->errors[] = 'Файл не выбран';
                        break;
                    default:
                        $this->errors[]= 'Загрузите верный формат файла';
                }
                return;
            }
        }



    }

    private function get_extension($filename) {
        $array = explode(".", $filename);
        return end($array);
  }
}