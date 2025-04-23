<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class LogService
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function logActivity($nim, $list)
    {
        $log = $this->getBasicLog($nim, $list);
        DB::table('log_activity')->insert($log);
    }

    protected function getBasicLog($nim, $list)
    {
        $log = [];
        $log['method'] = $this->request->getMethod();
        $log['agent'] = $this->request->header('user-agent');
        $log['ip'] = $this->request->ip();
        $log['list'] = $list;
        $log['nim'] = $nim;
        return $log;
    }

    public function logError($nim, $list, Throwable $error)
    {
        $log = $this->getBasicLog($nim, $list);
        $log['error'] = $error->getMessage();
        DB::table('log_error')->insert($log);
        //simpan log dalam bentuk file .log
        Log::channel('daily')->error($error);
    }

    public function logDatabase($nim, $list, $table, $id_table, $data)
    {
        $log = $this->getBasicLog($nim, $list);
        $log['table'] = $table;
        $log['id_table'] = $id_table;
        $log['data'] = json_encode($data);
        DB::table('log_database')->insert($log);
    }
}
