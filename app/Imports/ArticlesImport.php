<?php

namespace App\Imports;

use App\Models\Article;
use App\Services\GlobalService;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithColumnLimit;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ArticlesImport implements ToModel,WithHeadingRow,SkipsEmptyRows,WithColumnLimit
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Article([
        ]);
    }


    public function endColumn(): string
    {
        $codeBarre = GlobalService::get_code_barre();
        if ($codeBarre) {
            return 'H';
        }
        return 'G';
    }
}
