
<?php
require_once 'db.php';
require_once 'includes/functions.php';

require_role('talent');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: opportunities.php');
    exit;
}

check_csrf();

$opportunityId = (int) ($_POST['opportunity_id'] ?? 0);
$talentId = (int) $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT id
    FROM opportunities
    WHERE id = ?
      AND (deadline IS NULL OR deadline >= CURDATE())
");
$stmt->execute([$opportunityId]);

$opportunity = $stmt->fetch();

if (!$opportunity) {
    $_SESSION['flash'] = 'That opportunity is no longer available.';
    header('Location: opportunities.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO applications (talent_id, opportunity_id)
        VALUES (?, ?)
    ");

    $stmt->execute([$talentId, $opportunityId]);

    $_SESSION['flash'] = 'Your application has been submitted.';

} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        $_SESSION['flash'] = 'You have already applied for this opportunity.';
    } else {
        throw $e;
    }
}

header('Location: dashboard.php');
exit;
?>
