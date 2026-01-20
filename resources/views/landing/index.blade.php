@extends('layouts.landing')

@section('content')
<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg fixed-top navbar-custom sticky sticky-dark" id="navbar">
    <div class="container">
        <a class="navbar-brand logo" href="{{ route('landing') }}">
            <span class="fw-bold fs-4 text-primary">Ledgerly</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="mdi mdi-menu"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item active">
                    <a href="#home" class="nav-link">Home</a>
                </li>
                <li class="nav-item">
                    <a href="#features" class="nav-link">Features</a>
                </li>
                <li class="nav-item">
                    <a href="#how-it-works" class="nav-link">How It Works</a>
                </li>
                <li class="nav-item">
                    <a href="#showcase" class="nav-link">Showcase</a>
                </li>
                <li class="nav-item">
                    <a href="#pricing" class="nav-link">Pricing</a>
                </li>
            </ul>
            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-rounded navbar-btn ms-2">Sign In</a>
            <a href="{{ route('register') }}" class="btn btn-primary btn-rounded navbar-btn ms-2">Get Started Free</a>
        </div>
    </div>
</nav>
<!-- Navbar End -->

<!-- Hero Section Start -->
<section class="hero-section-modern" id="home">
    <!-- Animated Background Shapes -->
    <div class="hero-bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        <div class="shape shape-4"></div>
    </div>

    <div class="container">
        <div class="row align-items-center min-vh-100 py-5">
            <div class="col-lg-6">
                <div class="hero-content">
                    <!-- Badge Pill -->
                    <div class="hero-badge mb-3" data-aos="fade-up">
                        <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fs-6">
                            <i data-feather="zap" class="me-1" style="width:14px;height:14px;"></i>
                            100% Free Forever
                        </span>
                    </div>

                    <!-- Animated Title -->
                    <h1 class="hero-title display-4 fw-bold mb-4" data-aos="fade-up" data-aos-delay="100">
                        Take Control of Your
                        <span class="text-gradient">Financial Future</span>
                    </h1>

                    <!-- Description -->
                    <p class="lead text-muted mb-4" data-aos="fade-up" data-aos-delay="200">
                        Ledgerly helps you master your money. Track income & expenses, visualize spending patterns with beautiful charts, manage multiple accounts, and collaborate with family—all in one powerful platform.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="d-flex flex-wrap gap-3 mb-5" data-aos="fade-up" data-aos-delay="300">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg btn-hover-animate px-4">
                            <i data-feather="rocket" class="me-2" style="width:18px;height:18px;"></i>Start Free Today
                        </a>
                        <a href="#features" class="btn btn-outline-dark btn-lg px-4">
                            <i data-feather="play-circle" class="me-2" style="width:18px;height:18px;"></i>See Features
                        </a>
                    </div>

                    <!-- Mini Stats Row -->
                    <div class="row g-4" data-aos="fade-up" data-aos-delay="400">
                        <div class="col-auto">
                            <div class="d-flex align-items-center">
                                <div class="mini-stat-icon bg-soft-success rounded-circle me-2">
                                    <i data-feather="check" class="text-success"></i>
                                </div>
                                <div>
                                    <span class="fw-bold d-block">IQD & USD</span>
                                    <small class="text-muted">Multi-Currency</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex align-items-center">
                                <div class="mini-stat-icon bg-soft-primary rounded-circle me-2">
                                    <i data-feather="clock" class="text-primary"></i>
                                </div>
                                <div>
                                    <span class="fw-bold d-block">24/7</span>
                                    <small class="text-muted">Access Anywhere</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex align-items-center">
                                <div class="mini-stat-icon bg-soft-info rounded-circle me-2">
                                    <i data-feather="users" class="text-info"></i>
                                </div>
                                <div>
                                    <span class="fw-bold d-block">Team</span>
                                    <small class="text-muted">Collaboration</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mt-5 mt-lg-0">
                <!-- Floating Dashboard Mockup -->
                <div class="hero-mockup-wrapper" data-aos="fade-left" data-aos-delay="200">
                    <div class="hero-mockup floating-animation">
                        <!-- Browser Frame -->
                        <div class="browser-mockup shadow-lg">
                            <div class="browser-header">
                                <div class="browser-dots">
                                    <span class="dot dot-red"></span>
                                    <span class="dot dot-yellow"></span>
                                    <span class="dot dot-green"></span>
                                </div>
                                <div class="browser-url">
                                    <i data-feather="lock" style="width:12px;height:12px;" class="me-1 text-success"></i>
                                    ledgerly.com/dashboard
                                </div>
                            </div>
                            <div class="browser-body">
                                <!-- Dashboard Preview -->
                                <div class="dashboard-preview p-3">
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="preview-card bg-success bg-opacity-10 rounded-3 p-2">
                                                <small class="text-muted d-block">Total Income</small>
                                                <h6 class="mb-0 text-success fw-bold">5,250,000 IQD</h6>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="preview-card bg-danger bg-opacity-10 rounded-3 p-2">
                                                <small class="text-muted d-block">Total Expense</small>
                                                <h6 class="mb-0 text-danger fw-bold">3,180,000 IQD</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="preview-chart bg-light rounded-3 p-3 text-center">
                                        <div class="chart-placeholder">
                                            <div class="donut-chart mx-auto">
                                                <div class="donut-inner">
                                                    <span class="fw-bold text-primary">65%</span>
                                                    <small class="d-block text-muted">Saved</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Cards -->
                    <div class="floating-card floating-card-income floating-animation-delayed">
                        <i data-feather="trending-up" class="text-success me-2"></i>
                        <span class="fw-semibold text-success">+2,500,000</span>
                    </div>
                    <div class="floating-card floating-card-expense floating-animation-delayed-2">
                        <i data-feather="trending-down" class="text-danger me-2"></i>
                        <span class="fw-semibold text-danger">-1,200,000</span>
                    </div>
                    <div class="floating-card floating-card-balance floating-animation-delayed-3">
                        <i data-feather="wallet" class="text-primary me-2"></i>
                        <span class="fw-semibold text-primary">Balance</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Hero Section End -->

