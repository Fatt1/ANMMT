<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SqliLabController extends Controller
{
    public function showBlind()
    {
        return view('student.sqli.blind');
    }

    public function probeBlind(Request $request)
    {
        $id = $request->query('id', '1');
        $message = 'KHONG';
        $messageType = 'error';
        $sql = "SELECT id FROM users WHERE id = $id LIMIT 1";
        $sqlParameterized = "SELECT id FROM users WHERE id = ? LIMIT 1";
        try {
            // [VULNERABLE HERE] User input is concatenated directly into SQL query.
            // FIX: Use parameter binding / prepared statements for id.
            //$rows = DB::select($sql);
            $rows = DB::select($sqlParameterized, [$id]); // This is the secure way to do it.
            if (!empty($rows)) {
                $message = 'CO';
                $messageType = 'success';
            }
        } catch (\Throwable $e) {
            $message = 'KHONG';
            $messageType = 'error';
        }

        return view('student.sqli.blind', [
            'id' => $id,
            'sql' => $sql,
            'message' => $message,
            'messageType' => $messageType,
        ]);
    }

    public function showOob()
    {
        return view('student.sqli.oob');
    }

    public function showUnion()
    {
        return view('student.sqli.union');
    }

    public function productDetailUnion(Request $request)
    {
        $id = $request->query('id', '1');
        $sql = "SELECT id, name, description, price FROM products WHERE id = $id LIMIT 1";
        $sqlParameterized = "SELECT id, name, description, price FROM products WHERE id = ? LIMIT 1";
        $message = 'KHONG';
        $messageType = 'error';
        $rows = [];
        $dbError = null;

        try {
            // [VULNERABLE HERE] Raw request parameter is concatenated directly into SQL.
            $rows = DB::select($sql);
            // $rows = DB::select($sqlParameterized, [$id]); // This is the secure way to do it.
            if (!empty($rows)) {
                $message = 'CO';
                $messageType = 'success';
            }
        } catch (\Throwable $e) {
            $dbError = $e->getMessage();
        }

        return view('student.sqli.union', [
            'id' => $id,
            'sql' => $sql,
            'rows' => $rows,
            'dbError' => $dbError,
            'message' => $message,
            'messageType' => $messageType,
        ]);
    }

    public function testOob(Request $request)
    {
        $channel = $request->query('channel', 'attacker.lab');

        // [VULNERABLE HERE] User input is directly injected into a DB call chain.
        // FIX: Never concatenate user input into SQL. Validate allow-list + bindings.
        $sql = "SELECT LOAD_FILE(CONCAT('\\\\', '$channel', '\\\\share\\\\probe.txt')) AS probe";

        $dbError = null;
        $rows = [];

        try {
            $rows = DB::select($sql);
        } catch (\Throwable $e) {
            $dbError = $e->getMessage();
        }

        return view('student.sqli.oob', [
            'channel' => $channel,
            'sql' => $sql,
            'rows' => $rows,
            'dbError' => $dbError,
        ]);
    }
}