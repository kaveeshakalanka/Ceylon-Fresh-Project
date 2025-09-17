<?php 
session_start();
$page_title = "Home"; 

// Regions data 
$regions = [
    "western" => [
        "title" => "Western Region",
        "meta" => "Colombo and coastal delicacies with vibrant street flavors.",
        "calories" => "60 calories",
        "serves" => "Serves 4",
        "desc" => "From lamprais to kottu, the Western region blends colonial influences with modern street eats.",
        "image" => "assets/images/western.jpg"
    ],
    "northern" => [
        "title" => "Northern Region",
        "meta" => "Jaffna specialties and Tamil cuisine rich in spices.",
        "calories" => "80 calories",
        "serves" => "Serves 3-4",
        "desc" => "Known for rich curries, earthy flavors and generous use of palmyrah and dried fish.",
        "image" => "assets/images/northern.jpg"
    ],
    "southern" => [
        "title" => "Southern Region",
        "meta" => "Galle and Matara coastal flavors with fresh seafood.",
        "calories" => "70 calories",
        "serves" => "Serves 4",
        "desc" => "Sun-kissed coasts inspire zesty sambols, coconut-forward curries and fresh seafood grills.",
        "image" => "assets/images/southern.jpg"
    ],
    "central" => [
        "title" => "Central Region",
        "meta" => "Kandy and hill-country specialties featuring hearty produce.",
        "calories" => "65 calories",
        "serves" => "Serves 3",
        "desc" => "Highland fare highlights hearty vegetables, fragrant rice dishes and teaside snacks.",
        "image" => "assets/images/central.jpg"
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $page_title; ?> - Ceylon Fresh</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-content">
    <h1>CEYLON FRESH</h1>
    <p>“Purely Fresh, Truly Sri Lankan”</p>
    <p>“Ceylon Fresh delivers authentic Sri Lankan traditional meals, made with the finest ingredients, straight from our kitchen to your table.”</p>
    <a href="product.php" class="btn btn-primary">Shop Now</a>
    <a href="about.php" class="btn btn-secondary">Learn More</a>
  </div>
  <div class="hero-image">
    <img src="assets/images/homepage1.jpg"alt="foods">
  </div>
</section>

<!-- Regions -->
<section class="product-showcase">
  <h2>Sri Lankan Regional Cuisine</h2>
  <div class="cuisine-cards">
    <?php foreach ($regions as $key => $region): ?>
      <div class="cuisine-card">
        <img src="<?= $region['image']; ?>" alt="<?= $region['title']; ?>">
        <h3><?= $region['title']; ?></h3>
        <p><?= $region['meta']; ?></p>
        <small><?= $region['calories']; ?> | <?= $region['serves']; ?></small>
        <p><?= $region['desc']; ?></p>
        <a href="product.php?region=<?= $key ?>" class="btn btn-primary">Explore</a>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<hr>

<!-- Features -->
<section class="features">
  <div class="features-grid">
    <div class="feature-card"><h3>  Fast Delivery</h3><p>Fresh produce delivered within 24 hours</p></div>
    <div class="feature-card"><h3> 100% Organic</h3><p>Certified organic and pesticide-free</p></div>
    <div class="feature-card"><h3> Premium Quality</h3><p>Hand-picked from the best farms</p></div>
    <div class="feature-card"><h3> Health First</h3><p>Nutritious options for your family</p></div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

    <script src="assets/js/script.js"></script>
</body>
</html>
