<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3mobile.css">
<body>

<?php
$id = base64_decode(base64_decode($_GET['id']));
$no = $_GET['no'];
if($id){
//echo 'Hi! Halaman ini masih proses pengerjaan....<br>';
//echo 'Username Ig = '.$id.'<br>';
//echo 'No = '.$no.'<br>';
    $url = 'https://www.instagram.com/'.$id.'/media/';
    $content = file_get_contents($url);
    $hasil = json_decode($content);
    $width = $hasil->items[$no]->images->low_resolution->width;
    $height = $hasil->items[$no]->images->low_resolution->height;
    $url_img = $hasil->items[$no]->images->standard_resolution->url;
    $caps = $hasil->items[$no]->caption->text;
    $likes = $hasil->items[$no]->likes->count;
    $codeIg = $hasil->items[$no]->code;
    $tipe = $hasil->items[$no]->type;
?>
<center>
    <table border ="0">
        <tr><td align="center">
                <?php if ($tipe == 'image' OR $tipe == 'video'){ ?>
                <img src="<?php echo $url_img;?>" alt="ig" width="<?php echo $width;?>" height="<?php echo $height;?>"></td>
                <?php }
                else if ($tipe=='carousel'){
                ?> 
                    <img src="<?php echo $url_img;?>" alt="ig" width="<?php echo $width;?>" height="<?php echo $height;?>"></td>
                    <?php if (!empty($hasil->items[$no]->carousel_media[1])){ ?>
                    <tr><td align="center">
                    <img src="<?php echo $hasil->items[$no]->carousel_media[1]->images->standard_resolution->url;?>" alt="ig" width="<?php echo $hasil->items[$no]->carousel_media[1]->images->low_resolution->width;?>" height="<?php echo $hasil->items[$no]->carousel_media[1]->images->low_resolution->height;?>"></td>
                     <?php if (!empty($hasil->items[$no]->carousel_media[2])){ ?>
                    <tr><td align="center">
                    <img src="<?php echo $hasil->items[$no]->carousel_media[2]->images->standard_resolution->url;?>" alt="ig" width="<?php echo $hasil->items[$no]->carousel_media[2]->images->low_resolution->width;?>" height="<?php echo $hasil->items[$no]->carousel_media[2]->images->low_resolution->height;?>"></td>
                     <?php if (!empty($hasil->items[$no]->carousel_media[3])){ ?>
                    <tr><td align="center">
                    <img src="<?php echo $hasil->items[$no]->carousel_media[3]->images->standard_resolution->url;?>" alt="ig" width="<?php echo $hasil->items[$no]->carousel_media[3]->images->low_resolution->width;?>" height="<?php echo $hasil->items[$no]->carousel_media[3]->images->low_resolution->height;?>"></td>
                        <?php if (!empty($hasil->items[$no]->carousel_media[4])){ ?>
                    <tr><td align="center">
                    <img src="<?php echo $hasil->items[$no]->carousel_media[4]->images->standard_resolution->url;?>" alt="ig" width="<?php echo $hasil->items[$no]->carousel_media[4]->images->low_resolution->width;?>" height="<?php echo $hasil->items[$no]->carousel_media[4]->images->low_resolution->height;?>"></td>
                <?php } } } } } ?>
        <tr><td align="center">
               <?php echo "@".$id;?>
            </td>
        <tr><td align="center">
                <div class="w3-border"><?php echo $caps;?></div>
            </td>
        <tr><td align="center">
                <?php echo $likes.'❤';?>
            </td>
        <tr><td align="center">
                <a href="https://www.instagram.com/p/<?php echo $codeIg?>"><font color="red">Open in Instagram</font></a>
            </td>
    </table>
    <br><br><br>
    <font size="1">Made with ❤ by Kyrozyn<br>for myRirinaBots❤</font>
    
    <?php
}
else{
    echo "What r u looking for?";
}
        
   

