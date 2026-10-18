
<?php
require 'db.php';
require_once 'includes/functions.php';

require_role('talent');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: opportunities.php');
    exit;
}

check_csrf();

$id = (int)($_POST['opportunity_id'] ?? 0);

$q = $pdo->prepare(
    'SELECT id FROM opportunities
     WHERE id = ?
     AND (deadline IS NULL OR deadline >= CURDATE())'
);

$q->execute([$id]);

if (!$q->fetch()) {
    $_SESSION['flash'] = 'That opportunity is no longer available.';
} else {
    $q = $pdo->prepare(
        'SELECT id FROM applications
         WHERE talent_id = ? AND opportunity_id = ?'
    );

    $q->execute([$_SESSION['user_id'], $id]);

    if ($q->fetch()) {
        $_SESSION['flash'] = 'You have already applied for this opportunity.';
    } else {
        $q = $pdo->prepare(
            'INSERT INTO applications(talent_id, opportunity_id)
             VALUES(?, ?)'
        );

        $q->execute([$_SESSION['user_id'], $id]);

        $_SESSION['flash'] = 'Your application has been submitted.';
    }
}

header('Location: dashboard.php');
exit;
?>