<?php
session_start();
include 'includes/auth.php';
include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/db.php';
include 'includes/user.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$formData = ['name' => '', 'email' => '', 'rating' => '', 'message' => ''];
$editFeedback = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : htmlspecialchars($_POST['email']);
    $rating = isset($_POST['rating']) && $_POST['rating'] !== '' ? (int)$_POST['rating'] : null;
    $message = htmlspecialchars($_POST['message']);

    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("UPDATE feedback SET name = ?, email = ?, rating = ?, message = ? WHERE id = ?");
        $stmt->execute([$name, $email, $rating, $message, $id]);
        $_SESSION['success'] = 'Feedback updated successfully!';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO feedback (name, email, rating, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $rating, $message]);
            $_SESSION['success'] = 'Thank you for your feedback!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
        }
    }

    $_POST = [];
    echo '<script>window.location.href = window.location.pathname;</script>';
    exit;
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM feedback WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    echo '<div class="alert alert-danger">Feedback deleted.</div>';
}

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM feedback WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editFeedback = $stmt->fetch();
}
?>

<link rel="stylesheet" href="css/feedback.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<div class="container my-5 feedback-page">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success text-center"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <h2 class="section-title text-center mb-5">Any Feedback is Highly Appreciated!</h2>

    <div class="row">
        <!-- Feedback Display -->
        <div class="col-md-6">
            <?php
            $stmt = $pdo->query("SELECT * FROM feedback ORDER BY submitted_at DESC");
            while ($row = $stmt->fetch()):
            ?>
                <div class="feedback-card p-3 mb-4 border rounded">
                    <h5><?= htmlspecialchars($row['name']) ?></h5>
                    <p class="text-muted small"><?= htmlspecialchars($row['email']) ?></p>
                    <?php if (!empty($row['rating'])): ?>
                        <div class="mb-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="bi <?= $i <= $row['rating'] ? 'bi-star-fill text-dark' : 'bi-star text-dark' ?>"></i>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                    <p><?= htmlspecialchars($row['message']) ?></p>
                    <small class="text-muted"><?= $row['submitted_at'] ?></small><br>
                    <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning mt-2 me-2">Edit</a>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger mt-2" onclick="return confirm('Are you sure?')">Delete</a>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Feedback Form -->
        <div class="col-md-6">
            <h4 class="mb-3 text-center"><?= $editFeedback ? 'Edit Feedback' : 'Leave Your Feedback' ?></h4>
            <form method="POST" id="feedbackForm" class="p-4 border rounded bg-light">
                <?php if ($editFeedback): ?>
                    <input type="hidden" name="id" value="<?= $editFeedback['id'] ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?= $editFeedback['name'] ?? '' ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email (optional)</label>
                    <input type="email" name="email" class="form-control" value="<?= $_SESSION['user_email'] ?? $editFeedback['email'] ?? '' ?>" <?= isset($_SESSION['user_email']) ? 'readonly' : '' ?>>
                </div>
                <div class="mb-3">
                    <label class="form-label d-block">Rating</label>
                    <div class="rating d-flex">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>" hidden <?= (isset($editFeedback['rating']) && $editFeedback['rating'] == $i) ? 'checked' : '' ?>>
                            <label for="star<?= $i ?>" class="me-1" style="cursor:pointer;">
                                <i class="bi bi-star-fill fs-4 text-dark" data-index="<?= $i ?>"></i>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Comment (optional)</label>
                    <textarea name="message" rows="4" class="form-control"><?= $editFeedback['message'] ?? '' ?></textarea>
                </div>
                <button class="btn btn-primary w-100"><?= $editFeedback ? 'Update Feedback' : 'Submit Feedback'; ?></button>
            </form>
        </div>
    </div>
</div>

<script>
    const stars = document.querySelectorAll('.rating i');
    let selected = document.querySelector('input[name=rating]:checked')?.value || 0;

    // Add event listeners to each star
    stars.forEach((star, index) => {
        star.addEventListener('mouseover', () => highlightStars(index + 1, true));
        star.addEventListener('mouseout', () => highlightStars(selected, false));
        star.addEventListener('click', () => {
            selected = index + 1;
            document.getElementById('star' + selected).checked = true;
            highlightStars(selected, false);
        });
    });

    // Highlight function for star states
    function highlightStars(rating, isHover = false) {
        stars.forEach((star, index) => {
            star.classList.remove('active', 'hovered');
            if (index < rating) {
                star.classList.add(isHover ? 'hovered' : 'active');
            }
        });
    }

    highlightStars(selected);
</script>




<?php include 'includes/footer.php'; ?>
