<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use View;
use DB;

use App\Menu;
use App\Settings;
use App\UserMenu;
use App\UserMenuActions;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        View::composer('frontend.*', function ($view) {

        // $categories = \App\Category::where('categoryStatus', 1)->get();


        /*$menus = [];
        $subCat = [];
        foreach ($categories as $category) {            
            $menus[$category->name] = [];
            $subCat[$category->name] = [];
            $subCategories = DB::table('sub_categories')
            ->join('categories', 'categories.id', '=', 'sub_categories.category_id')
            ->where('sub_categories.category_id', $category->id)
            ->where('sub_categories.status', 1)
            ->select('sub_categories.*')
            ->get();
            foreach ($subCategories as $subCategory) { 
                $menus[$category->name][$category->id][$subCategory->name]['id'] = $subCategory->id;
                $subCat[$category->name][$subCategory->name]['id'] = $subCategory->id;
           
            }            
        }*/



        /*$view->with('menus', $menus);
        $view->with('males', $males);
        $view->with('females', $females);
        $view->with('fixedProducts', $fixedProducts);
        $view->with('featuredImges', $featuredImges);
        $view->with('newProducts', $newProducts);*/
    });

        View::composer('*',function($menus){
            $activeMenu = Menu::where('menuStatus',1)->get();
            $menus->with('activeMenu',$activeMenu);
        });

        // View::composer('*',function($siteInfo){
        //     $information = Settings::where('siteStatus',1)->first();
        //     $siteInfo->with('information',$information);
        // });

        //Link for Add New Button
        View::composer('*',function($addLink){
            $routeName = \Request::route()->getName();

            if ($routeName)
            {
                $userMenus = UserMenu::where('menuLink',$routeName)->first();
                if ($userMenus)
                {
                    $userMenuAction = UserMenuActions::where('parentmenuId',@$userMenus->id)->where('menuType',1)->first();

                    if(@$userMenuAction->actionLink)
                    {
                        $actionLink = @$userMenuAction->actionLink;
                    }
                    else
                    {
                        $actionLink = 'admin.index';
                    }
                }
                else
                {
                    $actionLink = 'admin.index';
                }
                
            }
            $addLink->with('addNewLink',@$actionLink);
        });

        //Link for Go Back
        View::composer('*',function($backLink){
            // $link = "";
            $routeName = \Request::route()->getName();

            if ($routeName)
            {
                $userMenuAction = UserMenuActions::where('actionLink',@$routeName)->first();
                if ($userMenuAction)
                {
                    $userMenu = UserMenu::where('id',@$userMenuAction->parentmenuId)->first();
                    if ($userMenu)
                    {
                        $link = $userMenu->menuLink;
                    }
                    else
                    {
                        $link = 'admin.index';                         
                    }
                }
                else
                {
                    $link = 'admin.index';                        
                }
            }

            $backLink->with('goBackLink',@$link); 
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
