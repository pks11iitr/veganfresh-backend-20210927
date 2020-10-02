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
}
