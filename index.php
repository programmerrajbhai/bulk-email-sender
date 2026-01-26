<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Ultima Sender Pro</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            --sidebar-width: 260px;
            --bg-light: #f1f5f9;
            --text-dark: #0f172a;
        }

        body { font-family: 'Outfit', sans-serif; background-color: var(--bg-light); color: var(--text-dark); overflow-x: hidden; }

        /* Sidebar & Layout */
        #wrapper { display: flex; width: 100%; align-items: stretch; transition: all 0.3s; }
        #sidebar-wrapper { min-height: 100vh; width: var(--sidebar-width); background: #ffffff; border-right: 1px solid #e2e8f0; position: fixed; z-index: 1000; transition: all 0.3s; }
        #sidebar-wrapper .sidebar-brand { padding: 1.5rem; font-size: 1.5rem; font-weight: 800; color: #1e40af; display: flex; align-items: center; justify-content: center; letter-spacing: -0.5px; }
        #sidebar-wrapper .list-group-item { border: none; padding: 1rem 2rem; font-weight: 500; color: #64748b; transition: all 0.2s; margin: 5px 15px; border-radius: 10px; cursor: pointer; }
        #sidebar-wrapper .list-group-item:hover, #sidebar-wrapper .list-group-item.active { background: #eff6ff; color: #1e40af; font-weight: 700; }
        #sidebar-wrapper .list-group-item i { width: 25px; text-align: center; margin-right: 10px; }
        #page-content-wrapper { width: 100%; margin-left: var(--sidebar-width); transition: all 0.3s; }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            #sidebar-wrapper { margin-left: -260px; }
            #page-content-wrapper { margin-left: 0; }
            #wrapper.toggled #sidebar-wrapper { margin-left: 0; }
            #sidebar-overlay { display: none; position: fixed; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.5); z-index: 900; top: 0; left: 0; }
            #wrapper.toggled #sidebar-overlay { display: block; }
        }

        /* UI Elements */
        .navbar-custom { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-bottom: 1px solid #e2e8f0; padding: 15px 30px; position: sticky; top: 0; z-index: 800; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); background: white; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-3px); }
        .icon-box { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 15px; }
        .bg-light-primary { background: #dbeafe; color: #2563eb; }
        .bg-light-success { background: #dcfce7; color: #16a34a; }
        .bg-light-warning { background: #fef9c3; color: #ca8a04; }
        .bg-light-danger { background: #fee2e2; color: #dc2626; }
        .stat-value { font-size: 1.8rem; font-weight: 700; color: #0f172a; }
        
        /* Button */
        .btn-gradient { background: var(--primary-gradient); color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 600; transition: all 0.3s; }
        .btn-gradient:hover { background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%); color: white; transform: translateY(-2px); }

        /* Template Selector */
        .template-card { border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; cursor: pointer; transition: all 0.2s; text-align: center; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; background: white; }
        .template-card:hover { border-color: #2563eb; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1); }
        .template-card.active { border-color: #2563eb; background: #eff6ff; color: #1e40af; font-weight: bold; ring: 2px solid #bfdbfe; }
        .template-card i { font-size: 1.5rem; margin-bottom: 5px; color: #64748b; }
        .template-card.active i { color: #2563eb; }
        
        #logs { background: #0f172a; color: #4ade80; height: 320px; overflow-y: auto; padding: 20px; font-family: 'Courier New', monospace; border-radius: 8px; font-size: 13px; }
    </style>
</head>

<body>

    <div id="wrapper">
        <div id="sidebar-overlay" onclick="toggleMenu()"></div>

        <div id="sidebar-wrapper">
            <div class="sidebar-brand mt-3 mb-4"><i class="fas fa-paper-plane me-2"></i> ULTIMA PRO</div>
            <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action active" onclick="switchTab('dashboard', this)"><i class="fas fa-chart-pie"></i> Dashboard</a>
                <a class="list-group-item list-group-item-action" onclick="switchTab('campaign', this)"><i class="fas fa-pen-nib"></i> Design Email</a>
                <a class="list-group-item list-group-item-action" onclick="switchTab('clients', this)"><i class="fas fa-users"></i> Client List</a>
                <a class="list-group-item list-group-item-action" onclick="switchTab('history', this)"><i class="fas fa-history"></i> History</a>
                <a class="list-group-item list-group-item-action text-danger mt-5" href="#" onclick="location.reload()"><i class="fas fa-power-off"></i> Reboot</a>
            </div>
        </div>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-custom d-flex justify-content-between align-items-center">
                <button class="btn btn-light shadow-sm text-primary border" id="menu-toggle" onclick="toggleMenu()"><i class="fas fa-bars"></i></button>
                <div class="d-flex align-items-center">
                    <span class="badge bg-success rounded-pill me-2 px-3">System Online</span>
                </div>
            </nav>

            <div class="container-fluid p-4">

                <div id="view-dashboard" class="view-section">
                    <div class="row g-4 mb-4">
                        <div class="col-6 col-lg-3"><div class="card stat-card p-4 h-100"><div class="icon-box bg-light-primary"><i class="fas fa-envelope"></i></div><div class="stat-value" id="stat_total">0</div><div class="stat-label">Total Emails</div></div></div>
                        <div class="col-6 col-lg-3"><div class="card stat-card p-4 h-100"><div class="icon-box bg-light-success"><i class="fas fa-check-circle"></i></div><div class="stat-value" id="stat_sent">0</div><div class="stat-label">Sent</div></div></div>
                        <div class="col-6 col-lg-3"><div class="card stat-card p-4 h-100"><div class="icon-box bg-light-warning"><i class="fas fa-clock"></i></div><div class="stat-value" id="stat_pending">0</div><div class="stat-label">Pending</div></div></div>
                        <div class="col-6 col-lg-3"><div class="card stat-card p-4 h-100"><div class="icon-box bg-light-danger"><i class="fas fa-times-circle"></i></div><div class="stat-value" id="stat_failed">0</div><div class="stat-label">Failed</div></div></div>
                    </div>
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="card p-4 mb-4 h-100"><h6 class="fw-bold mb-4">Analytics Overview</h6><canvas id="emailChart" style="max-height: 300px;"></canvas></div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card p-4 h-100">
                                <h6 class="fw-bold mb-3">Quick Launch</h6>
                                <div class="progress mb-4" style="height: 8px; border-radius: 4px;"><div id="progressBar" class="progress-bar bg-primary" style="width: 0%"></div></div>
                                <button id="startBtn" class="btn btn-gradient w-100 py-3 mb-4" onclick="startSending()">START CAMPAIGN</button>
                                <div id="logs"><div class="log-entry">> Waiting for command...</div></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="view-campaign" class="view-section d-none">
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <div class="card p-4 shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h4 class="fw-bold m-0"><i class="fas fa-layer-group me-2 text-primary"></i> Template Library</h4>
                                    <button class="btn btn-outline-dark rounded-pill px-4" onclick="openPreview()"><i class="fas fa-eye me-2"></i>Preview</button>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted text-uppercase small">Select a Professional Design:</label>
                                    <div class="row g-3">
                                        <div class="col-6 col-md-3"><div class="template-card" onclick="loadTemplate('fintech')"><i class="fas fa-wallet text-primary"></i> <span>FinTech Transaction</span></div></div>
                                        <div class="col-6 col-md-3"><div class="template-card" onclick="loadTemplate('corporate')"><i class="fas fa-building text-dark"></i> <span>Corporate Official</span></div></div>
                                        <div class="col-6 col-md-3"><div class="template-card" onclick="loadTemplate('security')"><i class="fas fa-shield-alt text-danger"></i> <span>Security Alert</span></div></div>
                                        <div class="col-6 col-md-3"><div class="template-card" onclick="loadTemplate('invoice')"><i class="fas fa-file-invoice text-success"></i> <span>Modern Invoice</span></div></div>
                                        <div class="col-6 col-md-3"><div class="template-card" onclick="loadTemplate('social')"><i class="fas fa-bell text-info"></i> <span>Social Notification</span></div></div>
                                        <div class="col-6 col-md-3"><div class="template-card" onclick="loadTemplate('welcome')"><i class="fas fa-hand-sparkles text-warning"></i> <span>Welcome Onboard</span></div></div>
                                        <div class="col-6 col-md-3"><div class="template-card" onclick="loadTemplate('promo')"><i class="fas fa-gift text-danger"></i> <span>Big Sale Promo</span></div></div>
                                        <div class="col-6 col-md-3"><div class="template-card" onclick="loadTemplate('newsletter')"><i class="fas fa-newspaper text-secondary"></i> <span>Weekly Digest</span></div></div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="fw-bold mb-1">Email Subject</label>
                                    <input type="text" id="emailSubject" class="form-control form-control-lg bg-light" placeholder="Enter subject line here...">
                                </div>

                                <div class="mb-3">
                                    <label class="fw-bold mb-1">Email Body</label>
                                    <textarea id="summernote"></textarea>
                                </div>

                                <div class="text-end">
                                    <button class="btn btn-primary btn-lg px-5 rounded-3" onclick="saveTemplate()"><i class="fas fa-save me-2"></i> Save Campaign</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="view-clients" class="view-section d-none">
                    <div class="card p-4">
                        <div class="d-flex justify-content-between mb-4"><h4 class="fw-bold">Client Database</h4><button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#bulkModal"><i class="fas fa-plus me-2"></i> Add Emails</button></div>
                        <table id="clientsTable" class="table table-hover w-100"><thead class="bg-light"><tr><th>ID</th><th>Email</th><th>Status</th><th>Action</th></tr></thead><tbody></tbody></table>
                    </div>
                </div>
                <div id="view-history" class="view-section d-none">
                    <div class="card p-4"><h4 class="fw-bold">Sent History</h4><table id="historyTable" class="table table-hover w-100"><thead class="bg-light"><tr><th>ID</th><th>Email</th><th>Status</th><th>Action</th></tr></thead><tbody></tbody></table></div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Live Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" style="background: #e2e8f0;">
                    <div id="previewContent" style="margin: 0 auto; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bulkModal"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Import Emails</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><textarea id="bulkEmailsInput" class="form-control" rows="8" placeholder="Paste emails here (one per line)..."></textarea></div><div class="modal-footer"><button class="btn btn-primary w-100" onclick="bulkImport()">Import Now</button></div></div></div></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // --- 8 PROFESSIONAL TEMPLATES (Preserved 100%) ---
        const templates = {
            fintech: `<div style="font-family:'Helvetica Neue', Helvetica, Arial, sans-serif; background-color:#f5f7fa; padding:40px 0;"><div style="max-width:500px; margin:0 auto; background-color:#ffffff; border:1px solid #e1e4e8; border-radius:8px; overflow:hidden;"><div style="text-align:center; padding:30px; border-bottom:1px solid #f0f0f0;"><h2 style="margin:0; color:#003087; font-weight:bold;">Transaction Alert</h2></div><div style="padding:40px 30px; text-align:center;"><div style="font-size:32px; font-weight:bold; color:#111; margin-bottom:10px;">$489.00 USD</div><p style="color:#666; margin-top:0;">Paid to <strong>Service Provider Inc.</strong></p><hr style="border:0; border-top:1px solid #eee; margin:30px 0;"><p style="color:#555; font-size:14px; line-height:1.5;">You've successfully sent a payment. If this wasn't you, please contact support immediately.</p><a href="#" style="display:inline-block; background-color:#003087; color:#ffffff; padding:12px 30px; text-decoration:none; border-radius:25px; font-weight:bold; font-size:14px; margin-top:20px;">View Transaction Details</a></div><div style="background-color:#fafafa; padding:20px; text-align:center; color:#999; font-size:12px; border-top:1px solid #eee;"><p>Transaction ID: #8X9201L</p><p>&copy; 2026 Your Company. All rights reserved.</p></div></div></div>`,
            corporate: `<div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; padding: 40px 0;"><table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 15px rgba(0,0,0,0.05);"><tr><td align="center" style="padding: 30px; border-bottom: 3px solid #2563eb;"><h1 style="margin: 0; color: #1e293b; letter-spacing: 1px;">OFFICIAL NOTICE</h1></td></tr><tr><td style="padding: 40px; color: #475569; font-size: 16px; line-height: 1.6;"><p>Dear <strong>Client</strong>,</p><p>We are writing to update you regarding your account status. Our team has reviewed your profile and we have some important information for you.</p><div style="background:#f1f5f9; padding:15px; border-left:4px solid #2563eb; margin:20px 0;"><strong>Action Required:</strong> Please verify your latest invoice details.</div><p>Click the button below to access your secure dashboard.</p><br><center><a href="#" style="background-color: #2563eb; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 4px; font-weight: 600;">Go to Dashboard</a></center></td></tr><tr><td align="center" style="background-color: #f8fafc; padding: 20px; color: #94a3b8; font-size: 12px; border-top: 1px solid #e2e8f0;"><p>&copy; 2026 Your Corporation. 123 Business Road, City.</p></td></tr></table></div>`,
            security: `<div style="background-color:#fef2f2; padding:40px 0; font-family:Arial, sans-serif;"><div style="max-width:500px; margin:0 auto; background-color:#ffffff; border:1px solid #fee2e2; border-top: 4px solid #dc2626; padding:40px; border-radius:8px;"><div style="text-align:center; margin-bottom:20px;"><span style="background:#fee2e2; color:#dc2626; padding:10px 15px; border-radius:50%; font-size:20px;">⚠️</span></div><h2 style="color:#991b1b; margin-top:0; text-align:center;">New Login Detected</h2><p style="color:#374151; line-height:1.6; text-align:center;">We detected a login to your account from a new device.</p><div style="background:#f9fafb; padding:15px; margin:20px 0; border-radius:6px; font-size:14px; color:#4b5563;"><p style="margin:5px 0;"><strong>Device:</strong> iPhone 15 Pro</p><p style="margin:5px 0;"><strong>Location:</strong> Dhaka, Bangladesh</p><p style="margin:5px 0;"><strong>Time:</strong> Just now</p></div><div style="text-align:center; margin-top:30px;"><a href="#" style="background-color:#dc2626; color:white; padding:12px 30px; text-decoration:none; border-radius:4px; font-weight:bold;">Secure My Account</a></div></div></div>`,
            invoice: `<div style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6; padding: 40px 0;"><div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 8px;"><div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 2px solid #f3f4f6; padding-bottom: 20px; margin-bottom: 30px;"><h2 style="margin: 0; color: #111;">INVOICE</h2><span style="color: #666; font-size: 14px;">#INV-2026-001</span></div><p style="color:#555;">Hello,</p><p style="color:#555;">Thank you for your business. Here is the receipt for your recent purchase.</p><table width="100%" style="margin: 30px 0; border-collapse: collapse;"><thead><tr style="background-color: #f9fafb; color: #374151; font-size: 14px;"><th style="padding: 12px; text-align: left;">Description</th><th style="padding: 12px; text-align: right;">Amount</th></tr></thead><tbody><tr><td style="padding: 15px; border-bottom: 1px solid #eee; color:#111;">Web Development Service</td><td style="padding: 15px; border-bottom: 1px solid #eee; text-align: right; color:#111;">$450.00</td></tr><tr><td style="padding: 15px; border-bottom: 1px solid #eee; font-weight:bold;">Total Paid</td><td style="padding: 15px; border-bottom: 1px solid #eee; text-align: right; font-weight:bold; color:#16a34a;">$450.00</td></tr></tbody></table><a href="#" style="display:block; width:100%; text-align:center; background-color: #0f172a; color: white; padding: 15px 0; text-decoration: none; border-radius: 6px;">Download PDF Receipt</a></div></div>`,
            social: `<div style="background-color:#f0f2f5; padding:40px 0; font-family:Arial, sans-serif;"><div style="max-width:500px; margin:0 auto; background-color:#ffffff; border-radius:8px; padding:20px; border:1px solid #ddd;"><div style="display:flex; align-items:center; margin-bottom:15px;"><div style="width:40px; height:40px; background:#1877f2; border-radius:50%; margin-right:10px;"></div><h3 style="margin:0; color:#1877f2;">New Notification</h3></div><p style="font-size:16px; color:#333;"><strong>John Doe</strong> and 3 others commented on your post.</p><div style="background:#f7f8fa; padding:15px; border-left:3px solid #1877f2; color:#555; font-style:italic;">"This is a great update! Thanks for sharing..."</div><br><a href="#" style="background-color:#1877f2; color:white; padding:10px 20px; text-decoration:none; border-radius:4px; font-size:14px;">View Comment</a></div></div>`,
            welcome: `<div style="background-color:#fdf4ff; padding:40px 0; font-family:Verdana, sans-serif;"><div style="max-width:500px; margin:0 auto; background-color:#ffffff; border-radius:16px; padding:40px; text-align:center; box-shadow: 0 4px 20px rgba(0,0,0,0.05);"><h1 style="color:#9333ea; margin:0 0 10px 0;">You're In! 🚀</h1><p style="color:#666; font-size:16px; margin-bottom:30px;">Thanks for signing up. We're excited to help you grow your business.</p><a href="#" style="background-color:#9333ea; color:white; padding:15px 40px; text-decoration:none; border-radius:50px; font-weight:bold; font-size:16px; box-shadow: 0 4px 10px rgba(147, 51, 234, 0.3);">Get Started</a><p style="margin-top:30px; color:#aaa; font-size:12px;">If you have any questions, just reply to this email.</p></div></div>`,
            promo: `<div style="background-color:#111; padding:40px 0; font-family:'Arial Black', sans-serif; text-transform:uppercase;"><div style="max-width:600px; margin:0 auto; background-color:#ffffff; border: 4px solid #000; padding:40px; text-align:center;"><h1 style="color:#000; font-size:50px; margin:0; line-height:1;">FLASH SALE</h1><h2 style="color:#e11d48; margin:10px 0;">50% OFF EVERYTHING</h2><p style="font-family:Arial; color:#333; text-transform:none; font-size:16px;">This offer is valid for the next 24 hours only. Don't miss out!</p><br><a href="#" style="background-color:#e11d48; color:white; padding:20px 50px; text-decoration:none; display:inline-block; font-size:20px; font-weight:bold;">SHOP NOW</a></div></div>`,
            newsletter: `<div style="font-family:Georgia, serif; background-color:#fff; padding:20px; color:#333;"><div style="max-width:600px; margin:0 auto; border-bottom:2px solid #333; padding-bottom:15px; margin-bottom:30px;"><h1 style="font-style:italic; margin:0;">The Daily Digest</h1><span style="font-family:Arial; font-size:12px; color:#666;">Issue #42 • Jan 26, 2026</span></div><div style="max-width:600px; margin:0 auto;"><h2 style="color:#111; margin-bottom:10px;">Top Story: Future of Tech</h2><p style="line-height:1.8; color:#444;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.</p><a href="#" style="color:#2563eb; text-decoration:underline; font-family:Arial;">Read more &rarr;</a><hr style="margin:30px 0; border:0; border-top:1px solid #eee;"><div style="background:#f9f9f9; padding:20px;"><h3 style="margin-top:0;">Quick Links</h3><ul style="padding-left:20px; margin:0;"><li><a href="#" style="color:#333;">Market Updates</a></li><li><a href="#" style="color:#333;">New Features</a></li><li><a href="#" style="color:#333;">Community Forum</a></li></ul></div></div></div>`
        };

        // --- CHART & FUNCTIONS ---
        let emailChart;
        $(document).ready(function() {
            $('#summernote').summernote({ height: 350 });
            initChart();
            updateStats();
        });

        // 1. Template Functions
        function loadTemplate(type) {
            $('.template-card').removeClass('active');
            $(event.currentTarget).addClass('active');
            if(templates[type]) {
                $('#summernote').summernote('code', templates[type]);
                const Toast = Swal.mixin({toast: true, position: 'top-end', showConfirmButton: false, timer: 1000});
                Toast.fire({icon: 'success', title: 'Template Applied!'});
            }
        }

        function openPreview() {
            var content = $('#summernote').summernote('code');
            $('#previewContent').html(content);
            new bootstrap.Modal(document.getElementById('previewModal')).show();
        }

        // 2. Chart
        function initChart() {
            const ctx = document.getElementById('emailChart').getContext('2d');
            emailChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Sent', 'Pending', 'Failed'],
                    datasets: [{ data: [0, 0, 0], backgroundColor: ['#16a34a', '#ca8a04', '#dc2626'], borderWidth: 0 }]
                },
                options: { responsive: true, cutout: '75%', plugins: { legend: { position: 'right' } } }
            });
        }

        function updateStats() {
            $.post("api.php", {action: 'get_stats'}, function(data) {
                $('#stat_total').text(data.total);
                $('#stat_sent').text(data.sent);
                $('#stat_pending').text(data.pending);
                $('#stat_failed').text(data.failed);
                let width = data.total > 0 ? Math.round((data.sent / data.total) * 100) : 0;
                $('#progressBar').css('width', width + '%');
                emailChart.data.datasets[0].data = [data.sent, data.pending, data.failed];
                emailChart.update();
                
                // Initial Load (Last Saved)
                if($('#emailSubject').val() == '' && data.subject) {
                    $('#emailSubject').val(data.subject);
                    $('#summernote').summernote('code', data.body);
                }
            });
        }

        // 3. UI Logic
        function toggleMenu() { document.getElementById("wrapper").classList.toggle("toggled"); }
        function switchTab(viewId, link) {
            $('.view-section').addClass('d-none'); $('#view-' + viewId).removeClass('d-none');
            $('.list-group-item').removeClass('active'); $(link).addClass('active');
            if(window.innerWidth <= 768) toggleMenu();
            if(viewId === 'clients') loadClients('all');
            if(viewId === 'history') loadClients('Sent');
        }

        // 4. Actions
        function saveTemplate() {
            $.post("api.php", {action: 'save_template', subject: $('#emailSubject').val(), body: $('#summernote').summernote('code')}, function(res) {
                Swal.fire({ icon: 'success', title: 'Saved!', timer: 1500, showConfirmButton: false });
            });
        }
        function bulkImport() {
            var emails = $('#bulkEmailsInput').val();
            if(!emails) return Swal.fire('Error', 'Empty list!', 'error');
            
            // Show processing alert for big lists
            Swal.fire({title: 'Importing...', text: 'Please wait', allowOutsideClick: false, didOpen: () => { Swal.showLoading() }});
            
            $.post("api.php", {action: 'bulk_import', emails: emails}, function(res) {
                Swal.fire('Success', res.message, 'success'); 
                $('#bulkModal').modal('hide'); $('#bulkEmailsInput').val(''); updateStats();
                if(!$('#view-clients').hasClass('d-none')) loadClients('all');
            });
        }
        function loadClients(filter) {
            var tableId = (filter === 'Sent') ? '#historyTable' : '#clientsTable';
            if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().destroy();
            $.post("api.php", {action: 'get_clients_list', filter: filter}, function(res) {
                var rows = "";
                res.data.forEach(function(item) {
                    var btn = (item.status === 'Sent') ? `<button class="btn btn-sm btn-outline-primary" onclick="resendEmail(${item.id})"><i class="fas fa-redo"></i> Resend</button>` : '-';
                    var badge = item.status == 'Sent' ? 'bg-success' : (item.status == 'Pending' ? 'bg-warning text-dark' : 'bg-danger');
                    rows += `<tr><td>#${item.id}</td><td>${item.email}</td><td><span class="badge ${badge} rounded-pill">${item.status}</span></td><td>${btn}</td></tr>`;
                });
                $(tableId + ' tbody').html(rows);
                $(tableId).DataTable({ pageLength: 10, lengthChange: false, searching: true });
            });
        }
        
        // --- FIXED RESEND FUNCTION ---
        function resendEmail(id) {
            $.post("api.php", {action: 'resend_email', id: id}, function() {
                const Toast = Swal.mixin({toast: true, position: 'top-end', showConfirmButton: false, timer: 2000});
                Toast.fire({icon: 'success', title: 'Queued for Resending'}); 
                
                // Immediately refresh table to show "Pending" status
                loadClients('Sent'); 
                updateStats();
            });
        }
        
        function startSending() {
            var btn = $('#startBtn'); var logs = $('#logs');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Sending...');
            function sendBatch() {
                $.post("process.php", function(response) {
                    try {
                        var res = JSON.parse(response); updateStats();
                        if (res.status === 'finished') {
                            Swal.fire('Finished!', 'All emails sent.', 'success'); btn.prop('disabled', false).html('START CAMPAIGN');
                        } else if (res.status === 'error') {
                            logs.prepend("<div class='log-entry text-danger'>❌ " + res.message + "</div>");
                        } else {
                            logs.prepend(res.log); setTimeout(sendBatch, 2000);
                        }
                    } catch(e) { console.log(response); }
                });
            }
            sendBatch();
        }
    </script>
</body>
</html>