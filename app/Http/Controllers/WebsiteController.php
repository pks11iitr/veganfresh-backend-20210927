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


    public function privacy(){
        return view('Website.privacy');
    }

    public function cookies(){
        return view('Website.cookies');
    }

    public function covied(){
        return view('Website.covied');
    }

    public function carrers(){
        return view('Website.carrers');
    }

    public function fssai(){
        return view('Website.fssai');
    }




}
