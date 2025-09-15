<?php
session_start();
require_once __DIR__ . '/database/connection.php';

// Regional product categories with database category IDs
$regional_products = [
    'western' => [
        'name' => '🌴 Western Region',
        'subtitle' => 'Colombo & coastal delicacies',
        'category_id' => 1,
        'products' => [
            'Kottu Roti' => 'Chopped flatbread stir-fry with vegetables and meat',
            'Hoppers (Appa)' => 'Plain, egg, and milk hoppers - traditional Sri Lankan pancakes',
            'String Hoppers (Idiyappam)' => 'Steamed rice noodle cakes served with curry',
            'Lamprais' => 'Dutch Burgher rice dish wrapped in banana leaf',
            'Fish Ambul Thiyal' => 'Sour fish curry with tamarind and spices',
            'Cutlets & Patties' => 'Street food snacks with spiced fillings',
            'Pol Sambol' => 'Coconut sambol with chili and lime'
        ]
    ],
    'northern' => [
        'name' => '🌶 Northern Region',
        'subtitle' => 'Jaffna specialties & Tamil cuisine',
        'category_id' => 2,
        'products' => [
            'Jaffna Crab Curry' => 'Rich and spicy crab curry from Jaffna',
            'Odiyal Kool' => 'Traditional seafood porridge with palm flour',
            'Nandu Kulambu' => 'Authentic Jaffna crab curry',
            'Pittu' => 'Steamed coconut & rice flour cylinders',
            'Vadai' => 'Paruppu vadai, ulundu vadai - crispy lentil fritters',
            'Jaffna Mutton Curry' => 'Spicy mutton curry with Jaffna spices',
            'Thosai & Idli' => 'South Indian influenced rice and lentil dishes'
        ]
    ],
    'southern' => [
        'name' => '🐟 Southern Region',
        'subtitle' => 'Galle & Matara coastal flavors',
        'category_id' => 3,
        'products' => [
            'Ambul Thiyal (Southern)' => 'Southern style sour fish curry',
            'Maalu Baduma' => 'Fried fish curry with aromatic spices',
            'Kiribath with Lunu Miris' => 'Milk rice with chili sambol',
            'Coconut Roti with Lunu Miris' => 'Traditional coconut roti with spicy sambol',
            'Polos Curry' => 'Young jackfruit curry cooked in coconut milk',
            'Kalu Dodol' => 'Sweet dessert from Hambantota/Kalutara',
            'Halmilla Fish Curry' => 'Traditional fish curry with local spices'
        ]
    ],
    'central' => [
        'name' => '🍃 Central & Uva Region',
        'subtitle' => 'Hill country & Kandyan cuisine',
        'category_id' => 4,
        'products' => [
            'Kandyan Rice & Curry' => 'Traditional Kandyan style rice and curry',
            'Milk Rice with Honey' => 'Sweet milk rice drizzled with honey',
            'Kos Ata Curry' => 'Jackfruit seeds curry with coconut milk',
            'Bath Curry' => 'Red rice with chicken/mutton curries',
            'Thalaguli' => 'Sesame & jaggery sweet traditional confection',
            'Kevum & Kokis' => 'Traditional sweets for festive occasions',
            'Herbal Porridge (Kola Kenda)' => 'Traditional herbal porridge with medicinal herbs'
        ]
    ]
];

