<!-- methodes -->
<?php
session_start();
require 'vendor/autoload.php'; // Inclure Composer autoload
$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->tweets->tweets;


// Récupérer le nom d'utilisateur envoyé via le formulaire via la méthode POST
$user = $_POST['user'] ?? '';
// Utilisation de l'opérateur ternaire (?) pour définir une valeur par défaut en cas de non-existence du paramètre 'user' dans la requête
$message = $_POST['message'] ?? '';
$tweetId = $_POST['id'] ?? '';
$newMessage = $_POST['newMessage'] ?? '';
$like = $_POST['like'] ?? '';

$login = $_GET['login'] ?? '';
$login_connecte = isset($_SESSION['login']) ? $_SESSION['login'] : null;
// si on a login dans session alors on recupère login dans session sinon on recupère null


if ($user && $message) {
    $collection->insertOne([
        'user' => $user,
        'message' => $message,
        'timestamp' => new MongoDB\BSON\UTCDateTime(),
        'likes' => 0
    ]);
}

// Supprimer un tweet en fonction de son identifiant MongoDB
if ($tweetId) {
    $collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($tweetId)]);
}
if ($like) {
    $collection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($tweetId)],
        ['$inc' => ['likes' => 1]]
    );
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

    <div class="container mx-auto max-w-xs">
        <?php if (!$login_connecte) : ?>
            <a href="connexion.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">Connexion</a>
            <a href="inscription.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-4">Inscription</a>
        <?php endif; ?>
        <?php if ($login_connecte) : ?>
            <a href="deconnexion.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">Déconnexion</a>
        <?php endif; ?>

        <h1 class="text-2xl font-bold mb-4">Tweets</h1>
        <?php
        $tweets = $collection->find([], [
            // recharge les information
            'sort' => ['timestamp' => -1], // Tri par date décroissante
        ]);

        foreach ($tweets as $tweet) : ?>
            <div class="bg-white rounded-lg shadow p-4 mb-4">
                <div class="flex items-center">
                    <p class="font-bold"><?= $tweet['user'] ?></p>
                    <p class="ml-4 text-gray-500"><?= $tweet['timestamp']->toDateTime()->format('Y-m-d H:i:s') ?></p>
                </div>
                <p class="mt-2"><?= $tweet['message'] ?></p>
                <p class="mt-2 text-gray-500">Likes: <?= $tweet['likes'] ?? 0 ?></p>
                <div class="flex justify-between">


                    <form action="likes.php" method="GET">
                        <input type="hidden" name="id" value="<?= $tweet["_id"] ?>">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2">Likes</button>
                    </form>

                    <?php if ($login_connecte = $user) : ?>
                    <form action="update.php" method="GET">
                        <input type="hidden" name="id" value="<?= $tweet["_id"] ?>">
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mt-2">Update</button>
                    </form>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <input type="hidden" name="id" value="<?= $tweet["_id"] ?>">
                        <button type="submit" name="delete" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mt-2">Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach ?>

        <form method="POST" class="bg-white rounded-lg shadow p-4 mb-4">
            <input type="text" name="user" placeholder="Nom d'utilisateur" class="w-full px-4 py-2 mb-2 border rounded">
            <input type="text" name="message" placeholder="Tweet" class="w-full px-4 py-2 mb-2 border rounded">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Tweet</button>
        </form>
    </div>
</body>

</html>

<?php
var_dump($login);

var_dump($tweetId);
var_dump($user);
var_dump($tweet["_id"]); ?>