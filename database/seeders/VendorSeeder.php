<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vendor::query()->delete();

        Vendor::create([
            'id'            => 1,
            'name'          => "L'Oreal Professional",
            'contact_name'  => 'Jane Loreal',
            'email'         => 'contact@lorealprofessional.com',
            'phone'         => '+1 555-0101',
            'website'       => 'https://www.lorealprofessional.com',
            'tax_number'    => '29GGPPP1234A1Z5',
            'payment_terms' => 'Net 30',
            'bank_name'     => 'Paris National Bank',
            'bank_account'  => 'FR76300060000000000000001',
            'bank_code'     => 'PNBFR2B',
            'address'       => '14 Rue Royale, Paris, France',
            'description'   => 'Supplier of professional hair colors, developers, and styling sprays.',
            'status'        => true,
        ]);

        Vendor::create([
            'id'            => 2,
            'name'          => 'Wella Professionals',
            'contact_name'  => 'Marcus Wella',
            'email'         => 'orders@wellaprofessionals.com',
            'phone'         => '+49 89 2345-0',
            'website'       => 'https://www.wella.com',
            'tax_number'    => 'DE123456789',
            'payment_terms' => 'Net 15',
            'bank_name'     => 'Deutsche Bank München',
            'bank_account'  => 'DE89370400440532013000',
            'bank_code'     => 'DEUTDEDBMM',
            'address'       => 'Berliner Straße 65, Darmstadt, Germany',
            'description'   => 'Supplier of hair shampoo, hair masks, and premium styling oils.',
            'status'        => true,
        ]);

        Vendor::create([
            'id'            => 3,
            'name'          => 'Schwarzkopf Professional',
            'contact_name'  => 'Fritz Schwarzkopf',
            'email'         => 'fritz@schwarzkopf.de',
            'phone'         => '+49 211 797-0',
            'website'       => 'https://www.schwarzkopf-professional.com',
            'tax_number'    => 'DE987654321',
            'payment_terms' => 'COD',
            'bank_name'     => 'Commerzbank Düsseldorf',
            'bank_account'  => 'DE44300400000012345678',
            'bank_code'     => 'COBAFR2D',
            'address'       => 'Henkelstraße 67, Düsseldorf, Germany',
            'description'   => 'Supplier of styling clays, powders, and root lifters.',
            'status'        => true,
        ]);
    }
}
