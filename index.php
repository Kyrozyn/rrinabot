<?php
/*task : add 2 server
 * Bug known :
 * Can't delete images
 */ 
require __DIR__ . '/vendor/autoload.php';
use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use \LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\ImageMessage;
use \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use \LINE\LINEBot\SignatureValidator as SignatureValidator;
use Medoo\Medoo;
use Cloudinary\Api;
use Cloudinary\Uploader;

include 'settings/setChannel.php';
$cl = new Cloudinary;
//$randcloud = random_int(0, 1);
//if ($randcloud == 0){
Cloudinary::config(array( 
  "cloud_name" => "ririnabots", 
  "api_key" => "742917597548863", 
  "api_secret" => "bwsyPfpLuFNqCwlrask1BB0brNs",
  "resource_type" => "raw"  
));//}
/*else{
    Cloudinary::config(array( 
  "cloud_name" => "dkqny9ym1", 
  "api_key" => "633148617174128", 
  "api_secret" => "2EZeqMiSOMPV94E_d6__85OJnVs",
  "resource_type" => "raw"  
));}*/


$database = new Medoo([
	// required
	'database_type' => 'pgsql',
	'database_name' => 'dg5btd4vm4f4b',
	'server' => 'ec2-54-221-207-192.compute-1.amazonaws.com',
	'username' => 'ezpqrjxealchtz',
	'password' => 'bdbacaf6a770548e4e6ea11f4b37cc2860a06209902626fd8071cd28a9d76a9a',
    ]);

////////////////////////////////////////////////////////////////////////////////////
$httpClient = new CurlHTTPClient($channel_access_token);
$bot = new LINEBot($httpClient, ['channelSecret' => $channel_secret]);
$configs =  [
    'settings' => ['displayErrorDetails' => true],
];
$app = new Slim\App($configs);

