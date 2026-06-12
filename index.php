<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>TechNova Solutions | Enterprise Web Hosting</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            line-height: 1.6;
            color: #2d3748;
            overflow-x: hidden;
        }

        /* Navigation */
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            letter-spacing: -0.5px;
        }

        .logo span {
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-left: 5px;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 500;
        }

        .nav-links a:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }

        .btn-login {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            backdrop-filter: blur(10px);
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.8rem;
            cursor: pointer;
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 0 20px;
        }

        .hero-content {
            max-width: 800px;
            animation: fadeInUp 1s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            font-weight: 800;
        }

        .hero-content p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2.5rem;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            display: inline-block;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        }

        /* Stats Bar */
        .stats-bar {
            background: white;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            position: relative;
            z-index: 10;
        }

        .stats-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #667eea;
        }

        .stat-label {
            color: #718096;
            font-size: 0.9rem;
        }

        /* Services Section */
        .services {
            padding: 5rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: #2d3748;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 1rem auto;
            border-radius: 2px;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .service-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .service-icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }

        .service-card h3 {
            margin-bottom: 1rem;
            color: #2d3748;
        }

        /* About Section */
        .about {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5rem 2rem;
            text-align: center;
        }

        .about-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .about h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .about p {
            font-size: 1.1rem;
            line-height: 1.8;
        }

        /* Footer */
        .footer {
            background: #1a202c;
            color: #a0aec0;
            padding: 3rem 2rem 1rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .footer-section h4 {
            color: white;
            margin-bottom: 1rem;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid #2d3748;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
                width: 100%;
                flex-direction: column;
                gap: 1rem;
                padding: 1rem 0;
            }

            .nav-links.show {
                display: flex;
            }

            .mobile-menu-btn {
                display: block;
            }

            .hero-content h1 {
                font-size: 2rem;
            }

            .section-title {
                font-size: 2rem;
            }
        }

        /* Animations */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .floating {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">🚀 TechNova Solutions <span>EST. 2026</span></div>
            <div class="nav-links" id="navLinks">
                <a href="#home">Home</a>
                <a href="#services">Services</a>
                <a href="#about">About</a>
                <a href="#contact">Contact</a>
                <a href="login.php" class="btn-login">🔐 Admin Portal</a>
            </div>
            <button class="mobile-menu-btn" onclick="toggleMenu()">☰</button>
        </div>
    </nav>

    <section id="home" class="hero">
        <div class="hero-content">
            <h1 class="floating">Innovative Tech Solutions</h1>
            <p>Enterprise-grade web hosting with military-grade security. Trusted by 5,000+ businesses worldwide.</p>
            <a href="login.php" class="btn-primary">Get Started →</a>
        </div>
    </section>

    <div class="stats-bar">
        <div class="stats-grid">
            <div>
                <div class="stat-number">99.9%</div>
                <div class="stat-label">Uptime Guarantee</div>
            </div>
            <div>
                <div class="stat-number">24/7</div>
                <div class="stat-label">Support</div>
            </div>
            <div>
                <div class="stat-number">5,000+</div>
                <div class="stat-label">Happy Clients</div>
            </div>
            <div>
                <div class="stat-number">150ms</div>
                <div class="stat-label">Avg Response Time</div>
            </div>
        </div>
    </div>

    <section id="services" class="services">
        <h2 class="section-title">Our Premium Services</h2>
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">🖥️</div>
                <h3>Cloud Web Hosting</h3>
                <p>Lightning-fast SSD servers with unlimited bandwidth and 24/7 monitoring.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🔒</div>
                <h3>SSL Security Suite</h3>
                <p>Free SSL certificates with automatic renewal and DDoS protection.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">💾</div>
                <h3>Automated Backups</h3>
                <p>Daily backups with 30-day retention and one-click restore.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🌐</div>
                <h3>Domain Management</h3>
                <p>Custom DNS configuration, nameserver setup, and domain forwarding.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">⚡</div>
                <h3>CDN Integration</h3>
                <p>Global content delivery network for faster loading worldwide.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🛡️</div>
                <h3>Advanced Firewall</h3>
                <p>WAF protection with real-time threat detection and blocking.</p>
            </div>
        </div>
    </section>

    <section id="about" class="about">
        <div class="about-content">
            <h2>About TechNova Solutions</h2>
            <p>Founded in 2026, TechNova Solutions has rapidly grown into a leading provider of secure web hosting and
                IT infrastructure solutions. Our mission is to empower businesses with reliable, scalable, and secure
                hosting services. With state-of-the-art data centers and a team of certified experts, we deliver 99.9%
                uptime and 24/7 customer support. We're not just a hosting provider - we're your technology partner.</p>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>TechNova Solutions</h4>
                <p>Empowering businesses with cutting-edge hosting solutions since 2026.</p>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <p><a href="#home" style="color:#a0aec0; text-decoration:none;">Home</a></p>
                <p><a href="#services" style="color:#a0aec0; text-decoration:none;">Services</a></p>
                <p><a href="#about" style="color:#a0aec0; text-decoration:none;">About</a></p>
            </div>
            <div class="footer-section">
                <h4>Contact</h4>
                <p>📧 info@technova.com</p>
                <p>📞 +1 (555) 123-4567</p>
                <p>📍 123 Tech Street, Silicon Valley, CA</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 TechNova Solutions. All rights reserved. | Secured with 🔒 SSL Encryption</p>
        </div>
    </footer>

    <script>
        function toggleMenu() {
            const navLinks = document.getElementById('navLinks');
            navLinks.classList.toggle('show');
        }

        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                    if (window.innerWidth <= 768) {
                        document.getElementById('navLinks').classList.remove('show');
                    }
                }
            });
        });

        // Animate stats on scroll
        const observerOptions = {
            threshold: 0.5
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const stats = document.querySelectorAll('.stat-number');
                    stats.forEach(stat => {
                        const finalValue = stat.innerText;
                        stat.innerText = '0';
                        let current = 0;
                        const target = parseFloat(finalValue);
                        const increment = target / 50;
                        const timer = setInterval(() => {
                            current += increment;
                            if (current >= target) {
                                stat.innerText = finalValue;
                                clearInterval(timer);
                            } else {
                                stat.innerText = Math.floor(current) + (finalValue.includes('%') ? '%' : '');
                            }
                        }, 30);
                    });
                    observer.disconnect();
                }
            });
        }, observerOptions);

        observer.observe(document.querySelector('.stats-bar'));

        // Session storage check
        console.log('TechNova Solutions Loaded - ' + new Date().toLocaleString());
    </script>
</body>

</html>