<!-- Features Section Start (Applock-Style) -->
<section class="section bg-light" id="features">
    <div class="container">
        <!-- Section Header -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill mb-3" data-aos="fade-up">POWERFUL FEATURES</span>
                <h2 class="fw-bold mb-3" data-aos="fade-up" data-aos-delay="100">Everything You Need to Master Your Finances</h2>
                <div class="title-underline mx-auto" data-aos="fade-up" data-aos-delay="150"></div>
                <p class="text-muted mt-3" data-aos="fade-up" data-aos-delay="200">Ledgerly combines simplicity with powerful features to give you complete financial visibility and control.</p>
            </div>
        </div>

        <!-- Applock-Style Features Layout -->
        <div class="row align-items-center">
            <!-- Left Features -->
            <div class="col-lg-4 col-md-6">
                <div class="feature-item-left" data-aos="fade-right" data-aos-delay="100">
                    <div class="feature-icon-wrapper bg-soft-primary">
                        <i data-feather="repeat" class="text-primary"></i>
                    </div>
                    <div class="feature-content">
                        <h5 class="mb-2">Transaction Management</h5>
                        <p class="text-muted mb-0">Record income, expenses, and transfers. Filter, search, and categorize everything effortlessly.</p>
                    </div>
                </div>

                <div class="feature-item-left" data-aos="fade-right" data-aos-delay="200">
                    <div class="feature-icon-wrapper bg-soft-success">
                        <i data-feather="trending-up" class="text-success"></i>
                    </div>
                    <div class="feature-content">
                        <h5 class="mb-2">Income Sources</h5>
                        <p class="text-muted mb-0">Manage multiple accounts—cash, bank, cards. Track balances in real-time automatically.</p>
                    </div>
                </div>

                <div class="feature-item-left" data-aos="fade-right" data-aos-delay="300">
                    <div class="feature-icon-wrapper bg-soft-info">
                        <i data-feather="tag" class="text-info"></i>
                    </div>
                    <div class="feature-content">
                        <h5 class="mb-2">Expense Categories</h5>
                        <p class="text-muted mb-0">Organize by type—rent, food, utilities. Identify savings opportunities instantly.</p>
                    </div>
                </div>
            </div>

            <!-- Center Mobile/Dashboard Image -->
            <div class="col-lg-4 text-center my-5 my-lg-0" data-aos="zoom-in">
                <div class="center-mockup">
                    <div class="phone-mockup">
                        <div class="phone-screen">
                            <div class="phone-header bg-primary text-white p-2">
                                <small class="fw-semibold">Ledgerly</small>
                            </div>
                            <div class="phone-content p-2">
                                <div class="mini-card bg-success bg-opacity-10 rounded p-2 mb-2">
                                    <small class="text-success fw-bold">+500K IQD</small>
                                </div>
                                <div class="mini-card bg-danger bg-opacity-10 rounded p-2 mb-2">
                                    <small class="text-danger fw-bold">-200K IQD</small>
                                </div>
                                <div class="mini-chart-bars">
                                    <div class="bar bar-1"></div>
                                    <div class="bar bar-2"></div>
                                    <div class="bar bar-3"></div>
                                    <div class="bar bar-4"></div>
                                    <div class="bar bar-5"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Features -->
            <div class="col-lg-4 col-md-6">
                <div class="feature-item-right" data-aos="fade-left" data-aos-delay="100">
                    <div class="feature-icon-wrapper bg-soft-warning">
                        <i data-feather="bar-chart-2" class="text-warning"></i>
                    </div>
                    <div class="feature-content">
                        <h5 class="mb-2">Visual Analytics</h5>
                        <p class="text-muted mb-0">Beautiful charts show your financial health. Income vs expense, monthly trends, breakdowns.</p>
                    </div>
                </div>

                <div class="feature-item-right" data-aos="fade-left" data-aos-delay="200">
                    <div class="feature-icon-wrapper bg-soft-danger">
                        <i data-feather="users" class="text-danger"></i>
                    </div>
                    <div class="feature-content">
                        <h5 class="mb-2">Team Collaboration</h5>
                        <p class="text-muted mb-0">Share with family or team. Role-based permissions keep everyone organized.</p>
                    </div>
                </div>

                <div class="feature-item-right" data-aos="fade-left" data-aos-delay="300">
                    <div class="feature-icon-wrapper bg-soft-primary">
                        <i data-feather="file-text" class="text-primary"></i>
                    </div>
                    <div class="feature-content">
                        <h5 class="mb-2">PDF Reports</h5>
                        <p class="text-muted mb-0">Export detailed financial reports. Monthly summaries, custom date ranges, category breakdowns.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Features Grid -->
        <div class="row mt-5 pt-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card-modern feature-card-primary">
                    <div class="feature-card-icon">
                        <i data-feather="dollar-sign"></i>
                    </div>
                    <h5>Multi-Currency</h5>
                    <p class="text-muted mb-0">Choose IQD or USD at registration. Switch currencies anytime from settings.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card-modern feature-card-success">
                    <div class="feature-card-icon">
                        <i data-feather="smartphone"></i>
                    </div>
                    <h5>Mobile-First Design</h5>
                    <p class="text-muted mb-0">Fully responsive on any device. Manage finances on phone, tablet, or desktop.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card-modern feature-card-info">
                    <div class="feature-card-icon">
                        <i data-feather="shield"></i>
                    </div>
                    <h5>Secure & Private</h5>
                    <p class="text-muted mb-0">Account-scoped tenancy, role-based permissions, CSRF protection, encrypted sessions.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Features Section End -->