$app->get('/', function($req, $res)
{
    header('Location: http://kyrozyn.xyz');exit();
});
///////////////////////////////////////////////////////////////////////////////////
// buat route untuk webhook
$app->post('/bot', function ($request, $response) use ($bot,$database,$randcloud)
{
    include 'function.php';
    // get request body and line signature header
    $body        = file_get_contents('php://input');
    file_put_contents('php://stderr', 'Body: '.$body);
    //Pengaturan Ajah
$data = json_decode($body, true);
if(is_array($data['events'])){
    foreach ($data['events'] as $event){
    /*Init var*/
        //message text
        $userMessage = strtolower($event['message']['text']);
        $userMessageORI = $event['message']['text'];
        $userMessageArrA = explode('*', $userMessage);
        $userMessageArrB = explode('*', $userMessageORI);
        $Arr_calc = explode(' ',$userMessage);
        $userId = $event['source']['userId'];
        $gp = $bot->getProfile($userId);
        $getprofile = $gp->getJSONDecodedBody();
        $ProfileNama = $getprofile['displayName'];
        //
        $groupId = $event['source']['groupId'];
        //
        $eventType = $event['type'];
        $messageType = $event['message']['type'];
        $sourceType = $event['source']['type'];
        $eventType = $event['type'];
        $replyToken = $event['replyToken'];
        //
        $adminGroup = $database->get("admin_group", "userid",["groupid[=]" => $groupId]);
        $hanyaadmin = $database->get("admin_group", "hanyaadmin",["groupid[=]" => $groupId]);
        $groupIdDb = $database->get("admin_group", "groupid",["userid[=]" => $userId]);
        $diam = $database->get("admin_group", "diam",["groupid[=]" => $groupId]);
        $hasadmin = $database->get("su_admin","userid",["userid[=]" => $userId]);
        //
        $picId = $event['message']['id'];
        //
        if ($eventType == 'message' && $messageType == 'text'){
            //Jika Admin
            if($hasadmin == $userId){
                   if($userMessageArrB[0] == '!global'){
                       $insertAdmin = $database->insert("command_text", ["jika" => $userMessageArrA[1],"maka" => $userMessageArrB[2],"userid" => $userId, "groupid" => "0"]);
                       $result = $bot->replyText($replyToken,"Ok! Keyword berhasil ditambahkan scr global (itu karena kamu super adminku <3)");
                   }
                   if($userMessageArrB[0]== '!delglobal'){
                       $balas = $database->get("command_text","jika",["jika[=]"=> $userMessageArrA[1], "groupid[=]"=>"0"]);
                    if($balas == $userMessageArrA[1]){
                        $insertAdmin = $database->delete("command_text", ["jika" => $userMessageArrA[1] ]);
                        $result = $bot->replyText($replyToken,"Ok! Keyword global berhasil dihapus <3");
                    }
                    else{
                        $result = $bot->replyText($replyToken,"Keyword tidak ditemukan! >,<");
                    }
                   }
                   if($userMessageArrB[0] == '!leaveall'){
                      $succ = 0;
                      $fail = 0;
                      $datas_groupId = $database->select("sessions","groupid");
                      foreach ($datas_groupId as $data_groupId){ 
                        $result = $bot->leaveGroup($data_groupId);
                        $database->delete("sessions", ["groupid" => $data_groupId]);
                        if ($result){
                            $succ = $succ +1;
                        }
                        else{
                            $fail = $fail +1;
                        }
                      }
                    $result = $bot->replyText($replyToken,"OK! Leaving from all group... \nSucc : ".$succ."\nFail : ".$fail);  
                   }
                   if($userMessageArrB[0] == '!delallkeywords'){
                       $database->delete("command_text",["groupid[=]" => $groupId]);
                       $result = $bot->replyText($replyToken,"Ok! Semua keyword di grup ini berhasil dihapus");
                   }
                   if($userMessageArrB[0] == '!stats'){
                       $SessionsCount = $database->count("sessions");
                       $GroupCount = $database->count("admin_group");
                       $CommandCount = $database->count("command_text");
                       $CommandCountImg = $database->count("command_text_img");
                       $Jadi = new TextMessageBuilder("Hi~\nIn Group : ".$SessionsCount."\nAdded me to group : ".$GroupCount."\nKeyword Available in all Group : ".$CommandCount."\nKeyword Pic Available in all Group : ".$CommandCountImg);
                       $result = $bot->replyMessage($replyToken, $Jadi);
                   }
                  if($userMessageArrB[0]== '!bc'){
                      $database->update("command_text", ["maka" => $userMessageArrB[1]],["jika[=]"=> 'bc',"groupid[=]" => 'bc']);$database->update("sessions",["bc" => false]);
                      $bot->replyText($replyToken, "Ok! Pesan bc kamu telah diedit");
                  }
                  if($userMessageArrB[0]== '!pushbc'){
                    $url = 'https://ririnabot.herokuapp.com/sessionsupdate.php';
                    $atas = new ButtonTemplateBuilder(NULL, "Push BC", NULL, [
                                new UriTemplateActionBuilder('Klik disini!', $url),    
                                ]);
                    $bawah = new TemplateMessageBuilder('Admin.Mode',$atas);
                    $result = $bot->replyMessage($replyToken, $bawah);
                  }
                  if($userMessageArrB[0]== '!regadm'){
                      $database->insert("su_admin",["userid" => $userMessageArrB[1]]);
                      $bot->replyText($replyToken, "Ok!");
                  }
                  if($userMessageArrB[0]== '!takeadmin'){
                      $insertAdmin = $database->update("admin_group", ["userid" => $userId], ["groupid[=]" => $groupId]);
                      $satu = new TextMessageBuilder($replyToken,"!!Admin grup telah diambil oleh SuperAdmin!!");
                      $gp = $bot->getProfile($userId);
                      $getprofile = $gp->getJSONDecodedBody();
                      $ProfileNama = $getprofile['displayName'];
                      $dua = new TextMessageBuilder($replyToken,"Admin group disini adalah : ".$ProfileNama);
                      $kirim = new MultiMessageBuilder();
                      $kirim->add($satu);
                      $kirim->add($dua);
                      $bot->replyMessage($replyToken, $kirim);
                  }
            }
            //Grup
            if($sourceType == 'group'){
                if($userMessage == '!aku!' AND $adminGroup == "0"){
                    if(!$userId == ''){
                        $insertAdmin = $database->update("admin_group", ["userid" => $userId], ["groupid[=]" => $groupId]);
                        $result = $bot->replyText($replyToken,"Ok! Sekarang ". $ProfileNama." jadi admin disini!");}
                    else{
                        $result = $bot->replyText($replyToken,"Oh! Sepertinya kamu belum add aku atau line kamu belum update (he he)");
                    }
                    /*$foo = $database->get("admin_group", "userid",["groupid[=]" => $groupId]);
                    $kirim = new TextMessageBuilder("Logs sql =". print_r($database->error(),true)."\nCurrent group admin = ".$foo);
                    $result = $bot->replyMessage($replyToken, $kirim);*/
                }
                if($userMessage=='!ga!' AND $adminGroup=='0'){
                    $result = $bot->replyText($replyToken,"Kalau gamau ngapain diklik! >//<");
                }
                if(!$adminGroup=="0"){
                if($userMessageArrB[0]=='!add' AND ($hanyaadmin == false OR $userId == $adminGroup)){
                    $kata = $database->get("command_text","jika",["jika[=]" => $userMessageArrB[1], "groupid[=]"=>$groupId]);
                    $katapic = $database->get("command_text_img","jika",["jika[=]" => $userMessageArrB[1], "groupid[=]"=>$groupId]);
                    if(!$kata == $userMessageArrB[1] AND !$katapic == $userMessageArrB[1]){
                        $insertAdmin = $database->insert("command_text", ["jika" => $userMessageArrA[1],"maka" => $userMessageArrB[2],"userid" => $userId, "groupid" => $groupId]);
                        $result = $bot->replyText($replyToken,"Ok! Keyword berhasil ditambahkan");}
                    else{
                        $result = $bot->replyText($replyToken,"Keyword sudah ada :( Keyword gagal ditambahkan");
                    }

                   // $kirim = new TextMessageBuilder("Logs sql =". print_r($database->error(),true)."\nCurrent group admin = ".$foo);
                    //$result = $bot->replyMessage($replyToken, $kirim);
                }
                if($userMessageArrB[0]=='!addpic' AND ($hanyaadmin == false OR $userId == $adminGroup)){
                    $kata = $database->get("command_text","jika",["jika[=]" => $userMessageArrB[1], "groupid[=]"=>$groupId]);
                    $katapic = $database->get("command_text_img","jika",["jika[=]" => $userMessageArrB[1], "groupid[=]"=>$groupId]);
                    if(!$kata == $userMessageArrB[1] AND !$katapic == $userMessageArrB[1]){
                    $insertAdmin = $database->insert("command_text_img", ["jika" => $userMessageArrA[1],"maka" => "0","userid" => $userId, "groupid" => $groupId,]);
                    $result = $bot->replyText($replyToken,"Ok! Sekarang kirimkan gambarmu!");}
                    else{
                        $result = $bot->replyText($replyToken,"Keyword sudah ada :( Keyword gagal ditambahkan");
                    }
                   // $kirim = new TextMessageBuilder("Logs sql =". print_r($database->error(),true)."\nCurrent group admin = ".$foo);
                    //$result = $bot->replyMessage($replyToken, $kirim);
                }
                if($userMessageArrB[0]=='!del' AND ($hanyaadmin == false OR $userId == $adminGroup)){
                    $balas = $database->get("command_text","jika",["jika[=]"=> $userMessageArrB[1], "groupid[=]"=>$groupId]);     
                    if($balas == $userMessageArrB[1]){
                        $insertAdmin = $database->delete("command_text", ["jika" => $userMessageArrB[1], "groupid[=]"=>$groupId ]);
                        $result = $bot->replyText($replyToken,"Ok! Keyword berhasil dihapus");
                    }
                    else{
                        $result = $bot->replyText($replyToken,"Keyword tidak ditemukan! >,<");
                    }
                    //$insertAdmin = $database->delete("command_text", ["jika[=]" => $userMessageArrB[1] ]);
                    //$result = $bot->replyText($replyToken,"Ok! Keyword berhasil ditambahkan");
                    //$kirim = new TextMessageBuilder("Logs sql =". print_r($database->error(),true)."\nCurrent group admin = ".$foo);
                    //$result = $bot->replyMessage($replyToken, $kirim);
                }
                if($userMessageArrB[0]=='!delpic' AND ($hanyaadmin == false OR $userId == $adminGroup)){
                    $balas = $database->get("command_text_img","jika",["jika[=]"=> $userMessageArrA[1], "groupid[=]"=>$groupId]);
                    if($balas == $userMessageArrB[1]){
                        $urll = $database->get("command_text_img","maka",["jika[=]"=> $userMessageArrA[1], "groupid[=]"=>$groupId]);
                        $insertAdmin = $database->delete("command_text_img", ["jika" => $userMessageArrA[1], "groupid[=]"=>$groupId ]);
                       // $boo = explode('/', $urll);
                        //$booo = explode('.',$boo[7]);
                        $result = $bot->replyText($replyToken,"Ok! Keyword berhasil dihapus");
                        //Uploader::destroy($booo[0]);
                        
                    }
                    else{
                        $result = $bot->replyText($replyToken,"Keyword tidak ditemukan! >,<");
                    }
                    //$insertAdmin = $database->delete("command_text", ["jika[=]" => $userMessageArrB[1] ]);
                    //$result = $bot->replyText($replyToken,"Ok! Keyword berhasil ditambahkan");
                    //$kirim = new TextMessageBuilder("Logs sql =". print_r($database->error(),true)."\nCurrent group admin = ".$foo);
                    //$result = $bot->replyMessage($replyToken, $kirim);
                }
                if($userMessage=='!cekmode' AND $userId == $adminGroup){
                    if($hanyaadmin){
                    $result = $bot->replyText($replyToken,"Mode hanya admin = Ya");}
                    else{
                    $result = $bot->replyText($replyToken,"Mode hanya admin = Tidak");}
                }
                if($userMessage=='!hanyaadmin' AND $userId == $adminGroup){
                    if($hanyaadmin){
                        $database->update("admin_group", ["hanyaadmin" => false], ["groupid[=]" => $groupId]);
                        $result = $bot->replyText($replyToken,"Ok! Sekarang semua orang bisa add keywords :3");}
                    else{
                        $database->update("admin_group", ["hanyaadmin" => true], ["groupid[=]" => $groupId]);
                        $result = $bot->replyText($replyToken,"Ok! Hanya admin yg bisa add keywords");}
                }
                if($userMessage=='!diam' AND ($userId == $adminGroup OR $hanyaadmin == false)){
                    if($diam){
                        $database->update("admin_group", ["diam" => false], ["groupid[=]" => $groupId]);
                        $result = $bot->replyText($replyToken,"Terimakasih ^_^");}
                    else{
                        $database->update("admin_group", ["diam" => true], ["groupid[=]" => $groupId]);
                        $result = $bot->replyText($replyToken,"Ok! Aku diam! :(");}
                }
                if($userMessage=='!leave'){
                    $result = $bot->replyText($replyToken,"Kenapa? :( Oke have a nice day!! ^_^ ");
                    $result = $bot->leaveGroup($groupId);
                    $database->delete("sessions", ["groupid" => $groupId]);
                }
                if($userMessage=='!count'){
                    $ha = $database->count("command_text",["groupid[=]" => $groupId]);
                    $result = $bot->replyText($replyToken,$ha);
                }
                if($userMessage=='!help'){
                    $url = 'http://line.me/R/home/public/post?id=cfb3506s&postId=1150449533705032020';
                    $atas = new ButtonTemplateBuilder(NULL, "Help", NULL, [
                                new UriTemplateActionBuilder('Klik disini!', $url),    
                                ]);
                    $bawah = new TemplateMessageBuilder('Help',$atas);
                    $result = $bot->replyMessage($replyToken, $bawah);
                }
                if($userMessage=='!whoadmin'){
                    $gp = $bot->getProfile($adminGroup);
                    $getprofile = $gp->getJSONDecodedBody();
                    $ProfileNama = $getprofile['displayName'];
                    $kirim = $bot->replyText($replyToken,"Admin group disini adalah : ".$ProfileNama);
                }
                if($userMessage=='!gantiadmin' AND $userId==$adminGroup){
                    $satu = new TextMessageBuilder("Ok! Jadi, siapa yang mau jadi admin sekarang?");
                    $database->update("admin_group", ["userid" => "0"], ["groupid[=]" => $groupId]);
                    $atas = new ConfirmTemplateBuilder("Siapa???",[
                           new MessageTemplateActionBuilder('Aku!','!aku!'),
                           new MessageTemplateActionBuilder('Ga!','!ga!'),
                        ]);
                    $dua = new TemplateMessageBuilder('PilihAdmin',$atas);
                    $kirim = new MultiMessageBuilder();
                    $kirim->add($satu);
                    $kirim->add($dua);
                    $result = $bot->replyMessage($replyToken, $kirim);
                }
                if($userMessageArrB[0]=='!nim' AND $diam == false){
                    $link = 'https://kyrozyn:anyand32@myanimelist.net/api/anime/search.xml?q='.$userMessageArrB[1];
                    $no = 0;
                    $no = $userMessageArrB[2]+$no;
                    if ($no ==0){
                        $max_care = 3;
                    }
                    else{
                    $max_care = ($no*4)-1;}
                    $min_care = $max_care-3;
                    $xml = simplexml_load_file($link);    
                    $json_string = json_encode($xml);    
                    $result_array = json_decode($json_string, TRUE);
                    $a = $result_array;
                    if(!empty($a['entry'][$no]['title'])){
                    if(is_array($a['entry'][$no]['synonyms'])){
                    $synonyms ='--';
                    }
                    else{
                    $synonyms = $a['entry'][$no]['synonyms'];
                    }
                    $score0 = $a['entry'][$min_care]['score'];
                    $entry0 = limit_words($a['entry'][$min_care]['title'], 6)."...\n★".$score0;
                    $image0 = $a['entry'][$min_care]['image'];
                    $url0 = 'https://myanimelist.net/anime/'.$a['entry'][$min_care]['id'];
                    if(!isset($a['entry'][$min_care+1])){
                        $entry1 = '==';
                        $image1 = 'https://myanimelist.cdn-dena.com/img/sp/icon/apple-touch-icon-256.png';
                        $url1 = 'https://myanimelist.net/a.jpg';  
                    }
                    else{
                        $score1 = $a['entry'][$min_care+1]['score'];
                        $entry1 = limit_words($a['entry'][$min_care+1]['title'],6)."...\n★".$score1;
                        $image1 = $a['entry'][$min_care+1]['image'];
                        $url1 = 'https://myanimelist.net/anime/'.$a['entry'][$min_care+1]['id'];
                    }
                    if(!isset($a['entry'][$min_care+2])){
                        $entry2 = '==';
                        $image2 = 'https://myanimelist.cdn-dena.com/img/sp/icon/apple-touch-icon-256.png';
                        $url2 = 'https://myanimelist.net/a.jpg';  
                    }
                    else{
                        $score2 = $a['entry'][$min_care+2]['score'];
                        $entry2 = limit_words($a['entry'][$min_care+2]['title'],6)."...\n★".$score2;
                        $image2 = $a['entry'][$min_care+2]['image'];
                        $url2 = 'https://myanimelist.net/anime/'.$a['entry'][$min_care+2]['id'];
                    }
                    if(!isset($a['entry'][$max_care])){
                        $entry3 = '==';
                        $image3 = 'https://myanimelist.cdn-dena.com/img/sp/icon/apple-touch-icon-256.png';
                        $url3 = 'https://myanimelist.net/a.jpg';  
                    }
                    else{
                        $score3 = $a['entry'][$max_care]['score'];
                        $entry3 = limit_words($a['entry'][$max_care]['title'],6)."...\n★".$score3;
                        $image3 = $a['entry'][$max_care]['image'];
                        $url3 = 'https://myanimelist.net/anime/'.$a['entry'][$max_care]['id'];
                    }
                    if($no == 0){
                    $noo=$no+2;
                    $next = "!nim*".$userMessageArrB[1]."*".$noo;
                    $nextt = $next;
                    }
                    else{
                        $noo=$no+1;
                        $next = "!nim*".$userMessageArrB[1]."*".$noo;
                        $nextt = $next;
                    }
                    $atas = new CarouselTemplateBuilder(
                            [
                                    new CarouselColumnTemplateBuilder(NULL,$entry0 ,$image0, [
                                       new UriTemplateActionBuilder("More info..", $url0),  
                                    ]),
                                     new CarouselColumnTemplateBuilder(NULL,$entry1 ,$image1, [
                                       new UriTemplateActionBuilder("More info..", $url1),  
                                    ]),
                                    new CarouselColumnTemplateBuilder(NULL,$entry2 ,$image2, [
                                       new UriTemplateActionBuilder("More info..", $url2),  
                                    ]),
                                   new CarouselColumnTemplateBuilder(NULL,$entry3 ,$image3, [
                                       new UriTemplateActionBuilder("More info..", $url3),  
                                    ]),
                                    new CarouselColumnTemplateBuilder(NULL,"Next Result" ,'https://myanimelist.cdn-dena.com/img/sp/icon/apple-touch-icon-256.png', [
                                       new MessageTemplateActionBuilder("Next",$nextt),  
                                    ]),
                                ]
                                );
                    $bawah = new TemplateMessageBuilder('Anime ',$atas);
                    $bot->replyMessage($replyToken, $bawah);
                    /*$satu = new TextMessageBuilder("== ".$a['entry'][$no]['title']." ==\n"
                            . "Synonyms : ".$synonyms."\n"
                            . "Episodes : ".$a['entry'][$no]['episodes']."\n"
                            . "Score : ".$a['entry'][$no]['score']."\n"
                            . "Type : ".$a['entry'][$no]['type']."\n"
                            . "Status : ".$a['entry'][$no]['status']."\n"
                            . "Start Date : ".$a['entry'][$no]['start_date']."\n"
                            . "End Date : ".$a['entry'][$no]['end_date']."\n"
                            . "Synopsis : ". html_entity_decode(str_replace("<br />"," ",str_replace("&#039;","'",str_replace("[i]"," ",str_replace("[/i]"," ",$a['entry'][$no]['synopsis'])))))."\n");    
                    $dua = new ImageMessageBuilder($a['entry'][$no]['image'],$a['entry'][$no]['image']);*/    
                    }
                    else{
                    
                    $Juduls = limit_words($a['entry']['title'],6);
                    $Rating = $a['entry']['score'];
                    $Kirim = $Juduls."... \n★".$Rating;
                    $Website = 'https://myanimelist.net/anime/'.$a['entry']['id'];
                    $atas = 
                                    new ButtonTemplateBuilder(NULL, $Kirim ,$a['entry']['image'], [
                                       new UriTemplateActionBuilder("More info..", $Website),  
                                    ])
                                ;
                    $bawah = new TemplateMessageBuilder('Anime',$atas);
                    $result = $bot->replyMessage($replyToken, $bawah);
                    
                    /*$satu = new TextMessageBuilder("== ".$a['entry']['title']." ==\n"
                            . "Synonyms : ".$synonyms."\n"
                            . "Episodes : ".$a['entry']['episodes']."\n"
                            . "Score : ".$a['entry']['score']."\n"
                            . "Type : ".$a['entry']['type']."\n"
                            . "Status : ".$a['entry']['status']."\n"
                            . "Start Date : ".$a['entry']['start_date']."\n"
                            . "End Date : ".$a['entry']['end_date']."\n"
                            . "Synopsis : ". html_entity_decode(str_replace("<br />"," ",str_replace("&#039;","'",str_replace("[i]"," ",str_replace("[/i]"," ",$a['entry']['synopsis'])))))."\n");
                    $dua = new ImageMessageBuilder($a['entry']['image'],$a['entry']['image']);*/
                    }
                    /*$aaa = new MultiMessageBuilder();
                    $aaa->add($dua);
                    $aaa->add($satu);
                    $result = $bot->replyMessage($replyToken, $aaa);*/
                }

                if($userMessage == '!list'){
                    $url = 'http://adf.ly/18224627/banner/ririnabot.herokuapp.com/list_keyword.php?groupid='.base64_encode($groupId);
                    $atas = new ButtonTemplateBuilder(NULL, "List Keyword", NULL, [
                                new UriTemplateActionBuilder('Klik disini!', $url),    
                                ]);
                    $bawah = new TemplateMessageBuilder('List Keyword',$atas);
                    $result = $bot->replyMessage($replyToken, $bawah);
                }
                if($Arr_calc[0] == '!ig'){
                    $buat_id = base64_encode(base64_encode($Arr_calc[1]));
                    //////
                    $url = 'https://www.instagram.com/'.$Arr_calc[1].'/media/';
                    $content = file_get_contents($url);
                    $hasil = json_decode($content);
                    $nani = array_rand($hasil->items,5);
                    //blah nani :")
                    $buat_no1 = $nani[0];
                    $buat_no2 = $nani[1];
                    $buat_no3 = $nani[2];
                    $buat_no4 = $nani[3];
                    $buat_no5 = $nani[4];
                    ///
                    $like1 = $hasil->items[$nani[0]]->likes->count;
                    $cap1 = "❤".$like1;
                    $url1 = $hasil->items[$nani[0]]->images->standard_resolution->url;
                    ///
                    $like2 = $hasil->items[$nani[1]]->likes->count;
                    $cap2 = "❤".$like2;
                    $url2 = $hasil->items[$nani[1]]->images->standard_resolution->url;
                    ///
                    $like3 = $hasil->items[$nani[2]]->likes->count;
                    $cap3 = "❤".$like3;
                    $url3 = $hasil->items[$nani[2]]->images->standard_resolution->url;
                    //
                    $like4 = $hasil->items[$nani[3]]->likes->count;
                    $cap4 = "❤".$like4;  
                    $url4 = $hasil->items[$nani[3]]->images->standard_resolution->url;
                    //
                    $like5 = $hasil->items[$nani[4]]->likes->count;
                    $cap5 = "❤".$like5;  
                    $url5 = $hasil->items[$nani[4]]->images->standard_resolution->url;
                    if(!empty($nani)){
                    $atas = new CarouselTemplateBuilder(
                            [
                                    new CarouselColumnTemplateBuilder(NULL, $cap1,$url1, [
                                       new UriTemplateActionBuilder('More..', 'https://ririnabot.herokuapp.com/ig.php?id='.$buat_id.'&no='.$buat_no1),  
                                    ]),
                                    new CarouselColumnTemplateBuilder(NULL, $cap2,$url2 , [
                                       new UriTemplateActionBuilder('More..', 'https://ririnabot.herokuapp.com/ig.php?id='.$buat_id.'&no='.$buat_no2),
                                    ]),
                                    new CarouselColumnTemplateBuilder(NULL, $cap3,$url3 , [
                                       new UriTemplateActionBuilder('More..', 'https://ririnabot.herokuapp.com/ig.php?id='.$buat_id.'&no='.$buat_no3),
                                    ]),
                                    new CarouselColumnTemplateBuilder(NULL, $cap4,$url4 , [
                                       new UriTemplateActionBuilder('More..', 'https://ririnabot.herokuapp.com/ig.php?id='.$buat_id.'&no='.$buat_no4),
                                    ]),
                                    new CarouselColumnTemplateBuilder(NULL, $cap5,$url5 , [
                                       new UriTemplateActionBuilder('More..', 'https://ririnabot.herokuapp.com/ig.php?id='.$buat_id.'&no='.$buat_no5),
                                    ]),
                                ]
                                );
                    $bawah = new TemplateMessageBuilder('Ig '.$Arr_calc[1],$atas);}
                    else{
                        $bawah = new TextMessageBuilder("Username Not found / private account");
                    }
                        $result = $bot->replyMessage($replyToken, $bawah);
                }
                if(!$diam){
                    $balas = $database->get("command_text","maka",["jika[=]"=> $userMessage, "groupid[=]"=>$groupId]);
                    if($balas){
                        $result = $bot->replyText($replyToken,$balas);}
                    else{
                        $balas = $database->get("command_text","maka",["jika[=]"=> $userMessage, "groupid[=]"=> "0"]);
                        if($balas){
                            $result = $bot->replyText($replyToken,$balas);
                        }
                        else{
                            $bc = $database->get("sessions","bc",["groupid[=]" => $groupId]);
                            if($bc == false){
                                $balas = $database->get("command_text","maka",["jika[=]"=> 'bc', "groupid[=]"=> 'bc']);
                                $database->update("sessions",["bc" => true],["groupid[=]" => $groupId]);
                                $result = $bot->replyText($replyToken,$balas);
                                //$bot->replyText($replyToken, print_r($bc));
                            }
                            else {
                                $balas = $database->get("command_text_img","maka",["jika[=]"=> $userMessage, "groupid[=]"=>$groupId]);
                                if ($balas){
                                    $img = new ImageMessageBuilder($balas,$balas);
                                    $bot->replyMessage($replyToken, $img);
                                }
                            }
                        }
                    }    
                }
                }
            }
            else if ($sourceType == 'user') {
                    if($userMessage == '!userid'){
                        $bot->replyText($replyToken, $userId);
                    }
            }
        }
        /*if ($eventType == 'message' && $messageType == 'image'){
            $messageId = $event['message']['id'];
            $result = $bot->replyText($replyToken,"Anda ngirim gambar, id = ".$messageId);
        }*/
        else if ($eventType == 'join'){
            $database->insert("sessions", ["groupid" => $groupId]);
            $satu = new TextMessageBuilder("Hai! Aku Ririna siap melayani grup ini ^_^");
            if($adminGroup=='0' OR $adminGroup==''){
                if($adminGroup==''){
                $database->insert("admin_group", ["groupid" => $groupId]);}
                $dua = new TextMessageBuilder("Sebelum memulai semua, silahkan tentukan siapa yang menjadi admin di grup disini ^_^");
                $kirim = new MultiMessageBuilder();
                $atas = new ConfirmTemplateBuilder('Siapa???',[
                           new MessageTemplateActionBuilder('Aku!','!aku!'),
                           new MessageTemplateActionBuilder('Ga!','!ga!'),
                        ]);
                $aku = new TemplateMessageBuilder('PilihAdmin',$atas);
                $kirim->add($satu);
                $kirim->add($dua);
                $kirim->add($aku);
            }
            else{
                $kirim = new MultiMessageBuilder();
                $gp = $bot->getProfile($adminGroup);
                $getprofile = $gp->getJSONDecodedBody();
                $ProfileNama = $getprofile['displayName'];
                $dua = new TextMessageBuilder("Admin group disini adalah : ".$ProfileNama);
                $kirim->add($satu);
                $kirim->add($dua);
            }
            //$kirim = new TextMessageBuilder("Logs sql =". print_r($database->error(),true));            
            $result = $bot->replyMessage($replyToken, $kirim);
        }
         else if ($eventType == 'leave'){
            $database->delete("sessions", ["groupid" => $groupId]);
         }
         else if ($eventType == 'message' AND $messageType == 'location'){
            $latitude = $event['message']['latitude'];
            $longitude = $event['message']['longitude'];
            $url = 'https://api.openweathermap.org/data/2.5/weather?lat='.$latitude.'&lon='.$longitude.'&APPID=8a143d1771f6ec5cbf7cf007ef2ee65e&units=metric';
            $content = file_get_contents($url);
            $hasil = json_decode($content,true);
            $greetings = new TextMessageBuilder("Weather : ".$hasil['weather'][0]['main']."\nTemp : ".$hasil['main']['temp']."°C");
            $result - $bot->replyMessage($event['replyToken'], $greetings); 
         }
         else if ($eventType == 'message' AND $messageType == 'image' /*AND $hasadmin == $userId*/){
            $uril = "https://backupriribot.herokuapp.com/index.php/content/".$picId;
            $blah = strval($database->get("command_text_img","maka",["userid[=]"=> $userId, "groupid[=]" => $groupId,"uploaded" =>false]));
            if ($blah == '0'){
                $aa = Cloudinary\Uploader::upload($uril,["public_id" => $picId,"resource_type" => "auto"]);
               // $urlPindah = "http://uploads.im/api?upload=".$aa['secure_url'];
                //$content = file_get_contents($urlPindah);
                //$hasils = json_decode($content);
                //$linkkkkk = $url = str_replace( 'http://', 'https://', $hasils->data->img_url );
                $linkkkkk = "https://res.cloudinary.com/ririnabots/image/upload/v1510399594/".$picId;

                $database->update("command_text_img",["maka" => $aa['secure_url'],"uploaded" => TRUE],["userid[=]" => $userId, "groupid[=]" => $groupId,"maka[=]"=>"0"]);
                $bot->replyText($replyToken, "OK! Keyword berhasil ditambahkan [".$randcloud."] "/*.$aa['secure_url']*/);
            }
         }
    }
}
});

$app->get('/content/{messageId}', function($req, $res) use ($bot)
{
    // get message content
    $route      = $req->getAttribute('route');
    $messageId = $route->getArgument('messageId');
    $result = $bot->getMessageContent($messageId);

    // set response
    $res->write($result->getRawBody());

    return $res->withHeader('Content-Type', $result->getHeader('Content-Type'));
});

$app->run();
