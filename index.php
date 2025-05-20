<?php
require_once __DIR__ . '/classes/Secretaire.php';

/*---------------------------------------------
| 1. TRAITEMENT INSERT / UPDATE (POST)
|    → pattern Post-Redirect-Get pour éviter
|      les doubles soumissions avec F5
----------------------------------------------*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Champs obligatoires
    $nom     = trim($_POST['nom']     ?? '');
    $prenom  = trim($_POST['prenom']  ?? '');
    $adresse = trim($_POST['adresse'] ?? '');

    if ($nom && $prenom && $adresse) {

        $id = ($_POST['id'] !== '') ? (int)$_POST['id'] : null;

        if ($id) {
            Secretaire::update($id, $nom, $prenom, $adresse);
            $msg = 'Utilisateur mis à jour.';
        } else {
            Secretaire::insert($nom, $prenom, $adresse);
            $msg = 'Nouvel utilisateur ajouté.';
        }
    } else {
        $msg = 'Tous les champs sont requis.';
    }

    header('Location: index.php?msg=' . urlencode($msg));
    exit;
}

/*---------------------------------------------
| 2. SUPPRESSION (GET ?delete=ID)
----------------------------------------------*/
if (isset($_GET['delete'])) {
    Secretaire::delete((int)$_GET['delete']);
    header('Location: index.php?msg=' . urlencode('Utilisateur supprimé.'));
    exit;
}

/*---------------------------------------------
| 3. PRÉCHARGEMENT POUR MODIFIER
----------------------------------------------*/
$id      = $_GET['id'] ?? null;
$current = $id ? Secretaire::find((int)$id) : null;

/* valeurs par défaut pour le formulaire */
$nom      = $current['nom']     ?? '';
$prenom   = $current['prenom']  ?? '';
$adresse  = $current['adresse'] ?? '';

/*---------------------------------------------
| 4. RECHERCHE OU LISTE COMPLÈTE
|    (la recherche utilise GET ?search=mot)
----------------------------------------------*/
$searchTerm  = $_GET['search'] ?? '';
$secretaires = $searchTerm !== ''
             ? Secretaire::search($searchTerm)
             : Secretaire::all();

/*---------------------------------------------
| 5. MESSAGE FLASH OPTIONNEL
----------------------------------------------*/
$flash = $_GET['msg'] ?? null;
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Gestion des secrétaires</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-4">

        <?php if ($flash): ?>
        <div class="alert alert-info"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <!-- ========= Formulaire ADD / EDIT ========= -->
        <h2 class="mb-3">
            <?= $id ? 'Modifier un utilisateur' : 'Ajouter un utilisateur' ?>
        </h2>

        <form method="post" class="border p-3 rounded">
            <input type="hidden" name="id" value="<?= htmlspecialchars($id ?: '') ?>">

            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($nom) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($prenom) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Adresse</label>
                <input type="text" name="adresse" class="form-control" value="<?= htmlspecialchars($adresse) ?>"
                    required>
            </div>

            <button class="btn btn-primary">
                <?= $id ? 'Mettre à jour' : 'Enregistrer' ?>
            </button>
        </form>

        <!-- ========= Barre de recherche ========= -->
        <div class="mt-5">
            <h3 class="text-center mb-4">Rechercher un secrétaire</h3>

            <form method="get" class="row g-3 justify-content-center">
                <div class="col-auto">
                    <label class="visually-hidden" for="search">Nom ou prénom</label>
                    <input type="text" id="search" name="search" class="form-control"
                        placeholder="Entrez un nom ou un prénom" value="<?= htmlspecialchars($searchTerm) ?>">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary">Rechercher</button>
                </div>
            </form>
        </div>

        <!-- ========= Tableau ========= -->
        <h3 class="text-center mt-5">
            <?= $searchTerm ? 'Résultats de recherche' : 'Tous les secrétaires' ?>
        </h3>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Adresse</th>
                    <th width="160">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($secretaires): ?>
                <?php foreach ($secretaires as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['nom']) ?></td>
                    <td><?= htmlspecialchars($u['prenom']) ?></td>
                    <td><?= htmlspecialchars($u['adresse']) ?></td>
                    <td>
                        <a href="index.php?id=<?= $u['id'] ?>" class="me-2">Modifier</a>
                        <a href="index.php?delete=<?= $u['id'] ?>" class="link-danger"
                            onclick="return confirm('Supprimer ce secrétaire ?');">
                            Supprimer
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">Aucun résultat</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>