
<?php
require_once 'db.php';
require_once 'includes/functions.php';

require_role('talent');

// Applications must be submitted through a POST request.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: opportunities.php');
    exit;
}

// Verify that the request came from a valid form.
check_csrf();

$opportunityId = (int) ($_POST['opportunity_id'] ?? 0);
$talentId = (int) $_SESSION['user_id'];

// Check whether the opportunity exists and is still available.
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
    // Save the application.
    $stmt = $pdo->prepare("
        INSERT INTO applications (talent_id, opportunity_id)
        VALUES (?, ?)
    ");

    $stmt->execute([$talentId, $opportunityId]);

    $_SESSION['flash'] = 'Your application has been submitted.';

} catch (PDOException $e) {
    // Error code 23000 covers integrity constraint violations,
    // including the unique key preventing duplicate applications.
    if ($e->getCode() === '23000') {
        $_SESSION['flash'] = 'You have already applied for this opportunity.';
    } else {
        throw $e;
    }
}

// Return the talent to their dashboard.
header('Location: dashboard.php');
exit;
?>
