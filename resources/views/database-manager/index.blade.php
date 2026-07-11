<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Database Manager - WISP</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/database-manager.css') }}">
</head>
<body>
    <div class="db-manager-container">
        <!-- Header -->
        <div class="db-header">
            <div class="db-header-left">
                <a href="{{ route('admin.page') }}" class="nav-link {{ request()->routeIs('admin.page') ? 'active' : '' }}" data-section="dashboard"><i class="fas fa-tachometer-alt"></i></a>
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="db-logo">
                <h1>Database Manager</h1>
            </div>
            <div class="db-header-right">
                <span class="db-user">{{ auth()->user()->username ?? 'Admin' }}</span>
                <form method="POST" action="{{ route('auth.logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="db-logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="db-content">
            <!-- Left Sidebar - Tables List -->
            <div class="db-sidebar">
                <div class="db-sidebar-header">
                    <i class="fas fa-database"></i>
                    <span>Tables</span>
                </div>
                <div class="db-sidebar-search">
                    <input type="text" id="tableSearchInput" placeholder="Search tables..." class="db-search-input">
                </div>
                <div class="db-tables-list" id="tablesList">
                    <div class="db-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading tables...</p>
                    </div>
                </div>
            </div>

            <!-- Center - Data Grid -->
            <div class="db-center">
                <div class="db-view-container" id="viewContainer">
                    <div class="db-empty-state">
                        <i class="fas fa-database"></i>
                        <h2>Select a table to view data</h2>
                        <p>Choose a table from the list on the left to display its contents</p>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar - Details & Edit -->
            <div class="db-right-panel">
                <div class="db-right-header">
                    <h3><i class="fas fa-info-circle"></i> Details</h3>
                    <button class="db-close-panel" onclick="dbManager.closeRightPanel()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="db-right-content" id="rightPanelContent">
                    <div class="db-empty-right">
                        <i class="fas fa-mouse-pointer"></i>
                        <p>Select a row to view details</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Types Legend -->
    <div class="db-legend" id="dbLegend" style="display: none;">
        <div class="db-legend-content">
            <h4>Data Types</h4>
            <div class="db-legend-grid">
                <div class="db-legend-item">
                    <span class="db-type-badge" style="background: #e8f5e9; color: #2e7d32;">INT</span>
                    <span>Integer</span>
                </div>
                <div class="db-legend-item">
                    <span class="db-type-badge" style="background: #e3f2fd; color: #1565c0;">VARCHAR</span>
                    <span>String</span>
                </div>
                <div class="db-legend-item">
                    <span class="db-type-badge" style="background: #fff3e0; color: #e65100;">DATETIME</span>
                    <span>DateTime</span>
                </div>
                <div class="db-legend-item">
                    <span class="db-type-badge" style="background: #f3e5f5; color: #7b1fa2;">TEXT</span>
                    <span>Text</span>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/database-manager.js') }}"></script>
</body>
</html>
