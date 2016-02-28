<?php
    //this is the basic way of getting a database handler from PDO, PHP's built in quasi-ORM
    $dbhandle = new PDO("sqlite:scrabble.sqlite") or die("Failed to open DB");
    if (!$dbhandle) die ($error);
    
    function generate_rack($n){
        $tileBag = "AAAAAAAAABBCCDDDDEEEEEEEEEEEEFFGGGHHIIIIIIIIIJKLLLLMMNNNNNNOOOOOOOOPPQRRRRRRSSSSTTTTTTUUUUVVWWXYYZ";
        $rack_letters = substr(str_shuffle($tileBag), 0, $n);
        $temp = str_split($rack_letters);
        sort($temp);
        return implode($temp);
    };
    
    //Do 0->1 loop, then 0-2 loop...
    //Stick the query in, save the answer(?), go back in
    
    //Afterwards spit it all out
    
    //this is a sample query which gets some data, the order by part shuffles the results
    //the limit 0, 10 takes the first 10 results.
    // you might want to consider taking more results, implementing "pagination",
    // ordering by rank, etc.
    //$query = "SELECT rack, words FROM racks WHERE length=7 and weight <= 10 order by random() limit 0, 10";
    
    //
    $input = generate_rack(7);
    
    //Generates substrings
    for($i = 0; $i < pow(2, strlen($input)); $i++){
        $ans = "";
        for($j = 0; $j < strlen($input); $j++){
            //if the jth digit of i is 1 then include letter
            if (($i >> $j) % 2) {
                $ans .= $input[$j];
            }
        }
        if (strlen($ans) > 1)
        {
            $racks[] = $ans;
        }
    }
    $racks = array_unique($racks);
    
    $query = "SELECT words FROM racks WHERE rack=:input";
    $words = array();
    
    //this next line could actually be used to provide user_given input to the query to
    //avoid SQL injection attacks
    $statement = $dbhandle->prepare($query);
    $statement->bindParam(':input', $subrack);
    
    foreach($racks as $subrack) {
        $statement->execute();
    
        //The results of the query are typically many rows of data
        //there are several ways of getting the data out, iterating row by row,
        //I chose to get associative arrays inside of a big array
        //this will naturally create a pleasant array of JSON data when I echo in a couple lines
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        if (json_encode($results) != "[]")
        {
            //It will look like this [{"words":"EN@@NE"}]
            //Get rid of the unneeded by : and the }]
            //Note that this concatenates
            $pre_words[] = $results;
        }
        
    }
    
    
    //this part is perhaps overkill but I wanted to set the HTTP headers and status code
    //making to this line means everything was great with this request
    header('HTTP/1.1 200 OK');
    //this lets the browser know to expect json
    header('Content-Type: application/json');
    //this creates json and gives it back to the browser
    echo json_encode(array($input, $pre_words));
    //echo json_encode($pre_words);
?>