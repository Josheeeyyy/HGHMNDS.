<?php
session_start();
include 'includes/auth.php';
include 'includes/db.php'; // Ensure $pdo is initialized
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>HGHMNDS.</title>
    <link rel="icon" type="image/x-icon" href="assets/clothes.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/navbar.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="css/footer.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="css/index.css?v=<?= time(); ?>">
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body class="d-flex flex-column h-100">

    <!-- Session Alerts -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success text-center mb-0">
            <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger text-center mb-0">
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <main class="flex-shrink-0">
        <?php include 'includes/navbar.php'; ?>

        <!-- Banner Video -->
    <div class="banner-wrapper">
        <video autoplay muted loop playsinline class="w-100" style="max-height: 600px; object-fit: cover;">
            <source src="images/banner.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>



    <!-- Static New Drops Section -->
    <div class="container my-5 featured-products">
        <h3 class="mb-4">New Drops</h3>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-4 g-4">
            <?php
                $shoes = [
                ['img' => 'images/backpack.jpg', 'hover_img' => 'images/backpackhover.jpg', 'title' => 'NOVABLAST 5 LITE-SHOW', 'desc' => 'Men Shoes', 'price' => '₱150'],
                ['img' => 'images/longsleeve.jpg', 'hover_img' => 'images/longsleevehover.jpg', 'title' => 'NOVABLAST 5 LITE-SHOW', 'desc' => 'Women Shoes', 'price' => '₱130'],
                ['img' => 'images/short.jpg', 'hover_img' => 'images/shorthover.jpg', 'title' => 'GEL-CUMULUS 27 LITE-SHOW', 'desc' => 'Men Shoes', 'price' => '₱110'],
                ['img' => 'images/hoodie.jpg', 'hover_img' => 'images/hoodiehover.jpg', 'title' => 'GEL-CUMULUS 27 LITE-SHOW', 'desc' => 'Women Shoes', 'price' => '₱120'],
   ];
            foreach ($shoes as $shoe): ?>
                <div class="col">
                <div class="card product-card h-100">
        <div class="card-img-wrapper position-relative">
            <img src="<?= htmlspecialchars($shoe['img']); ?>" class="card-img-top main-img" alt="<?= htmlspecialchars($shoe['title']); ?>">
            <img src="<?= htmlspecialchars($shoe['hover_img']); ?>" class="card-img-top hover-img position-absolute top-0 start-0" alt="<?= htmlspecialchars($shoe['title']); ?>">
        </div>
        <div class="card-body d-flex flex-column text-center">
            <h5 class="card-title"><?= htmlspecialchars($shoe['title']); ?></h5>
            <p class="card-text text-muted small"><?= htmlspecialchars($shoe['desc']); ?></p>
            <p class="card-text fw-bold mt-auto"><?= htmlspecialchars($shoe['price']); ?></p>
        </div>
        </div>
        </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Full-Width Unified Promotional Banner -->
    <section class="promo-banners d-flex flex-wrap">
        <img src="images/banner1.jpg" alt="Banner 1" class="promo-img">
        <img src="images/banner2.jpg" alt="Banner 2" class="promo-img">
        <img src="images/banner3.jpg" alt="Banner 3" class="promo-img">
    </section>


    <?php include 'includes/footer.php'; ?>
    <?php if (file_exists('includes/user.php')) include 'includes/user.php'; ?>
</main>

</body>
</html>
