<?php
/**
 * ============================================================================
 * PAGE ADMINISTRATION - VERSION POO
 * ============================================================================
 *
 * Cette version utilise la Programmation Orientée Objet (POO).
 *
 * Changements par rapport à la version procédurale :
 * - Protection de page avec Auth::requireAdmin()
 * - Récupération des utilisateurs avec User::findAll()
 * - Code beaucoup plus court et lisible
 */

// Charger l'autoloader
require_once 'autoload.php';

// Importer les classes nécessaires
use App\Services\Auth;
use App\Services\Session;
use App\Models\User;

// Démarrer la session
Session::start();

// Protection : Administrateur requis
Auth::requireAdmin();

// Récupérer tous les utilisateurs
$users = User::findAll('id DESC');

// Inclure le header HTML
include_once 'header.php';
?>

<!-- ========================================================================== -->
<!-- CONTENU HTML DE LA PAGE -->
<!-- ========================================================================== -->

<main>

    <h2>Admin Dashboard - Version POO</h2>

    <p>Manage all users in the system.</p>

    <?php if (count($users) > 0): ?>

        <table border="1" cellpadding="10" cellspacing="0">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($users as $user): ?>

                    <tr>
                        <!-- ID -->
                        <td><?php echo htmlspecialchars($user->getId()); ?></td>

                        <!-- Email -->
                        <td><?php echo htmlspecialchars($user->getEmail()); ?></td>

                        <!-- Admin Status -->
                        <td><?php echo $user->isAdmin() ? 'Yes' : 'No'; ?></td>

                        <!-- Created At -->
                        <td><?php echo htmlspecialchars($user->getCreatedAt()); ?></td>

                        <!-- Actions -->
                        <td>
                            <a href="edit_user_poo.php?id=<?php echo $user->getId(); ?>">Edit</a>
                            |
                            <?php if ($user->getId() != Auth::id()): ?>
                                <?php if ($user->isAdmin()): ?>
                                    <a href="toggle_admin_poo.php?id=<?php echo $user->getId(); ?>"
                                       onclick="return confirm('Remove admin rights for this user?');">Remove Admin</a>
                                <?php else: ?>
                                    <a href="toggle_admin_poo.php?id=<?php echo $user->getId(); ?>"
                                       onclick="return confirm('Make this user an administrator?');">Make Admin</a>
                                <?php endif; ?>
                                |
                            <?php endif; ?>
                            <a href="delete_user_poo.php?id=<?php echo $user->getId(); ?>"
                               onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

        <p>
            <strong>Total users:</strong> <?php echo count($users); ?>
        </p>

    <?php else: ?>

        <p>No users found in the database.</p>

    <?php endif; ?>

    <p><a href="index.php">Back to Home</a></p>

</main>

</body>
</html>

<!--
===============================================================================
COMPARAISON PROCÉDURAL vs POO
===============================================================================

PROCÉDURAL (admin.php) - environ 120 lignes :
- Protection manuelle :
  if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
      header('Location: index.php');
      exit();
  }

- Récupération des utilisateurs :
  try {
      $stmt = $pdo->prepare("SELECT id, email, is_admin, created_at FROM users ORDER BY id DESC");
      $stmt->execute();
      $users = $stmt->fetchAll();
  } catch (PDOException $e) {
      $users = [];
      $error = "Error loading users.";
  }

- Affichage avec tableaux associatifs :
  $user['id'], $user['email'], $user['is_admin'], $user['created_at']

POO (admin_poo.php) - environ 100 lignes :
- Protection en une ligne :
  Auth::requireAdmin();

- Récupération en une ligne :
  $users = User::findAll('id DESC');

- Affichage avec objets et méthodes :
  $user->getId(), $user->getEmail(), $user->isAdmin(), $user->getCreatedAt()

AVANTAGES DE LA VERSION POO :
✅ Protection de page plus simple et sécurisée
✅ Récupération des données plus courte
✅ Méthodes expressives ($user->isAdmin() au lieu de $user['is_admin'] == 1)
✅ Auto-complétion dans l'IDE (grâce aux types)
✅ Moins d'erreurs (pas de typo sur les clés de tableau)
✅ Code plus maintenable

AVANTAGE DES OBJETS vs TABLEAUX :
- Tableau : $user['email'] (peut avoir une typo, pas d'auto-complétion)
- Objet : $user->getEmail() (auto-complétion, type-safe, pas de typo possible)

===============================================================================
-->
