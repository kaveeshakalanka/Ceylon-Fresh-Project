<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us - Ceylon Fresh</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>
    
<style>
body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: url("assets/images/homepage.jpg") no-repeat center center fixed; background-size: cover; }
.site-header, .site-footer { position: relative !important; z-index: 1000; }
.overlay { background: rgba(0, 0, 0, 0.6); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
.container { max-width: 1200px; background: linear-gradient(rgba(5,5,5,0.6), rgba(0,0,0,0.8)); padding: 30px 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); color: #f5f4f4; }
h2 { color: #f5f4f4; text-align: center; margin-bottom: 20px; font-size: 2rem; }
.about-content { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
.about-text { padding: 20px; background: rgba(255, 255, 255, 0.05); border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.2); }
.about-image { text-align: center; padding: 10px; }
.about-image img { width: 100%; max-width: 100%; height: 280px; object-fit: cover; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
.about-text h3 { color: #33c216ff; margin-bottom: 15px; font-size: 1.3rem; }
.about-text p { line-height: 1.6; font-size: 14px; margin-bottom: 15px; color: #e0e0e0; }
.features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin: 30px 0; justify-items: center; max-width: 1000px; margin-left: auto; margin-right: auto; }
.feature-card { background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.2); text-align: center; transition: transform 0.3s ease; }
.feature-card:hover { transform: translateY(-5px); background: rgba(255, 255, 255, 0.1); }
.feature-card h4 { color: #ffffffff; margin-bottom: 10px; font-size: 1.1rem; }
.feature-card p { color: #ddd; font-size: 13px; line-height: 1.4; }
.mission-section { background: rgba(255, 255, 255, 0.05); padding: 25px; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.2); margin: 20px 0; text-align: center; }
.mission-section h3 { color: #58e03cff; margin-bottom: 15px; font-size: 1.4rem; }
.mission-section p { color: #e0e0e0; font-size: 15px; line-height: 1.6; }
@media (max-width: 1024px) { .about-content { grid-template-columns: 1fr; gap: 20px; } .features-grid { grid-template-columns: repeat(2, 1fr); max-width: 700px; } }
@media (max-width: 768px) { .container { padding: 20px; max-width: 95%; } h2 { font-size: 1.8rem; } .features-grid { grid-template-columns: 1fr; gap: 15px; max-width: 400px; } .about-image img { height: 220px; } }
</style>

<div class="overlay">
    <div class="container">
        <h2>About Ceylon Fresh</h2>

        <div class="about-content">
            <div class="about-text">
                <h3>Our Story</h3>
                <p>
                    Welcome to <strong>Ceylon Fresh</strong>, your trusted source for delicious, authentic Sri Lankan cuisine delivered straight to your home. 
                    Born out of a love for our island's rich culinary heritage, we aim to bring the bold flavors and traditional recipes of Sri Lanka to food lovers everywhere.
                </p>
                <p>
                    Founded in 2025, Ceylon Fresh emerged from a simple yet powerful vision: to preserve and share the authentic flavors of Sri Lanka with the world. 
                    Our journey began when our founders, passionate about Sri Lankan cuisine, realized that many people outside the island were missing out on the incredible diversity of our traditional dishes.
                </p>
            </div>
            <div class="about-image">
                <img src="assets/images/homepage.jpg" alt="Sri Lankan Traditional Cuisine">
            </div>
        </div>

        <div class="mission-section">
            <h3>Our Mission</h3>
            <p>
                We are committed to using fresh, locally sourced ingredients and traditional cooking techniques to ensure every bite takes you back to the heart of Ceylon. 
                Whether you are a Sri Lankan abroad missing the flavors of home or a food enthusiast looking to explore something new – we've got something for everyone.
            </p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <h4> Authentic Flavors</h4>
                <p>Traditional recipes passed down through generations, prepared with authentic Sri Lankan spices and cooking methods.</p>
            </div>
            <div class="feature-card">
                <h4> Fresh Ingredients</h4>
                <p>We source the freshest local ingredients and traditional spices to ensure the highest quality in every dish.</p>
            </div>
            <div class="feature-card">
                <h4> Regional Specialties</h4>
                <p>From Western coastal delicacies to Northern Tamil cuisine, we bring you the diverse flavors of all Sri Lankan regions.</p>
            </div>
            <div class="feature-card">
                <h4> Expert Chefs</h4>
                <p>Our team of experienced chefs specializes in regional Sri Lankan dishes, ensuring authentic preparation methods.</p>
            </div>
            <div class="feature-card">
                <h4> Fast Delivery</h4>
                <p>Quick and reliable delivery service to bring fresh, hot meals directly to your doorstep.</p>
            </div>
            <div class="feature-card">
                <h4> Quality Promise</h4>
                <p>We guarantee the quality and authenticity of every dish, with a satisfaction promise for all our customers.</p>
            </div>
        </div>

        <div class="about-content">
            <div class="about-image">
                <img src="assets/images/checkout.jpg" alt="Sri Lankan Food Preparation">
            </div>
            <div class="about-text">
                <h3>Our Commitment</h3>
                <p>
                    At Ceylon Fresh, we partner with local chefs and home cooks who specialize in regional Sri Lankan dishes – from spicy kottu and fragrant rice & curry to sweet delights like watalappam and kiri pani.
                </p>
                <p>
                    Our mission is to make it easy for you to experience the taste of Sri Lanka, wherever you are. We believe that food is not just nourishment, but a bridge that connects cultures and brings people together.
                </p>
                <p>
                    Thank you for choosing Ceylon Fresh. We look forward to serving you authentic, flavorful, and unforgettable meals that celebrate the rich culinary heritage of Sri Lanka!
                </p>
            </div>
        </div>
    </div>
</div>

   <?php include_once 'includes/footer.php'; ?>
    <script src="assets/js/script.js"></script>
</body>
</html>