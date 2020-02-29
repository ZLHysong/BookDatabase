<?php

    function getvar($key) {
        if (isset($_REQUEST[$key])) {
            return $_REQUEST[$key];
        }
        return null;
    }

    function renderHeader() {
        echo "
        <div class='container-fluid mb-3' style='background-color: #DDDDDD'>
            <div class='container'>
                <div class='row'>
                    <div class='col'>
                        <h1><a href='http://192.168.33.10/BookDatabase/' style='color: black'>Book Database</a></h1>
                    </div>
                </div>
            </div>
        </div>";
    }

    function injectCSS() {
        echo "
        <style>
            body {
                /*background-image: url('../images/bg.jpg');*/
            }
    
            .img-100 {
                width: 100%;
            }
    
            .unowned {
                opacity: 50%;
            }
    
            .metal {
                margin-top: 15px;
                padding-top: 10px;
                position: relative;
                outline: none;
    
                font: bold 6em/2em 'Helvetica Neue', Arial, Helvetica, Geneva, sans-serif;
                text-align: center;
                color: hsla(0, 0%, 20%, 1);
                text-shadow: hsla(0, 0%, 40%, .5) 0 -1px 0, hsla(0, 0%, 100%, .6) 0 2px 1px;
    
                background-color: hsl(0, 0%, 90%);
                box-shadow: inset hsl(42.1, 100%, 50%) 0 0px 0px 2px,
                    /* border */
                    inset hsla(39.4, 100%, 49.2%, 0.8) 0 -1px 5px 2px,
                    /* soft SD */
                    inset hsla(0, 0%, 0%, .25) 0 -1px 0px 3px,
                    /* bottom SD */
                    inset hsla(0, 0%, 30%, .7) 0 2px 1px 3px,
                    /* top HL */
    
                    hsla(0, 0%, 0%, .15) 0 -5px 6px 2px,
                    /* outer SD */
                    hsla(0, 0%, 30%, .5) 0 2px 2px 2px;
                /* outer HL */
    
                transition: color .2s;
            }
    
            .metal.linear {
                width: 100%;
                font-size: 1em;
                height: 80px;
                border-radius: .5em;
                background-image: -webkit-repeating-linear-gradient(left, hsla(0, 0%, 100%, 0) 0%, 
                                    hsla(0, 0%, 100%, 0) 6%, 
                                    hsla(0, 0%, 100%, .1) 7.5%), 
                                -webkit-repeating-linear-gradient(left, hsla(0, 0%, 0%, 0) 0%, 
                                    hsla(0, 0%, 0%, 0) 4%, 
                                    hsla(0, 0%, 0%, .03) 4.5%), 
                                -webkit-repeating-linear-gradient(left, 
                                    hsla(0, 0%, 100%, 0) 0%, 
                                    hsla(0, 0%, 100%, 0) 1.2%, 
                                    hsla(0, 0%, 100%, .15) 2.2%), 
                                linear-gradient(180deg, 
                                    hsl(42.2, 100%, 61.6%) 0%, 
                                    hsl(39.6, 100%, 82.2%) 47%, 
                                    hsl(39.6, 96.5%, 77.8%) 53%, 
                                    hsl(47.8, 100%, 75.9%)100%);
            }
        </style>";
    }

    function getAuthorList() {
        global $mysqli;
        $data['results'] = [];

        $sql = "SELECT * FROM `authors` ORDER BY `last_name`, `first_name`";
        $result = $mysqli->query($sql);

        $format = getvar('format');
        if($format == 'json') {
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    array_push($data['results'], $row);
                }
            }
            return $data;
        } else {
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['first_name'] . " " . $row['last_name'] ."</option>";
                }
            } else {
                echo "No Results Found";
            }
        }
    }

    function addSeries() {
        global $mysqli;

        $author = getvar('author');
        $name = getvar('name');
        $numOfBooks = getvar('numOfBooks');

        if (checkSeriesInfo($author, $name, $numOfBooks)) {
            return;
        }

        $sql = "INSERT INTO `series` (author_id, title, num_of_books) VALUES ('$author', '$name', '$numOfBooks')";
        if ($mysqli->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }
    }

    function checkSeriesInfo($author, $name, $numOfBooks) {
        if(!$author) {
            echo "No Author provided\n";
            return 1;
        }

        if(!checkAuthorExist($author)) {
            echo "That Author is not in our database\n";
            return 1;
        }

        if(!$name) {
            echo "No Series Name provided\n";
            return 1;
        }

        if(checkSeriesExist($name, $author)) {
            echo "That Series (by that Author) already exists\n";
            return 1;
        }

        if (!$numOfBooks) {
            echo "Please provide a number of books\n";
            return 1;
        }

    }

    function checkAuthorExist($author) {
        global $mysqli;

        $sql = "SELECT * FROM `authors` WHERE `id` = '$author';";
        $result = $mysqli->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return 1;
        } else {
            return 0;
        }
    }

    function checkSeriesExist($name, $author) {
        global $mysqli;

        $sql = "SELECT * FROM `series` WHERE (`title` like '$name' AND `author_id` = $author) OR (`title` like 'The $name' AND `author_id` = $author);";
        $result = $mysqli->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return 1;
        } else {
            return 0;
        }
    }
?>