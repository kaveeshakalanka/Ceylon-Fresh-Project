<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - Ceylon Fresh</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once 'includes/header.php'; ?>
    
<style>
body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: url("assets/images/homepage.jpg") no-repeat center center fixed; background-size: cover; }
.site-header, .site-footer { position: relative !important; z-index: 1000; }
.overlay { background: rgba(0, 0, 0, 0.6); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
.container { max-width: 1200px; background: linear-gradient(rgba(5,5,5,0.6), rgba(0,0,0,0.8)); padding: 30px 40px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.3); color: #f5f4f4; }
h2 { color: #f5f4f4; text-align: center; margin-bottom: 20px; font-size: 2rem; }
.contact-info { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px; }
.contact-item { text-align: center; padding: 10px; background: rgba(255, 255, 255, 0.1); border-radius: 6px; border-left: 3px solid #27a727ff; }
.contact-item h3 { color: #f5f4f4; margin-bottom: 5px; font-size: 1rem; }
.contact-item p { color: #ddd; font-size: 0.85rem; line-height: 1.3; }
.contact-form { background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.2); }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
.form-group { margin-bottom: 12px; }
.form-group label { display: block; margin-bottom: 6px; color: #fcfcfc; font-weight: 500; }
.form-group input, .form-group textarea { width: 100%; padding: 10px 12px; border: 1px solid rgba(255, 255, 255, 0.3); 
    border-radius: 6px; font-size: 14px; background: rgba(255, 255, 255, 0.1); color: #f5f4f4; transition: border-color 0.3s ease; }
.form-group input:focus, .form-group textarea:focus { outline: none; border-color: #99711cff; box-shadow: 0 0 5px rgba(255, 107, 53, 0.3); }
.form-group textarea { min-height: 100px; resize: vertical; }
.btn-submit { background: linear-gradient(135deg, #137506ff, #28a728ff); color: white; padding: 10px 25px; 
    border: none; border-radius: 6px; font-size: 14px; font-weight: bold; cursor: pointer; transition: all 0.3s ease; width: 100%; }
.btn-submit:hover { background: linear-gradient(135deg, #324d28ff, #2b4b24ff); transform: translateY(-2px); }
@media (max-width: 1024px) { .contact-info { grid-template-columns: repeat(2, 1fr); gap: 12px; } }
@media (max-width: 768px) { .container { padding: 20px; max-width: 95%; } h2 { font-size: 1.8rem; } .contact-info { grid-template-columns: 1fr; gap: 10px; } .form-row { grid-template-columns: 1fr; gap: 10px; } }
</style>

<div class="overlay">
    <div class="container">
        <h2>Contact Us</h2>

        <div class="contact-info">
            <div class="contact-item">
                <h3>📧 Email</h3>
                <p>info@ceylonfresh.com<br>orders@ceylonfresh.com</p>
            </div>
            
            <div class="contact-item">
                <h3>📞 Phone</h3>
                <p>+94 11 234 5678<br>+94 77 123 4567</p>
            </div>
            
            <div class="contact-item">
                <h3>📍 Address</h3>
                <p>123 Galle Road<br>Colombo 03, Sri Lanka</p>
            </div>
            
            <div class="contact-item">
                <h3>🕒 Hours</h3>
                <p>Mon-Sat: 9:00 AM - 9:00 PM<br>Sunday: 10:00 AM - 8:00 PM</p>
            </div>
        </div>

        <div class="contact-form">
            <h3 style="color: #f5f4f4; margin-bottom: 15px; text-align: center;">Send us a Message</h3>
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" required></textarea>
                </div>
                
                <button type="submit" class="btn-submit">Send Message</button>
            </form>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
    <script src="assets/js/script.js"></script>
</body>
</html>