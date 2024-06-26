<?php
session_start();
// on renvoi les donnée dans session qui est accesible entre différentes pages

require 'vendor/autoload.php'; // Inclure Composer autoload
$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->tweets->users;


$login = $_POST['login'] ?? '';

if ($login) {
    $collection->insertOne(['login' => $login]);
    header('Location: connexion.php');
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.2/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="flex justify-center items-center h-screen">
        <div class="bg-white p-6 rounded shadow">
            <h1 class="text-2xl font-bold mb-4">Inscription</h1>
            <form method="POST" action="inscription.php" class="space-y-4">
                <div>
                    <label for="login" class="block text-gray-700 font-bold mb-2">Login :</label>
                    <input type="text" name="login" id="login" class="border border-gray-300 rounded p-2 w-full">
                </div>
                <div>
                    <input type="submit" value="Inscription" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                </div>
            </form>
        </div>
    </div>

    <?php
    var_dump($login);
    ?>