<!-- How It Works Section Start -->
<section class="section" id="how-it-works">
    <div class="container">
        <!-- Section Header -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <span class="badge bg-soft-success text-success px-3 py-2 rounded-pill mb-3" data-aos="fade-up">SIMPLE PROCESS</span>
                <h2 class="fw-bold mb-3" data-aos="fade-up" data-aos-delay="100">Get Started in Minutes</h2>
                <div class="title-underline mx-auto bg-success" data-aos="fade-up" data-aos-delay="150"></div>
                <p class="text-muted mt-3" data-aos="fade-up" data-aos-delay="200">Ledgerly is designed to be simple. Here's how easy it is to get started.</p>
            </div>
        </div>

        <!-- Timeline Steps -->
        <div class="steps-timeline">
            <div class="row">
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div class="step-connector d-none d-lg-block"></div>
                        <div class="step-content">
                            <div class="step-icon bg-primary">
                                <i data-feather="user-plus" class="text-white"></i>
                            </div>
                            <h5 class="mt-4">Sign Up Free</h5>
                            <p class="text-muted">Create your free account in seconds. Choose your preferred currency (IQD or USD) and start immediately. No credit card required.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="step-item">
                        <div class="step-number step-number-success">2</div>
                        <div class="step-connector d-none d-lg-block"></div>
                        <div class="step-content">
                            <div class="step-icon bg-success">
                                <i data-feather="settings" class="text-white"></i>
                            </div>
                            <h5 class="mt-4">Set Up Workspace</h5>
                            <p class="text-muted">Add your income sources (cash, bank accounts) and create expense types. Your workspace is automatically ready to use.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="step-item">
                        <div class="step-number step-number-info">3</div>
                        <div class="step-content">
                            <div class="step-icon bg-info">
                                <i data-feather="play" class="text-white"></i>
                            </div>
                            <h5 class="mt-4">Start Tracking</h5>
                            <p class="text-muted">Record your first transaction. Watch your dashboard come alive with charts and insights. Invite family to collaborate.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- How It Works Section End -->

