<?php 

                    $urlPindah = "http://uploads.im/api?upload="."https://res.cloudinary.com/ririnabots/image/upload/v1506862317/6778930211728.jpg";
                    $content = file_get_contents($urlPindah);
                    $hasils = json_decode($content);
                    $linkkkkk = $url = str_replace( 'http://', 'https://', $hasils->data->img_url );
                    echo $linkkkkk;
                    //print_r($hasils);