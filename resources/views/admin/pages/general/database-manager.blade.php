@extends('admin.base-admin')

@section('admin-section', 'database-manager')

@section('content')
<link rel="stylesheet" href="{{ asset('css/database-manager.css') }}?v={{ filemtime(public_path('css/database-manager.css')) }}">

<div class="content-section active" id="database-manager">
    {{-- Page Header --}}
    <div class="admin-page-header">
        <h2 class="admin-page-title">
            <span class="admin-title-icon-box">
                <i class="fas fa-database"></i>
            </span>
            Database Manager
        </h2>
    </div>

    {{-- Main Container Card --}}
    <div class="db-manager-card">
        <div class="db-content">
            <!-- Left Sidebar - Tables List -->
            <div class="db-sidebar">
                <div class="db-sidebar-header">
                    <i class="fas fa-table text-primary"></i>
                    <span>Tables</span>
                </div>
                <div class="db-sidebar-search">
                    <input type="text" id="tableSearchInput" placeholder="Search tables..." class="db-search-input">
                </div>
                <div class="db-tables-list" id="tablesList">
                    <div class="db-loading">
                        <i class="fas fa-spinner fa-spin text-primary"></i>
                        <p>Loading tables...</p>
                    </div>
                </div>
            </div>

            <!-- Center - Data Grid -->
            <div class="db-center">
                <div class="db-view-container" id="viewContainer">
                    <div class="db-empty-state">
                        <i class="fas fa-database text-primary opacity-50"></i>
                        <h2>Select a table to view data</h2>
                        <p>Choose a table from the list on the left to display its contents</p>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar - Details & Edit -->
            <div class="db-right-panel">
                <div class="db-right-header">
                    <h3><i class="fas fa-info-circle text-primary"></i> Details</h3>
                    <button class="db-close-panel" onclick="dbManager.closeRightPanel()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="db-right-content" id="rightPanelContent">
                    <div class="db-empty-right">
                        <i class="fas fa-mouse-pointer text-primary opacity-50"></i>
                        <p>Select a row to view details</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Types Legend -->
<div class="db-legend hidden" id="dbLegend">
    <div class="db-legend-content">
        <h4>Data Types</h4>
        <div class="db-legend-grid">
            <div class="db-legend-item">
                <span class="db-type-badge badge-type-int">INT</span>
                <span>Integer</span>
            </div>
            <div class="db-legend-item">
                <span class="db-type-badge badge-type-varchar">VARCHAR</span>
                <span>String</span>
            </div>
            <div class="db-legend-item">
                <span class="db-type-badge badge-type-datetime">DATETIME</span>
                <span>DateTime</span>
            </div>
            <div class="db-legend-item">
                <span class="db-type-badge badge-type-text">TEXT</span>
                <span>Text</span>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/database-manager.js') }}"></script>
@endsection
