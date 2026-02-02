<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultima Sender | Enterprise</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root { --primary: #6366f1; --dark: #0f172a; --bg: #f1f5f9; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: #334155; overflow-x: hidden; }
        
        .sidebar { width: 260px; height: 100vh; position: fixed; background: var(--dark); color: #fff; z-index: 1000; }
        .sidebar .brand { padding: 30px; font-size: 1.4rem; font-weight: 800; color: #fff; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .nav-item { padding: 14px 25px; cursor: pointer; color: #94a3b8; font-weight: 500; transition: 0.3s; display: flex; align-items: center; border-left: 3px solid transparent; }
        .nav-item:hover, .nav-item.active { background: rgba(255,255,255,0.05); color: #fff; border-left-color: var(--primary); }
        .nav-item i { width: 25px; font-size: 1.1rem; }

        .main { margin-left: 260px; padding: 30px; }
        .card { border: none; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); background: #fff; margin-bottom: 24px; }
        
        .stat-val { font-size: 2.2rem; font-weight: 700; color: var(--dark); line-height: 1; }
        .stat-lbl { font-size: 0.9rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .tpl-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 15px; }
        .tpl-card { border: 2px solid #e2e8f0; border-radius: 12px; padding: 20px; text-align: center; cursor: pointer; transition: 0.3s; }
        .tpl-card:hover, .tpl-card.active { border-color: var(--primary); background: #eef2ff; transform: translateY(-3px); }
        .tpl-card i { font-size: 2rem; color: var(--primary); margin-bottom: 10px; display: block; }

        #logs { background: #1e293b; color: #4ade80; height: 400px; overflow-y: auto; padding: 20px; font-family: 'Courier New', monospace; border-radius: 12px; font-size: 13px; line-height: 1.7; border: 1px solid #334155; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand"><i class="fas fa-paper-plane me-2"></i> ULTIMA PRO</div>
    <div class="nav-item active" onclick="tab('dashboard', this)"><i class="fas fa-chart-pie"></i> Dashboard</div>
    <div class="nav-item" onclick="tab('campaign', this)"><i class="fas fa-pen-nib"></i> Campaign</div>
    <div class="nav-item" onclick="tab('smtp', this)"><i class="fas fa-server"></i> SMTP Config</div>
    <div class="nav-item" onclick="tab('clients', this)"><i class="fas fa-users"></i> Recipients</div>
    <div class="nav-item" onclick="tab('history', this)"><i class="fas fa-history"></i> History</div>
</div>

<div class="main">
    
    <div id="dashboard" class="view">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div><h2 class="fw-bold m-0 text-dark">Dashboard</h2><p class="text-muted m-0">System Status & Analytics</p></div>
            <div>
                <span class="badge bg-white text-dark border px-3 py-2 fs-6 me-2 shadow-sm">Quota: <span id="quota" class="fw-bold text-primary">...</span></span>
                <button class="btn btn-danger btn-sm px-3 py-2 rounded-pill" onclick="resetQuota()"><i class="fas fa-sync-alt"></i> Reset</button>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-3"><div class="card p-4"><div class="stat-val" id="st_total">0</div><div class="stat-lbl mt-2">Total Targets</div></div></div>
            <div class="col-md-3"><div class="card p-4"><div class="stat-val text-success" id="st_sent">0</div><div class="stat-lbl mt-2">Sent Success</div></div></div>
            <div class="col-md-3"><div class="card p-4"><div class="stat-val text-warning" id="st_pending">0</div><div class="stat-lbl mt-2">In Queue</div></div></div>
            <div class="col-md-3"><div class="card p-4"><div class="stat-val text-danger" id="st_failed">0</div><div class="stat-lbl mt-2">Failed</div></div></div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card p-4 h-100">
                    <h5 class="fw-bold mb-4"><i class="fas fa-terminal me-2"></i>Live Server Terminal</h5>
                    <div id="logs"><div>> System Initialized... Waiting for command...</div></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card p-4 h-100">
                    <h5 class="fw-bold mb-4">Control Center</h5>
                    <div class="progress mb-4" style="height: 10px; border-radius: 5px;"><div id="prog" class="progress-bar bg-primary" style="width:0%"></div></div>
                    <button id="startBtn" class="btn btn-primary w-100 py-3 mb-3 fw-bold rounded-3 shadow-sm" onclick="startSending()"><i class="fas fa-rocket me-2"></i> START BLAST</button>
                    <button class="btn btn-outline-dark w-100 py-2 rounded-3" onclick="stopSending()"><i class="fas fa-pause me-2"></i> PAUSE</button>
                    <div class="mt-4 text-center text-muted small">
                        <i class="fas fa-shield-alt text-success me-1"></i> Anti-Spam Mode Active<br>
                        <i class="fas fa-sync text-primary me-1"></i> Auto-Rotation Enabled
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="campaign" class="view d-none">
        <div class="card p-4">
            <div class="d-flex justify-content-between mb-4">
                <h4 class="fw-bold">Email Designer</h4>
                <button class="btn btn-success px-4 rounded-pill" onclick="saveData()"><i class="fas fa-save me-2"></i> Save</button>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-md-6"><label class="fw-bold small text-muted mb-2">SENDER NAME</label><input type="text" id="senderName" class="form-control p-3" placeholder="e.g. Support Team"></div>
                <div class="col-md-6"><label class="fw-bold small text-muted mb-2">LOGO URL</label><input type="text" id="logoUrl" class="form-control p-3" placeholder="https://..." onchange="updateLogo()"></div>
            </div>
            <label class="fw-bold small text-muted mb-3">PROFESSIONAL TEMPLATES</label>
            <div class="tpl-grid mb-4">
                <div class="tpl-card" onclick="loadTpl('paypal', this)"><i class="fab fa-paypal"></i><span>PayPal</span></div>
                <div class="tpl-card" onclick="loadTpl('coinbase', this)"><i class="fas fa-university"></i><span>Coinbase</span></div>
                <div class="tpl-card" onclick="loadTpl('corporate', this)"><i class="fas fa-building"></i><span>Official</span></div>
                <div class="tpl-card" onclick="loadTpl('invoice', this)"><i class="fas fa-file-invoice-dollar"></i><span>Invoice</span></div>
                <div class="tpl-card" onclick="loadTpl('security', this)"><i class="fas fa-user-shield"></i><span>Security</span></div>
            </div>
            <div class="mb-3"><input type="text" id="subject" class="form-control p-3 fw-bold" placeholder="Subject Line..."></div>
            <textarea id="summernote"></textarea>
        </div>
    </div>

    <div id="smtp" class="view d-none">
        <div class="card p-4">
            <h4 class="fw-bold mb-4">SMTP Manager</h4>
            <div class="alert alert-light border d-flex align-items-center mb-4">
                <i class="fas fa-info-circle fs-4 text-primary me-3"></i>
                <div><strong>Format:</strong> <code>email@gmail.com|app_password</code> (One per line)</div>
            </div>
            <textarea id="smtpInput" class="form-control p-3 font-monospace" rows="10" placeholder="user1@outlook.com|app_pass&#10;user2@gmail.com|app_pass"></textarea>
            <button class="btn btn-primary w-100 py-3 mt-3 fw-bold rounded-3" onclick="addSmtp()"><i class="fas fa-plus-circle me-2"></i> Add Accounts</button>
        </div>
    </div>

    <div id="clients" class="view d-none">
        <div class="card p-4">
            <h4 class="fw-bold mb-4">Recipient Database</h4>
            <textarea id="bulkInput" class="form-control p-3 mb-3" rows="6" placeholder="Paste emails here..."></textarea>
            <button class="btn btn-dark w-100 py-2 rounded-3 mb-4" onclick="importEmails()"><i class="fas fa-upload me-2"></i> Import List</button>
            <table class="table table-hover" id="clientTable"><thead class="table-light"><tr><th>ID</th><th>Email</th><th>Status</th></tr></thead><tbody></tbody></table>
        </div>
    </div>

    <div id="history" class="view d-none">
        <div class="card p-4">
            <h4 class="fw-bold mb-4">Sent History</h4>
            <table class="table table-hover" id="historyTable"><thead class="table-light"><tr><th>ID</th><th>Email</th><th>Status</th></tr></thead><tbody></tbody></table>
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
    let currentLogo = "https://via.placeholder.com/150";

    $(document).ready(function(){
        $('#summernote').summernote({ height: 400 });
        refreshStats();
    });

    function tab(id, el) {
        $('.view').addClass('d-none');
        $('#'+id).removeClass('d-none');
        $('.nav-item').removeClass('active');
        $(el).addClass('active');
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
            
            if($('#subject').val() == '') {
                $('#subject').val(d.subject);
                $('#summernote').summernote('code', d.body);
                $('#senderName').val(d.sender_name);
                $('#logoUrl').val(d.logo_url);
                currentLogo = d.logo_url || currentLogo;
            }
        }, 'json');
    }

    function updateLogo(){ currentLogo = $('#logoUrl').val(); }

    const tpls = {
        paypal: `<div style="background:#f5f7f9;padding:40px 0;font-family:Arial,sans-serif;"><div style="max-width:500px;margin:auto;background:#fff;border:1px solid #e6e6e6;border-radius:8px;overflow:hidden;"><div style="padding:25px;text-align:center;border-bottom:1px solid #f0f0f0;"><img src="{{LOGO}}" width="120" style="display:block;margin:0 auto;"></div><div style="padding:40px 30px;text-align:center;"><h2 style="color:#2c2e2f;font-size:24px;margin-bottom:10px;">You sent a payment of $490.00 USD</h2><p style="color:#666;font-size:16px;">Transaction ID: 9283-9283-12</p><div style="margin:30px 0;text-align:left;"><table width="100%" style="border-collapse:collapse;"><tr style="border-bottom:1px solid #eee;"><td style="padding:10px 0;color:#666;">To Merchant</td><td style="text-align:right;font-weight:bold;">$490.00</td></tr><tr><td style="padding:15px 0;font-size:18px;font-weight:bold;">Total</td><td style="text-align:right;font-size:18px;font-weight:bold;color:#0070ba;">$490.00</td></tr></table></div><a href="#" style="background:#0070ba;color:#fff;padding:12px 35px;text-decoration:none;border-radius:25px;font-weight:bold;display:inline-block;">View Transaction Details</a></div></div></div>`,
        coinbase: `<div style="background:#fff;padding:40px 0;font-family:Arial,sans-serif;"><div style="max-width:550px;margin:auto;border:1px solid #ececec;border-radius:8px;"><div style="padding:30px;border-bottom:1px solid #ececec;text-align:center;"><img src="{{LOGO}}" width="100"></div><div style="padding:40px;"><h2 style="color:#1652f0;margin-top:0;">New Device Confirmation</h2><p style="color:#050f19;">We detected a sign-in attempt.</p><a href="#" style="background:#1652f0;color:#fff;padding:15px 0;display:block;text-align:center;text-decoration:none;border-radius:4px;font-weight:bold;">Authorize Device</a></div></div></div>`,
        corporate: `<div style="background:#eee;padding:40px 0;font-family:sans-serif;"><div style="max-width:600px;margin:auto;background:#fff;border-top:5px solid #2563eb;padding:40px;"><div style="text-align:center;margin-bottom:30px;"><img src="{{LOGO}}" width="120"></div><h2 style="color:#1f2937;">Official Notification</h2><p>Dear Client, please review your account updates.</p></div></div>`,
        invoice: `<div style="font-family:Arial,sans-serif;padding:20px;"><div style="max-width:600px;margin:auto;border:1px solid #ddd;padding:30px;"><table width="100%"><tr><td><img src="{{LOGO}}" width="100"></td><td style="text-align:right;"><h2 style="margin:0;color:#333;">INVOICE</h2><p style="color:#777;">#INV-2026</p></td></tr></table><hr style="margin:20px 0;border:0;border-top:1px solid #eee;"><table width="100%"><tr><td>Service Fee</td><td style="text-align:right;">$150.00</td></tr><tr><td style="font-weight:bold;">Total</td><td style="text-align:right;font-weight:bold;color:green;">$150.00</td></tr></table><br><a href="#" style="display:block;background:#333;color:#fff;padding:12px;text-align:center;text-decoration:none;">Pay Invoice</a></div></div>`,
        security: `<div style="background:#fff1f2;padding:40px 0;font-family:Arial;"><div style="max-width:500px;margin:auto;background:#fff;border:1px solid #ffe4e6;border-top:4px solid #e11d48;border-radius:8px;padding:40px;"><div style="text-align:center;"><img src="{{LOGO}}" width="60" style="margin-bottom:20px;"><h2 style="color:#be123c;">Security Alert</h2><p>Suspicious activity detected.</p></div></div></div>`
    };

    function loadTpl(key, el){
        $('.tpl-card').removeClass('active');
        $(el).addClass('active');
        $('#summernote').summernote('code', tpls[key].replace(/{{LOGO}}/g, currentLogo));
        Swal.fire({toast:true, position:'top-end', icon:'success', title:'Template Loaded!', showConfirmButton:false, timer:1000});
    }

    function startSending(){
        if(isSending) return;
        isSending = true;
        $('#startBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        
        function batch(){
            if(!isSending) return;
            $.post('process.php', function(res){
                if(typeof res === 'string') { 
                    try { res = JSON.parse(res); } 
                    catch(e) { console.error("Parse Error:", res); retryBatch(); return; } 
                }
                refreshStats();
                if(res.status == 'finished'){
                    isSending = false;
                    Swal.fire('Done!', 'All emails sent', 'success');
                    $('#startBtn').prop('disabled', false).html('<i class="fas fa-rocket me-2"></i> START BLAST');
                } else if(res.status == 'quota_error'){
                    isSending = false;
                    Swal.fire('Quota Exceeded!', 'Add more SMTP accounts.', 'error');
                    $('#startBtn').prop('disabled', false).html('<i class="fas fa-rocket me-2"></i> START BLAST');
                } else {
                    $('#logs').prepend('<div>' + res.log + '</div>');
                    // সফল হলে ১ সেকেন্ড পর পরেরটা
                    setTimeout(batch, 1000);
                }
            }).fail(function() {
                // নেটওয়ার্ক ফেল করলে রিকানেক্ট চেষ্টা করবে
                retryBatch();
            });
        }
        
        function retryBatch() {
            if(!isSending) return;
            $('#logs').prepend('<div style="color:orange">⚠ Network error! Retrying in 3s...</div>');
            setTimeout(batch, 3000);
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
            let h=''; res.data.forEach(r=>{ h+=`<tr><td>${r.id}</td><td>${r.email}</td><td>${r.status}</td></tr>`; });
            $(t+' tbody').html(h); $(t).DataTable();
        });
    }
</script>
</body>
</html>