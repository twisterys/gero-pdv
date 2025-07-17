<?php

namespace Database\Seeders;

use App\Models\PosSettings;
use App\Models\Vente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PosSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = [
            [
                'key'=>'type_vente',
                'label'=>'Document de vente',
                'value'=>'bc',
            ],
            [
                'key'=>'type_retour',
                'label'=>'Document de retour',
                'value'=>'br',
            ],
            [
                'key'=>'type_pos',
                'label'=>'Type de point de vente',
                'value'=>'parfums',
            ],
            [
                'key'=>'ticket',
                'label'=>'Billet',
                'value'=>0
            ],
            [
                'key'=>'modifier_prix',
                'label'=>'Modfication de prix',
                'value'=>0
            ],
            [
                'key'=>'ticket_template',
                'label'=>'Modèle de billet',
                'value'=>'<p style="text-align: center;">[Date_et_heure]</p>
<table style="border-collapse: collapse; width: 100%; height: 22px;" border="0">
<tbody>
<tr style="height: 22px;">
<td style="width: 46.3569%; height: 22px;">R&eacute;f&eacute;rence:</td>
<td style="width: 46.3569%; height: 22px;">[Reference]</td>
</tr>
</tbody>
</table>
<div style="width: 100%; white-space: nowrap; overflow: hidden;">======================================================================================================</div>
<p>[Tableau]</p>
<div style="width: 100%; white-space: nowrap; overflow: hidden;">======================================================================================================</div>
<table style="border-collapse: collapse; width: 100%; height: 66px;" border="0">
<tbody>
<tr style="height: 22px;">
<td style="width: 46.1917%; height: 22px;">Total HT</td>
<td style="width: 46.1917%; height: 22px; text-align: right;">[Total_HT]</td>
</tr>
<tr style="height: 22px;">
<td style="width: 46.1917%; height: 22px;">Total TVA</td>
<td style="width: 46.1917%; height: 22px; text-align: right;">[Total_TVA]</td>
</tr>
<tr style="height: 22px;">
<td style="width: 46.1917%; height: 22px;">Total TTC</td>
<td style="width: 46.1917%; height: 22px; text-align: right;">[Total_TTC]</td>
</tr>
</tbody>
</table>
<p style="text-align: right;">&nbsp;</p>'
            ]
        ];
       foreach ($options as $option){
           PosSettings::where('key',$option['key'])->firstOr(function () use($option){
               PosSettings::create([
                   'key' => $option['key'],
                   'label' => $option['label'],
                   'value' => $option['value'],
                   ]);
           });
       }
    }
}