<!-- Dashboard Showcase Section Start -->
<section class="section bg-light" id="showcase">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5" data-aos="fade-right">
                <span class="badge bg-soft-info text-info px-3 py-2 rounded-pill mb-3">DASHBOARD</span>
                <h2 class="fw-bold mb-4">Real-Time Financial Overview</h2>
                <p class="text-muted mb-4">Your dashboard is your financial command center. See income, expenses, and net balance at a glance.</p>

                <!-- Vertical Tab Navigation -->
                <ul class="nav nav-pills showcase-tabs flex-column" role="tablist">
                    <li class="nav-item mb-2">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-charts" role="tab">
                            <i data-feather="pie-chart" class="me-2"></i>
                            Visual Charts
                            <span class="tab-arrow"><i data-feather="chevron-right"></i></span>
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-transactions" role="tab">
                            <i data-feather="list" class="me-2"></i>
                            Transaction List
                            <span class="tab-arrow"><i data-feather="chevron-right"></i></span>
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-collaboration" role="tab">
                            <i data-feather="users" class="me-2"></i>
                            Team Collaboration
                            <span class="tab-arrow"><i data-feather="chevron-right"></i></span>
                        </a>
                    </li>
                </ul>

                <div class="mt-4">
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        Explore Dashboard <i data-feather="arrow-right" class="ms-2" style="width:16px;height:16px;"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-7 mt-5 mt-lg-0" data-aos="fade-left">
                <!-- Browser Mockup with Tab Content -->
                <div class="browser-mockup-large shadow-lg">
                    <div class="browser-header">
                        <div class="browser-dots">
                            <span class="dot dot-red"></span>
                            <span class="dot dot-yellow"></span>
                            <span class="dot dot-green"></span>
                        </div>
                        <div class="browser-url">
                            <i data-feather="lock" style="width:12px;height:12px;" class="me-1 text-success"></i>
                            ledgerly.com/dashboard
                        </div>
                    </div>
                    <div class="browser-body tab-content">
                        <!-- Charts Tab -->
                        <div class="tab-pane fade show active" id="tab-charts" role="tabpanel">
                            <div class="showcase-content p-4">
                                <div class="row g-3 mb-3">
                                    <div class="col-3">
                                        <div class="stat-box bg-primary bg-opacity-10 rounded p-2 text-center">
                                            <small class="text-muted d-block">Income</small>
                                            <span class="text-primary fw-bold">5.2M</span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="stat-box bg-danger bg-opacity-10 rounded p-2 text-center">
                                            <small class="text-muted d-block">Expense</small>
                                            <span class="text-danger fw-bold">3.1M</span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="stat-box bg-success bg-opacity-10 rounded p-2 text-center">
                                            <small class="text-muted d-block">Balance</small>
                                            <span class="text-success fw-bold">2.1M</span>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="stat-box bg-info bg-opacity-10 rounded p-2 text-center">
                                            <small class="text-muted d-block">Savings</small>
                                            <span class="text-info fw-bold">40%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="chart-area bg-white rounded p-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="donut-chart-large mx-auto">
                                                <div class="donut-inner">
                                                    <i data-feather="pie-chart" class="text-primary" style="width:40px;height:40px;"></i>
                                                </div>
                                            </div>
                                            <p class="text-center text-muted mt-2 mb-0 small">Income vs Expense</p>
                                        </div>
                                        <div class="col-6">
                                            <div class="bar-chart-preview">
                                                <div class="bar-row"><div class="bar-fill" style="width:80%;"></div><span>Jan</span></div>
                                                <div class="bar-row"><div class="bar-fill" style="width:65%;"></div><span>Feb</span></div>
                                                <div class="bar-row"><div class="bar-fill" style="width:90%;"></div><span>Mar</span></div>
                                                <div class="bar-row"><div class="bar-fill" style="width:75%;"></div><span>Apr</span></div>
                                            </div>
                                            <p class="text-center text-muted mt-2 mb-0 small">Monthly Trends</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transactions Tab -->
                        <div class="tab-pane fade" id="tab-transactions" role="tabpanel">
                            <div class="showcase-content p-4">
                                <div class="transaction-list">
                                    <div class="transaction-item d-flex align-items-center p-2 bg-white rounded mb-2">
                                        <div class="transaction-icon bg-success bg-opacity-10 rounded-circle me-3">
                                            <i data-feather="arrow-down-left" class="text-success"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <strong>Salary Income</strong>
                                            <small class="d-block text-muted">Bank Account</small>
                                        </div>
                                        <span class="text-success fw-bold">+2,500,000</span>
                                    </div>
                                    <div class="transaction-item d-flex align-items-center p-2 bg-white rounded mb-2">
                                        <div class="transaction-icon bg-danger bg-opacity-10 rounded-circle me-3">
                                            <i data-feather="arrow-up-right" class="text-danger"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <strong>Rent Payment</strong>
                                            <small class="d-block text-muted">Housing</small>
                                        </div>
                                        <span class="text-danger fw-bold">-800,000</span>
                                    </div>
                                    <div class="transaction-item d-flex align-items-center p-2 bg-white rounded mb-2">
                                        <div class="transaction-icon bg-danger bg-opacity-10 rounded-circle me-3">
                                            <i data-feather="arrow-up-right" class="text-danger"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <strong>Groceries</strong>
                                            <small class="d-block text-muted">Food</small>
                                        </div>
                                        <span class="text-danger fw-bold">-150,000</span>
                                    </div>
                                    <div class="transaction-item d-flex align-items-center p-2 bg-white rounded">
                                        <div class="transaction-icon bg-info bg-opacity-10 rounded-circle me-3">
                                            <i data-feather="repeat" class="text-info"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <strong>Transfer</strong>
                                            <small class="d-block text-muted">Cash to Bank</small>
                                        </div>
                                        <span class="text-info fw-bold">500,000</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Collaboration Tab -->
                        <div class="tab-pane fade" id="tab-collaboration" role="tabpanel">
                            <div class="showcase-content p-4">
                                <div class="team-members">
                                    <div class="team-member d-flex align-items-center p-2 bg-white rounded mb-2">
                                        <div class="member-avatar bg-primary text-white rounded-circle me-3">JD</div>
                                        <div class="flex-grow-1">
                                            <strong>John Doe</strong>
                                            <small class="d-block text-muted">Owner</small>
                                        </div>
                                        <span class="badge bg-primary">Full Access</span>
                                    </div>
                                    <div class="team-member d-flex align-items-center p-2 bg-white rounded mb-2">
                                        <div class="member-avatar bg-success text-white rounded-circle me-3">AS</div>
                                        <div class="flex-grow-1">
                                            <strong>Alice Smith</strong>
                                            <small class="d-block text-muted">Admin</small>
                                        </div>
                                        <span class="badge bg-success">Admin</span>
                                    </div>
                                    <div class="team-member d-flex align-items-center p-2 bg-white rounded mb-2">
                                        <div class="member-avatar bg-info text-white rounded-circle me-3">BJ</div>
                                        <div class="flex-grow-1">
                                            <strong>Bob Johnson</strong>
                                            <small class="d-block text-muted">Member</small>
                                        </div>
                                        <span class="badge bg-info">Member</span>
                                    </div>
                                    <div class="team-member d-flex align-items-center p-2 bg-white rounded">
                                        <div class="member-avatar bg-secondary text-white rounded-circle me-3">CW</div>
                                        <div class="flex-grow-1">
                                            <strong>Carol White</strong>
                                            <small class="d-block text-muted">Viewer</small>
                                        </div>
                                        <span class="badge bg-secondary">View Only</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Dashboard Showcase Section End -->

