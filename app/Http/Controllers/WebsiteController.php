<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function index(){
        return view('website.index');
    }





    public function sendmail(){
        return "ddd";
    }


    public function aboutus(){
       
        return view('Website.about');
    }   

    public function contactus(){

        return view('Website.contact');
    }




}
