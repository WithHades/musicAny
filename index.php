<!DOCTYPE HTML> 
<html>
<head>
<meta charset="utf-8">
<title>在线小工具-音乐链接解析)</title>
<style>
.error {color: #FF0000;}
</style>
</head>
<body> 
<h2>目前仅支持网易云音乐、虾米音乐、酷我音乐</h2>
<form action="index.php" method="post"> 
   音乐链接: <input type="text" name="musicUrl" >
   <br><br>
   <input type="submit" name="submit" value="一键解析"> 
</form>
<br></br>
<?php
$musicUrl = $_POST["musicUrl"];   
if($musicUrl!="")
{
	if(strpos($musicUrl,"kuwo.cn")){
		if(preg_match('/\d{6,10}/', $musicUrl, $reg))
		{
			$url = "http://antiserver.kuwo.cn/anti.s?rid=MUSIC_".$reg[0]."&response=url&type=convert_url&format=mp3";
			$html = file_get_contents($url);
			if($html)echo "歌曲下载链接为：".$html;
		}
	}
	if(strpos($musicUrl,"163.com")){
		if(preg_match('/\d{8,12}/', $musicUrl, $reg))
		{
			$jsData = file_get_contents("music_163.js");
			$oScript = new COM("MSScriptControl.ScriptControl"); 
			$oScript->Language = "JavaScript"; 
			$oScript->AllowUI = false; 
			$oScript->AddCode("$jsData"); 
			$params = $oScript->Run("get", $reg[0]); 
			$tmp = explode('|',$params);
			$data = array(
				'params' => $tmp[0],
				'encSecKey' => $tmp[1]
			);
			$data = http_build_query($data);
			$opts = array(
				'http' => array(
					'method' => "POST",
					'header' => "Content-type: application/x-www-form-urlencoded",
					'content' => $data,
					'timeout' => 15 * 60
					)
			);
			$context = stream_context_create($opts);
			$html = file_get_contents("http://music.163.com/weapi/song/enhance/player/url?csrf_token=", false, $context);
			if($html){
				$str = json_decode($html,true);
				echo "歌曲下载链接为：".$str['data'][0]['url'];
			}
		}
	}
	if(strpos($musicUrl,"xiami.com")){
		if(preg_match('/\d{8,12}/', $musicUrl, $reg))
		{
			$url = "http://www.xiami.com/widget/xml-single/sid/".$reg[0];
			$html = file_get_contents($url);
			if($html){
				preg_match("/<\!\[CDATA\[([^\]].*)\]\]><\/location>/i",$html,$location);
				
				$count = (int)substr($location[1], 0, 1);
				$url = substr($location[1], 1);
				$line = floor(strlen($url) / $count);
				$loc_5 = strlen($url) % $count;
				$loc_6 = array();
				$loc_7 = 0;
				$loc_8 = '';
				$loc_9 = '';
				$loc_10 = '';
				while ($loc_7 < $loc_5){
					$loc_6[$loc_7] = substr($url, ($line+1)*$loc_7, $line+1);
					$loc_7++;
				}
				$loc_7 = $loc_5;
				while($loc_7 < $count){
					$loc_6[$loc_7] = substr($url, $line * ($loc_7 - $loc_5) + ($line + 1) * $loc_5, $line);
					$loc_7++;
				}
				$loc_7 = 0;
				while ($loc_7 < strlen($loc_6[0])){
					$loc_10 = 0;
					while ($loc_10 < count($loc_6)){
						$loc_8 .= @$loc_6[$loc_10][$loc_7];
						$loc_10++;
					}
					$loc_7++;
				}
				$loc_9 = str_replace('^', 0, urldecode($loc_8));
				echo "歌曲下载链接为：".$loc_9;				
			}
		}
	}
}
?> 
</body>
</html>