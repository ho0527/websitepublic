<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Video List Page</title>
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <div class="container">
            <h1 class="h2 py-5 text-center">Video List</h1>
            <div class="row">
                <?php
                    $data=json_decode(file_get_contents("../videos.json"),true);
                    for($i=0;$i<count($data);$i=$i+1){
                        ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card border-0 shadow-lg mb-4">
                                <div class="card-header py-3">
                                    <span class="h6 fw-bold"><?php echo($data[$i]["title"]); ?></span>
                                </div>
                                <video src="../previews/<?php echo($data[$i]["preview"]); ?>.mp4" class="video card-img rounded-0" muted="muted" autoplay loop></video>
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <i class="icon icon-clock me-2"></i>
                                        <span class="me-3"><?php echo($data[$i]["duration"]); ?></span>
                                        <i class="icon icon-eye me-2"></i>
                                        <span><?php echo(number_format($data[$i]["views"],0)); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                ?>
            </div>
        </div>
        <script src="index.js"></script>
    </body>
</html>