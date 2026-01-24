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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-bg: #f3f4f6;
            --sidebar-width: 260px;
            --text-dark: #2d3748;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--secondary-bg);
            overflow-x: hidden;
        }

        /* --- Sidebar Styles --- */
        #wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            width: var(--sidebar-width);
            margin-left: -var(--sidebar-width);
            background: var(--primary-gradient);
            transition: margin .25s ease-out;
            position: fixed;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        #sidebar-wrapper .list-group {
            width: var(--sidebar-width);
        }

        #sidebar-wrapper .list-group-item {
            background-color: transparent;
            color: rgba(255, 255, 255, 0.8);
            border: none;
            padding: 1rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        #sidebar-wrapper .list-group-item:hover,
        #sidebar-wrapper .list-group-item.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            border-left: 4px solid #fff;
        }

        #sidebar-wrapper .list-group-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        #page-content-wrapper {
            width: 100%;
            margin-left: 0;
            transition: margin .25s ease-out;
        }

        /* Toggled State */
        #wrapper.toggled #sidebar-wrapper {
            margin-left: 0;
        }

        /* Desktop View */
        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }

            #page-content-wrapper {
                margin-left: var(--sidebar-width);
            }

            #wrapper.toggled #sidebar-wrapper {
                margin-left: -var(--sidebar-width);
            }
            
            #wrapper.toggled #page-content-wrapper {
                margin-left: 0;
            }
        }

        /* --- Top Navbar --- */
        .navbar-custom {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 15px 20px;
        }

        /* --- Cards & UI Elements --- */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            background: white;
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.2;
            position: absolute;
            right: 20px;
            top: 20px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        /* --- Terminal Logs --- */
        #logs {
            background: #1e1e2e;
            color: #50fa7b;
            height: 350px;
            overflow-y: auto;
            padding: 15px;
            font-family: 'Courier New', monospace;
            border-radius: 10px;
            border: 1px solid #333;
            font-size: 13px;
        }
        
        .log-entry {
            margin-bottom: 4px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding-bottom: 2px;
        }

        /* --- Buttons --- */
        .btn-gradient {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-gradient:hover {
            transform: scale(1.05);
            color: white;
            box-shadow: 0 10px 20px rgba(118, 75, 162, 0.4);
        }

        /* --- Tables --- */
        .table thead th {
            background-color: #f8f9fa;
            color: #6c757d;
            border-bottom: 2px solid #e9ecef;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Mobile Adjustments */
        .nav-link { cursor: pointer; }
    </style>
</head>

<body>

    <div class="d-flex" id="wrapper">

        <div id="sidebar-wrapper">
            <div class="sidebar-heading"><i class="fas fa-rocket me-2"></i>Ultima Pro</div>
            <div class="list-group list-group-flush mt-3">
                <a class="list-group-item list-group-item-action active" id="tab-dashboard" onclick="switchTab('dashboard', this)">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a class="list-group-item list-group-item-action" id="tab-campaign" onclick="switchTab('campaign', this)">
                    <i class="fas fa-pen-nib"></i> Email Design
                </a>
                <a class="list-group-item list-group-item-action" id="tab-clients" onclick="switchTab('clients', this)">
                    <i class="fas fa-users"></i> Audience List
                </a>
                <a class="list-group-item list-group-item-action" id="tab-history" onclick="switchTab('history', this)">
                    <i class="fas fa-history"></i> Sent History
                </a>
                <a class="list-group-item list-group-item-action" href="#" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Refresh System
                </a>
            </div>
        </div>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-custom d-flex justify-content-between align-items-center">
                <button class="btn btn-light shadow-sm text-primary" id="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="d-flex align-items-center">
                    <span class="badge bg-success rounded-pill px-3 py-2 me-2">System Online</span>
                    <div class="dropdown">
                        <button class="btn btn-light rounded-circle shadow-sm" type="button">
                            <i class="fas fa-user"></i>
                        </button>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">

                <div id="view-dashboard" class="view-section">
                    <h3 class="mb-4 text-dark fw-bold">Overview</h3>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="card stat-card p-4 border-start border-4 border-primary">
                                <i class="fas fa-envelope stat-icon text-primary"></i>
                                <div class="text-muted small text-uppercase fw-bold">Total Emails</div>
                                <div class="stat-value text-primary" id="stat_total">0</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card p-4 border-start border-4 border-success">
                                <i class="fas fa-check-circle stat-icon text-success"></i>
                                <div class="text-muted small text-uppercase fw-bold">Sent Successfully</div>
                                <div class="stat-value text-success" id="stat_sent">0</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card p-4 border-start border-4 border-warning">
                                <i class="fas fa-clock stat-icon text-warning"></i>
                                <div class="text-muted small text-uppercase fw-bold">Pending</div>
                                <div class="stat-value text-warning" id="stat_pending">0</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card p-4 border-start border-4 border-danger">
                                <i class="fas fa-times-circle stat-icon text-danger"></i>
                                <div class="text-muted small text-uppercase fw-bold">Failed</div>
                                <div class="stat-value text-danger" id="stat_failed">0</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-8 mb-4">
                            <div class="card p-4 h-100">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold"><i class="fas fa-terminal me-2"></i>Live Console</h5>
                                    <span class="badge bg-dark">Real-time</span>
                                </div>
                                <div id="logs">
                                    <div class="log-entry">> System initialized...</div>
                                    <div class="log-entry">> Waiting for command...</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <div class="card p-4 h-100 text-center">
                                <h5 class="fw-bold mb-4">Action Center</h5>
                                <div class="progress mb-4" style="height: 30px; border-radius: 15px; background: #e9ecef;">
                                    <div id="progressBar" class="progress-bar bg-gradient" role="progressbar" 
                                        style="width: 0%; background: var(--primary-gradient); font-weight: bold;">0%</div>
                                </div>
                                <button id="startBtn" class="btn btn-gradient w-100 py-3 shadow-lg" onclick="startSending()">
                                    <i class="fas fa-paper-plane me-2"></i> START CAMPAIGN
                                </button>
                                <p class="text-muted mt-3 small">Ensure your template is saved before starting.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="view-campaign" class="view-section d-none">
                    <h3 class="mb-4 text-dark fw-bold">Email Template Design</h3>
                    <div class="card p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Subject Line</label>
                            <input type="text" id="emailSubject" class="form-control form-control-lg" placeholder="e.g., Special Offer Just for You!">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Email Body</label>
                            <textarea id="summernote"></textarea>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary btn-lg px-5 rounded-pill" onclick="saveTemplate()">
                                <i class="fas fa-save me-2"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>

                <div id="view-clients" class="view-section d-none">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="text-dark fw-bold">Audience List</h3>
                        <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#bulkModal">
                            <i class="fas fa-plus me-2"></i> Import Emails
                        </button>
                    </div>
                    <div class="card p-3">
                        <div class="table-responsive">
                            <table id="clientsTable" class="table table-hover align-middle" style="width:100%">
                                <thead><tr><th>ID</th><th>Email Address</th><th>Status</th><th>Actions</th></tr></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="view-history" class="view-section d-none">
                    <h3 class="mb-4 text-dark fw-bold">Sent History</h3>
                    <div class="card p-3">
                        <div class="table-responsive">
                            <table id="historyTable" class="table table-hover align-middle" style="width:100%">
                                <thead><tr><th>ID</th><th>Email Address</th><th>Delivery Status</th><th>Actions</th></tr></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="bulkModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Import Bulk Emails</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Paste your email list below. Supports: <b>One per line</b> or <b>Comma separated</b>.</p>
                    <textarea id="bulkEmailsInput" class="form-control" rows="10" placeholder="client1@email.com&#10;client2@email.com&#10;client3@email.com" style="background: #f8f9fa; border: 2px dashed #ced4da;"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" onclick="bulkImport()">Start Import</button>
                </div>
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
        // --- UI Logic: Sidebar Toggle ---
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");

        toggleButton.onclick = function () {
            el.classList.toggle("toggled");
        };

        // --- UI Logic: Tab Switcher ---
        function switchTab(viewId, linkElement) {
            // Hide all views
            $('.view-section').addClass('d-none');
            // Show selected view
            $('#view-' + viewId).removeClass('d-none');
            
            // Update Sidebar Active State
            $('.list-group-item').removeClass('active');
            $(linkElement).addClass('active');

            // Load specific data
            if(viewId === 'clients') loadClients('all');
            if(viewId === 'history') loadClients('Sent');
        }

        // --- Core Application Logic (Joss Logic) ---
        $(document).ready(function() {
            $('#summernote').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            });
            updateStats();
        });

        // 1. Update Dashboard Stats
        function updateStats() {
            $.post("api.php", {action: 'get_stats'}, function(data) {
                $('#stat_total').text(data.total);
                $('#stat_sent').text(data.sent);
                $('#stat_pending').text(data.pending);
                $('#stat_failed').text(data.failed);
                $('#progressBar').css('width', data.percent + '%').text(data.percent + '%');
                
                if($('#emailSubject').val() == '') {
                    $('#emailSubject').val(data.subject);
                    $('#summernote').summernote('code', data.body);
                }
            });
        }

        // 2. Save Template
        function saveTemplate() {
            var sub = $('#emailSubject').val();
            var body = $('#summernote').summernote('code');
            $.post("api.php", {action: 'save_template', subject: sub, body: body}, function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: 'Email template updated successfully.',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }

        // 3. Bulk Import
        function bulkImport() {
            var emails = $('#bulkEmailsInput').val();
            if(!emails) return Swal.fire('Warning', 'Please paste email list first.', 'warning');
            
            $.post("api.php", {action: 'bulk_import', emails: emails}, function(res) {
                Swal.fire('Success', res.message, 'success');
                $('#bulkModal').modal('hide');
                $('#bulkEmailsInput').val('');
                updateStats();
                // If on clients tab, reload table
                if(!$('#view-clients').hasClass('d-none')) loadClients('all');
            });
        }

        // 4. Load Tables
        function loadClients(filter) {
            var tableId = (filter === 'Sent') ? '#historyTable' : '#clientsTable';
            
            if ($.fn.DataTable.isDataTable(tableId)) {
                $(tableId).DataTable().destroy();
            }

            $.post("api.php", {action: 'get_clients_list', filter: filter}, function(res) {
                var rows = "";
                res.data.forEach(function(item) {
                    var statusBadge = '';
                    if(item.status == 'Sent') statusBadge = '<span class="badge bg-success rounded-pill">Delivered</span>';
                    else if(item.status == 'Pending') statusBadge = '<span class="badge bg-warning text-dark rounded-pill">In Queue</span>';
                    else statusBadge = '<span class="badge bg-danger rounded-pill">Failed</span>';

                    var btn = (item.status === 'Sent') 
                        ? `<button class="btn btn-sm btn-outline-primary rounded-pill" onclick="resendEmail(${item.id})"><i class="fas fa-redo"></i> Resend</button>` 
                        : '<span class="text-muted small">--</span>';
                    
                    rows += `<tr>
                        <td class="fw-bold">#${item.id}</td>
                        <td>${item.email}</td>
                        <td>${statusBadge}</td>
                        <td>${btn}</td>
                    </tr>`;
                });
                $(tableId + ' tbody').html(rows);
                $(tableId).DataTable({
                    "pageLength": 10,
                    "language": { "search": "Quick Search:" }
                });
            });
        }

        // 5. Resend
        function resendEmail(id) {
            $.post("api.php", {action: 'resend_email', id: id}, function() {
                const Toast = Swal.mixin({
                    toast: true, position: 'top-end', showConfirmButton: false, timer: 3000
                });
                Toast.fire({ icon: 'success', title: 'Added back to queue' });
                loadClients('Sent');
                updateStats();
            });
        }

        // 6. Sending Process
        function startSending() {
            var btn = $('#startBtn');
            var logs = $('#logs');
            btn.prop('disabled', true).addClass('disabled').html('<span class="spinner-border spinner-border-sm me-2"></span> Sending...');
            
            function sendBatch() {
                $.post("process.php", function(response) {
                    try {
                        var res = JSON.parse(response);
                        updateStats();

                        if (res.status === 'finished') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Campaign Finished!',
                                text: 'All emails have been processed.',
                                confirmButtonColor: '#667eea'
                            });
                            btn.prop('disabled', false).removeClass('disabled').html('<i class="fas fa-paper-plane me-2"></i> START CAMPAIGN');
                            return;
                        } 
                        else if (res.status === 'error') {
                            logs.append("<div class='log-entry text-danger'>❌ " + res.message + "</div>");
                        } 
                        else {
                            logs.append(res.log);
                            logs.scrollTop(logs[0].scrollHeight);
                            setTimeout(sendBatch, 2000); 
                        }
                    } catch(e) { console.log(response); }
                });
            }
            sendBatch();
        }
    </script>

</body>
</html>