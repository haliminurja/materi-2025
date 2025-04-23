<?php

namespace App\Services;

use App\Models\LogActivity;
use App\Models\LogDatabase;
use App\Models\LogError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class LogService
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function logActivity($list)
    {
        $log = $this->getBasicLog($list);
        LogActivity::create($log);
    }

    protected function getBasicLog($list)
    {
        return [
            'method' => $this->request->getMethod(),
            'agent' => $this->request->header('user-agent'),
            'ip'     => $this->request->ip(),
            'list'   => $list,
        ];
    }

    public function logError($list, Throwable $error)
    {
        $log = $this->getBasicLog($list);
        $log['path'] = $this->request->path();
        $log['error'] = $error->getMessage();

        LogError::create($log);
        Log::channel('daily')->error($error);
    }

    public function logDatabase($list, $table, $id_table, $data)
    {
        $log = $this->getBasicLog($list);
        $log['table'] = $table;
        $log['id_table'] = $id_table;
        $log['data'] = $data;

        LogDatabase::create($log);
    }
}
