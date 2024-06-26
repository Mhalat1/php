<!-- methodes -->
<?php
session_start();
require 'vendor/autoload.php'; // Inclure Composer autoload
$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->tweets->tweets;
$collection_user = $client->tweets->users;


// Récupérer le nom d'utilisateur envoyé via le formulaire via la méthode POST
$user = $_POST['user'] ?? '';
// Utilisation de l'opérateur ternaire (?) pour définir une valeur par défaut en cas de non-existence du paramètre 'user' dans la requête
$message = $_POST['message'] ?? '';
$tweetId = $_POST['id'] ?? '';
$newMessage = $_POST['newMessage'] ?? '';
$like = $_POST['like'] ?? '';
$deleteId = $_POST['deleteId'] ?? '';
$login = $_GET['login'] ?? '';
$login_connecte = isset($_SESSION['login']) ? $_SESSION['login'] : null;


$commentaire = $_POST['commentaire'] ?? '';
// si on a login dans session alors on recupère login dans session sinon on recupère null


if ($user && $message) {
    $collection->insertOne([
        'user' => $user,
        'message' => $message,
        'timestamp' => new MongoDB\BSON\UTCDateTime(),
        'likes' => 0,
        'comments' => [],
    ]);
}

if ($commentaire && $tweetId) {
    $collection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($tweetId)],
        ['$push' => ['comments' => [
            'user' => $login_connecte,
            'message' => $commentaire,
            'timestamp' => new MongoDB\BSON\UTCDateTime()
        ]]]
    );
}

// Supprimer un tweet en fonction de son identifiant MongoDB
if ($deleteId) {
    $collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($deleteId)]);
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

<body class="bg-blue-100">

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
                    <form action="follow.php" method="GET" class="ml-auto">
                        <input type="hidden" name="id" value="<?= $tweet["user"] ?>">

                    </form>
                </div>
                <p class="mt-2"><?= $tweet['message'] ?></p>
                <p class="mt-2 text-gray-500">Likes: <?= $tweet['likes'] ?? 0 ?></p>
                <div class="flex justify-between">



                        <form action="likes.php" method="GET" class="mr-2">
                            <input type="hidden" name="id" value="<?= $tweet["_id"] ?>">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded mt-2">Like</button>
                        </form>
                        <?php if ($tweet['user'] == $login_connecte) : ?>
                        <?php if ($tweet['user'] != $login_connecte) : ?>
                            <form action="retweet.php" method="GET" class="mr-2">
                                <input type="hidden" name="id" value="<?= $tweet["_id"] ?>">
                                <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded mt-2">Retweet</button>
                            </form>
                        <?php endif; ?>




                        <form action="update.php" method="GET" class="mr-2">
                            <input type="hidden" name="id" value="<?= $tweet["_id"] ?>">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded mt-2">Update</button>
                        </form>


                        <form method="POST" action="" class="mr-2">
                            <input type="hidden" name="deleteId" value="<?= $tweet["_id"] ?>">
                            <button type="submit" name="delete" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded mt-2">Delete</button>
                        </form>
                    <?php endif; ?>

                    <?php if ($login_connecte == 'moderateur') : ?>
                        <form method="POST" action="" class="mr-2">
                            <input type="hidden" name="deleteId" value="<?= $tweet["_id"] ?>">
                            <button type="submit" name="delete-modo" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded mt-2">Admin Delete</button>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="flex">
                    
                    <form method="POST" action="" class="mr-2 bg-white rounded-lg shadow p-4">

                        <?php foreach ($tweet['comments'] as $comment) : ?>
                            <div class="border-t-2 border-gray-200 p-2 bg-gray-100">
                                <p class="text-blue-500"><?= $comment['user'] ?> : <span class="font-semibold"><?= $comment['message'] ?></span> : <?= $tweet['timestamp']->toDateTime()->format('Y-m-d H:i:s') ?></p>
                            </div>
                        <?php endforeach; ?>
                        <input type="hidden" name="user" value="<?= $login ?>" class="w-full px-4 py-2 mb-2 border rounded mt-2 bg-gray-50">
                        <input type="hidden" name="id" value="<?= $tweet["_id"] ?>">
                        <input type="text" name="commentaire" placeholder="Commentaire" class="w-full px-4 py-2 mb-2 border rounded mt-2 bg-gray-50">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded mt-2">Commenter</button>
                    </form>
                </div>
            </div>
        <?php endforeach ?>

        <form method="POST" class="bg-white rounded-lg shadow p-4 mb-4">
            <input type="hidden" name="user" value="<?= $login_connecte ?>" placeholder="Nom d'utilisateur" class="w-full px-4 py-2 mb-2 border rounded">
            <input type="text" name="message" placeholder="Tweet" class="w-full px-4 py-2 mb-2 border rounded">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Tweet</button>
        </form>
    </div>
</body>

</html>

<?php
var_dump($login_connecte);

var_dump($commentaire);

var_dump($tweet["_id"]); ?>