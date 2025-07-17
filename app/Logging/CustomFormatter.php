<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;

class CustomFormatter extends LineFormatter
{
    public function format(array|\Monolog\LogRecord $record): string
    {
        $tenant = session()->get('tenant');
        $date = $record['datetime']->format('Y-m-d H:i:s');
        $file = isset($record['context']['exception']) ? $record['context']['exception']->getFile() : '';
        $line = isset($record['context']['exception']) ? $record['context']['exception']->getLine() : '';
        $message = $record['message'];
        $level = $record['level_name'];
        $formatted = "[$date] $level $tenant $file $line  $message \n";
        return $formatted;
    }
}
