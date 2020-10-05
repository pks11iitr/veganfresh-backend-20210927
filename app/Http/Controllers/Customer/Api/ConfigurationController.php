<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Configuration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConfigurationController extends Controller
{
    public function contact(Request $request){

        $contact=Configuration::whereIn('param', ['whatsapp_contact', 'office_address', 'contact_number'])->select('param', 'value')->get();
        return [
            'status'=>'success',
            'data'=>compact('contact')
        ];

    }

    public function complaintcategory(Request $request){

        $categories=Configuration::whereIn('param', ['complaints_categories'])
            ->select('param', 'value')->first();

        $list=[];
        if($categories->value??null){
            $categories=explode('**', $categories->value);
            foreach($categories as $cat)
                $list[]=['name'=>$cat];
        }



        return [
            'status'=>'success',
            'data'=>compact('list')
        ];

    }
}