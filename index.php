<?php 
function filter($input){

    return preg_replace('/[\x09\x0a\x0b\x0c\x0d\x1c\x1d\x1e\x1f\x20]/', '', $input);
}

function parseState($st="")  {
        $result = [];
        for ($i=0; $i < strlen($st) ; $i++) { 
            if ($st[$i] == "\""){
                $key="";
                $separator="";
                $value="";
                $i++;
                while($i < strlen($st) &&$st[$i] !="\""){
                    $key .=$st[$i];
                      $i++;
                }
                
                if ($i >= strlen($st) || $key==""){
                    $i--;
                    continue;
                }
                
                $captureI=$i;
                $i++;

                 while($i < strlen($st) &&$st[$i] !="\""){
                    $separator .=$st[$i];
                    
                      $i++;
                }
             
                if ($i >= strlen($st)){

                    continue;
                }
                
                    
                $separator = filter($separator);
                if  ($separator != ":"){
                    $i=$captureI;
                    $i--;
                    continue;
                }

                $i++;

                while($i < strlen($st) &&$st[$i] !="\""){
                    $value .=$st[$i];
                      $i++;
                }

                  if ($i >= strlen($st))
                    continue;
                
                array_push($result,$key,$value);
                $i--;
            }
        }


    return   $result;
}

function Debug_log($input){
        $std =fopen('php://stdin', 'w');
         fwrite( $std,$input."\n");
         fclose(  $std);
}

    $state= $_GET['state']; 
    $path= "-1";
    $host= "";


  
    $params = parseState($state);

    for ($i=0; $i < sizeof($params) ; $i+=2) { 
         if ( $params[$i] == "return_to"){
            if ($path == "-1"){
                $path = $params[$i+1];
            }
         }else   if ( $params[$i] == "target_host"){
            if ($host == ""){
                $host = $params[$i+1];
            }
         }
    }

    
    if (str_contains( $path,"\\") ||str_contains( $path,"@")){
        echo "path is invalid";
        http_response_code(400);
        die(1);
    }
     if ($path != "-1" && !str_starts_with( $path,"/")){
           echo "path is invalid";
                 http_response_code(400);
            die(1);
    }

     if (str_contains( $path,"</")){
       $path=  str_replace("</","<\\/",$path);
       }
   

        if ( !str_ends_with($host,"example.com")){
             echo "host is invalid";
                   http_response_code(400);
             die(1);
        }
        if (str_contains( $host,"\n") ||str_contains( $host,"\r")){
            echo "host is invalid";
                  http_response_code(400);
            die(1);
        }
      if (!preg_match("/^[a-zA-Z0-9.:-]+$/",$host)){
            echo "host is invalid";
                  http_response_code(400);
            die(1);
        }
    //path not \@
    //host  [-,.,:]
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
  <?php
   if (isset($state)){
        var_dump(parseState($state));
    } 
    ?>

    <script>    
  
        <?php    Debug_log("my_path= ".$path); ?>
        window.location = "https://<?php echo $host?><?php echo ($path=="-1"? "/":$path);?>?token=AAA";

    </script>



</body>
</html>