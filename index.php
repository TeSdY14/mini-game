<?php

spl_autoload_register(function ($class) {
    include $class . '.php';
});

session_start();

if (isset($_GET['deconnexion'])) {
    session_destroy();
    header('Location: .');
    exit();
}

if (isset ($_SESSION['perso'])) {
    $perso = $_SESSION['perso'];
}

$pdo = new PDO('mysql:dbname=mini_game;host=127.0.0.1;port=3306', 'tesdy', 'demo');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$persoManager = new PersonnagesManager($pdo);

// Traitement du formulaire
if (isset($_POST['creer']) && isset($_POST['nom'])) {
    $perso = new Personnage(['nom' => $_POST['nom']]);

    if (!$perso->nomValid()) {
        $msg = 'Le nom choisi n\'est pas valide';
        unset($perso);
    } elseif ($persoManager->exists($perso->getNom())) {
        $msg = "Le nom du personnage existe déjà";
        unset($perso);
    } else {
        $persoManager->add($perso);
    }
} elseif (isset($_POST['utiliser']) && isset($_POST['nom'])) {
    if ($persoManager->exists($_POST['nom'])) {
        $perso = $persoManager->get($_POST['nom']);
    } else {
        $msg = 'Ce personnage n\'existe pas';
    }
} elseif(isset($_GET['frapper'])) {
    if(!isset($perso)) {
        $msg = 'Merci de créer un personnage ou de vous identifier.';
    } else {
        if (!$persoManager->exists((int) $_GET['frapper'])) {
            $msg = 'Le personnage que vous voulez frapper n\'existe pas.';
        } else {
            $persoAFrapper = $persoManager->get((int) $_GET['frapper']);

            $retour = $perso->frapper($persoAFrapper);

            switch ($retour) {
                case Personnage::CEST_MOI :
                    $msg = 'Mais pourquoi se frapper soi-même ?';
                    break;
                case Personnage::PERSO_FRAPPE :
                    $msg = 'le personnage a bien été frappé';
                    $persoManager->updateXp($perso);
                    $persoManager->update($persoAFrapper);
                    break;
                case Personnage::PERSO_TUE :
                    $msg = 'Le coup ultime, vous avez tué ce personnage.';
                    $persoManager->update($perso);
                    $persoManager->delete($persoAFrapper);
                    break;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>TP : Mini Jeu de Combat</title>
</head>

<body>
<p>Nombre de personnage en lisse : <?= $persoManager->count() ?></p>
<?php
if (isset($msg)) echo '<p>', $msg, '</p>';
?>

<?php
if (isset($perso)) {
    ?>
    <p><a href="?deconnexion=1">Déconnexion</a></p>
    <fieldset>
        <legend>Mes Informations</legend>
        <p>
            Nom : <?= htmlspecialchars($perso->getNom()) ?> <br>
            Dégâts : <?= $perso->getDegats() ?> <br>
            Expérience : <?= $perso->getExperience() ?> <br>
            Niveau : <?= $perso->getNiveau() ?> <br>
            Force : <?= $perso->getPforce() ?> <br>
        </p>
    </fieldset>

    <fieldset>
        <legend>Qui frapper ?</legend>
        <p>
            <?php
            $persos = $persoManager->getList($perso->getNom());
            if (empty($persos)) {
                echo 'Aucun ennemi à frapper.';
            } else {
                foreach ($persos as $ennemi) {
                    echo '<a href="?frapper=', $ennemi->getId(), '">', htmlspecialchars($ennemi->getNom()), '</a> (dégâts : ', $ennemi->getDegats(), ') <br/>';
                }
            }
            ?>
        </p>
    </fieldset>
    <?php
} else {
    ?>
    <form action="" method="post">
        <p>
            <label>Nom :
                <input type="text" name="nom" maxlength="50">
            </label>
            <input type="submit" value="Créer un personnage" name="creer">
            <input type="submit" value="Utiliser ce personnage" name="utiliser">
        </p>
    </form>
    <?php
}
?>
</body>
</html>
<?php
if (isset($perso)) {
    $_SESSION['perso'] = $perso;
}
