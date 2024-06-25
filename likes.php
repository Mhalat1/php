<!-- methodes -->
<?php
require 'vendor/autoload.php'; // Inclure Composer autoload
$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->tweets->tweets;



$tweetId = $_GET['id'] ?? '';

if ($tweetId) {
    echo 'OK';
    $collection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($tweetId)],
        ['$inc' => ['likes' => 1]]
    );
    header('Location: index.php');
} else {
    echo 'NON';
}
