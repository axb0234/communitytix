<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CommunityTix — Event Ticketing for Community Organisations</title>
    <meta name="description" content="Free, open-source event ticketing and member management for community centres, cultural associations, clubs and non-profits. RSVP events, paid tickets via PayPal, Pay What You Can pricing, blog, and more.">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url') }}">
    <meta property="og:title" content="CommunityTix — Event Ticketing for Community Organisations">
    <meta property="og:description" content="Free, open-source event ticketing and member management for community centres, cultural associations, clubs and non-profits.">
    <meta property="og:image" content="{{ asset('help-images/dashboard.png') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="CommunityTix — Event Ticketing for Community Organisations">
    <meta name="twitter:description" content="Free, open-source event ticketing and member management for community centres, cultural associations, clubs and non-profits.">
    <meta name="twitter:image" content="{{ asset('help-images/dashboard.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #18bc9c;
            --accent: #e74c3c;
        }
        body {
            font-family: 'Noto Sans', sans-serif;
            color: #333;
        }

        /* Navbar */
        .navbar-landing {
            background: var(--primary);
            padding: 1rem 0;
        }
        .navbar-landing .navbar-brand {
            color: #fff;
            font-weight: 700;
            font-size: 1.5rem;
        }
        .navbar-landing .navbar-brand:hover { color: #fff; }
        .navbar-landing .nav-link {
            color: rgba(255,255,255,0.85);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }
        .navbar-landing .nav-link:hover { color: var(--secondary); }
        .navbar-landing .btn-cta {
            background: var(--secondary);
            border-color: var(--secondary);
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 0.5rem 1.25rem;
            border-radius: 4px;
        }
        .navbar-landing .btn-cta:hover {
            background: #15a085;
            border-color: #15a085;
            color: #fff;
        }

        /* Hero */
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, #1a252f 100%);
            color: #fff;
            padding: 6rem 0 5rem;
        }
        .hero h1 {
            font-size: 2.75rem;
            font-weight: 700;
            line-height: 1.2;
            max-width: 700px;
        }
        .hero .lead {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin-top: 1.25rem;
        }
        .hero .btn-hero-primary {
            background: var(--secondary);
            border-color: var(--secondary);
            color: #fff;
            font-weight: 600;
            padding: 0.75rem 2rem;
            font-size: 1.05rem;
            border-radius: 4px;
        }
        .hero .btn-hero-primary:hover {
            background: #15a085;
            border-color: #15a085;
            color: #fff;
        }
        .hero .btn-hero-outline {
            border: 2px solid rgba(255,255,255,0.4);
            color: #fff;
            font-weight: 600;
            padding: 0.75rem 2rem;
            font-size: 1.05rem;
            border-radius: 4px;
        }
        .hero .btn-hero-outline:hover {
            border-color: #fff;
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        /* Trust Bar */
        .trust-bar {
            background: #f8f9fa;
            padding: 2rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        .trust-bar p {
            font-size: 1.05rem;
            color: #6c757d;
            font-weight: 500;
        }

        /* Section */
        .section-heading {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.75rem;
        }
        .section-subheading {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 2.5rem;
        }

        /* Feature Cards */
        .feature-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 2rem 1.5rem;
            text-align: center;
            height: 100%;
            transition: all 0.3s ease;
            background: #fff;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--secondary);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
        }
        .feature-card h5 {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.75rem;
        }
        .feature-card p {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        /* Screenshots */
        .browser-frame {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            overflow: hidden;
            border: 1px solid #dee2e6;
        }
        .browser-frame-bar {
            background: #e9ecef;
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .browser-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ccc;
        }
        .browser-dot.red { background: #e74c3c; }
        .browser-dot.yellow { background: #f39c12; }
        .browser-dot.green { background: #2ecc71; }
        .browser-frame img {
            width: 100%;
            display: block;
        }

        /* Steps */
        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--secondary);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        /* Demo Card */
        .demo-card {
            background: linear-gradient(135deg, var(--primary) 0%, #1a252f 100%);
            color: #fff;
            border-radius: 12px;
            padding: 3rem;
        }
        .demo-card .btn-demo {
            background: var(--secondary);
            border-color: var(--secondary);
            color: #fff;
            font-weight: 600;
            padding: 0.75rem 2rem;
            font-size: 1.05rem;
            border-radius: 4px;
        }
        .demo-card .btn-demo:hover {
            background: #15a085;
            border-color: #15a085;
            color: #fff;
        }
        .demo-card code {
            background: rgba(255,255,255,0.15);
            padding: 0.2rem 0.5rem;
            border-radius: 3px;
            color: var(--secondary);
        }

        /* Contact CTA */
        .contact-cta {
            background: #f8f9fa;
        }
        .contact-card {
            background: #fff;
            border-radius: 12px;
            padding: 3rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #e9ecef;
        }
        .contact-card .btn-contact {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
            font-weight: 600;
            padding: 0.75rem 2rem;
            font-size: 1.05rem;
            border-radius: 4px;
        }
        .contact-card .btn-contact:hover {
            background: #c0392b;
            border-color: #c0392b;
            color: #fff;
        }

        /* Footer */
        .footer-landing {
            background: var(--primary);
            color: rgba(255,255,255,0.7);
            padding: 2rem 0;
        }
        .footer-landing a { color: var(--secondary); }

        /* Responsive */
        @media (max-width: 768px) {
            .hero { padding: 4rem 0 3rem; }
            .hero h1 { font-size: 2rem; }
            .hero .lead { font-size: 1.05rem; }
            .section-heading { font-size: 1.5rem; }
            .demo-card { padding: 2rem; }
            .contact-card { padding: 2rem; }
        }
        @media (max-width: 576px) {
            .hero h1 { font-size: 1.6rem; }
            .hero .btn-hero-primary,
            .hero .btn-hero-outline { width: 100%; }
        }
    </style>
</head>
<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-landing sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-ticket-alt me-2"></i>CommunityTix</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#landingNav" aria-controls="landingNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="landingNav">
                <ul class="navbar-nav ms-auto me-3">
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#demo">Demo</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                </ul>
                <a href="https://demo.communitytix.org" class="btn btn-cta" target="_blank">Try Demo</a>
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="hero">
        <div class="container">
            <h1>Your community deserves better than spreadsheets and email chains</h1>
            <p class="lead">CommunityTix gives your organisation a professional event ticketing site with member management, Pay What You Can pricing, and everything you need to run events — completely free.</p>
            <div class="d-flex flex-wrap gap-3 mt-4">
                <a href="#demo" class="btn btn-hero-primary"><i class="fas fa-play me-2"></i>See Live Demo</a>
                <a href="#contact" class="btn btn-hero-outline"><i class="fas fa-envelope me-2"></i>Get in Touch</a>
            </div>
        </div>
    </section>

    {{-- Trust Bar --}}
    <section class="trust-bar">
        <div class="container text-center">
            <p class="mb-0"><i class="fas fa-building me-2"></i>Built for community centres, cultural associations, clubs &amp; non-profits</p>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="py-5">
        <div class="container">
            <div class="text-center">
                <h2 class="section-heading">Everything Your Organisation Needs</h2>
                <p class="section-subheading">One platform to manage events, members, and content — no technical skills required.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-calendar-alt"></i></div>
                        <h5>Events &amp; Ticketing</h5>
                        <p>Create free RSVP events or paid ticket events with secure PayPal checkout. Multiple ticket types, capacity limits, and automatic confirmation.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-heart"></i></div>
                        <h5>Pay What You Can</h5>
                        <p>Offer flexible pricing with suggested amounts so everyone can participate, regardless of budget. Perfect for community-focused events.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-users"></i></div>
                        <h5>Member Management</h5>
                        <p>Online signup, admin approval workflow, promote members to committee roles, and suspend or activate accounts as needed.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-newspaper"></i></div>
                        <h5>Blog &amp; Content</h5>
                        <p>Publish news and updates with a built-in WYSIWYG editor. Keep your community informed and engaged with rich content.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-tachometer-alt"></i></div>
                        <h5>Admin Dashboard</h5>
                        <p>At-a-glance stats, recent orders, event summaries, and CSV exports. Everything you need to manage your organisation in one place.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-paint-brush"></i></div>
                        <h5>Your Brand, Your Site</h5>
                        <p>Get your own subdomain, upload your logo, customise colours, and create a homepage that reflects your organisation's identity.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Screenshot Showcase --}}
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center">
                <h2 class="section-heading">See It in Action</h2>
                <p class="section-subheading">A clean, modern interface designed for real community organisations.</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-6">
                    <div class="browser-frame">
                        <div class="browser-frame-bar">
                            <div class="browser-dot red"></div>
                            <div class="browser-dot yellow"></div>
                            <div class="browser-dot green"></div>
                        </div>
                        <img src="{{ asset('help-images/dashboard.png') }}" alt="CommunityTix Admin Dashboard">
                    </div>
                    <p class="text-center text-muted mt-2 small">Admin Dashboard — stats, orders and event overview</p>
                </div>
                <div class="col-lg-6">
                    <div class="browser-frame">
                        <div class="browser-frame-bar">
                            <div class="browser-dot red"></div>
                            <div class="browser-dot yellow"></div>
                            <div class="browser-dot green"></div>
                        </div>
                        <img src="{{ asset('help-images/event-edit.png') }}" alt="CommunityTix Event Editor with PWYC">
                    </div>
                    <p class="text-center text-muted mt-2 small">Event Editor — tickets, PWYC pricing and flyer upload</p>
                </div>
                <div class="col-lg-6">
                    <div class="browser-frame">
                        <div class="browser-frame-bar">
                            <div class="browser-dot red"></div>
                            <div class="browser-dot yellow"></div>
                            <div class="browser-dot green"></div>
                        </div>
                        <img src="{{ asset('help-images/events-list.png') }}" alt="CommunityTix Public Events Page">
                    </div>
                    <p class="text-center text-muted mt-2 small">Public Events Page — your community-facing event listing</p>
                </div>
                <div class="col-lg-6">
                    <div class="browser-frame">
                        <div class="browser-frame-bar">
                            <div class="browser-dot red"></div>
                            <div class="browser-dot yellow"></div>
                            <div class="browser-dot green"></div>
                        </div>
                        <img src="{{ asset('help-images/members.png') }}" alt="CommunityTix Member Management">
                    </div>
                    <p class="text-center text-muted mt-2 small">Member Management — approve, promote and manage your community</p>
                </div>
            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section class="py-5">
        <div class="container">
            <div class="text-center">
                <h2 class="section-heading">How It Works</h2>
                <p class="section-subheading">Get your organisation online in three simple steps.</p>
            </div>
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="step-number">1</div>
                    <h5 class="fw-bold" style="color: var(--primary);">We Set You Up</h5>
                    <p class="text-muted">Get in touch and we'll create your subdomain, configure your branding, and hand you the keys to your admin panel.</p>
                </div>
                <div class="col-md-4">
                    <div class="step-number">2</div>
                    <h5 class="fw-bold" style="color: var(--primary);">Add Your Events</h5>
                    <p class="text-muted">Create events with ticket types, PWYC pricing, or free RSVPs. Upload flyers, write descriptions, and set capacity.</p>
                </div>
                <div class="col-md-4">
                    <div class="step-number">3</div>
                    <h5 class="fw-bold" style="color: var(--primary);">Sell Tickets &amp; Grow</h5>
                    <p class="text-muted">Share your event links, collect payments via PayPal, track orders, and build your community membership.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Live Demo --}}
    <section id="demo" class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="demo-card text-center">
                        <h2 class="fw-bold mb-3"><i class="fas fa-play-circle me-2"></i>Try the Live Demo</h2>
                        <p class="mb-3" style="opacity:0.9;">Explore everything CommunityTix has to offer — no signup needed. Browse events, view the admin dashboard, create a test event, and see PWYC pricing in action.</p>
                        <div class="mb-4">
                            <p class="mb-1"><strong>Demo Admin Login:</strong></p>
                            <p class="mb-0">Email: <code>admin@demo.communitytix.org</code></p>
                            <p>Password: <code>demo2025</code></p>
                        </div>
                        <a href="https://demo.communitytix.org" class="btn btn-demo" target="_blank"><i class="fas fa-external-link-alt me-2"></i>Open Demo Site</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Contact CTA --}}
    <section id="contact" class="contact-cta py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="contact-card text-center">
                        <h2 class="fw-bold mb-3" style="color: var(--primary);">Ready to Get Started?</h2>
                        <p class="text-muted mb-4">CommunityTix is free for community organisations. Get in touch and we'll have your site up and running in no time.</p>
                        <div class="mb-4">
                            <p class="mb-1"><i class="fas fa-user me-2" style="color: var(--secondary);"></i><strong>Anirban Bhaumik</strong></p>
                            <p class="mb-0"><i class="fas fa-envelope me-2" style="color: var(--secondary);"></i><a href="mailto:anirban@communitytix.org">anirban@communitytix.org</a></p>
                        </div>
                        <a href="mailto:anirban@communitytix.org?subject=Interested%20in%20CommunityTix" class="btn btn-contact"><i class="fas fa-paper-plane me-2"></i>Send an Email</a>
                        <p class="text-muted small mt-3 mb-0">We'll typically reply within 24 hours. No obligations, no sales pitch — just a conversation about what your organisation needs.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="footer-landing">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} CommunityTix. Powered by <a href="https://laravel.com" target="_blank">Laravel</a>.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Smooth scrolling for anchor links --}}
    <script>
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
    </script>
</body>
</html>
