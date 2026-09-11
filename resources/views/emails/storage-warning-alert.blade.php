<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>Critical Storage Warning — WISP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; -webkit-text-size-adjust: 100%; }
        body { margin: 0; padding: 0; width: 100% !important; background-color: #F8FAFC; font-family: 'Inter', system-ui, -apple-system, sans-serif; color: #334155; }
        table { border-collapse: collapse; }
        .wrapper { width: 100%; background-color: #F8FAFC; padding: 24px 12px; }
        .email-container { max-width: 580px; width: 100%; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #FECACA; overflow: hidden; box-shadow: 0 4px 20px rgba(239, 68, 68, 0.08); }
        .header { background: linear-gradient(135deg, #EF4444, #B91C1C); padding: 22px 24px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 1.25rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; font-family: 'Plus Jakarta Sans', sans-serif; }
        .content { padding: 32px 28px; line-height: 1.6; }
        .alert-box { background-color: #FEF2F2; border-left: 4px solid #EF4444; padding: 14px 16px; border-radius: 8px; margin-bottom: 20px; }
        .alert-title { font-weight: 700; color: #991B1B; margin-bottom: 4px; font-size: 14px; }
        .alert-text { font-size: 13px; color: #7F1D1D; margin: 0; }
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; }
        .stat-card { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 14px; text-align: center; }
        .stat-label { font-size: 11px; text-transform: uppercase; color: #64748B; font-weight: 700; margin-bottom: 4px; }
        .stat-value { font-size: 1.25rem; font-weight: 800; color: #EF4444; font-family: 'Plus Jakarta Sans', sans-serif; }
        .steps { background-color: #F8FAFC; border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid #E2E8F0; }
        .steps h3 { margin-top: 0; font-size: 14px; font-weight: 700; color: #0F172A; font-family: 'Plus Jakarta Sans', sans-serif; }
        .steps ol { padding-left: 18px; margin: 0; }
        .steps li { margin-bottom: 10px; font-size: 13px; color: #475569; }
        .btn-wrap { text-align: center; margin: 24px 0 12px; }
        .btn { display: inline-block; width: 100%; max-width: 280px; background: #EF4444; color: #ffffff !important; font-weight: 700; text-decoration: none; padding: 13px 24px; border-radius: 10px; text-align: center; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25); }
        .footer { padding: 18px 24px; text-align: center; font-size: 12px; color: #94A3B8; border-top: 1px solid #F1F5F9; background-color: #FAFAFA; }
        
        @media only screen and (max-width: 600px) {
            .wrapper { padding: 8px 4px !important; }
            .email-container { width: 100% !important; border-radius: 12px !important; }
            .content { padding: 20px 16px !important; }
            .stats-grid { grid-template-columns: 1fr !important; }
            .btn { width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="email-container">
            <div class="header">
                <h1>Critical Storage Warning</h1>
            </div>
            <div class="content">
                <div class="alert-box">
                    <div class="alert-title">AWS Storage Usage Exceeded 90%</div>
                    <p class="alert-text">The system storage has reached a critical threshold. Immediate action is required to prevent service disruptions.</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-label">Total Storage Used</div>
                        <div class="stat-value">{{ $formattedSize }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Percentage Used</div>
                        <div class="stat-value">{{ number_format($percentage, 2) }}%</div>
                    </div>
                </div>

                <div class="steps">
                    <h3>Recommended Actions (AWS Hosting):</h3>
                    <ol>
                        <li><strong>Run Cleanup Tools:</strong> Log in to the <a href="{{ route('admin.maintenance.page') }}" style="color: #E8674A; font-weight: 600;">WISP Admin Panel</a> and execute the **Clean Expired Messages** tool to purge expired media.</li>
                        <li><strong>Resize EBS Volumes:</strong> Expand EC2 partition using `sudo resize2fs` or `sudo xfs_growfs`.</li>
                        <li><strong>Set Up S3 Lifecycle Rules:</strong> Automatically transition older files to Glacier or expire them.</li>
                    </ol>
                </div>

                <div class="btn-wrap">
                    <a href="{{ route('admin.maintenance.page') }}" class="btn">Access Admin Panel</a>
                </div>
            </div>
            <div class="footer">
                WISP Automated System Health Monitor &bull; AWS Cloud Integration
            </div>
        </div>
    </div>
</body>
</html>