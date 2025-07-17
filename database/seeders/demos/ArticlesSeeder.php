<?php

namespace Database\Seeders\demos;

use App\Models\Article;
use App\Services\ReferenceService;
use App\Services\StockService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ArticlesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                "designation" => "iPhone 13 Pro",
                "description" => "Latest smartphone from Apple with advanced camera features",
                "taxe" => "20",
                "prix_vente" => "999.99",
                "prix_achat" => "750.00"
            ],
            [
                "designation" => "Sony WH-1000XM4",
                "description" => "Premium noise-canceling headphones with exceptional sound quality",
                "taxe" => "0",
                "prix_vente" => "349.99",
                "prix_achat" => "270.00"
            ],
            [
                "designation" => "Dell XPS 13",
                "description" => "Ultra-portable laptop with stunning display and powerful performance",
                "taxe" => "20",
                "prix_vente" => "1299.99",
                "prix_achat" => "1100.00"
            ],
            [
                "designation" => "Philips Hue Starter Kit",
                "description" => "Smart lighting system with customizable colors and schedules",
                "taxe" => "20",
                "prix_vente" => "199.99",
                "prix_achat" => "150.00"
            ],
            [
                "designation" => "Samsung Galaxy Watch 4",
                "description" => "Smartwatch with advanced fitness tracking and health monitoring",
                "taxe" => "0",
                "prix_vente" => "299.99",
                "prix_achat" => "220.00"
            ],
            [
                "designation" => "Nintendo Switch",
                "description" => "Hybrid gaming console for home and portable gaming experiences",
                "taxe" => "20",
                "prix_vente" => "299.99",
                "prix_achat" => "250.00"
            ],
            [
                "designation" => "Logitech MX Master 3",
                "description" => "Wireless mouse with advanced features for productivity",
                "taxe" => "0",
                "prix_vente" => "99.99",
                "prix_achat" => "80.00"
            ],
            [
                "designation" => "Apple AirPods Pro",
                "description" => "Wireless earbuds with active noise cancellation and customizable fit",
                "taxe" => "20",
                "prix_vente" => "249.99",
                "prix_achat" => "200.00"
            ],
            [
                "designation" => "LG OLED C1",
                "description" => "Premium OLED TV with stunning picture quality and smart features",
                "taxe" => "20",
                "prix_vente" => "1999.99",
                "prix_achat" => "1800.00"
            ],
            [
                "designation" => "GoPro HERO 10 Black",
                "description" => "Action camera with 5.3K video recording and advanced stabilization",
                "taxe" => "0",
                "prix_vente" => "499.99",
                "prix_achat" => "400.00"
            ],
            [
                "designation" => "Bose QuietComfort 45",
                "description" => "Premium noise-canceling headphones for immersive listening experience",
                "taxe" => "20",
                "prix_vente" => "329.99",
                "prix_achat" => "270.00"
            ],
            [
                "designation" => "Microsoft Surface Pro 8",
                "description" => "Versatile 2-in-1 laptop with high-resolution touchscreen display",
                "taxe" => "20",
                "prix_vente" => "1199.99",
                "prix_achat" => "950.00"
            ],
            [
                "designation" => "Amazon Kindle Paperwhite",
                "description" => "E-reader with glare-free display and waterproof design",
                "taxe" => "0",
                "prix_vente" => "139.99",
                "prix_achat" => "100.00"
            ],
            [
                "designation" => "Razer BlackWidow V3 Pro",
                "description" => "Wireless mechanical gaming keyboard with customizable RGB lighting",
                "taxe" => "20",
                "prix_vente" => "229.99",
                "prix_achat" => "180.00"
            ],
            [
                "designation" => "Samsung Odyssey G9",
                "description" => "Ultra-wide gaming monitor with curved display and high refresh rate",
                "taxe" => "20",
                "prix_vente" => "1499.99",
                "prix_achat" => "1200.00"
            ],
            [
                "designation" => "Nikon Z6 II",
                "description" => "Mirrorless camera with 24.5MP sensor and 4K video recording",
                "taxe" => "0",
                "prix_vente" => "1999.99",
                "prix_achat" => "1500.00"
            ],
            [
                "designation" => "Fitbit Charge 5",
                "description" => "Advanced fitness tracker with built-in GPS and health monitoring features",
                "taxe" => "20",
                "prix_vente" => "179.99",
                "prix_achat" => "140.00"
            ],
            [
                "designation" => "Anker Soundcore Liberty Air 2 Pro",
                "description" => "True wireless earbuds with active noise cancellation and personalized sound",
                "taxe" => "0",
                "prix_vente" => "129.99",
                "prix_achat" => "90.00"
            ],
            [
                "designation" => "Xbox Series X",
                "description" => "Next-gen gaming console with 4K gaming and fast load times",
                "taxe" => "20",
                "prix_vente" => "499.99",
                "prix_achat" => "400.00"
            ],
            [
                "designation" => "Canon EOS R5",
                "description" => "Full-frame mirrorless camera with 8K video recording and advanced autofocus",
                "taxe" => "20",
                "prix_vente" => "3899.99",
                "prix_achat" => "3400.00"
            ],
            [
                "designation" => "Ultimate Ears Boom 3",
                "description" => "Portable Bluetooth speaker with deep bass and 360-degree sound",
                "taxe" => "0",
                "prix_vente" => "149.99",
                "prix_achat" => "110.00"
            ],
            [
                "designation" => "Acer Predator XB273U GX",
                "description" => "27-inch gaming monitor with WQHD resolution and high refresh rate",
                "taxe" => "20",
                "prix_vente" => "699.99",
                "prix_achat" => "550.00"
            ],
            [
                "designation" => "Garmin fenix 7",
                "description" => "Multisport GPS smartwatch with advanced performance metrics",
                "taxe" => "20",
                "prix_vente" => "799.99",
                "prix_achat" => "650.00"
            ],
            [
                "designation" => "Corsair K70 RGB TKL",
                "description" => "Mechanical gaming keyboard with compact design and customizable RGB lighting",
                "taxe" => "0",
                "prix_vente" => "139.99",
                "prix_achat" => "100.00"
            ],
            [
                "designation" => "Roku Ultra",
                "description" => "Streaming device with 4K HDR and Dolby Vision support",
                "taxe" => "20",
                "prix_vente" => "99.99",
                "prix_achat" => "80.00"
            ],
            [
                "designation" => "Sonos One (Gen 2)",
                "description" => "Smart speaker with built-in voice control and multi-room audio",
                "taxe" => "20",
                "prix_vente" => "199.99",
                "prix_achat" => "150.00"
            ],
            [
                "designation" => "LG C1 OLED",
                "description" => "4K OLED TV with AI picture and sound enhancements",
                "taxe" => "0",
                "prix_vente" => "1799.99",
                "prix_achat" => "1500.00"
            ],
            [
                "designation" => "Fujifilm X-T4",
                "description" => "Mirrorless camera with 26.1MP sensor and 4K video recording",
                "taxe" => "20",
                "prix_vente" => "1699.99",
                "prix_achat" => "1400.00"
            ],
            [
                "designation" => "JBL Flip 5",
                "description" => "Portable waterproof Bluetooth speaker with powerful sound",
                "taxe" => "20",
                "prix_vente" => "119.99",
                "prix_achat" => "90.00"
            ],
            [
                "designation" => "Asus ROG Swift PG279Q",
                "description" => "27-inch gaming monitor with IPS panel and 165Hz refresh rate",
                "taxe" => "0",
                "prix_vente" => "699.99",
                "prix_achat" => "550.00"
            ],
            [
                "designation" => "iPad Air (2022)",
                "description" => "Powerful tablet with A14 Bionic chip and 10.9-inch Liquid Retina display",
                "taxe" => "20",
                "prix_vente" => "599.99",
                "prix_achat" => "450.00"
            ],
            [
                "designation" => "Beats Studio Buds",
                "description" => "True wireless earbuds with active noise cancellation and comfortable fit",
                "taxe" => "20",
                "prix_vente" => "149.99",
                "prix_achat" => "110.00"
            ],
            [
                "designation" => "TP-Link Archer AX6000",
                "description" => "Wi-Fi 6 router with high-speed connectivity and advanced security features",
                "taxe" => "0",
                "prix_vente" => "299.99",
                "prix_achat" => "250.00"
            ],
            [
                "designation" => "Canon EOS-1D X Mark III",
                "description" => "Professional DSLR camera with 20.1MP sensor and 4K video recording",
                "taxe" => "20",
                "prix_vente" => "6499.99",
                "prix_achat" => "5500.00"
            ],
            [
                "designation" => "Samsung Odyssey G7",
                "description" => "27-inch curved gaming monitor with QLED panel and 240Hz refresh rate",
                "taxe" => "20",
                "prix_vente" => "799.99",
                "prix_achat" => "650.00"
            ],
            [
                "designation" => "Huawei MateBook X Pro",
                "description" => "Sleek ultrabook with 3K touchscreen display and powerful performance",
                "taxe" => "0",
                "prix_vente" => "1499.99",
                "prix_achat" => "1200.00"
            ],
            [
                "designation" => "Netgear Nighthawk RAX200",
                "description" => "Tri-band Wi-Fi 6 router with blazing-fast speeds and extensive coverage",
                "taxe" => "20",
                "prix_vente" => "499.99",
                "prix_achat" => "400.00"
            ],
            [
                "designation" => "Fitbit Versa 3",
                "description" => "Health and fitness smartwatch with built-in GPS and heart rate monitoring",
                "taxe" => "20",
                "prix_vente" => "229.99",
                "prix_achat" => "180.00"
            ],
            [
                "designation" => "BenQ EW3270U",
                "description" => "32-inch 4K HDR monitor with Eye-Care technology and USB-C connectivity",
                "taxe" => "0",
                "prix_vente" => "499.99",
                "prix_achat" => "400.00"
            ]
        ];
        Schema::disableForeignKeyConstraints();
//        Article::truncate() ;
        foreach ($data as $item){

            $article= new Article();
            $article->designation = $item['designation'];
            $article->description = $item['description'];
            $article->taxe = $item['taxe'];
            $article->prix_vente = $item['prix_vente'];
            $article->prix_achat = $item['prix_achat'];
            $article->unite_id = 1;
            $article->reference = ReferenceService::generateReference('art');
            ReferenceService::incrementCompteur('art');
            $article->save();
            $randomValue = rand(0, 100);

        }
    }
}
