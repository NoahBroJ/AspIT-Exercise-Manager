<?php
function GetLatestAuthorId($exerciseId) {
    /*If exercise has an id that is a number*/
    if (isset($exerciseId)) {
        if (is_numeric($exerciseId)) {
            /*Find the latest author to the exercise*/
            require "connect.php";

            $stmt = $dbh->prepare(
                "SELECT UserId FROM authors 
                WHERE ExerciseId = ? 
                ORDER BY Timestamp DESC 
                LIMIT 1;"
            );
            $stmt->bindParam(1, $exerciseId);
            $stmt->execute();

            if ($author = $stmt->fetch(PDO::FETCH_ASSOC)) {
                /*Close connection and return result*/
                $dbh = null;
                return $author['UserId'];
            } else {
                /*error no author found*/
                $dbh = null;
                throw new Exception('Authors not found');
            }
        } else {
            /*error not a number*/
            $dbh = null;
            throw new Exception('Invalid argument type');
        }
    } else {
        /*error null argument*/
        $dbh = null;
        throw new Exception('Argument null');
    }
}
?>