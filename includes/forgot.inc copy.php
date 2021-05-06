<?PHP
include 'databasecontroll.php';

    $stmt = $conn->prepare('show tables');
    $stmt->execute();
    while ($ids = $stmt->fetch(PDO::FETCH_ASSOC))
    {
        $stmt = $conn->prepare('ALTER TABLE ? COLLATE utf8_general_ci');
        $stmt->execute([$ids['Tables_in_chat']]);
    }
    echo "The collation of your database has been successfully changed!";

