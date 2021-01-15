<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content='<?=htmlspecialchars_decode($data["article"]['meta_description']);?>'>
    <meta name="keywords" content='<?=htmlspecialchars_decode($data["article"]['meta_keywords']);?>'>
    <title><?=htmlspecialchars_decode($data["article"]["meta_title"]);?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

</head>
<body>

<div class="app">
    <div class="all-classes-container">
        <div class="row-color">
            <div class="col-12">
                <ul class="list-group list-group-horizontal">
                    <li class="list-group-item">
                        <a href="/">Home</a>
                    </li>
                    <?php
                    foreach ($data['list'] as $item):
                        ?>
                        <li class="list-group-item">
                            <a href="/article/article/<?=$item['id']?>"><?=htmlspecialchars_decode($item['title']);?></a>
                        </li>
                    <?php
                    endforeach;
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="message-block col-sm-12">
        <?php if($_SESSION['errors']):?>
            <?php foreach ($_SESSION['errors'] as $error):?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?=$error;?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endforeach;?>
            <?php unset($_SESSION['errors']);?>
        <?php elseif ($_SESSION['messages']):?>
            <?php foreach ($_SESSION['messages'] as $message):?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <?=$message;?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endforeach;?>
            <?php unset($_SESSION['messages']);?>
        <?php endif;?>
    </div>

    <?php include 'View/'.$content_view; ?>
</div>

<script>
    $('#validatedInputGroupCustomFile').on('change',function(){
        //get the file name
        let fileName = $(this).val();
        //replace the "Choose a file" label
        $(this).next('.custom-file-label').html(fileName);
    })
</script>

</body>
</html>
