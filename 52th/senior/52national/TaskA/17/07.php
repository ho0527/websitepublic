<?php
    // 透過 07.html 瀏覽圖片時，瀏覽器會向伺服器發送 HTTP 請求，並帶上 HTTP 頭 "Referer"
    // 若 Referer 是 07.html，則允許瀏覽圖片
    // 若 Referer 不是 07.html，則禁止瀏覽圖片
    // 取得 HTTP 頭 "Referer" 的值
    @$referer = $_SERVER["HTTP_REFERER"];

    // 判斷 Referer 是否是 07.html
    if ($referer=="http://localhost/52th/senior/52national/TaskA/17/07.html"||$referer=="http://hiiamchris.hopto.org/52th/senior/52national/TaskA/17/07.html") {
        // 允許瀏覽圖片
        // 取得圖片的路徑
        $image_path= "images/".$_GET["image"];
        // 讀取圖片並輸出
        readfile($image_path);
    }else {
        // 禁止瀏覽圖片
        // 回傳 Status Code: 403
        http_response_code(403);
        echo("Status Code:403");
    }
?>