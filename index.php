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
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --sidebar-width: 260px;
            --bg-light: #f3f4f6;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-light);
            overflow-x: hidden;
        }

        /* --- Sidebar & Layout --- */
        #wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
            transition: all 0.3s;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            width: var(--sidebar-width);
            background: var(--primary-gradient);
            position: fixed;
            z-index: 1000;
            transition: all 0.3s;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
        }

        #sidebar-wrapper .sidebar-brand {
            padding: 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
        }

        #sidebar-wrapper .list-group-item {
            background: transparent;
            color: rgba(255, 255, 255, 0.8);
            border: none;
            padding: 1rem 1.5rem;
            font-weight: 500;
            transition: 0.2s;
        }

        #sidebar-wrapper .list-group-item:hover,
        #sidebar-wrapper .list-group-item.active {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            border-left: 4px solid #fff;
        }

        #page-content-wrapper {
            width: 100%;
            margin-left: var(--sidebar-width);
            transition: all 0.3s;
        }

        /* --- Mobile Responsive Logic --- */
        @media (max-width: 768px) {
            #sidebar-wrapper {
                margin-left: -260px; /* Hide sidebar by default on mobile */
            }
            #page-content-wrapper {
                margin-left: 0;
            }
            #wrapper.toggled #sidebar-wrapper {
                margin-left: 0; /* Show when toggled */
            }
            
            /* Overlay when menu is open */
            #sidebar-overlay {
                display: none;
                position: fixed;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.5);
                z-index: 900;
                top: 0; left: 0;
            }
            #wrapper.toggled #sidebar-overlay {
                display: block;
            }
        }

        /* --- UI Elements --- */
        .navbar-custom {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 15px;
            position: sticky;
            top: 0;
            z-index: 800;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
            background: white;
            margin-bottom: 20px;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
        }
        .stat-card .icon-bg {
            position: absolute;
            right: -10px;
            bottom: -10px;
            font-size: 4rem;
            opacity: 0.1;
            transform: rotate(-15deg);
        }

        .stat-value { font-size: 1.8rem; font-weight: 700; line-height: 1.2; }
        .stat-label { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.8; }

        /* Terminal Log */
        #logs {
            background: #1e1e2e;
            color: #50fa7b;
            height: 300px;
            overflow-y: auto;
            padding: 15px;
            font-family: monospace;
            border-radius: 10px;
            font-size: 12px;
        }

        .btn-gradient {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            font-weight: 600;
        }
        
        /* Table Responsive Fix */
        .dataTables_wrapper .row { margin: 0; }
    </style>
</head>

