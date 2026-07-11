<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Outfit', 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 40px 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #fecaca; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.08); overflow: hidden; }
        .header { background: linear-gradient(135deg, #ef4444, #b91c1c); padding: 32px 24px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 1.5rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .body { padding: 32px 24px; line-height: 1.6; }
        .alert-box { background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 16px; border-radius: 8px; margin-bottom: 24px; }
        .alert-title { font-weight: 700; color: #991b1b; margin-bottom: 4px; }
        .alert-text { font-size: 0.9rem; color: #7f1d1d; margin: 0; }
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }
        .stat-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; text-align: center; }
        .stat-label { font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 600; margin-bottom: 4px; }
        .stat-value { font-size: 1.25rem; font-weight: 700; color: #0f172a; }
        .steps { background-color: #f8fafc; border-radius: 12px; padding: 24px; margin-bottom: 24px; border: 1px solid #e2e8f0; }
        .steps h3 { margin-top: 0; font-size: 1.05rem; font-weight: 700; color: #1e293b; }
        .steps ol { padding-left: 20px; margin: 0; }
        .steps li { margin-bottom: 12px; font-size: 0.9rem; }
        .btn { display: inline-block; background: #6366f1; color: #ffffff !important; font-weight: 600; text-decoration: none; padding: 12px 24px; border-radius: 10px; text-align: center; box-shadow: 0 4px 10px rgba(99, 102, 241, 0.2); }
        .btn:hover { background: #4f46e5; }
        .footer { padding: 24px; text-align: center; font-size: 0.8rem; color: #94a3b8; border-top: 1px solid #f1f5f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Critical Storage Warning</h1>
        </div>
        <div class="body">
            <div class="alert-box">
                <div class="alert-title">AWS Storage Usage Exceeded 90%</div>
                <p class="alert-text">The system storage has reached a critical threshold. Action is required immediately to prevent service disruptions and upload failures.</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Storage Used</div>
                    <div class="stat-value" style="color: #ef4444;">{{ $formattedSize }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Percentage Used</div>
                    <div class="stat-value" style="color: #ef4444;">{{ number_format($percentage, 2) }}%</div>
                </div>
            </div>

            <div class="steps">
                <h3>Recommended Actions (AWS Hosting):</h3>
                <ol>
                    <li><strong>Run Cleanup Tools:</strong> Log in to the <a href="{{ route('admin.maintenance.page') }}" style="color: #6366f1; font-weight: 600;">WISP Admin Panel</a> and execute the **Clean Expired Messages** tool to purge expired messages and their media files from S3/EBS.</li>
                    <li><strong>Resize EBS Volumes (EBS Storage):</strong> If you are using EBS for files:
                        <ul>
                            <li>Go to the **AWS EC2 Console > Elastic Block Store > Volumes**.</li>
                            <li>Select your volume, click **Actions > Modify Volume**, and increase the size.</li>
                            <li>Run `sudo resize2fs` or `sudo xfs_growfs` inside the EC2 instance to expand the partition.</li>
                        </ul>
                    </li>
                    <li><strong>Set Up S3 Lifecycle Rules (S3 Storage):</strong> If media files are stored on Amazon S3:
                        <ul>
                            <li>Configure a lifecycle policy to automatically transition older files to **Glacier** or permanently delete them after expiry.</li>
                        </ul>
                    </li>
                    <li><strong>Rotate and Compress System Logs:</strong> Run the **Clear Old Activity Logs** tool, or configure `logrotate` on your AWS instance to compress log files.</li>
                </ol>
            </div>

            <div style="text-align: center; margin-top: 32px;">
                <a href="{{ route('admin.maintenance.page') }}" class="btn">Access Admin Control Center</a>
            </div>
        </div>
        <div class="footer">
            WISP Automated System Health Monitor &bull; AWS Cloud Integration
        </div>
    </div>
</body>
</html>
