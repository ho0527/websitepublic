<?php
    date_default_timezone_set("Asia/Taipei");
    $nowdate=date("Y-m-d");

    function getfile($filepath,$keyword=""){
        if(file_exists($filepath)){
            $type="html";
            $filecontent=file_get_contents($filepath);
            $frontmattered=false;
            $frontmatter=[];
            $frontmattertemp=explode("---",$filecontent);
            if(1<=count($frontmattertemp)){
                $frontmatter=explode("\n",$frontmattertemp[1]);
                $frontmattered=true;
            }
            $filename=explode("/",$filepath);
            $filename=$filename[count($filename)-1];
            $data=[
                "date"=>preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}-$/",substr($filename,0,11))?substr($filename,0,10):false,
                "title"=>"",
                "tags"=>"",
                "draft"=>"",
                "summary"=>"",
                "cover"=>"",
                "detail"=>$frontmattered?explode("---",$filecontent)[2]:$filecontent
            ];
            $havetitle=false;
            $havecover=false;
            $keywordcheck=$keyword==""?true:false;

            for($i=0;$i<count($frontmatter);$i=$i+1){
                if($frontmatter[$i]!=""){
                    $keyandvalue=explode(": ",$frontmatter[$i]);
                    $key=$keyandvalue[0];
                    array_shift($keyandvalue);
                    $data[$key]=implode(": ",$keyandvalue);
                    if($key=="title"){
                        $havetitle=true;
                    }
                    if($key=="cover"){
                        $havecover=true;
                    }
                }
            }

            if(!$havetitle){
                $detail=$data["detail"];
                $detailtemp=explode("<h1>",$detail);
                if(2<=count($detailtemp)){
                    $detailtemp=explode("</h1>",$detailtemp[1])[0];
                    $data["title"]=$detailtemp;
                }else{
                    if($data["date"]!=false){
                        $link=explode("/",$filepath);
                        $link=$link[count($link)-1];
                        $temlink=explode(".",substr($link,10));
                        array_pop($temlink);
                        $link=implode(".",$temlink);

                        $data["title"]=ucwords(implode(" ",explode("-",$link)));
                    }
                }
            }

            if(!$havecover){
                if($data["date"]!=false){
                    $link=explode("/",$filepath);
                    $link=$link[count($link)-1];
                    $temlink=explode(".",substr($link,10));
                    array_pop($temlink);
                    $link=implode(".",$temlink);

                    $data["cover"]=ucwords($link.".jpg");
                }
            }

            $keywordlist=explode("/",$keyword);

            foreach($data as $key){
                for($i=0;$i<count($keywordlist);$i=$i+1){
                    if(strpos($key,$keywordlist[$i])){
                        $keywordcheck=true;
                        break;
                    }
                }

                if($keywordcheck==true){
                    break;
                }
            }

            if($keywordcheck){
                return $data;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
?>