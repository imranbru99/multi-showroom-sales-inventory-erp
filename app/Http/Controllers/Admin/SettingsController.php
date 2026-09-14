<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function information()
    {
        $title = "Update Website Information";
    	$settings = Settings::where('id',1)->first();
        
        return view('admin.settings.information')->with(compact('title','settings'));
    	
    }

    public function updatSettings(Request $request){
        $settingId = $request->settingId;

        $setting = Settings::find($settingId);

        $this->validate(request(), [
            'siteLogo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'           
                    
        ]);

       if($request->siteLogo){
            @unlink($setting->siteLogo);
            $logo = \App\helperClass::_uploadImage($request->siteLogo);
            $setting->update( [
                'siteLogo' => $logo,          
            ]);
        }

        $setting->update( [
            'mobile1' => $request->mobile1,         
            'mobile2' => $request->mobile2,  
            'siteEmail1' => $request->siteEmail1,  
            'siteEmail2' => $request->siteEmail2,  
            'siteAddress1' => $request->siteAddress1,  
            'siteAddress2' => $request->siteAddress2,  
            'sitestatus' => $request->sitestatus,  
            'metaTitle' => $request->metaTitle,            
            'metaKeyword' => $request->metaKeyword,            
            'metaDescription' => $request->metaDescription,            
            'orderBy' => $request->orderBy,           
        ]);

       

        return redirect(route('site.info'))->with('msg','Information Updated Successfully');     
    }

    

    public function adminLogo()
    {
        $title = "Update Admin Panel Logo";
        $logos = Settings::where('id',1)->first();
        return view('admin.settings.adminLogo')->with(compact('title','logos'));
    }

    public function updatadminLogo(Request $request){
        $adminLogoId = $request->adminLogoId;

        $setting = Settings::find($adminLogoId);

        $this->validate(request(), [
            'adminLogo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'           
                    
        ]);

       if($request->adminLogo){
            @unlink($setting->adminLogo);
            $logo = \App\helperClass::_uploadImage($request->adminLogo);
            $setting->update( [
                'adminLogo' => $logo,          
            ]);
        }
        

        return redirect(route('admin.logo'))->with('msg','Logo Updated Successfully');     
    }

    
}
