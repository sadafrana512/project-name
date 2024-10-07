<?php
namespace App\Http\Controllers;
use ZipArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Record;
use Intervention\Image\Facades\Image;

class RecordController extends Controller
{
    public function basic(){      
        return view('basic_elements');

       }
       public function dashboard()
       {
           $record = Record::latest()->first(); // Fetch the latest record
           return view('dashboard', compact('record'));
       }
       public function record(Request $request)
       {
           $filenames = [];
           $formDetails = $request->only(['property_name', 'location', 'date', 'start', 'interval', 'type']);
       
           if ($request->hasFile('files')) {
               foreach ($request->file('files') as $file) {
                   $filename = time() . '_' . $file->getClientOriginalName();
                   $file->move(public_path('assets/images'), $filename);
                   $imagePath = public_path('assets/images/') . $filename;
                   $this->addTextToImage($imagePath, $formDetails);
                   $filenames[] = '/assets/images/' . $filename;
               }
           }
       
           $record = new Record();
           $record->fill($formDetails);
           $record->file = json_encode($filenames);
       
           if ($record->save()) {
               // Fetch only the newly inserted record
               $record = Record::latest()->first();
               return view('dashboard', compact('record'))->with('success', 'Record inserted successfully!');
           } else {
               return redirect()->back()->with('error', 'Failed to insert record.');
           }
       }
       
       private function addTextToImage($imagePath, $formDetails)
       {
           $image = Image::make($imagePath);
           
           // Example: Add Property Name to the image
           $image->text($formDetails['property_name'], 100, 100, function($font) {
               $font->file(public_path('public/fonts/arial.ttf'));
               $font->size(24);
               $font->color('#ffffff');
               $font->align('left');
               $font->valign('top');
           });
       
           // Add more text overlays as needed
       
           // Save the modified image
           $image->save($imagePath);
       }
 // downloadzipcontrollercode
 public function downloadZip(Request $request)
 {
     $recordId = $request->input('record_id');
 
     $record = Record::find($recordId);
 
     if (!$record) {
         return redirect()->back()->with('error', 'Record not found.');
     }
 
     $images = json_decode($record->file);
 
     // Create a new zip archive
     $zipFileName = 'images_' . time() . '.zip';
     $zipFilePath = public_path($zipFileName);
     $zip = new ZipArchive;
 
     if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
         // Add each image to the zip file
         foreach ($images as $image) {
             $imageName = basename($image);
             $zip->addFile(public_path($image), $imageName);
         }
 
         $zip->close();
 
         // Serve the zip file for download
         return response()->download($zipFilePath)->deleteFileAfterSend(true);
     } else {
         return redirect()->back()->with('error', 'Failed to create zip file.');
     }
}
}