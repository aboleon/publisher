<?php declare(strict_types = 1);

namespace Database\Seeders\Aboleon\Publisher;

use Aboleon\Publisher\Models\Pages;
use Illuminate\Database\Eloquent\Model;
use Aboleon\Publisher\Models\PagesCreateContent;

class Seeder extends \Illuminate\Database\Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $data = [
            [
                'title' => 'Accueil',
                'parent' => 0,
                'published' => 1,
                'is_nav'=>1,
                'is_primary'=>1,
                'type' => 'home'
            ],            [
                'title' => 'Qui sommes-nous ?',
                'parent' => 0,
                'published' => 1,
                'type' => 'about'
            ],
            [
                'title' => 'Contact',
                'parent' => 0,
                'published' => 1,
                'is_nav'=>1,
                'is_primary'=>1,
                'type' => 'contact'
            ],

            [
                'title' => 'Général',
                'parent' => 0,
                'published' => 1,
                'type' => 'generics'
            ]
        ];


        $pages = new PagesCreateContent();

        foreach($data as $val) {
            $pages->setupPage($val);
        }

        $generics = Pages::whereType('generics')->value('id');

        $data = [
            [
                'title' => 'Pied de page',
                'parent' => $generics,
                'published' => null,
                'type' => 'footer'
            ],
            [
                'title' => 'Mentions légales',
                'parent' => $generics,
                'published' => 1,
                'is_nav'=>1,
                'type' => 'legal'
            ],
            [
                'title' => 'Politique de confidentialité',
                'parent' => $generics,
                'published' => 1,
                'is_nav'=>1,
                'type' => 'rgpd'
            ]
        ];


        foreach($data as $val) {
            $pages->setupPage($val);
        }
    }
}
