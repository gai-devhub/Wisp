/*
 * Lightweight Database Manager frontend
 * - Loads table names quickly (no per-table counts) to keep latency low
 * - Uses AbortController to timeout slow requests
 */

class DatabaseManager {
    constructor() {
        this.currentTable = null;
        this.tables = [];
        this.selectedRow = null;
        this.tableSearch = document.getElementById('tableSearchInput');
        this.tablesList = document.getElementById('tablesList');
        this.viewContainer = document.getElementById('viewContainer');
        this.rightPanel = document.getElementById('rightPanelContent');
        if (this.tableSearch) this.tableSearch.addEventListener('input', (e) => this.filterTables(e.target.value));
        this.loadTables();
    }

    async fetchWithTimeout(url, opts = {}, timeout = 8000) {
        const controller = new AbortController();
        const id = setTimeout(() => controller.abort(), timeout);
        try {
            const res = await fetch(url, Object.assign({ credentials: 'same-origin', signal: controller.signal }, opts));
            clearTimeout(id);
            return res;
        } catch (e) {
            clearTimeout(id);
            throw e;
        }
    }

    async loadTables() {
        this.tablesList.innerHTML = '<div class="db-loading"><i class="fas fa-spinner fa-spin"></i><p>Loading tables...</p></div>';
        try {
            const res = await this.fetchWithTimeout('/api/database/tables', { headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } }, 7000);
            if (!res.ok) {
                const text = await res.text();
                console.error('Tables API error', res.status, text);
                this.showTablesError('Server error loading tables (' + res.status + ')');
                return;
            }
            const text = await res.text();
            let data;
            try { data = JSON.parse(text); } catch (e) {
                console.error('Non-JSON response loading tables', text.slice(0,300));
                this.showTablesError('Unexpected server response. Maybe session expired.');
                return;
            }
            if (!Array.isArray(data.tables)) {
                this.showTablesError('Invalid server response.');
                return;
            }
            this.tables = data.tables;
            this.renderTablesList();
        } catch (e) {
            console.error('Failed to load tables', e);
            if (e.name === 'AbortError') this.showTablesError('Request timed out loading tables');
            else this.showTablesError('Failed to load tables. Check server or authentication.');
        }
    }

    renderTablesList() {
        if (!this.tables || this.tables.length === 0) {
            this.tablesList.innerHTML = '<div class="db-loading"><p>No tables found</p></div>';
            return;
        }
        this.tablesList.innerHTML = this.tables.map(t => `
            <div class="db-table-item" data-table="${t.name}" onclick="dbManager.loadTableData('${t.name}')">
                <i class="fas fa-table db-table-icon"></i>
                <span>${t.name}</span>
                <span class="db-table-count">${t.rows === null || t.rows === undefined ? '' : new Intl.NumberFormat().format(t.rows)}</span>
            </div>
        `).join('');
    }

    filterTables(query) {
        const q = (query || '').toLowerCase();
        document.querySelectorAll('.db-table-item').forEach(item => {
            const name = (item.dataset.table || '').toLowerCase();
            item.style.display = name.includes(q) ? '' : 'none';
        });
    }

    async loadTableData(tableName) {
        this.viewContainer.innerHTML = '<div class="db-empty-state"><i class="fas fa-spinner fa-spin"></i><p>Loading table...</p></div>';
        try {
            const res = await this.fetchWithTimeout(`/api/database/table/${encodeURIComponent(tableName)}`, { headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } }, 8000);
            if (!res.ok) {
                const txt = await res.text();
                console.error('Table API error', res.status, txt);
                this.showError('Failed to load table data (' + res.status + ')');
                return;
            }
            const text = await res.text();
            let data;
            try { data = JSON.parse(text); } catch (e) { console.error('Non-JSON table response', text.slice(0,300)); this.showError('Unexpected server response'); return; }
            this.currentTable = data;
            this.renderTableData(data);
            this.updateActiveTableItem(tableName);
            this.closeRightPanel();
        } catch (e) {
            console.error('Error loading table', e);
            this.showError(e.name === 'AbortError' ? 'Request timed out' : 'Unable to load table data');
        }
    }

    renderTableData(data) {
        const columns = (data.columns || []).map(c => c.COLUMN_NAME);
        const rows = Array.isArray(data.data) ? data.data : [];
        const header = columns.map(c => `<th>${c}</th>`).join('');
        const body = rows.map((r, i) => `<tr onclick="dbManager.selectRow(${i}, event)" class="db-clickable-row">${columns.map(col => `<td>${this.escapeHtml(r[col] === null ? 'NULL' : String(r[col]))}</td>`).join('')}</tr>`).join('');
        this.viewContainer.innerHTML = `
            <div class="db-table-view">
                <div class="db-table-header">
                    <div class="db-table-title"><h2><i class="fas fa-table"></i> ${data.table}</h2></div>
                    <div class="db-table-actions">
                        <button class="db-action-btn" onclick="dbManager.exportTableCSV()"><i class="fas fa-download"></i> Export CSV</button>
                        <button class="db-action-btn" onclick="dbManager.refreshTable()"><i class="fas fa-sync-alt"></i> Refresh</button>
                    </div>
                </div>
                <div class="db-data-table"><table class="db-table"><thead><tr>${header}</tr></thead><tbody>${body}</tbody></table></div>
            </div>
        `;
    }

    selectRow(index, event) {
        if (!this.currentTable || !this.currentTable.data[index]) return;
        
        // Remove active class from previous rows
        document.querySelectorAll('.db-clickable-row').forEach(row => row.classList.remove('active-row'));
        
        // Add active class to clicked row
        const target = event.target.closest('tr');
        if (target) target.classList.add('active-row');

        this.selectedRow = {
            index: index,
            data: this.currentTable.data[index],
            columns: this.currentTable.columns
        };
        
        this.showRowDetails(this.selectedRow);
    }

    showRowDetails(row) {
        const columnNames = row.columns.map(c => c.COLUMN_NAME);
        let detailsHTML = '<div class="db-details-wrapper">';
        
        columnNames.forEach(col => {
            const value = row.data[col];
            const colInfo = row.columns.find(c => c.COLUMN_NAME === col);
            const isNull = value === null || value === undefined;
            const displayValue = isNull ? 'NULL' : String(value);

            detailsHTML += `
                <div class="db-detail-field">
                    <div class="db-field-label">${col}</div>
                    <div class="db-field-value ${isNull ? 'null' : ''}">
                        ${this.escapeHtml(displayValue)}
                    </div>
                    <div class="db-field-type">${colInfo ? colInfo.COLUMN_TYPE : ''}</div>
                </div>
            `;
        });

        detailsHTML += `
            <div class="db-form-actions" style="margin-top: 16px;">
                <button class="db-btn-save" onclick="dbManager.openEditForm()">
                    <i class="fas fa-edit"></i> Edit Row
                </button>
            </div>
        </div>`;

        this.rightPanel.innerHTML = detailsHTML;
    }

    openEditForm() {
        if (!this.selectedRow) return;

        const columnNames = this.selectedRow.columns.map(col => col.COLUMN_NAME);
        let formHTML = '<div class="db-edit-form">';
        
        columnNames.forEach((col, idx) => {
            const value = this.selectedRow.data[col];
            const colInfo = this.selectedRow.columns.find(c => c.COLUMN_NAME === col);
            const colType = colInfo ? colInfo.COLUMN_TYPE.toLowerCase() : 'text';
            const displayValue = value === null ? '' : String(value);

            let inputType = 'text';
            if (colType.includes('int')) inputType = 'number';
            else if (colType.includes('datetime') || colType.includes('date')) inputType = 'datetime-local';
            else if (colType.includes('email')) inputType = 'email';

            formHTML += `
                <div class="db-form-group">
                    <label for="field_${idx}">${col}</label>
                    ${colType.includes('text') ? 
                        `<textarea id="field_${idx}" data-column="${col}">${this.escapeHtml(displayValue)}</textarea>` :
                        `<input type="${inputType}" id="field_${idx}" data-column="${col}" value="${this.escapeHtml(displayValue)}">`
                    }
                    <div class="db-field-type">${colInfo ? colInfo.COLUMN_TYPE : ''}</div>
                </div>
            `;
        });

        formHTML += `
            <div class="db-form-actions">
                <button class="db-btn-save" onclick="dbManager.saveRowChanges()">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <button class="db-btn-cancel" onclick="dbManager.showRowDetails(dbManager.selectedRow)">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </div>`;

        this.rightPanel.innerHTML = formHTML;
    }

    async saveRowChanges() {
        if (!this.selectedRow) return;
        
        const inputs = document.querySelectorAll('[data-column]');
        const changes = {};
        
        inputs.forEach(input => {
            changes[input.dataset.column] = input.value || null;
        });
        
        try {
            const primaryKeyColumn = this.selectedRow.columns.length > 0 ? this.selectedRow.columns[0].COLUMN_NAME : 'id';
            const primaryKeyValue = this.selectedRow.data[primaryKeyColumn];
            
            const response = await fetch(`/api/database/table/${this.currentTable.table}/row`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    rowData: changes,
                    whereColumn: primaryKeyColumn,
                    whereValue: primaryKeyValue
                })
            });
            
            if (!response.ok) {
                let errBody = null;
                try { errBody = await response.json(); } catch (e) { errBody = await response.text(); }
                throw new Error((errBody && errBody.error) ? errBody.error : String(errBody || 'Failed to save changes'));
            }

            let result;
            try {
                result = await response.json();
            } catch (err) {
                const text = await response.text();
                console.error('Save row API returned non-JSON response:', text);
                this.showNotification('Row saved but server returned unexpected response', 'success');
                await this.loadTableData(this.currentTable.table);
                return;
            }
            this.showNotification(result.message || 'Row saved successfully', 'success');
            await this.loadTableData(this.currentTable.table);
            
        } catch (error) {
            console.error('Error saving row:', error);
            this.showNotification('Error: ' + error.message, 'error');
        }
    }

    closeRightPanel() {
        this.rightPanel.innerHTML = `
            <div class="db-empty-right">
                <i class="fas fa-mouse-pointer"></i>
                <p>Select a row to view details</p>
            </div>
        `;
        this.selectedRow = null;
    }

    updateActiveTableItem(tableName) {
        document.querySelectorAll('.db-table-item').forEach(item => {
            item.classList.remove('active');
        });
        
        const activeItem = document.querySelector(`[data-table="${tableName}"]`);
        if (activeItem) {
            activeItem.classList.add('active');
        }
    }

    getDataType(value) {
        if (value === null || value === undefined || value === 'NULL') {
            return 'null';
        }
        if (!isNaN(value) && value !== '') {
            return 'number';
        }
        if (/^\d{4}-\d{2}-\d{2}/.test(value)) {
            return 'date';
        }
        return 'text';
    }

    formatCellValue(value) {
        if (value === null || value === undefined) {
            return '<span style="color: var(--text-muted); font-style: italic;">NULL</span>';
        }
        const stringValue = String(value);
        if (stringValue.length > 100) {
            return this.escapeHtml(stringValue.substring(0, 100)) + '...';
        }
        return this.escapeHtml(stringValue);
    }

    formatNumber(num) {
        return new Intl.NumberFormat().format(num);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    exportTableCSV() {
        if (!this.currentTable) return;

        const data = this.currentTable;
        const columnNames = data.columns.map(col => col.COLUMN_NAME);
        
        let csv = columnNames.join(',') + '\n';
        
        data.data.forEach(row => {
            const values = columnNames.map(col => {
                const value = row[col] !== null ? String(row[col]) : '';
                return value.includes(',') || value.includes('"') 
                    ? `"${value.replace(/"/g, '""')}"` 
                    : value;
            });
            csv += values.join(',') + '\n';
        });

        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${data.table}_${new Date().getTime()}.csv`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    }

    refreshTable() {
        if (this.currentTable) {
            this.loadTableData(this.currentTable.table);
        }
    }

    showNotification(message, type = 'success') {
        let container = document.getElementById('dbNotificationContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'dbNotificationContainer';
            container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
            document.body.appendChild(container);
        }

        const notification = document.createElement('div');
        const bgColor = type === 'success' ? '#d4edda' : '#f8d7da';
        const textColor = type === 'success' ? '#155724' : '#721c24';
        const borderColor = type === 'success' ? '#c3e6cb' : '#f5c6cb';
        
        notification.style.cssText = `
            background-color: ${bgColor};
            color: ${textColor};
            border: 1px solid ${borderColor};
            border-radius: 4px;
            padding: 12px 16px;
            margin-bottom: 10px;
            animation: slideIn 0.3s ease;
        `;
        notification.innerHTML = message;

        container.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    showError(message) {
        const viewContainer = document.getElementById('viewContainer');
        viewContainer.innerHTML = `
            <div class="db-empty-state">
                <i class="fas fa-exclamation-triangle" style="color: var(--danger);"></i>
                <h2>Error</h2>
                <p>${message}</p>
                <button onclick="location.reload()" class="db-action-btn" style="margin-top: 16px;">
                    <i class="fas fa-sync-alt"></i> Retry
                </button>
            </div>
        `;
    }

    showTablesError(message) {
        const tablesList = document.getElementById('tablesList');
        tablesList.innerHTML = `
            <div class="db-loading">
                <i class="fas fa-exclamation-circle" style="color: #dc3545;"></i>
                <p>${message}</p>
                <button onclick="dbManager.loadTables()" class="db-action-btn" style="margin-top: 16px;">
                    <i class="fas fa-sync-alt"></i> Retry
                </button>
            </div>
        `;
    }
}

// Initialize
let dbManager;
document.addEventListener('DOMContentLoaded', () => {
    dbManager = new DatabaseManager();
});