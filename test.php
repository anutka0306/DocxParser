
    <?php
include('docx_reader.php');

$doc = new Docx_reader();
$doc->setFile(__DIR__."/text_doc/1.Покраска сколов на автомобиле Land Rover.docx");

if(!$doc->get_errors()) {
    $html = $doc->to_html();
    $plain_text = $doc->to_plain_text();
//$html = $doc->clean_html($html);
//var_dump($html);
    echo $html;
} else {
    echo implode(', ',$doc->get_errors());
}
echo "\n";
?>


