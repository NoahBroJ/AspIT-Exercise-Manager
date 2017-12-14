<?php
/*Validate user logged in*/
if (isset($_SESSION['userId'])) {
    /*Get exercises from database*/
    $sql = 
        "CREATE TEMPORARY TABLE t1 AS 
        SELECT * 
        FROM authors 
        ORDER BY Timestamp DESC;

        SELECT exercises.ExerciseId, exercises.Title, subjects.SubjectName, users.FirstName, users.LastName, DATE_FORMAT(exercises.LastUpdated, '%e-%c-%Y %H:%i') AS 'Date'
        FROM exercises
        LEFT JOIN t1 ON exercises.ExerciseId = t1.ExerciseId
        LEFT JOIN subjects ON exercises.SubjectId = subjects.SubjectId
        LEFT JOIN users ON users.UserId = t1.UserId ";
    
    if ($_SESSION['accessLevel'] < 2) {
        /*Select all exercises with access level above 0 or with latest author equal to current user*/
        $sql .= "WHERE exercises.AccessLevel > 0 OR users.UserId = ? ";
    } else {
        /*Select all exercises with access level above 1 or with latest author equal to current user*/
        $sql .= "WHERE exercises.AccessLevel > 1 OR users.UserId = ? ";
    }
    
    $sql .= 
        "GROUP BY exercises.ExerciseId
        ORDER BY exercises.LastUpdated DESC;";
    
    require 'connect.php';
    
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(1, $_SESSION['userId']);
    $stmt->execute();
    $stmt->nextRowset();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        /*Insert php loop here with row values (Date index is named Date)*/
        ?>
        <tr>
            <td class="tTitle" data-id="<?php echo $row['ExerciseId'] ?>"><a href="#"> <?php echo $row['Title'] ?> </a></td>
            <td class="tAuthor"> <?php echo $row['FirstName'] . ' ' . $row['LastName'] ?> </td>
            <td class="tSubject"> <?php echo $row['SubjectName'] ?> </td>
            <td class="tDate"> <?php echo $row['Date'] ?> </td>
        </tr>
        <?php
    }
} else {
    /*Error user not logged in*/
}
?>