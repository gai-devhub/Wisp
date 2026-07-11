<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DatabaseManagerController extends Controller
{
    public function databaseManagerPage(): View
    {
        return view('database-manager.index');
    }

    public function getTablesData()
    {
        try {
            $dbDriver = config('database.default');
            $database = config("database.connections.$dbDriver.database");

            $allTables = DB::select(
                'SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME',
                [$database]
            );

            $tables = [];
            foreach ($allTables as $tableInfo) {
                $tableName = $tableInfo->TABLE_NAME;
                $rowCount = 0;

                try {
                    $rowCount = DB::table($tableName)->count();
                } catch (\Throwable $e) {
                    continue;
                }

                $tables[] = [
                    'name' => $tableName,
                    'rows' => $rowCount,
                ];
            }

            return response()->json(['tables' => $tables]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to fetch tables'], 500);
        }
    }

    public function getTableData($table)
    {
        try {
            $dbDriver = config('database.default');
            $database = config("database.connections.$dbDriver.database");

            $validTables = DB::select(
                'SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?',
                [$database]
            );
            $validTableNames = array_column($validTables, 'TABLE_NAME');

            if (!in_array($table, $validTableNames, true)) {
                return response()->json(['error' => 'Invalid table'], 400);
            }

            $columns = DB::select(
                'SELECT COLUMN_NAME, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION ASC',
                [$database, $table]
            );

            $data = DB::table($table)->get();
            $totalCount = DB::table($table)->count();

            return response()->json([
                'table' => $table,
                'columns' => $columns,
                'data' => $data,
                'total' => $totalCount,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to fetch table data'], 500);
        }
    }

    public function updateRowData(Request $request, $table)
    {
        try {
            $dbDriver = config('database.default');
            $database = config("database.connections.$dbDriver.database");

            $validTables = DB::select(
                'SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?',
                [$database]
            );
            $validTableNames = array_column($validTables, 'TABLE_NAME');

            if (!in_array($table, $validTableNames, true)) {
                return response()->json(['error' => 'Invalid table'], 400);
            }

            $rowData = $request->json('rowData');
            $whereColumn = $request->json('whereColumn');
            $whereValue = $request->json('whereValue');

            if (!$rowData || !$whereColumn || !$whereValue) {
                return response()->json(['error' => 'Missing required data'], 400);
            }

            DB::table($table)
                ->where($whereColumn, $whereValue)
                ->update($rowData);

            return response()->json(['success' => true, 'message' => 'Row updated successfully']);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to update row: ' . $e->getMessage()], 500);
        }
    }
}
