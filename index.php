<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultima Sender Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .sidebar { width: 260px; position: fixed; height: 100vh; background: white; border-right: 1px solid #ddd; z-index: 100; }
        .content { margin-left: 260px; padding: 25px; }
        .nav-link { color: #555; padding: 12px 20px; font-weight: 500; cursor: pointer; }
        .nav-link.active { background: #e7f1ff; color: #0d6efd; border-right: 4px solid #0d6efd; }
        .card { border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-radius: 12px; margin-bottom: 20px; }
        .template-card { border: 1px solid #ddd; padding: 10px; border-radius: 8px; cursor: pointer; text-align: center; background: white; transition: 0.2s; }
        .template-card:hover { border-color: #0d6efd; transform: translateY(-3px); }
        #logs { background: #1e1e1e; color: #4cd137; height: 300px; overflow-y: auto; padding: 15px; font-family: monospace; border-radius: 8px; }
        @media(max-width:768px){ .sidebar{display:none;} .content{margin-left:0;} }
    </style>
</head>
<body>

<div class="sidebar d-none d-md-block">
    <div class="p-4 text-center text-primary fw-bold fs-4"><i class="fas fa-paper-plane"></i> Ultima Pro</div>
    <nav class="nav flex-column">
        <a class="nav-link active" onclick="showTab('dashboard', this)"><i class="fas fa-chart-pie me-2"></i> Dashboard</a>
        <a class="nav-link" onclick="showTab('campaign', this)"><i class="fas fa-pen-nib me-2"></i> Campaign</a>
        <a class="nav-link" onclick="showTab('clients', this)"><i class="fas fa-users me-2"></i> Client List</a>
        <a class="nav-link" onclick="showTab('history', this)"><i class="fas fa-history me-2"></i> History</a>
    </nav>
</div>

<div class="content">
    
    <div id="dashboard" class="page-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Dashboard</h3>
            <button class="btn btn-outline-danger btn-sm" onclick="resetQuota()"><i class="fas fa-sync-alt"></i> Reset Daily Limit</button>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3"><div class="card p-4 text-center"><h3><span id="st_total">0</span></h3><small class="text-muted">Total Emails</small></div></div>
            <div class="col-md-3"><div class="card p-4 text-center text-success"><h3><span id="st_sent">0</span></h3><small class="text-muted">Sent</small></div></div>
            <div class="col-md-3"><div class="card p-4 text-center text-warning"><h3><span id="st_pending">0</span></h3><small class="text-muted">Pending</small></div></div>
            <div class="col-md-3"><div class="card p-4 text-center text-danger"><h3><span id="st_failed">0</span></h3><small class="text-muted">Failed</small></div></div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card p-4 h-100">
                    <h5 class="mb-3">Live Terminal</h5>
                    <div id="logs">System Ready... Waiting for command.</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 h-100">
                    <h5 class="mb-3">Action Center</h5>
                    <div class="progress mb-3" style="height: 10px;"><div id="progBar" class="progress-bar bg-primary" style="width:0%"></div></div>
                    <button id="startBtn" class="btn btn-primary w-100 py-2 mb-3" onclick="startSending()"><i class="fas fa-rocket"></i> START SENDING</button>
                    <button class="btn btn-outline-dark w-100 py-2" onclick="location.reload()">Refresh System</button>
                </div>
            </div>
        </div>
    </div>

    <div id="campaign" class="page-section d-none">
        <div class="card p-4">
            <h4 class="mb-4">Design Email</h4>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="fw-bold">Sender Name (From)</label>
                    <input type="text" id="senderName" class="form-control" placeholder="e.g. Support Team">
                </div>
                <div class="col-md-6">
                    <label class="fw-bold">Logo URL (Direct Link)</label>
                    <input type="text" id="logoUrl" class="form-control" placeholder="https://i.imgur.com/example.png" onchange="updateLogo()">
                    <small class="text-muted">Paste your logo link here. It will appear in templates.</small>
                </div>
            </div>

            <label class="fw-bold mb-2">Select Template:</label>
            <div class="row g-2 mb-4">
                <div class="col-6 col-md-3"><div class="template-card" onclick="loadTpl('fintech')"><i class="fas fa-wallet text-primary"></i> Fintech</div></div>
                <div class="col-6 col-md-3"><div class="template-card" onclick="loadTpl('security')"><i class="fas fa-shield-alt text-danger"></i> Security</div></div>
                <div class="col-6 col-md-3"><div class="template-card" onclick="loadTpl('corporate')"><i class="fas fa-building text-dark"></i> Corporate</div></div>
                <div class="col-6 col-md-3"><div class="template-card" onclick="loadTpl('invoice')"><i class="fas fa-file-invoice text-success"></i> Invoice</div></div>
            </div>

            <div class="mb-3">
                <label class="fw-bold">Subject</label>
                <input type="text" id="subject" class="form-control" placeholder="Enter Subject">
            </div>
            <textarea id="summernote"></textarea>
            <button class="btn btn-success mt-3 w-100" onclick="saveData()">Save Campaign</button>
        </div>
    </div>

    <div id="clients" class="page-section d-none">
        <div class="card p-4">
            <div class="mb-3">
                <label class="fw-bold">Import Emails (Unlimited)</label>
                <textarea id="bulkInput" class="form-control" rows="5" placeholder="Paste emails here (one per line)..."></textarea>
                <button class="btn btn-dark mt-2 w-100" onclick="importEmails()">Add Emails</button>
            </div>
            <table class="table table-hover" id="clientTable">
                <thead class="table-light"><tr><th>ID</th><th>Email</th><th>Status</th><th>Action</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    
    <div id="history" class="page-section d-none">
        <div class="card p-4">
            <h4>Sent History</h4>
            <table class="table table-hover" id="historyTable">
                <thead class="table-light"><tr><th>ID</th><th>Email</th><th>Status</th><th>Action</th></tr></thead>
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
    let currentLogo = "https://via.placeholder.com/150x50?text=LOGO";
    
    $(document).ready(function(){
        $('#summernote').summernote({height: 350});
        refreshStats();
    });

    function showTab(id, el) {
        $('.page-section').addClass('d-none');
        $('#'+id).removeClass('d-none');
        $('.nav-link').removeClass('active');
        $(el).addClass('active');
        if(id=='clients') loadTable('all');
        if(id=='history') loadTable('Sent');
    }

    function updateLogo(){ 
        let val = $('#logoUrl').val();
        if(val) currentLogo = val;
    }

    // --- PRO TEMPLATES ---
    const templates = {
        fintech: `<div style="font-family:Arial, sans-serif; background:#f4f7f6; padding:40px 0;"><div style="max-width:500px; margin:auto; background:#fff; border-radius:8px; overflow:hidden; border:1px solid #e1e4e8;"><div style="padding:30px; text-align:center; border-bottom:1px solid #f0f0f0;"><img src="{{LOGO}}" width="120" style="margin-bottom:10px;"></div><div style="padding:40px 30px; text-align:center;"><h2 style="color:#333; margin-top:0;">Transaction Alert</h2><p style="font-size:16px; color:#555;">You authorized a payment of <strong>$490.00 USD</strong>.</p><div style="background:#fff4f4; border:1px solid #ffcccc; padding:15px; border-radius:5px; text-align:left; color:#d93025; font-size:14px; margin:20px 0;"><strong>Warning:</strong> If this wasn't you, please secure your account immediately.</div><a href="#" style="background:#0070ba; color:#fff; padding:12px 30px; text-decoration:none; border-radius:25px; font-weight:bold; display:inline-block;">View Transaction</a></div><div style="background:#f9f9f9; padding:15px; text-align:center; font-size:12px; color:#888;"><p>&copy; 2026 Support Team.</p></div></div></div>`,
        
        security: `<div style="font-family:Roboto, sans-serif; background:#fff0f0; padding:40px 0;"><div style="max-width:500px; margin:auto; background:#fff; border-radius:8px; border:1px solid #ffcccc;"><div style="padding:40px; text-align:center;"><img src="{{LOGO}}" width="60" style="margin-bottom:20px;"><h2 style="color:#d93025; margin:0;">Security Alert</h2><p style="color:#333; margin:15px 0;">New sign-in detected from a new device.</p><div style="background:#f9f9f9; padding:15px; border-left:4px solid #d93025; text-align:left; font-size:14px; color:#555; margin:20px 0;"><p style="margin:5px 0;"><strong>Device:</strong> Windows 10</p><p style="margin:5px 0;"><strong>Location:</strong> Dhaka, Bangladesh</p></div><a href="#" style="background:#d93025; color:#fff; padding:12px 30px; text-decoration:none; border-radius:4px; display:inline-block;">Secure Account</a></div></div></div>`,
        
        corporate: `<div style="font-family:sans-serif; background:#f4f4f4; padding:40px 0;"><div style="max-width:600px; margin:auto; background:#fff; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.05);"><div style="padding:30px; border-bottom:3px solid #0056b3; text-align:center;"><img src="{{LOGO}}" width="120"></div><div style="padding:40px;"><h2 style="color:#333; margin-top:0;">Official Notification</h2><p style="color:#555; line-height:1.6;">Dear Customer,</p><p style="color:#555; line-height:1.6;">This is an automated notification regarding your recent request. Please find the details in your dashboard.</p><br><div style="text-align:center;"><a href="#" style="background:#0056b3; color:#fff; padding:12px 25px; text-decoration:none; border-radius:4px;">Go to Dashboard</a></div></div></div></div>`,
        
        invoice: `<div style="font-family:Arial, sans-serif; background:#f8f9fa; padding:40px 0;"><div style="max-width:600px; margin:auto; background:#fff; border:1px solid #ddd; padding:40px;"><div style="text-align:center; margin-bottom:30px;"><img src="{{LOGO}}" width="100"></div><h2 style="border-bottom:2px solid #eee; padding-bottom:10px;">INVOICE</h2><p>Total Due: <strong style="color:green;">$150.00</strong></p><table width="100%" style="margin:20px 0; border-collapse:collapse;"><tr style="background:#f9f9f9;"><td style="padding:10px; border:1px solid #eee;">Service Fee</td><td style="padding:10px; border:1px solid #eee; text-align:right;">$150.00</td></tr></table><a href="#" style="display:block; background:#333; color:#fff; padding:12px; text-align:center; text-decoration:none;">Pay Now</a></div></div>`
    };

    function loadTpl(key){
        let html = templates[key].replace(/{{LOGO}}/g, currentLogo);
        $('#summernote').summernote('code', html);
        Swal.fire({icon: 'success', title: 'Template Loaded', timer: 800, showConfirmButton: false});
    }

    function saveData(){
        $.post('api.php', {
            action: 'save_template',
            subject: $('#subject').val(),
            body: $('#summernote').summernote('code'),
            sender_name: $('#senderName').val()
        }, function(res){
            Swal.fire('Saved!', res.message, 'success');
        });
    }

    function resetQuota(){
        Swal.fire({
            title: 'Are you sure?',
            text: "This will reset daily limits for all SMTP accounts!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Reset!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('api.php', {action: 'reset_quota'}, function(res){
                    Swal.fire('Reset!', res.message, 'success');
                });
            }
        });
    }

    function importEmails(){
        let emails = $('#bulkInput').val();
        if(!emails) return Swal.fire('Error', 'Empty list', 'error');
        Swal.showLoading();
        $.post('api.php', {action:'bulk_import', emails:emails}, function(res){
            Swal.fire('Success', res.message, 'success');
            $('#bulkInput').val('');
            refreshStats();
        });
    }

    function refreshStats(){
        $.post('api.php', {action: 'get_stats'}, function(d){
            $('#st_total').text(d.total); $('#st_sent').text(d.sent);
            $('#st_pending').text(d.pending); $('#st_failed').text(d.failed);
            let p = d.total > 0 ? Math.round((d.sent/d.total)*100) : 0;
            $('#progBar').css('width', p+'%').text(p+'%');
            
            if($('#subject').val() == '') {
                $('#subject').val(d.subject);
                $('#summernote').summernote('code', d.body);
                $('#senderName').val(d.sender_name);
            }
        });
    }

    function loadTable(filter){
        let tbl = (filter == 'Sent') ? '#historyTable' : '#clientTable';
        if($.fn.DataTable.isDataTable(tbl)) $(tbl).DataTable().destroy();
        
        $.post('api.php', {action: 'get_clients_list', filter: filter}, function(res){
            let rows = '';
            res.data.forEach(item => {
                let btn = (item.status == 'Sent') ? `<button class="btn btn-sm btn-warning" onclick="resend(${item.id})">Resend</button>` : '';
                rows += `<tr><td>${item.id}</td><td>${item.email}</td><td>${item.status}</td><td>${btn}</td></tr>`;
            });
            $(tbl + ' tbody').html(rows);
            $(tbl).DataTable();
        });
    }

    function resend(id){
        $.post('api.php', {action: 'resend_email', id:id}, function(){
            const Toast = Swal.mixin({toast: true, position: 'top-end', showConfirmButton: false, timer: 2000});
            Toast.fire({icon: 'success', title: 'Added to Queue'});
            loadTable('Sent');
            refreshStats();
        });
    }

    function startSending(){
        let btn = $('#startBtn');
        let logs = $('#logs');
        btn.prop('disabled', true).text('Sending...');
        
        function sendBatch(){
            $.post('process.php', function(res){
                try {
                    if(typeof res === 'string') res = JSON.parse(res);
                    refreshStats();
                    
                    if(res.status == 'finished'){
                        Swal.fire('Done!', 'All emails sent', 'success');
                        btn.prop('disabled', false).text('🚀 START SENDING');
                    } else if(res.status == 'error'){
                        logs.prepend('<div class="text-danger">'+res.message+'</div>');
                        btn.prop('disabled', false).text('🚀 START SENDING');
                    } else {
                        logs.prepend(res.log);
                        setTimeout(sendBatch, 2000);
                    }
                } catch(e){ console.log(e); }
            });
        }
        sendBatch();
    }
</script>
</body>
</html>