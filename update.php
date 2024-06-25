<!-- methodes -->
<?php
require 'vendor/autoload.php'; // Inclure Composer autoload
$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->tweets->tweets;


// Récupérer le nom d'utilisateur envoyé via le formulaire via la méthode POST
$user = $_POST['user'] ?? '';
// Utilisation de l'opérateur ternaire (?) pour définir une valeur par défaut en cas de non-existence du paramètre 'user' dans la requête
$message = $_POST['message'] ?? '';

$tweetId = $_POST['id'] ?? '';
$newMessage = $_POST['newMessage'] ?? '';

$id = $_GET['id'];


if ($newMessage && $tweetId) {
    $collection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($tweetId)],
        ['$set' => ['message' => $newMessage]]
    );
    header('Location: index.php');
}




?>
<!-- affichage -->
<!DOCTYPE html>
<html>

<head>
    <title>Tweets</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <h1 class="text-2xl font-bold mb-4">Tweets</h1>
    <div class="tweets">
        <?php
        $id = new MongoDB\BSON\ObjectId($id);

        // on doit passer l'id en object id pour pouvoir l'utiliser dans findOne //
        $tweet = $collection->findOne(['_id' => $id]);
        ?>

        <div class='tweet p-4 mb-4 bg-white rounded-lg shadow'>
            <p class="text-lg font-semibold"><?= $tweet['message'] ?></p>
            <p class="text-gray-500"><?= $tweet['timestamp']->toDateTime()->format('Y-m-d H:i:s') ?></p>
            <form action="update.php" method="POST" class="mt-4">
                <input type='hidden' name='id' value='<?= $tweet["_id"] ?>'>
                <input type='text' name='newMessage' value=<?= $tweet['message'] ?> class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:border-blue-500">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2">Modifier</button>
            </form>
        </div>

    </div>
</body>


<?php var_dump($id);

var_dump($tweet["_id"]); ?>