<body>

    <div id="wrapper">
        <div id="sidebar-overlay" onclick="toggleMenu()"></div>

        <div id="sidebar-wrapper">
            <div class="sidebar-brand">
                <i class="fas fa-rocket me-2"></i> Ultima Pro
            </div>
            <div class="list-group list-group-flush mt-3">
                <a class="list-group-item list-group-item-action active" onclick="switchTab('dashboard', this)">
                    <i class="fas fa-chart-pie me-2"></i> Dashboard
                </a>
                <a class="list-group-item list-group-item-action" onclick="switchTab('campaign', this)">
                    <i class="fas fa-pen-nib me-2"></i> Design Email
                </a>
                <a class="list-group-item list-group-item-action" onclick="switchTab('clients', this)">
                    <i class="fas fa-users me-2"></i> Client List
                </a>
                <a class="list-group-item list-group-item-action" onclick="switchTab('history', this)">
                    <i class="fas fa-history me-2"></i> Sent History
                </a>
                <a class="list-group-item list-group-item-action text-danger mt-5" href="#" onclick="location.reload()">
                    <i class="fas fa-power-off me-2"></i> Restart
                </a>
            </div>
        </div>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-custom d-flex justify-content-between align-items-center">
                <button class="btn btn-light shadow-sm text-primary border" id="menu-toggle" onclick="toggleMenu()">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="fw-bold text-secondary d-none d-md-block">System Dashboard</div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-success rounded-pill px-3 py-2">Online</span>
                </div>
            </nav>

            <div class="container-fluid p-3 p-md-4">

                <div id="view-dashboard" class="view-section">
                    <h4 class="mb-4 fw-bold">Overview</h4>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-lg-3">
                            <div class="card stat-card p-3 text-white bg-primary bg-gradient h-100">
                                <i class="fas fa-envelope icon-bg"></i>
                                <div class="stat-value" id="stat_total">0</div>
                                <div class="stat-label">Total</div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="card stat-card p-3 text-white bg-success bg-gradient h-100">
                                <i class="fas fa-check icon-bg"></i>
                                <div class="stat-value" id="stat_sent">0</div>
                                <div class="stat-label">Sent</div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="card stat-card p-3 text-dark bg-warning bg-gradient h-100">
                                <i class="fas fa-clock icon-bg"></i>
                                <div class="stat-value" id="stat_pending">0</div>
                                <div class="stat-label">Pending</div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="card stat-card p-3 text-white bg-danger bg-gradient h-100">
                                <i class="fas fa-times icon-bg"></i>
                                <div class="stat-value" id="stat_failed">0</div>
                                <div class="stat-label">Failed</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="card p-3 h-100">
                                <h6 class="fw-bold mb-3"><i class="fas fa-terminal me-2"></i>Live Console</h6>
                                <div id="logs">
                                    <div class="log-entry">> System Ready...</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card p-4 text-center h-100 justify-content-center">
                                <div class="progress mb-3" style="height: 25px;">
                                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%;">0%</div>
                                </div>
                                <button id="startBtn" class="btn btn-gradient shadow-lg" onclick="startSending()">
                                    <i class="fas fa-rocket me-2"></i> START CAMPAIGN
                                </button>
                                <small class="text-muted mt-2 d-block">Check template before starting</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="view-campaign" class="view-section d-none">
                    <h4 class="mb-4 fw-bold">Email Design</h4>
                    <div class="card p-4">
                        <div class="mb-3">
                            <label class="fw-bold mb-1">Subject</label>
                            <input type="text" id="emailSubject" class="form-control" placeholder="Email Subject">
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold mb-1">Message Body</label>
                            <textarea id="summernote"></textarea>
                        </div>
                        <button class="btn btn-primary w-100" onclick="saveTemplate()">Save Template</button>
                    </div>
                </div>

                <div id="view-clients" class="view-section d-none">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold m-0">Clients</h4>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bulkModal">
                            <i class="fas fa-plus"></i> Import
                        </button>
                    </div>
                    <div class="card p-3">
                        <div class="table-responsive">
                            <table id="clientsTable" class="table table-hover w-100" style="width:100%">
                                <thead><tr><th>ID</th><th>Email</th><th>Status</th><th>Action</th></tr></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="view-history" class="view-section d-none">
                    <h4 class="mb-4 fw-bold">History</h4>
                    <div class="card p-3">
                        <div class="table-responsive">
                            <table id="historyTable" class="table table-hover w-100" style="width:100%">
                                <thead><tr><th>ID</th><th>Email</th><th>Status</th><th>Action</th></tr></thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="bulkModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Emails</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea id="bulkEmailsInput" class="form-control" rows="8" placeholder="email1@gmail.com, email2@yahoo.com..."></textarea>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary w-100" onclick="bulkImport()">Import Now</button>
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
        // --- UI Logic ---
        function toggleMenu() {
            document.getElementById("wrapper").classList.toggle("toggled");
        }

        function switchTab(viewId, linkElement) {
            $('.view-section').addClass('d-none');
            $('#view-' + viewId).removeClass('d-none');
            
            // Sidebar active state
            $('.list-group-item').removeClass('active');
            $(linkElement).addClass('active');

            // Mobile: Close menu after click
            if(window.innerWidth <= 768) {
                toggleMenu();
            }

            if(viewId === 'clients') loadClients('all');
            if(viewId === 'history') loadClients('Sent');
        }

        // --- Core Logic ---
        $(document).ready(function() {
            $('#summernote').summernote({ height: 250, toolbar: [['style', ['bold', 'italic', 'underline', 'clear']], ['font', ['strikethrough']], ['para', ['ul', 'ol']]] });
            updateStats();
        });

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

        function saveTemplate() {
            $.post("api.php", {action: 'save_template', subject: $('#emailSubject').val(), body: $('#summernote').summernote('code')}, function(res) {
                Swal.fire('Saved', res.message, 'success');
            });
        }

        function bulkImport() {
            var emails = $('#bulkEmailsInput').val();
            if(!emails) return Swal.fire('Error', 'Paste emails first!', 'error');
            $.post("api.php", {action: 'bulk_import', emails: emails}, function(res) {
                Swal.fire('Success', res.message, 'success');
                $('#bulkModal').modal('hide');
                $('#bulkEmailsInput').val('');
                updateStats();
                if(!$('#view-clients').hasClass('d-none')) loadClients('all');
            });
        }

        function loadClients(filter) {
            var tableId = (filter === 'Sent') ? '#historyTable' : '#clientsTable';
            if ($.fn.DataTable.isDataTable(tableId)) $(tableId).DataTable().destroy();

            $.post("api.php", {action: 'get_clients_list', filter: filter}, function(res) {
                var rows = "";
                res.data.forEach(function(item) {
                    var btn = (item.status === 'Sent') 
                        ? `<button class="btn btn-sm btn-warning" onclick="resendEmail(${item.id})"><i class="fas fa-redo"></i></button>` 
                        : '<span class="text-muted">-</span>';
                    
                    rows += `<tr><td>${item.id}</td><td style="word-break:break-all;">${item.email}</td><td><span class="badge bg-${item.status=='Sent'?'success':(item.status=='Pending'?'warning':'danger')}">${item.status}</span></td><td>${btn}</td></tr>`;
                });
                $(tableId + ' tbody').html(rows);
                $(tableId).DataTable({ pageLength: 10, lengthChange: false, searching: true });
            });
        }

        function resendEmail(id) {
            $.post("api.php", {action: 'resend_email', id: id}, function() {
                const Toast = Swal.mixin({toast: true, position: 'top-end', showConfirmButton: false, timer: 2000});
                Toast.fire({icon: 'success', title: 'Queued'});
                loadClients('Sent');
                updateStats();
            });
        }

        function startSending() {
            var btn = $('#startBtn');
            var logs = $('#logs');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Sending...');
            
            function sendBatch() {
                $.post("process.php", function(response) {
                    try {
                        var res = JSON.parse(response);
                        updateStats();
                        if (res.status === 'finished') {
                            Swal.fire('Done', 'Campaign Finished!', 'success');
                            btn.prop('disabled', false).html('<i class="fas fa-rocket me-2"></i> START CAMPAIGN');
                        } else if (res.status === 'error') {
                            logs.append("<div class='text-danger'>" + res.message + "</div>");
                        } else {
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