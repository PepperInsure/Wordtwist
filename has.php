<?php
    $dict = file_get_contents("dictionary.txt");
    //echo $dict;
    $dictArr = explode("\n", $dict);
    //echo $dictArr[0];
    foreach($dictArr as $word)
    {
        if (hash("sha256", $word) == "eb0f7a3002c9713e1119c7697e36d95b4307f0df9bcb224a335532a72b6f25f2")
        {
            echo $word;
        }
    }
    //echo hash("sha256", "word_here");
?>