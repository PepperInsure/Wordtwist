$(document).ready(function(){
    
    var inputs = 
    {
        guess : ""
    };
    
    var indexname = ""
    var $resultsize = 0
    var $init_words = ""
    var $parsed = []
    var $got = "no"
    var $check_list = []
    var $words_left = 0
    var $win_flag = 0
    
    $(".input-wrapper").keyup(function(event)
    {
        indexname = event.target.id;
            inputs[indexname] = $(event.target).val();
    });
    
    $('#new_game').click(function()
    {
        $.post("word.php", function(word_list)
        {
            $init_words = word_list.splice(0, 1);
            $resultsize = word_list[0].length;
            //Take out the beginning which has the word
            
            $("#hidden_words").empty();
            $("#player_words").empty();
            $('#number_words_left').empty();
            $('#found_words').empty();
            //reset
            
            $('#player_words').append($init_words);
            
            $parsed = []
            var $pre_split = []
            for (var i = 0; i < $resultsize; i++)
            {
                //It will look like this [{"words":"EN@@NE"}]
                //Get rid of the unneeded by : and the }]
                //Some of them have @@, split it out
                $pre_split = (JSON.stringify(word_list[0][i]).slice(11, -3)).split("@@");
                $parsed.push.apply($parsed, $pre_split);
                
                //$('#hidden_words').append("<li>" + i + $pre_split.toString() + "</li>");
            };
            //Note that the new array is larger, gotta go through it again
            $words_left = $parsed.length;
            $('#number_words_left').append($words_left);
            for (var i = 0; i < $parsed.length; i++)
            {
                //$('#hidden_words').append("<li>" + $parsed[i] + "</li>");
                //Gives you every word. Only enable for debugging purposes.
            };
        }
        );
    });
    
    //For now it just gives back the answer, but later will check guesses
    $('#select_button').click(function()
    {
        $got = "no"
        $("#test").empty();
        //$("#test").append(inputs.guess);
        
        for (var i = 0; i < $parsed.length; i++)
        {
            if(inputs.guess == $parsed[i])
            {
                $got = "yes"
                
                $("#found_words").append("<li>" + inputs.guess);
                
                //Rip out the old words, decrement counter
                $parsed.splice(i, 1);
                $words_left = $words_left - 1;
                $("#number_words_left").empty().append($words_left);
                
                //update html
                
                if ($words_left == 0)
                {
                    $("#test").append("You win! Click new game to try again.");
                    $win_flag = 1;
                }
                else
                {
                    $("#test").append("Found one!");
                }
                
                break;
            }
        };
        if ($got == "no" && $win_flag == 0)
        {
            $("#test").append("Didn't find it.");
        }
        
    });

});