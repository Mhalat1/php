<?php
session_start();
require 'vendor/autoload.php'; // Inclure Composer autoload
$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->tweets->users;


// Récupérer la valeur du champ 'login' envoyé via le formulaire via la méthode POST
// Utilisation de l'opérateur ternaire (?) pour définir une valeur par défaut en cas
// de non-existence du paramètre 'login' dans la requête
$login = $_POST['login'] ?? '';

if ($login) { // if (isset($_POST['login']) && !empty($_POST['login'])) {
    
    // Rechercher un document dans la collection 'users' qui a le champ 'login' égal à la valeur de $login
    $user = $collection->findOne(['login' => $login]);
    if ($user) {
        // connecter
        $_SESSION['login'] = $login;
        $_SESSION['id'] = $user['_id'];
        header('Location: index.php');
    } else {
        // N'existe pas
    }

}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex justify-center items-center h-screen">
        <div class="bg-white p-6 rounded shadow">
            <h1 class="text-2xl font-bold mb-4">Connexion</h1>
            <form method="POST" action="connexion.php" class="space-y-4">
                <div>
                    <label for="login" class="block text-gray-700 font-bold mb-2">Login :</label>
                    <input type="text" name="login" id="login" class="border border-gray-300 rounded p-2 w-full">
                </div>
                <div>
                    <input type="submit" value="connexion" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" >
                </div>
            </form>
        </div>
    </div>

    <?php 
    var_dump($login);
    ?>