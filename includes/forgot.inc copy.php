<?PHP
foreach(PDO::getAvailableDrivers() as $driver) {
    echo $driver.'<br />';
  }