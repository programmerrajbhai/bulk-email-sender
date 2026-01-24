<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Unlimited Bulk Sender</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        #logs { height: 200px; overflow-y: scroll; background: #222; color: #0f0; padding: 10px; margin-top: 10px; font-family: monospace; }
        .btn { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; font-size: 16px; }
        .btn:disabled { background: #ccc; }
    </style>
</head>
<body>

<div class="container">
    <h2>Bulk Email Sender System</h2>
    <p>System Status: <span id="status">Ready</span></p>
    <button id="startBtn" class="btn" onclick="startSending()">Start Sending</button>
    
    <h3>Live Logs:</h3>
    <div id="logs"></div>
</div>

<script>
    function startSending() {
        var btn = document.getElementById('startBtn');
        var status = document.getElementById('status');
        var logs = document.getElementById('logs');

        btn.disabled = true;
        status.innerText = "Sending in progress...";

        function sendBatch() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "process.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function() {
                if (this.status == 200) {
                    try {
                        var response = JSON.parse(this.responseText);

                        if (response.status === 'finished') {
                            status.innerText = "Completed!";
                            logs.innerHTML += "<div>✅ All Emails Sent Successfully!</div>";
                            btn.disabled = false;
                            return; // Stop recursion
                        } 
                        else if (response.status === 'error') {
                            logs.innerHTML += "<div style='color:red'>❌ " + response.message + "</div>";
                            btn.disabled = false;
                            return;
                        }
                        else {
                            // Success batch
                            logs.innerHTML += response.log;
                            logs.scrollTop = logs.scrollHeight; // Auto scroll
                            
                            // ২ সেকেন্ড বিরতি দিয়ে পরের ব্যাচ কল করা
                            setTimeout(sendBatch, 2000); 
                        }
                    } catch (e) {
                        logs.innerHTML += "<div style='color:red'>JSON Error: " + this.responseText + "</div>";
                    }
                }
            };
            xhr.send();
        }

        // প্রথম ব্যাচ কল করা
        sendBatch();
    }
</script>

</body>
</html>