<!-- Stats Counter Section Start -->
<section class="section bg-counter-parallax">
    <div class="container position-relative">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto" data-aos="fade-up">
                <h3 class="text-white mb-3">Trusted by Users Worldwide</h3>
                <p class="text-white-50 lead">Join a growing community of users taking control of their finances with Ledgerly.</p>
            </div>
        </div>

        <div class="row" id="counter">
            <div class="col-lg-3 col-6" data-aos="fade-up" data-aos-delay="100">
                <div class="counter-card text-center">
                    <div class="counter-icon-modern">
                        <i data-feather="users"></i>
                    </div>
                    <h2 class="counter-value text-white mt-3" data-count="10000">0</h2>
                    <span class="text-white-50">Active Users</span>
                </div>
            </div>

            <div class="col-lg-3 col-6" data-aos="fade-up" data-aos-delay="200">
                <div class="counter-card text-center">
                    <div class="counter-icon-modern">
                        <i data-feather="repeat"></i>
                    </div>
                    <h2 class="counter-value text-white mt-3" data-count="500000">0</h2>
                    <span class="text-white-50">Transactions</span>
                </div>
            </div>

            <div class="col-lg-3 col-6" data-aos="fade-up" data-aos-delay="300">
                <div class="counter-card text-center">
                    <div class="counter-icon-modern">
                        <i data-feather="trending-up"></i>
                    </div>
                    <h2 class="counter-value text-white mt-3" data-count="95">0</h2>
                    <span class="text-white-50">Satisfaction %</span>
                </div>
            </div>

            <div class="col-lg-3 col-6" data-aos="fade-up" data-aos-delay="400">
                <div class="counter-card text-center">
                    <div class="counter-icon-modern">
                        <i data-feather="globe"></i>
                    </div>
                    <h2 class="counter-value text-white mt-3" data-count="50">0</h2>
                    <span class="text-white-50">Countries</span>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Stats Counter Section End -->

<!-- Pricing Section Start -->
<section class="section bg-light" id="pricing">
    <div class="container">
        <!-- Section Header -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <span class="badge bg-soft-warning text-warning px-3 py-2 rounded-pill mb-3" data-aos="fade-up">PRICING</span>
                <h2 class="fw-bold mb-3" data-aos="fade-up" data-aos-delay="100">Free Forever, No Hidden Costs</h2>
                <div class="title-underline mx-auto bg-warning" data-aos="fade-up" data-aos-delay="150"></div>
                <p class="text-muted mt-3" data-aos="fade-up" data-aos-delay="200">Ledgerly is completely free and open-source. All features are available to everyone, always.</p>
            </div>
        </div>

        <!-- Pricing Card -->
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8" data-aos="zoom-in">
                <div class="pricing-card-modern text-center">
                    <div class="pricing-badge-free">
                        <i data-feather="gift" class="text-white" style="width:40px;height:40px;"></i>
                    </div>
                    <h3 class="mt-4 mb-3">Free Plan</h3>
                    <div class="pricing-amount">
                        <span class="currency">$</span>
                        <span class="amount">0</span>
                        <span class="period">/Forever</span>
                    </div>

                    <div class="pricing-features text-start mt-4">
                        <div class="pricing-feature">
                            <i data-feather="check" class="text-success me-2"></i>
                            <span>Unlimited Income Sources</span>
                        </div>
                        <div class="pricing-feature">
                            <i data-feather="check" class="text-success me-2"></i>
                            <span>Unlimited Transactions</span>
                        </div>
                        <div class="pricing-feature">
                            <i data-feather="check" class="text-success me-2"></i>
                            <span>Unlimited Expense Types</span>
                        </div>
                        <div class="pricing-feature">
                            <i data-feather="check" class="text-success me-2"></i>
                            <span>Visual Charts & Analytics</span>
                        </div>
                        <div class="pricing-feature">
                            <i data-feather="check" class="text-success me-2"></i>
                            <span>PDF Export Reports</span>
                        </div>
                        <div class="pricing-feature">
                            <i data-feather="check" class="text-success me-2"></i>
                            <span>Team Collaboration</span>
                        </div>
                        <div class="pricing-feature">
                            <i data-feather="check" class="text-success me-2"></i>
                            <span>Multi-Currency (IQD & USD)</span>
                        </div>
                        <div class="pricing-feature">
                            <i data-feather="check" class="text-success me-2"></i>
                            <span>Role-Based Permissions</span>
                        </div>
                        <div class="pricing-feature">
                            <i data-feather="check" class="text-success me-2"></i>
                            <span>Mobile-Responsive Design</span>
                        </div>
                        <div class="pricing-feature">
                            <i data-feather="check" class="text-success me-2"></i>
                            <span>API Access</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-3">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg w-100">
                            Get Started Free <i data-feather="arrow-right" class="ms-2" style="width:18px;height:18px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Pricing Section End -->

