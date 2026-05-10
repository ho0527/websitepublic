<?php
    include("link.php");

    $key=$_GET["key"];
    
    if($key=="deactivatecompany"){
        $id=$_GET["id"]??0;
        $row=query($db,"SELECT*FROM `company` WHERE `id`=? AND `deactivatetime` IS NULL",[$id]);
        if($row){
            $row=$row[0];
            query($db,"UPDATE `company` SET `deactivatetime`=? WHERE `id`=?",[$time,$id]);
            query($db,"UPDATE `product` SET `hidetime`=? WHERE `companyid`=?",[$time,$id]);
            ?><script>alert("success:)");location.href="./company"</script><?php
        }else{
            ?><script>alert("failed:)");location.href="./company"</script><?php
        }
    }elseif($key=="hideproduct"){
        $id=$_GET["id"]??0;
        $row=query($db,"SELECT*FROM `product` WHERE `id`=? AND `hidetime` IS NULL",[$id]);
        if($row){
            $row=$row[0];
            query($db,"UPDATE `product` SET `hidetime`=? WHERE `id`=?",[$time,$id]);
            ?><script>alert("success:)");location.href="./products"</script><?php
        }else{
            ?><script>alert("failed:)");location.href="./products"</script><?php
        }
    }elseif($key=="deleteproduct"){
        $id=$_GET["id"]??0;
        $row=query($db,"SELECT*FROM `product` WHERE `id`=? AND `hidetime` IS NOT NULL",[$id]);
        if($row){
            $row=$row[0];
            query($db,"DELETE FROM `product` WHERE `id`=?",[$id]);
            ?><script>alert("success:)");location.href="./products"</script><?php
        }else{
            ?><script>alert("failed:)");location.href="./products"</script><?php
        }
    }elseif($key=="getproductlist"){
        $page=$_GET["page"]??0;
        $keyword=$_GET["keyword"]??"";
        $row=query($db,"SELECT*FROM `product` WHERE (`enname` LIKE ? OR `frname` LIKE ? OR `endescription` LIKE ? OR `frdescription` LIKE ?) AND `hidetime` IS NULL",["%".$keyword."%","%".$keyword."%","%".$keyword."%","%".$keyword."%"]);
        $data=[];
        $totalpage=ceil(count($row)/10);
        for($i=$page*10;$i<min(($page+1)*10,count($row));$i=$i+1){
            $companyrow=query($db,"SELECT*FROM `company` WHERE `id`=?",[$row[$i]["companyid"]])[0];
            $data[]=[
                "name"=>[
                    "en"=>$row[$i]["enname"],
                    "fr"=>$row[$i]["frname"],
                ],
                "description"=>[
                    "en"=>$row[$i]["endescription"],
                    "fr"=>$row[$i]["frdescription"],
                ],
                "gtin"=>$row[$i]["gtin"],
                "brand"=>$row[$i]["brandname"],
                "countryOfOrigin"=>$row[$i]["country"],
                "weight"=>[
                    "gross"=>$row[$i]["grossweight"],
                    "net"=>$row[$i]["contentweight"],
                    "unit"=>$row[$i]["weightunit"],
                ],
                "company"=>[
                    "companyName"=>$companyrow["name"],
                    "companyAddress"=>$companyrow["address"],
                    "companyTelephone"=>$companyrow["phone"],
                    "companyEmail"=>$companyrow["email"],
                    "owner"=>[
                        "name"=>$companyrow["ownername"],
                        "mobileNumber"=>$companyrow["ownerphone"],
                        "email"=>$companyrow["owneremail"],
                    ],
                    "contact"=>[
                        "name"=>$companyrow["contactname"],
                        "mobileNumber"=>$companyrow["contactphone"],
                        "email"=>$companyrow["contactemail"],
                    ]
                ]
            ];
        }
        echo(json_encode([
            "data"=>$data,
            "pagination"=>[
                "current_page"=>$page+1,
                "total_pages"=>$totalpage,
                "per_page"=>10,
                "next_page_url"=>($page!=0&&$page+1<$totalpage)?"http://web05.web.tw/20250614/05_module_d/products.json?keyword=".$keyword."&page="+$page+1:null,
                "prev_page_url"=>(0<$page-1)?"http://web05.web.tw/20250614/05_module_d/products.json?keyword=".$keyword."&page="+$page-1:null
            ]
        ]));
    }elseif($key=="getproduct"){
        $gtin=$_GET["gtin"];
        $row=query($db,"SELECT*FROM `product` WHERE `gtin`=? AND `hidetime` IS NULL",[$gtin]);
        if($row){
            $i=0;
            $companyrow=query($db,"SELECT*FROM `company` WHERE `id`=?",[$row[$i]["companyid"]])[0];
            echo(json_encode([
                "name"=>[
                    "en"=>$row[$i]["enname"],
                    "fr"=>$row[$i]["frname"],
                ],
                "description"=>[
                    "en"=>$row[$i]["endescription"],
                    "fr"=>$row[$i]["frdescription"],
                ],
                "gtin"=>$row[$i]["gtin"],
                "brand"=>$row[$i]["brandname"],
                "countryOfOrigin"=>$row[$i]["country"],
                "weight"=>[
                    "gross"=>$row[$i]["grossweight"],
                    "net"=>$row[$i]["contentweight"],
                    "unit"=>$row[$i]["weightunit"],
                ],
                "company"=>[
                    "companyName"=>$companyrow["name"],
                    "companyAddress"=>$companyrow["address"],
                    "companyTelephone"=>$companyrow["phone"],
                    "companyEmail"=>$companyrow["email"],
                    "owner"=>[
                        "name"=>$companyrow["ownername"],
                        "mobileNumber"=>$companyrow["ownerphone"],
                        "email"=>$companyrow["owneremail"],
                    ],
                    "contact"=>[
                        "name"=>$companyrow["contactname"],
                        "mobileNumber"=>$companyrow["contactphone"],
                        "email"=>$companyrow["contactemail"],
                    ]
                ]
            ]));
        }else{
            http_response_code(404);
            echo("404 NOT FOUND");
            echo("<input type='button' onclick=\"location.href='../products'\" value='back to products'>");
        }
    }
?>