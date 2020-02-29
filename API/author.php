<?php

    error_reporting(E_ALL);
    error_reporting(-1);
    ini_set('error_reporting', E_ALL);

    require_once('../../scripts/dbconnect.php');
    require_once('functions.php');

    function getAuthorInfo() {
        global $mysqli;

        $id = getvar('id');
        if ($id == null) {
            $id = 0;
        }

        $sql = "SELECT * FROM `authors` WHERE id=$id";
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
            echo "<div class='row'>
                    <div class='col-9'>
                        <h2>Author Details</h2>
                    </div>
                    <div class='col-3'>
                        <a href='#' class='btn btn-block btn-primary'>Edit Series</a>
                    </div>
                </div>
                <div class='row'>
                    <div class='col'>
                        <table class='table table-condensed table-bordered table-striped table-hover'>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                            </tr>";

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr id=row" . $row["id"] . ">";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['first_name'] . "</td>";
                    echo "<td>" . $row['last_name'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='255' class='text-center'>No Records Found</td></tr>";
            }

            echo "</table></div></div>";
        }
    }

    function getSeriesInfo() {
        global $mysqli;

        $id = getvar('id');
        if ($id == null) {
            $id = 0;
        }

        $sql = "SELECT * FROM `series` WHERE author_id=$id";
        $result = $mysqli->query($sql);
        
        echo "<div class='row'>
                <div class='col'>
                    <h2>Series Details</h2>
                </div>
            </div>";
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {     
                echo "<div class='row'>
                        <div class='col'>
                            <h3>" . $row['title'] . "</h3>
                        </div>
                    </div>
                    <div class='row'>";
                $seriesID = $row["id"];
                $seriesSQL = "SELECT * FROM `books` WHERE series_id=$seriesID ORDER BY num_in_series";
                $seriesResult = $mysqli->query($seriesSQL);
                $numOfBooks = $row['num_of_books'];
                if ($seriesResult->num_rows > 0) {
                    while($seriesRow = $seriesResult->fetch_assoc()) {
                        $ownedCSS = '';
                        if ($seriesRow['owned'] == 0) {
                            $ownedCSS = ' unowned';
                        }
                        echo "<div class='col-2 mb-2'>
                                <a href='http://192.168.33.10/BookDatabase/API/book.php?id=" . $seriesRow['id'] . "'>
                                    <img class='img img-100" . $ownedCSS . "' src='" . $seriesRow['cover_image'] . "'>
                                    <p class='text-center metal linear'>" . $seriesRow['title'] . "<br>(" . $seriesRow['num_in_series'] . " of " . $numOfBooks . ")</p>" .
                                "</a>
                            </div>";
                    }
                }
                echo '</div>';
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Author Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <?php injectCSS(); ?>
</head>
<body>
    <?php renderHeader(); ?>
    <div class="container">
        <?php getAuthorInfo(); ?>
        <?php getSeriesInfo(); ?>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