<!-- CTA Section Start -->
<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10" data-aos="fade-up">
                <div class="cta-card text-center">
                    <!-- Decorative shapes -->
                    <div class="cta-shape cta-shape-1"></div>
                    <div class="cta-shape cta-shape-2"></div>

                    <h2 class="text-white mb-3">Ready to Transform Your Financial Management?</h2>
                    <p class="text-white-50 lead mb-4">Join Ledgerly today and take complete control of your finances. It's free, powerful, and ready to use in minutes.</p>
                    <div class="mt-4">
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg me-2 px-4">
                            <i data-feather="rocket" class="me-2" style="width:18px;height:18px;"></i>Create Free Account
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">
                            <i data-feather="log-in" class="me-2" style="width:18px;height:18px;"></i>Sign In
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- CTA Section End -->

<!-- Footer Start -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-sm-6">
                <div>
                    <h5 class="mb-4 footer-list-title">
                        <span class="fw-bold text-primary fs-4">Ledgerly</span>
                    </h5>
                    <p class="text-muted">Your personal financial command center. Track income, manage expenses, visualize spending, and collaborate with your team—all in one powerful, free platform.</p>
                    <div class="social-icons mt-4">
                        <a href="#" class="social-icon"><i class="mdi mdi-facebook"></i></a>
                        <a href="#" class="social-icon"><i class="mdi mdi-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="mdi mdi-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="mdi mdi-github"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 offset-lg-1 col-sm-6">
                <div>
                    <h5 class="mb-4 footer-list-title">Quick Links</h5>
                    <ul class="list-unstyled footer-list-menu">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#pricing">Pricing</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-sm-6">
                <div>
                    <h5 class="mb-4 footer-list-title">Resources</h5>
                    <ul class="list-unstyled footer-list-menu">
                        <li><a href="{{ route('register') }}">Get Started</a></li>
                        <li><a href="{{ route('login') }}">Sign In</a></li>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">Support</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div>
                    <h5 class="mb-4 footer-list-title">Contact</h5>
                    <div class="footer-contact">
                        <div class="d-flex mb-3">
                            <i data-feather="mail" class="text-muted me-2 flex-shrink-0" style="width:18px;height:18px;"></i>
                            <span class="text-muted">support@ledgerly.com</span>
                        </div>
                        <div class="d-flex">
                            <i data-feather="globe" class="text-muted me-2 flex-shrink-0" style="width:18px;height:18px;"></i>
                            <span class="text-muted">www.ledgerly.com</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- Footer End -->

<!-- Footer Bottom Start -->
<section class="footer-bottom py-3">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <a href="{{ route('landing') }}" class="text-white text-decoration-none">
                    <span class="fw-bold fs-5">Ledgerly</span>
                </a>
            </div>
            <div class="col-lg-6 text-lg-end mt-3 mt-lg-0">
                <p class="text-white-50 mb-0">
                    <script>document.write(new Date().getFullYear())</script> © Ledgerly. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</section>
<!-- Footer Bottom End -->

<style>
/* ============================================
   MODERN LANDING PAGE STYLES
   ============================================ */

/* Hero Section Modern */
.hero-section-modern {
    min-height: 100vh;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding-top: 80px;
}

.hero-bg-shapes {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    overflow: hidden;
    pointer-events: none;
}

.hero-bg-shapes .shape {
    position: absolute;
    border-radius: 50%;
}

.shape-1 {
    width: 600px;
    height: 600px;
    background: linear-gradient(135deg, rgba(47, 85, 212, 0.1), rgba(87, 184, 255, 0.1));
    top: -200px;
    right: -150px;
    animation: float 20s ease-in-out infinite;
}

.shape-2 {
    width: 400px;
    height: 400px;
    background: linear-gradient(135deg, rgba(52, 209, 191, 0.1), rgba(47, 85, 212, 0.1));
    bottom: -100px;
    left: -100px;
    animation: float 15s ease-in-out infinite reverse;
}

.shape-3 {
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(253, 126, 20, 0.1));
    top: 40%;
    left: 10%;
    animation: float 12s ease-in-out infinite;
}

.shape-4 {
    width: 150px;
    height: 150px;
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(255, 193, 7, 0.1));
    top: 20%;
    right: 20%;
    animation: float 18s ease-in-out infinite reverse;
}

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-30px) rotate(5deg); }
}

