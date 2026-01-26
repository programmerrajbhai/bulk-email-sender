<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultima Sender Pro | Enterprise</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --secondary: #6366f1;
            --dark: #0f172a;
            --light: #f8fafc;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            overflow-x: hidden;
        }

        /* --- Sidebar Design --- */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            background: var(--dark);
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 4px 0 24px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .brand-logo {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(to right, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        .nav-links {
            padding: 20px 15px;
        }

        .nav-item {
            padding: 14px 20px;
            margin-bottom: 8px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            color: #94a3b8;
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .nav-item i { width: 25px; font-size: 1.1rem; }

        .nav-item:hover, .nav-item.active {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }

        .nav-item.active {
            background: var(--primary);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
        }

        /* --- Main Content --- */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            transition: all 0.3s ease;
        }

        /* --- Cards & UI Elements --- */
        .card-custom {
            background: white;
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            transition: transform 0.2s;
            padding: 25px;
            height: 100%;
        }

        .card-custom:hover { transform: translateY(-2px); }

        .stat-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-value { font-size: 2rem; font-weight: 700; color: var(--dark); line-height: 1.2; }
        .stat-label { color: #64748b; font-size: 0.9rem; font-weight: 600; }

        /* --- Buttons --- */
        .btn-gradient {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
            color: white;
        }

        .btn-dark-outline {
            background: transparent;
            border: 2px solid var(--dark);
            color: var(--dark);
            padding: 10px 25px;
            border-radius: 10px;
            font-weight: 600;
        }

        /* --- Template Grid --- */
        .tpl-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
        }

        .tpl-item {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .tpl-item:hover {
            border-color: var(--primary);
            background: #eef2ff;
        }

        .tpl-item.selected {
            border-color: var(--primary);
            background: var(--primary);
            color: white;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }
        
        .tpl-item.selected i { color: white; }
        .tpl-item.selected span { color: white; }

        .tpl-item i { font-size: 2rem; color: var(--primary); margin-bottom: 10px; display: block; transition: 0.2s; }
        .tpl-item span { font-weight: 600; color: #475569; font-size: 0.9rem; }

        /* --- Terminal --- */
        #logs {
            background: #1e293b;
            color: #4ade80;
            font-family: 'JetBrains Mono', monospace;
            padding: 20px;
            border-radius: 12px;
            height: 380px;
            overflow-y: auto;
            font-size: 13px;
            line-height: 1.6;
            border: 1px solid #334155;
            box-shadow: inset 0 0 20px rgba(0,0,0,0.5);
        }
        
        .log-entry { border-bottom: 1px solid rgba(255,255,255,0.1); padding: 4px 0; }

        /* --- Inputs --- */
        .form-control {
            border: 1px solid #cbd5e1;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .mobile-toggle { display: block !important; }
        }
    </style>
</head>
<body>

<div class="d-md-none bg-white p-3 shadow-sm d-flex justify-content-between align-items-center fixed-top mobile-toggle" style="display:none; z-index:1001;">
    <span class="brand-logo fs-4">ULTIMA</span>
    <button class="btn btn-light" onclick="document.querySelector('.sidebar').classList.toggle('active')"><i class="fas fa-bars"></i></button>
</div>

<div class="sidebar">
    <div class="sidebar-header">
        <div class="brand-logo"><i class="fas fa-paper-plane me-2"></i> ULTIMA PRO</div>
    </div>
    <div class="nav-links">
        <div class="nav-item active" onclick="tab('dashboard', this)"><i class="fas fa-chart-pie"></i> <span>Dashboard</span></div>
        <div class="nav-item" onclick="tab('campaign', this)"><i class="fas fa-pen-nib"></i> <span>Email Designer</span></div>
        <div class="nav-item" onclick="tab('smtp', this)"><i class="fas fa-server"></i> <span>SMTP Manager</span></div>
        <div class="nav-item" onclick="tab('clients', this)"><i class="fas fa-users"></i> <span>Client List</span></div>
        <div class="nav-item" onclick="tab('history', this)"><i class="fas fa-history"></i> <span>History</span></div>
    </div>
</div>

<div class="main-content">
    
    <div id="dashboard" class="view-section">
        <div class="d-flex justify-content-between align-items-center mb-5 mt-4 mt-md-0">
            <div>
                <h2 class="fw-bold m-0 text-dark">Dashboard Overview</h2>
                <p class="text-muted">Welcome back, system is ready to blast.</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white px-3 py-2 rounded-3 shadow-sm">
                    <span class="text-muted small fw-bold text-uppercase">Quota Left</span>
                    <div class="fw-bold text-primary" id="quota">Checking...</div>
                </div>
                <button class="btn btn-outline-danger btn-sm rounded-circle p-2" onclick="resetQuota()" title="Reset Limit"><i class="fas fa-sync-alt"></i></button>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-6 col-xl-3">
                <div class="card-custom stat-card">
                    <div>
                        <div class="stat-value" id="st_total">0</div>
                        <div class="stat-label">Total Recipients</div>
                    </div>
                    <div class="stat-icon bg-light text-primary"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card-custom stat-card">
                    <div>
                        <div class="stat-value text-success" id="st_sent">0</div>
                        <div class="stat-label">Successfully Sent</div>
                    </div>
                    <div class="stat-icon bg-light text-success"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card-custom stat-card">
                    <div>
                        <div class="stat-value text-warning" id="st_pending">0</div>
                        <div class="stat-label">Pending Queue</div>
                    </div>
                    <div class="stat-icon bg-light text-warning"><i class="fas fa-clock"></i></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="card-custom stat-card">
                    <div>
                        <div class="stat-value text-danger" id="st_failed">0</div>
                        <div class="stat-label">Failed Delivery</div>
                    </div>
                    <div class="stat-icon bg-light text-danger"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card-custom">
                    <h5 class="fw-bold mb-4"><i class="fas fa-terminal me-2"></i>Live Server Terminal</h5>
                    <div id="logs">
                        <div class="log-entry">> System initialized...</div>
                        <div class="log-entry">> Waiting for command...</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-custom">
                    <h5 class="fw-bold mb-4">Control Center</h5>
                    <label class="small fw-bold text-muted mb-2">PROGRESS</label>
                    <div class="progress mb-4" style="height: 10px; border-radius: 5px;">
                        <div id="prog" class="progress-bar bg-gradient" style="width: 0%"></div>
                    </div>
                    
                    <button id="startBtn" class="btn btn-gradient w-100 py-3 mb-3" onclick="startSending()">
                        <i class="fas fa-rocket me-2"></i> START CAMPAIGN
                    </button>
                    <button class="btn btn-dark-outline w-100 mb-3" onclick="stopSending()">
                        <i class="fas fa-pause me-2"></i> PAUSE
                    </button>
                    <div class="text-center">
                        <small class="text-muted">Speed: 1 mail/sec (Safe Mode)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="campaign" class="view-section d-none">
        <div class="card-custom">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold m-0">Campaign Editor</h4>
                <button class="btn btn-success btn-sm px-4" onclick="saveData()"><i class="fas fa-save me-2"></i> Save Changes</button>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="fw-bold small text-uppercase text-muted mb-2">Sender Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-user"></i></span>
                        <input type="text" id="senderName" class="form-control" placeholder="e.g. Support Team">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold small text-uppercase text-muted mb-2">Company Logo URL</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-image"></i></span>
                        <input type="text" id="logoUrl" class="form-control" placeholder="https://your-logo.png" onchange="updateLogo()">
                    </div>
                </div>
            </div>

            <label class="fw-bold small text-uppercase text-muted mb-3">Professional Templates</label>
            <div class="tpl-grid mb-4">
                <div class="tpl-item" onclick="loadTpl('paypal', this)"><i class="fab fa-paypal"></i><span>PayPal</span></div>
                <div class="tpl-item" onclick="loadTpl('coinbase', this)"><i class="fas fa-university"></i><span>Coinbase</span></div>
                <div class="tpl-item" onclick="loadTpl('corporate', this)"><i class="fas fa-building"></i><span>Official</span></div>
                <div class="tpl-item" onclick="loadTpl('invoice', this)"><i class="fas fa-file-invoice-dollar"></i><span>Invoice</span></div>
                <div class="tpl-item" onclick="loadTpl('security', this)"><i class="fas fa-shield-alt"></i><span>Security</span></div>
                <div class="tpl-item" onclick="loadTpl('welcome', this)"><i class="fas fa-hand-sparkles"></i><span>Welcome</span></div>
                <div class="tpl-item" onclick="loadTpl('offer', this)"><i class="fas fa-tags"></i><span>Promo</span></div>
                <div class="tpl-item" onclick="loadTpl('social', this)"><i class="fas fa-bell"></i><span>Social</span></div>
            </div>

            <div class="mb-3">
                <label class="fw-bold small text-uppercase text-muted mb-2">Subject Line</label>
                <input type="text" id="subject" class="form-control fw-bold" placeholder="Important Notification...">
            </div>
            
            <textarea id="summernote"></textarea>
        </div>
    </div>

    <div id="smtp" class="view-section d-none">
        <div class="card-custom">
            <h4 class="fw-bold mb-4">SMTP Configuration</h4>
            <div class="alert alert-primary d-flex align-items-center" role="alert">
                <i class="fas fa-info-circle fs-4 me-3"></i>
                <div>
                    <strong>Format (One per line):</strong><br>
                    Gmail: <code>email@gmail.com|app_password</code><br>
                    Others: <code>host|email|password</code>
                </div>
            </div>
            <textarea id="smtpInput" class="form-control mb-3 font-monospace" rows="10" placeholder="user1@gmail.com|abcd1234&#10;smtp.office365.com|user@outlook.com|pass456"></textarea>
            <button class="btn btn-primary w-100 py-3 fw-bold" onclick="addSmtp()"><i class="fas fa-plus-circle me-2"></i> Add Accounts</button>
        </div>
    </div>

    <div id="clients" class="view-section d-none">
        <div class="card-custom">
            <h4 class="fw-bold mb-4">Client Database</h4>
            <div class="mb-4">
                <label class="fw-bold small text-muted mb-2">Bulk Import Emails</label>
                <textarea id="bulkInput" class="form-control mb-3" rows="6" placeholder="Paste email list here (unlimited)..."></textarea>
                <button class="btn btn-dark w-100 py-2" onclick="importEmails()"><i class="fas fa-file-import me-2"></i> Import List</button>
            </div>
            <hr>
            <h5 class="fw-bold mt-4 mb-3">Database Preview</h5>
            <table class="table table-hover align-middle" id="clientTable">
                <thead class="table-light"><tr><th>ID</th><th>Email</th><th>Status</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div id="history" class="view-section d-none">
        <div class="card-custom">
            <h4 class="fw-bold mb-4">Sent History</h4>
            <table class="table table-hover align-middle" id="historyTable">
                <thead class="table-light"><tr><th>ID</th><th>Email</th><th>Status</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let isSending = false;
    let currentLogo = "https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg"; 

    $(document).ready(function(){
        $('#summernote').summernote({
            height: 400,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['codeview']]
            ]
        });
        refreshStats();
    });

    function tab(id, el) {
        $('.view-section').addClass('d-none');
        $('#'+id).removeClass('d-none');
        $('.nav-item').removeClass('active');
        $(el).addClass('active');
        
        // Mobile Sidebar Close
        if(window.innerWidth < 768) document.querySelector('.sidebar').classList.remove('active');

        if(id === 'clients') loadTable('all');
        if(id === 'history') loadTable('Sent');
    }

    function refreshStats() {
        $.post('api.php', {action:'get_stats'}, function(d){
            $('#st_total').text(d.total); $('#st_sent').text(d.sent);
            $('#st_pending').text(d.pending); $('#st_failed').text(d.failed);
            $('#quota').text(d.quota);
            let p = d.total > 0 ? Math.round((d.sent/d.total)*100) : 0;
            $('#prog').css('width', p+'%');
            
            // Sync Input fields
            if($('#subject').val() == '') {
                $('#subject').val(d.subject);
                $('#summernote').summernote('code', d.body);
                $('#senderName').val(d.sender_name);
                $('#logoUrl').val(d.logo_url);
                if(d.logo_url) currentLogo = d.logo_url;
            }
        }, 'json');
    }

    function updateLogo(){ currentLogo = $('#logoUrl').val(); }

    // --- PRO TEMPLATES ---
    const tpls = {
        paypal: `<div style="background:#f5f7f9;padding:40px 0;font-family:Arial,sans-serif;"><div style="max-width:500px;margin:auto;background:#fff;border:1px solid #e6e6e6;border-radius:8px;overflow:hidden;"><div style="padding:25px;text-align:center;border-bottom:1px solid #f0f0f0;"><img src="{{LOGO}}" width="120" style="display:block;margin:0 auto;"></div><div style="padding:40px 30px;text-align:center;"><h2 style="color:#2c2e2f;font-size:24px;margin-bottom:10px;">You sent a payment of $490.00 USD</h2><p style="color:#666;font-size:16px;">Transaction ID: 9283-9283-12</p><div style="margin:30px 0;text-align:left;"><table width="100%" style="border-collapse:collapse;"><tr style="border-bottom:1px solid #eee;"><td style="padding:10px 0;color:#666;">To Merchant</td><td style="text-align:right;font-weight:bold;">$490.00</td></tr><tr><td style="padding:15px 0;font-size:18px;font-weight:bold;">Total</td><td style="text-align:right;font-size:18px;font-weight:bold;color:#0070ba;">$490.00</td></tr></table></div><a href="#" style="background:#0070ba;color:#fff;padding:12px 35px;text-decoration:none;border-radius:25px;font-weight:bold;display:inline-block;">View Transaction Details</a></div></div></div>`,
        
        coinbase: `<div style="background:#fff;padding:40px 0;font-family:Arial,sans-serif;"><div style="max-width:550px;margin:auto;border:1px solid #ececec;border-radius:8px;"><div style="padding:30px;border-bottom:1px solid #ececec;text-align:center;"><img src="{{LOGO}}" width="100"></div><div style="padding:40px;"><h2 style="color:#1652f0;margin-top:0;">New Device Confirmation</h2><p style="color:#050f19;font-size:16px;">We detected a sign-in attempt from a new device.</p><div style="background:#f4f6f8;padding:15px;border-radius:4px;margin:20px 0;color:#5b616e;font-size:14px;"><p><strong>Device:</strong> Mac OS X - Chrome</p><p><strong>Location:</strong> Dhaka, BD</p></div><a href="#" style="background:#1652f0;color:#fff;padding:15px 0;display:block;text-align:center;text-decoration:none;border-radius:4px;font-weight:bold;">I Authorize This Device</a></div></div></div>`,
        
        corporate: `<div style="background:#eee;padding:40px 0;font-family:sans-serif;"><div style="max-width:600px;margin:auto;background:#fff;border-top:5px solid #2563eb;padding:40px;"><div style="text-align:center;margin-bottom:30px;"><img src="{{LOGO}}" width="120"></div><h2 style="color:#1f2937;">Official Notification</h2><p style="color:#4b5563;line-height:1.6;">Dear Client,<br><br>We are writing to inform you about important updates to our service terms.</p><br><a href="#" style="background:#2563eb;color:#fff;padding:12px 25px;text-decoration:none;border-radius:4px;">Read Full Notice</a></div></div>`,
        
        invoice: `<div style="font-family:Arial,sans-serif;padding:20px;"><div style="max-width:600px;margin:auto;border:1px solid #ddd;padding:30px;"><table width="100%"><tr><td><img src="{{LOGO}}" width="100"></td><td style="text-align:right;"><h2 style="margin:0;color:#333;">INVOICE</h2><p style="color:#777;">#INV-2026</p></td></tr></table><hr style="margin:20px 0;border:0;border-top:1px solid #eee;"><table width="100%"><tr><td>Service Fee</td><td style="text-align:right;">$150.00</td></tr><tr><td style="font-weight:bold;">Total</td><td style="text-align:right;font-weight:bold;color:green;">$150.00</td></tr></table><br><a href="#" style="display:block;background:#333;color:#fff;padding:12px;text-align:center;text-decoration:none;">Pay Invoice</a></div></div>`,

        security: `<div style="background:#fff1f2;padding:40px 0;font-family:Arial;"><div style="max-width:500px;margin:auto;background:#fff;border:1px solid #ffe4e6;border-top:4px solid #e11d48;border-radius:8px;padding:40px;"><div style="text-align:center;"><img src="{{LOGO}}" width="60" style="margin-bottom:20px;"><h2 style="color:#be123c;margin:0;">Security Alert</h2><p style="color:#374151;font-size:16px;margin:15px 0;">We detected a new login.</p><a href="#" style="background:#e11d48;color:#fff;padding:12px 30px;text-decoration:none;border-radius:6px;display:inline-block;font-weight:bold;">Secure My Account</a></div></div></div>`,

        welcome: `<div style="background:#eff6ff;padding:40px 0;font-family:Verdana;"><div style="max-width:500px;margin:auto;background:#fff;border-radius:16px;padding:40px;text-align:center;"><img src="{{LOGO}}" width="100" style="margin-bottom:20px;"><h1 style="color:#2563eb;margin:0;">Welcome Aboard! 🎉</h1><p style="color:#4b5563;font-size:16px;margin-top:10px;">We are thrilled to have you with us.</p><br><a href="#" style="background:#2563eb;color:#fff;padding:15px 40px;text-decoration:none;border-radius:50px;font-weight:bold;">Get Started</a></div></div>`,

        offer: `<div style="background:#000;padding:40px 0;font-family:'Arial Black';text-align:center;"><div style="max-width:600px;margin:auto;background:#fff;padding:40px;border:5px solid #000;"><img src="{{LOGO}}" width="150" style="margin-bottom:20px;"><h1 style="color:#dc2626;font-size:48px;margin:0;">FLASH SALE</h1><h2 style="margin:10px 0;">50% OFF EVERYTHING</h2><br><a href="#" style="background:#dc2626;color:#fff;padding:15px 40px;text-decoration:none;display:inline-block;font-size:20px;font-weight:bold;">SHOP NOW</a></div></div>`,

        social: `<div style="background:#f0f2f5;padding:40px 0;font-family:Arial;"><div style="max-width:500px;margin:auto;background:#fff;border:1px solid #ddd;border-radius:8px;padding:20px;"><div style="display:flex;align-items:center;"><img src="{{LOGO}}" width="50" style="border-radius:50%;margin-right:15px;"><div><strong style="font-size:16px;color:#1877f2;">New Notification</strong><p style="margin:0;color:#606770;">You have a new message request.</p></div></div><br><a href="#" style="display:block;background:#1877f2;color:#fff;text-align:center;padding:10px;text-decoration:none;border-radius:6px;">View Message</a></div></div>`
    };

    function loadTpl(key, el){
        // Visual Selection
        $('.tpl-item').removeClass('selected');
        $(el).addClass('selected');

        $('#summernote').summernote('code', tpls[key].replace(/{{LOGO}}/g, currentLogo));
        Swal.fire({
            toast: true, position: 'top-end', icon: 'success', 
            title: 'Template Loaded', showConfirmButton: false, timer: 1000
        });
    }

    function startSending(){
        if(isSending) return;
        isSending = true;
        $('#startBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Sending...');
        
        function batch(){
            if(!isSending) return;
            $.post('process.php', function(res){
                if(typeof res === 'string') {
                    try { res = JSON.parse(res); } catch(e) { console.error("Parse Error:", res); return; }
                }
                
                refreshStats();
                if(res.status == 'finished'){
                    isSending = false;
                    Swal.fire({icon: 'success', title: 'Mission Complete!', text: 'All emails sent successfully.'});
                    $('#startBtn').prop('disabled', false).html('<i class="fas fa-rocket me-2"></i> START CAMPAIGN');
                } else if(res.status == 'quota_error'){
                    isSending = false;
                    Swal.fire('Quota Exceeded!', 'Add more SMTP accounts.', 'error');
                    $('#logs').prepend('<div class="log-entry text-warning">⚠️ System Paused: Quota Exceeded</div>');
                    $('#startBtn').prop('disabled', false).html('<i class="fas fa-rocket me-2"></i> START CAMPAIGN');
                } else {
                    $('#logs').prepend('<div class="log-entry">' + res.log + '</div>');
                    setTimeout(batch, 1000);
                }
            });
        }
        batch();
    }

    function stopSending() { isSending = false; $('#startBtn').prop('disabled', false).html('<i class="fas fa-play me-2"></i> RESUME'); }
    function saveData() { $.post('api.php', {action:'save_template', subject:$('#subject').val(), body:$('#summernote').summernote('code'), sender_name:$('#senderName').val(), logo_url:$('#logoUrl').val()}, ()=>Swal.fire('Saved!')); }
    function importEmails() { $.post('api.php', {action:'bulk_import', emails:$('#bulkInput').val()}, (res)=>{ Swal.fire(res.message); refreshStats(); }); }
    function addSmtp() { $.post('api.php', {action:'add_smtp', accounts:$('#smtpInput').val()}, (res)=>{ Swal.fire(res.message); refreshStats(); }); }
    function resetQuota() { $.post('api.php', {action:'reset_quota'}, ()=>{ Swal.fire('Reset Done!'); refreshStats(); }); }
    function loadTable(f) { 
        let t = f=='Sent'?'#historyTable':'#clientTable'; 
        if($.fn.DataTable.isDataTable(t)) $(t).DataTable().destroy();
        $.post('api.php', {action:'get_clients_list', filter:f}, (res)=>{
            let h=''; res.data.forEach(r=>{ 
                let badge = r.status == 'Sent' ? 'bg-success' : (r.status == 'Pending' ? 'bg-warning' : 'bg-danger');
                h+=`<tr><td>#${r.id}</td><td>${r.email}</td><td><span class="badge ${badge}">${r.status}</span></td></tr>`; 
            });
            $(t+' tbody').html(h); $(t).DataTable();
        });
    }
</script>
</body>
</html>