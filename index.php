<?php

    error_reporting(E_ALL);
    error_reporting(-1);
    ini_set('error_reporting', E_ALL);

	require_once('../scripts/dbconnect.php');
	require_once('API/functions.php');

    function getBooks() {
        global $mysqli;

        $sql = "SELECT a.id,
                    a.title,
                    a.num_in_series,
                    b.id as `authorID`,
                    b.first_name as `authorFirst`,
                    b.last_name as `authorLast`,
                    c.title as `series`,
                    c.id as `seriesID`,
                    a.owned
                FROM `books` a
                    LEFT JOIN `authors` b
                        ON a.author_id = b.id
                    LEFT JOIN `series` c
                        ON a.series_id = c.id
                ORDER BY last_name, first_name, series, num_in_series";

        $result = $mysqli->query($sql);

        echo "<div class='row'>
                <div class='col-9'>
                    <h2>Books</h2>
                </div>
                <div class='col-3'>
                    <a href='#' class='btn btn-block btn-primary'>Add Book</a>
                </div>
            </div>
            <div class='row'>
                <div class='col'>
                    <table class='table table-condensed table-bordered table-striped table-hover'>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Series</th>
                            <th>Number in Series</th>
                            <th>Owned</th>
                        </tr>";

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if ($row['owned'] == 0) {
                    $owned = 'Unowned';
                } else {
                    $owned = 'Owned';
                }

                echo "<tr id=row" . $row["id"] . ">";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td><a href='http://192.168.33.10/BookDatabase/API/book.php?id=" . $row['id'] . "'>" . $row['title'] . "</a></td>";
                echo "<td><a href='http://192.168.33.10/BookDatabase/API/author.php?id=" . $row['authorID'] . "'>" . $row['authorFirst'] . " " . $row["authorLast"] . "</a></td>";
                echo "<td><a href='http://192.168.33.10/BookDatabase/API/series.php?id=" . $row['seriesID'] . "'>" . $row['series'] . "</a></td>";
                echo "<td>" . $row['num_in_series'] . "</td>";
                echo "<td>" . $owned . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='255' class='text-center'>No Records Found</td></tr>";
        }

        echo "</table></div></div>";
    }

    function getAuthors() {
        global $mysqli;

        $sql = "SELECT * FROM `authors`";

        $result = $mysqli->query($sql);

        echo "<div class='row'>
                <div class='col-9'>
                    <h2>Authors</h2>
                </div>
                <div class='col-3'>
                    <a href='#' class='btn btn-block btn-primary'>Add Author</a>
                </div>
            </div>
            <div class='row'>
                <div class='col'>
                    <table class='table table-condensed table-bordered table-striped table-hover'>
                        <tr>
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Info</th>
                        </tr>";

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr id=row" . $row["id"] . ">";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['first_name'] . "</td>";
                echo "<td>" . $row['last_name'] . "</td>";
                echo "<td><a href='http://192.168.33.10/BookDatabase/API/author.php?id=" . $row['id'] . "'>More Info</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='255' class='text-center'>No Records Found</td></tr>";
        }

        echo "</table></div></div>";
    }

    function getSeries() {
        global $mysqli;

        $sql = "SELECT * FROM `series`";

        $result = $mysqli->query($sql);

        echo "<div class='row'>
                <div class='col-9'>
                    <h2>Series</h2>
                </div>
                <div class='col-3'>
                    <a href='#' class='btn btn-block btn-primary'  data-toggle='modal' data-target='#addSeries'>Add Series</a>
                </div>
            </div>
            <div class='row'>
                <div class='col'>
                    <table class='table table-condensed table-bordered table-striped table-hover'>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                        </tr>";

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $authorID = $row["author_id"];
                $authorSQL = "SELECT * FROM `authors` where ID=$authorID";
                $authorResult =  $mysqli->query($authorSQL);
                $authorRow = $authorResult->fetch_assoc();
                $authorID = $authorRow['id'];
                $firstName = $authorRow['first_name'];
                $lastName = $authorRow['last_name'];

                echo "<tr id=row" . $row["id"] . ">";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td><a href='http://192.168.33.10/BookDatabase/API/series.php?id=" . $row['id'] . "'>" . $row['title'] . "</a></td>";
                echo "<td><a href='http://192.168.33.10/BookDatabase/API/author.php?id=$authorID'>$firstName $lastName</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='255' class='text-center'>No Records Found</td></tr>";
        }

        echo "</table></div></div>";
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Database</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <?php injectCSS(); ?>
</head>
<body>
    <?php renderHeader(); ?>
    <div class="container">
        <?php getBooks(); ?>
        <div class="row">
            <div class="col-6">
                <?php getAuthors(); ?>
            </div>
            <div class="col-6">
                <?php getSeries(); ?>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addSeries" tabindex="-1" role="dialog" aria-labelledby="addSeriesLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSeriesLabel">Add Series</h5>
                </div>
                <div class="modal-body">          

                    <div class="form-group">
                        <label for="seriesName">Series Name</label>
                        <input type="text" class="form-control" id="seriesName">
                    </div>

                    <div class="form-group">
                        <label for="authorName">Author Name</label>
                        <select class="form-control" id="authorName">
                            <option>OPTIONS HERE (IF YOU SEE THIS, SOMETHING BROKE)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="numOfBooks">Number of Books in the Series</label>
                        <input type="text" class="form-control" id="numOfBooks">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="addSeriesSubmit" onclick='addSeries()' class="btn btn-primary">Add This Series</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    
    <script>
        /* 
            This section grabs all the appropriate data such as suthor or series names when the modal is opened and populates the modal dropdowns with that data.
            This is done when opening the modal to keep it so that the modal is always up to date without needing to refresh inbetween updates.
        */
        $('#addSeries').on('show.bs.modal', function(e) {
            $.ajax({
                url: 'api/index.php',
                type: 'post',
                data: {command:'getAuthorList', format:'html'},
                dataType: 'html',
                success:function(response){
                    $("#authorName").empty();
                    $("#authorName").append(response);
                }
            });
        });

        function addSeries() {
            let author = $("#authorName").val(), name = $("#seriesName").val(), numOfBooks = $("#numOfBooks").val();
            console.log('Adding ' + name + ' by ' + author + ' with ' + numOfBooks + ' books');
            $.ajax({
                url: 'api/index.php',
                type: 'post',
                data: { command:'addSeries', name: name, author: author, numOfBooks: numOfBooks },
                success:function(response){
                    console.log(response);
                }
            });
        }

    </script>
</body>
</html>
