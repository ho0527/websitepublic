<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
        ?>
        <div class="nbar">
            <img src="media/icon/mainicon.gif" class="logo">
            <h1 class="title">咖啡商品展示系統</h1>
        </div>
        <div class="nbar2">
            <?php
                if($_SESSION["permission"]=="管理者"){
                    ?>
                    <div class="nbarbutton">
                        <input type="button" onclick="location.href='main.php'" value="標題">
                        <input type="button" onclick="location.href='product1.html'" value="上架商品">
                        <input type="button" onclick="location.href='admin.php'" value="會員管理">
                        <input type="button" onclick="location.href='api/api.php?logout='" value="登出">
                    </div>
                    <?php
                }else{
                    ?>
                    <div class="nbarbutton">
                        <input type="button" onclick="location.href='main.php'" value="標題">
                        <input type="button" onclick="location.href='api/api.php?logout='" value="登出">
                    </div>
                    <?php
                }
            ?>
        </div><br><br>
        <div class="centerdiv">
            <div class="mr20">
                <input type="text" id="word" placeholder="關鍵字">
                <input type="number" id="s" placeholder="最低價位">~
                <input type="number" id="b" placeholder="最高價位">
                <input type="button" id="search" value="查詢">
            </div>
            <div class="mr20">
                <input type="button" onclick="clearall()" value="0-40">
                <input type="button" onclick="clearall()" value="50-90">
                <input type="button" onclick="clearall()" value="1000-3000">
            </div>
            <div class="mr20">
                <input type="button" onclick="p1()" value="上一頁">
                <input type="button" onclick="p2()" value="下一頁">
                <input type="button" onclick="p1()" value="到最前一頁">
                <input type="button" onclick="p2()" value="到最後一頁">
            </div>
        </div>
        <div class="productmain" id="1">
            <div class="product left" id="p1">
                <table>
                    <tr>
                        <td class="producttd">商品名稱: coffee1</td>
                        <td class="producttd">相關連結: http://localhost/link/to/this/path</td>
                    </tr>
                    <tr>
                        <td class="producttd">商品簡介: <br>this coffee is good to drink~</td>
                        <td class="producttd" rowspan="3"><img src="media/picture/02.jpg" class="productimg"></td>
                    </tr>
                    <tr>
                        <td class="producttd">發佈日期: 2023/11/12</td>
                    </tr>
                    <tr>
                        <td class="producttd">費用: 100.00$</td>
                    </tr>
                </table>
            </div>
            <div class="product right" id="p2">
                <table>
                    <tr>
                        <td class="producttd">商品名稱: Cold Brew</td>
                        <td class="producttd">相關連結: https://cold.com/</td>
                    </tr>
                    <tr>
                        <td class="producttd">商品簡介: <br>Cold brew is made by steeping coffee grounds in cold water for an extended period, creating a rich and smooth cold coffee concentrate.</td>
                        <td class="producttd" rowspan="3"><img src="media/picture/04.jpg" class="productimg"></td>
                    </tr>
                    <tr>
                        <td class="producttd">發佈日期: 2023/11/12</td>
                    </tr>
                    <tr>
                        <td class="producttd">費用: 200.00$</td>
                    </tr>
                </table>
            </div>
            <div class="product left" id="p3">
                <table>
                    <tr>
                        <td class="producttd">商品名稱: Ristretto</td>
                        <td class="producttd">相關連結: https://maccccccccccccccccccccccccchiato.tw/index.tsx</td>
                    </tr>
                    <tr>
                        <td class="producttd">商品簡介: <br>Ristretto is even more concentrated than espresso, using less water to extract the coffee, resulting in a stronger and bolder flavor in a small serving.</td>
                        <td class="producttd" rowspan="3"><img src="media/picture/12.jpg" class="productimg"></td>
                    </tr>
                    <tr>
                        <td class="producttd">發佈日期: 2023/11/12</td>
                    </tr>
                    <tr>
                        <td class="producttd">費用: 40.50$</td>
                    </tr>
                </table>
            </div>
            <div class="product right" id="p4">
                <table>
                    <tr>
                        <td class="producttd">商品名稱: Latte</td>
                        <td class="producttd">相關連結: https://latte.com</td>
                    </tr>
                    <tr>
                        <td class="producttd">商品簡介: <br>A latte is a coffee made with espresso and a larger amount of steamed milk. It usually doesn't have frothy milk on top.</td>
                        <td class="producttd" rowspan="3"><img src="media/picture/10.jpg" class="productimg"></td>
                    </tr>
                    <tr>
                        <td class="producttd">發佈日期: 2023/11/12</td>
                    </tr>
                    <tr>
                            <td class="producttd">費用: 5000.05$</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="productmain" id="2">
            <div class="product left" id="p5">
                <table>
                    <tr>
                        <td class="producttd">商品名稱: chris</td>
                        <td class="producttd">相關連結: https://hiicmchris.ddns.net/</td>
                    </tr>
                    <tr>
                        <td class="producttd">商品簡介: <br>it is me!</td>
                        <td class="producttd" rowspan="3"><img src="media/picture/23.jpg" class="productimg"></td>
                    </tr>
                    <tr>
                        <td class="producttd">發佈日期: 2023/11/12</td>
                    </tr>
                    <tr>
                            <td class="producttd">費用: 1000000000.00$</td>
                    </tr>
                </table>
            </div>
        </div>
        <script src="main.js"></script>
    </body>
</html>