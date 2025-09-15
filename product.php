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
<style>
body { background: linear-gradient(135deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.6) 100%), url('assets/images/checkout.jpg'); 
    background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; }
.site-header { position: relative !important; z-index: 1000; }
.site-footer { position: relative !important; z-index: 1000; }
.region-section { margin-bottom: 60px; padding: 30px 0; }
.region-header { text-align: center; margin-bottom: 40px; padding: 25px; background: linear-gradient(135deg, rgba(30, 95, 63, 0.85), rgba(13, 61, 42, 0.85)); 
    border-radius: 15px; border-left: 5px solid #ff6b35; border-right: 5px solid #14a002; box-shadow: 0 8px 25px rgba(0,0,0,0.3); backdrop-filter: blur(10px); }
.region-title { color: #ffffff; font-size: 2.2rem; font-weight: 700; margin: 0 0 10px 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.8); }
.region-subtitle { color: #e0e0e0; font-size: 1.1rem; font-style: italic; margin: 0; font-weight: 500; text-shadow: 1px 1px 2px rgba(0,0,0,0.6); }
.products-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin: 24px 0; max-width: 1200px; margin-left: auto; margin-right: auto; }
.product-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 12px; text-align: center; box-shadow: 0 2px 6px rgba(0,0,0,0.08); 
    transition: all 0.3s ease; position: relative; overflow: hidden; width: 100%; height: 380px; display: flex; flex-direction: column; }
.product-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #14433e, #ff6b35); }
.product-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); border-color: #14433e; }
.product-card img { width: 100%; height: 150px; object-fit: cover; object-position: center; border-radius: 6px; margin-bottom: 8px; flex-shrink: 0; }
.product-card h3 { margin: 8px 0 6px; color: #14433e; font-size: 1.1rem; font-weight: 600; }
.product-card .price { color: #ff6b35; font-weight: bold; font-size: 1rem; margin: 6px 0; }
.product-card .description { color: #666; font-size: 0.85rem; line-height: 1.3; margin: 6px 0 6px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 1; 
    -webkit-box-orient: vertical; flex-grow: 1; min-height: 20px; }
.product-card .card-content { flex: 1; display: flex; flex-direction: column; justify-content: space-between; min-height: 0; }
.product-card .btn { margin-top: 8px; align-self: center; width: 90%; text-align: center; padding: 6px 12px; font-size: 0.8rem; }
.btn { display: inline-block; background: #14433e; color: #fff; padding: 8px 16px; border-radius: 5px; font-weight: 500; 
    text-decoration: none; border: 2px solid #14433e; transition: all 0.3s ease; font-size: 0.9rem; }
.btn:hover { background: #0f332f; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(20,67,62,0.3); }
.btn-outline { background: transparent; color: #14433e; border: 2px solid #14433e; }
.btn-outline:hover { background: #14433e; color: #fff; }
.page-title { color: #ffffff; font-size: 2.5rem; text-align: center; margin: 24px 0 40px; position: relative; text-shadow: 2px 2px 4px rgba(0,0,0,0.8); }
.page-title::after { content: ''; position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); width: 80px; height: 4px; background: #ff6b35; border-radius: 2px; }
.popup { position: fixed; top: 20px; right: 20px; background: rgba(255, 255, 255, 0.95); color: #14433e; padding: 12px 20px; border-radius: 8px; 
    box-shadow: 0 2px 10px rgba(0,0,0,0.15); opacity: 0; transition: all 0.3s ease; z-index: 9999; font-weight: 500; border-left: 3px solid #14433e; 
    transform: translateX(100%); backdrop-filter: blur(10px); font-size: 0.9rem; }
.popup.show { opacity: 1; transform: translateX(0); }
.popup i { margin-right: 6px; font-size: 1rem; color: #14433e; }
@media (max-width: 1200px) { .products-grid { grid-template-columns: repeat(3, 1fr); max-width: 900px; } }
@media (max-width: 900px) { .products-grid { grid-template-columns: repeat(2, 1fr); max-width: 600px; } }
@media (max-width: 768px) { .region-title { font-size: 1.8rem; } .region-subtitle { font-size: 1rem; } .products-grid { grid-template-columns: repeat(2, 1fr); 
    gap: 12px; max-width: 500px; } .product-card { height: 360px; } .product-card img { height: 110px; } .page-title { font-size: 2rem; } }
@media (max-width: 480px) { .products-grid { grid-template-columns: 1fr; max-width: 300px; } .product-card { height: 350px; } .product-card img { height: 110px; } 
    .popup { top: 10px; right: 10px; left: 10px; transform: translateY(-100%); padding: 10px 15px; font-size: 0.85rem; } 
    .popup.show { transform: translateY(0); } }
</style>

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