/* Text Gradient */
.text-gradient {
    background: linear-gradient(135deg, #2f55d4 0%, #34D1BF 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Mini Stat Icons */
.mini-stat-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mini-stat-icon svg {
    width: 18px;
    height: 18px;
}

/* Floating Animation */
.floating-animation {
    animation: floatMockup 6s ease-in-out infinite;
}

.floating-animation-delayed {
    animation: floatMockup 6s ease-in-out infinite;
    animation-delay: 1s;
}

.floating-animation-delayed-2 {
    animation: floatMockup 6s ease-in-out infinite;
    animation-delay: 2s;
}

.floating-animation-delayed-3 {
    animation: floatMockup 6s ease-in-out infinite;
    animation-delay: 0.5s;
}

@keyframes floatMockup {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}

/* Hero Mockup Wrapper */
.hero-mockup-wrapper {
    position: relative;
    padding: 20px;
}

/* Browser Mockup */
.browser-mockup {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.15);
}

.browser-header {
    background: #f1f3f5;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid #e9ecef;
}

.browser-dots {
    display: flex;
    gap: 6px;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.dot-red { background: #ff5f56; }
.dot-yellow { background: #ffbd2e; }
.dot-green { background: #27ca40; }

.browser-url {
    flex: 1;
    background: #fff;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    color: #6c757d;
    display: flex;
    align-items: center;
}

.browser-body {
    background: #f8f9fa;
    min-height: 250px;
}

.browser-mockup-large .browser-body {
    min-height: 350px;
}

/* Dashboard Preview */
.dashboard-preview {
    background: #f8f9fa;
}

/* Donut Chart Placeholder */
.donut-chart {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: conic-gradient(#2f55d4 0deg 234deg, #dc3545 234deg 360deg);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.donut-inner {
    width: 80px;
    height: 80px;
    background: #f8f9fa;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.donut-chart-large {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: conic-gradient(#2f55d4 0deg 234deg, #dc3545 234deg 306deg, #ffc107 306deg 360deg);
    display: flex;
    align-items: center;
    justify-content: center;
}

.donut-chart-large .donut-inner {
    width: 70px;
    height: 70px;
    background: #fff;
}

/* Floating Cards */
.floating-card {
    position: absolute;
    background: #fff;
    padding: 10px 16px;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    font-size: 14px;
}

.floating-card svg {
    width: 16px;
    height: 16px;
}

.floating-card-income {
    top: 20%;
    right: -20px;
}

.floating-card-expense {
    bottom: 30%;
    left: -30px;
}

.floating-card-balance {
    bottom: 10%;
    right: 10%;
}

/* Title Underline */
.title-underline {
    width: 60px;
    height: 3px;
    background: #2f55d4;
    border-radius: 2px;
}

/* Feature Items (Applock-style) */
.feature-item-left,
.feature-item-right {
    display: flex;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #fff;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.feature-item-left:hover,
.feature-item-right:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

.feature-item-left {
    text-align: right;
    flex-direction: row-reverse;
}

.feature-item-left .feature-icon-wrapper {
    margin-left: 1rem;
}

.feature-item-right .feature-icon-wrapper {
    margin-right: 1rem;
}

.feature-icon-wrapper {
    width: 50px;
    height: 50px;
    min-width: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.feature-icon-wrapper svg {
    width: 24px;
    height: 24px;
}

/* Center Mockup (Phone) */
.center-mockup {
    position: relative;
}

.phone-mockup {
    width: 220px;
    margin: 0 auto;
    background: #1a1a2e;
    border-radius: 30px;
    padding: 10px;
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
}

.phone-screen {
    background: #fff;
    border-radius: 22px;
    overflow: hidden;
}

.phone-header {
    text-align: center;
}

.phone-content {
    min-height: 200px;
}

.mini-chart-bars {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    height: 60px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 8px;
}

.mini-chart-bars .bar {
    width: 15%;
    background: linear-gradient(to top, #2f55d4, #57B8FF);
    border-radius: 4px;
}

.bar-1 { height: 60%; }
.bar-2 { height: 80%; }
.bar-3 { height: 45%; }
.bar-4 { height: 90%; }
.bar-5 { height: 70%; }

/* Feature Cards Modern */
.feature-card-modern {
    background: #fff;
    border-radius: 16px;
    padding: 2rem;
    height: 100%;
    border-left: 4px solid transparent;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.feature-card-modern:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
}

.feature-card-primary { border-left-color: #2f55d4; }
.feature-card-success { border-left-color: #34D1BF; }
.feature-card-info { border-left-color: #57B8FF; }

.feature-card-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #2f55d4, #57B8FF);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}

.feature-card-icon svg {
    width: 24px;
    height: 24px;
    color: #fff;
}

/* Steps Timeline */
.step-item {
    text-align: center;
    position: relative;
    padding: 0 20px;
}

.step-number {
    width: 60px;
    height: 60px;
    background: #2f55d4;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.5rem;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}

.step-number-success { background: #28a745; }
.step-number-info { background: #17a2b8; }

.step-connector {
    position: absolute;
    top: 30px;
    left: calc(50% + 40px);
    width: calc(100% - 80px);
    height: 2px;
    background: linear-gradient(90deg, #2f55d4, #28a745);
}

.step-item:nth-child(2) .step-connector {
    background: linear-gradient(90deg, #28a745, #17a2b8);
}

.step-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 1.5rem auto 0;
}

.step-icon svg {
    width: 24px;
    height: 24px;
}

/* Showcase Tabs */
.showcase-tabs .nav-link {
    background: #fff;
    color: #495057;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
    position: relative;
}

.showcase-tabs .nav-link svg {
    width: 20px;
    height: 20px;
}

.showcase-tabs .nav-link .tab-arrow {
    margin-left: auto;
    opacity: 0;
    transition: all 0.3s ease;
}

.showcase-tabs .nav-link .tab-arrow svg {
    width: 16px;
    height: 16px;
}

.showcase-tabs .nav-link:hover,
.showcase-tabs .nav-link.active {
    background: #2f55d4;
    color: #fff;
    border-color: #2f55d4;
}

.showcase-tabs .nav-link.active .tab-arrow {
    opacity: 1;
}

/* Showcase Content */
.showcase-content {
    background: #f8f9fa;
}

.transaction-icon,
.member-avatar {
    width: 40px;
    height: 40px;
    min-width: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

.transaction-icon svg {
    width: 18px;
    height: 18px;
}

/* Bar Chart Preview */
.bar-chart-preview {
    padding: 10px 0;
}

.bar-row {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
}

.bar-fill {
    height: 12px;
    background: linear-gradient(90deg, #2f55d4, #57B8FF);
    border-radius: 6px;
    margin-right: 8px;
}

.bar-row span {
    font-size: 11px;
    color: #6c757d;
}

/* Counter Section */
.bg-counter-parallax {
    background: linear-gradient(135deg, #2f55d4 0%, #1a3ba3 100%);
    position: relative;
}

.counter-card {
    padding: 2rem 1rem;
}

.counter-icon-modern {
    width: 70px;
    height: 70px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.counter-icon-modern svg {
    width: 32px;
    height: 32px;
    color: #fff;
}

.counter-value {
    font-size: 2.5rem;
    font-weight: 700;
}

/* Pricing Card Modern */
.pricing-card-modern {
    background: #fff;
    border-radius: 20px;
    padding: 3rem 2rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.pricing-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: linear-gradient(90deg, #2f55d4, #34D1BF);
}

.pricing-badge-free {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #34D1BF, #2f55d4);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.pricing-amount {
    margin: 1.5rem 0;
}

.pricing-amount .currency {
    font-size: 1.5rem;
    vertical-align: top;
    color: #2f55d4;
}

.pricing-amount .amount {
    font-size: 4rem;
    font-weight: 700;
    line-height: 1;
    color: #2f55d4;
}

.pricing-amount .period {
    color: #adb5bd;
    font-size: 1rem;
}

.pricing-feature {
    display: flex;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f3f5;
}

.pricing-feature:last-child {
    border-bottom: none;
}

.pricing-feature svg {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}

/* CTA Card */
.cta-card {
    background: linear-gradient(135deg, #2f55d4 0%, #1a3ba3 100%);
    border-radius: 20px;
    padding: 4rem 3rem;
    position: relative;
    overflow: hidden;
}

.cta-shape {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
}

.cta-shape-1 {
    width: 200px;
    height: 200px;
    top: -50px;
    right: -50px;
}

.cta-shape-2 {
    width: 150px;
    height: 150px;
    bottom: -30px;
    left: -30px;
}

/* Footer */
.footer {
    background: #1a1a2e;
    padding: 4rem 0 2rem;
}

.footer-list-title {
    color: #fff;
}

.footer-list-menu li {
    margin-bottom: 0.75rem;
}

.footer-list-menu a {
    color: #adb5bd;
    transition: all 0.3s ease;
    text-decoration: none;
}

.footer-list-menu a:hover {
    color: #fff;
    padding-left: 5px;
}

.social-icons {
    display: flex;
    gap: 10px;
}

.social-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    transition: all 0.3s ease;
    text-decoration: none;
}

.social-icon:hover {
    background: #2f55d4;
    color: #fff;
    transform: translateY(-3px);
}

.footer-bottom {
    background: #0d0d1a;
}

/* Navbar Scroll Effect */
.navbar-scrolled {
    background: rgba(255, 255, 255, 0.98) !important;
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
}

/* Button Hover Animation */
.btn-hover-animate {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.btn-hover-animate:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(47, 85, 212, 0.3);
}

/* Soft Background Colors */
.bg-soft-primary { background-color: rgba(47, 85, 212, 0.1) !important; }
.bg-soft-success { background-color: rgba(40, 167, 69, 0.1) !important; }
.bg-soft-info { background-color: rgba(23, 162, 184, 0.1) !important; }
.bg-soft-warning { background-color: rgba(255, 193, 7, 0.1) !important; }
.bg-soft-danger { background-color: rgba(220, 53, 69, 0.1) !important; }

/* Responsive Adjustments */
@media (max-width: 991.98px) {
    .hero-section-modern {
        padding-top: 100px;
    }

    .floating-card {
        display: none;
    }

    .feature-item-left {
        text-align: left;
        flex-direction: row;
    }

    .feature-item-left .feature-icon-wrapper {
        margin-left: 0;
        margin-right: 1rem;
    }

    .step-connector {
        display: none !important;
    }

    .counter-value {
        font-size: 2rem;
    }
}

@media (max-width: 767.98px) {
    .hero-title {
        font-size: 2rem;
    }

    .phone-mockup {
        width: 180px;
    }

    .pricing-card-modern {
        padding: 2rem 1.5rem;
    }

    .cta-card {
        padding: 2rem 1.5rem;
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Navbar scroll effect
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('navbar-scrolled');
        } else {
            navbar.classList.remove('navbar-scrolled');
        }
    });

    // Counter animation
    function animateCounters() {
        const counters = document.querySelectorAll('.counter-value');
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-count'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    counter.textContent = target.toLocaleString();
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(current).toLocaleString();
                }
            }, 16);
        });
    }

    // Intersection Observer for counter animation
    const counterSection = document.getElementById('counter');
    if (counterSection) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });

        observer.observe(counterSection);
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const offsetTop = target.offsetTop - 80;
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Simple AOS-like animation on scroll
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('[data-aos]');
        elements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;

            if (elementTop < windowHeight - 100) {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }
        });
    };

    // Initialize animation states
    document.querySelectorAll('[data-aos]').forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        element.style.transition = 'all 0.6s ease-out';
    });

    // Run on load and scroll
    animateOnScroll();
    window.addEventListener('scroll', animateOnScroll);
});
</script>
@endpush

@endsection
