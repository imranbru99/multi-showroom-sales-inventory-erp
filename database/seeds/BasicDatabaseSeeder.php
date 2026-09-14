<?php

use Illuminate\Database\Seeder;

class BasicDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        App\Category::create([
            'categoryName'=>'Online Shop',
        	'categoryStatus'=>'1',
        	'categoryType'=>'1',
        ]);


        App\Category::create([
        	'categoryName'=>'Leather',
        	'categoryStatus'=>'1',
            'categoryType'=>'1',
        ]);
        App\Category::create([
        	'categoryName'=>'Cosmetics',
        	'categoryStatus'=>'1',
            'categoryType'=>'1',
        ]);
        App\Category::create([
        	'categoryName'=>'Cloths',
        	'categoryStatus'=>'1',
            'categoryType'=>'1',
        ]);



        //subcategories   
        App\SubCategory::create([
        	'subcategoryName'=>'Leather & Fashion Design',
        	'category_id'=>'1',
            'subcategoryStatus'=>'1',
        ]);
        App\SubCategory::create([
        	'subcategoryName'=>'Online Shop',
        	'category_id'=>'1',
             'subcategoryStatus'=>'1',
        ]);
        App\SubCategory::create([
        	'subcategoryName'=>'Export & Import',
        	'category_id'=>'1',
             'subcategoryStatus'=>'1',
        ]);     
        App\SubCategory::create([
        	'subcategoryName'=>'Mens',
        	'category_id'=>'2',
            'subcategoryStatus'=>'1',
        ]);   
        App\SubCategory::create([
        	'subcategoryName'=>'Womens',
        	'category_id'=>'2',
            'subcategoryStatus'=>'1',
        ]);   
        App\SubCategory::create([
        	'subcategoryName'=>'Children',
        	'category_id'=>'2',
            'subcategoryStatus'=>'1',
        ]);

        App\SubCategory::create([
        	'subcategoryName'=>'Mens',
        	'category_id'=>'3',
            'subcategoryStatus'=>'1',
        ]);   
        App\SubCategory::create([
        	'subcategoryName'=>'Womens',
        	'category_id'=>'3',
            'subcategoryStatus'=>'1',
        ]);   
        App\SubCategory::create([
        	'subcategoryName'=>'Children',
        	'category_id'=>'3',
            'subcategoryStatus'=>'1',
        ]);

        App\SubCategory::create([
        	'subcategoryName'=>'Mens',
        	'category_id'=>'4',
            'subcategoryStatus'=>'1',
        ]);   
        App\SubCategory::create([
        	'subcategoryName'=>'Womens',
        	'category_id'=>'4',
            'subcategoryStatus'=>'1',
        ]);   
        App\SubCategory::create([
        	'subcategoryName'=>'Children',
        	'category_id'=>'4',
            'subcategoryStatus'=>'1',
        ]);

        echo "All Database Seeded Successfully!";
    }
}