// Mapping product names to image files
$product_images = [
    'Kottu Roti' => 'assets/images/kottu.jpg',
    'Hoppers (Appa)' => 'assets/images/hoppers.jpg',
    'String Hoppers (Idiyappam)' => 'assets/images/string-hoppers.jpg',
    'Lamprais' => 'assets/images/lamprais.jpg',
    'Fish Ambul Thiyal' => 'assets/images/Ambul-thiyal.jpg',
    'Cutlets & Patties' => 'assets/images/cuttlets-patties.jpg',
    'Pol Sambol' => 'assets/images/Pol-sambol.jpg',
    'Jaffna Crab Curry' => 'assets/images/Crab-curry.jpg',
    'Odiyal Kool' => 'assets/images/Odiyal-kool.jpg',
    'Nandu Kulambu' => 'assets/images/Nandu-kulambu.jpg',
    'Pittu' => 'assets/images/Pittu.jpg',
    'Vadai' => 'assets/images/Ulundu-vadei.jpg',
    'Jaffna Mutton Curry' => 'assets/images/Mutton-curry.jpg',
    'Thosai & Idli' => 'assets/images/Thosai-idly.jpg',
    'Ambul Thiyal (Southern)' => 'assets/images/Malu-Thiyal.jpg',
    'Maalu Baduma' => 'assets/images/malu-baduma.jpg',
    'Kiribath with Lunu Miris' => 'assets/images/Kiribath-lunumiris.jpg',
    'Coconut Roti with Lunu Miris' => 'assets/images/Roti.jpg',
    'Polos Curry' => 'assets/images/Polos-curry.jpg',
    'Kalu Dodol' => 'assets/images/Kalu-dodol.jpg',
    'Halmilla Fish Curry' => 'assets/images/Halmilla-fish-curry.jpg',
    'Kandyan Rice & Curry' => 'assets/images/Kandyan-dishes.jpg',
    'Milk Rice with Honey' => 'assets/images/m-r.jpg',
    'Kos Ata Curry' => 'assets/images/kosata-kalupol-maluwa.jpg',
    'Bath Curry' => 'assets/images/bath-curry.jpg',
    'Thalaguli' => 'assets/images/Thalaguli.jpg',
    'Kevum & Kokis' => 'assets/images/keyum-kokis.jpg',
    'Herbal Porridge (Kola Kenda)' => 'assets/images/Kola-kanda.jpg'
];

//  Handle Add to Cart
if (isset($_GET['add_to_cart']) && ctype_digit($_GET['add_to_cart'])) {
    $id = (int)$_GET['add_to_cart'];

    $product = get_product_by_id($id);
    if ($product && $product['is_active'] == 1) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        // Check if product already in cart
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $product['product_id']) {
                $item['quantity'] += 1;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $product['product_id'],
                'name' => $product['product_name'],
                'price' => (float)$product['price'],
                'quantity' => 1
            ];
        }

        $_SESSION['popup_message'] = "✅ " . $product['product_name'] . " added to cart!";
    } else {
        $_SESSION['popup_message'] = "❌ Product not found or inactive!";
    }

    
    header("Location: product.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Products - Ceylon Fresh</title>
<link rel="stylesheet" href="assets/css/style.css">

</head>
<body>
    <?php include_once 'includes/header.php'; ?>
    
    <div style="padding: 20px;">
<?php if (isset($_SESSION['popup_message'])): ?>
<div id="popup" class="popup show">
    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['popup_message']); ?>
</div>
<script src="assets/js/script.js"></script>
<?php unset($_SESSION['popup_message']); endif; ?>

<h1 class="page-title">Our Regional Delicacies</h1>

<?php foreach ($regional_products as $region_key => $region): ?>
<section class="region-section">
    <div class="region-header">
        <h2 class="region-title"><?= $region['name'] ?></h2>
        <p class="region-subtitle"><?= $region['subtitle'] ?></p>
    </div>
    
    <div class="products-grid">
        <?php 
        // Get products from database
        $region_products_db = get_products_by_category($region['category_id']);
        $has_products = false;
        
        while ($product = $region_products_db->fetch_assoc()):
            $has_products = true;
            $product_id = (int)$product['product_id'];
            // Use database image if available 
            $image_src = $product['image_url'] ? $product['image_url'] : ($product_images[$product['product_name']] ?? 'assets/images/homepage.jpg');
        ?>
        <article class="product-card">
            <img src="<?= htmlspecialchars($image_src) ?>" 
                 alt="<?= htmlspecialchars($product['product_name']) ?>">
            <div class="card-content">
                <h3><?= htmlspecialchars($product['product_name']) ?></h3>
                <p class="price">Rs. <?= number_format($product['price'], 2) ?></p>
                <p class="description"><?= htmlspecialchars($product['description']) ?></p>
                <a class="btn btn-outline" href="product.php?add_to_cart=<?= $product_id ?>">Add to Cart</a>
            </div>
        </article>
        <?php endwhile; ?>
        
        <?php if (!$has_products): ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 1.1rem;">No products available in this region yet.</p>
            <p style="font-size: 0.9rem; margin-top: 10px;">Check back soon for new additions!</p>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endforeach; ?>
    </div>
    
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>