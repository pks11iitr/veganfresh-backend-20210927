<?php
namespace App\Models\Traits;

use App\Models\Document;
use App\Models\ProductImage;

trait DocumentUploadTrait {

    public function saveDocument($file, $urlprefix, $data=[]){
        $name = $file->getClientOriginalName();
        $contents = file_get_contents($file);
        $path = $urlprefix.'/' . $this->id . '/' . rand(111, 999) . '_' . str_replace(' ','_', $name);
        \Storage::put($path, $contents, 'public');
        $document=new Document(['file_path'=>$path]);
        $this->gallery()->save($document);
    }

    public function gallery(){
        return $this->morphMany('App\Models\Document', 'entity');
    }

    public function saveImage($file, $urlprefix){
        $name = $file->getClientOriginalName();
        $contents = file_get_contents($file);
        $path = $urlprefix.'/' . $this->id . '/' . rand(111, 999) . '_' . str_replace(' ','_', $name);
        \Storage::put($path, $contents, 'public');
        $this->image=$path;
        $this->save();
    }
    public function saveImage1($file, $urlprefix){
        $name = $file->getClientOriginalName();
        $contents = file_get_contents($file);
        $path = $urlprefix.'/' . $this->id . '/' . rand(111, 999) . '_' . str_replace(' ','_', $name);
        \Storage::put($path, $contents, 'public');
        $this->image1=$path;
        $this->save();
    }

    public function saveDocumentImage($file, $urlprefix, $data=[]){
        $name = $file->getClientOriginalName();
        $contents = file_get_contents($file);
        $path = $urlprefix.'/' . $this->id . '/' . rand(111, 999) . '_' . str_replace(' ','_', $name);
        \Storage::put($path, $contents, 'public');
        $document=new ProductImage(['image'=>$path]);
        $this->gallerysize()->save($document);
    }
    public function gallerysize(){
        return $this->morphMany('App\Models\ProductImage', 'entity');
    }
}
