<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\Challan;
use App\Traits\CodeGeneration;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class ChallanController extends Controller
{
    use CodeGeneration;use CommonTrait;

    public function index(Request $request){
        $ChallanNo  = $request->ChallanNo;
        $MasterCode = Auth::user()->UserId;

        $CurrentPage = $request->pagination['current_page'];
        $PerPage     = 20;
        $Export      = $request->Export;

        $DateFrom = $request->DateFrom;
        $DateTo   = $request->DateTo . ' 24:59:59';
        $DateTo = date('Y-m-d',strtotime($DateTo));

        $CustomerCode = $request->CustomerCode;

        if ($MasterCode == 'admin'){
            $CustomerCode = '';
        }else{
            $CustomerCode = $MasterCode;
        }

        if ($Export == 'Y'){
            $CurrentPage = '%';
        }

        $sql = "EXEC doLoadChallanList '$MasterCode','$CustomerCode','$DateFrom','$DateTo','$ChallanNo','$PerPage','$CurrentPage'";
        //return $sql;
        return $this->getReportData($sql, $PerPage, $CurrentPage, $Export);
    }

    public function storeChallan(Request $request){
        $this->validate($request,[
           'ChallanNumber' => 'required',
           'ChallanImage' => 'required',
        ]);

//        $image = $request->input('ChallanImage');
//        $imageExtension = $this->getBase64FileExtension($image);
//        $name    = $request->ChallanNumber.'.' . $imageExtension;
//        $image->move(public_path('assets/images/challan_image/'), $name);

        //FOR IMAGE
        if ($request->ChallanImage) {
            $image   = $request->ChallanImage;
            $name    = $request->ChallanNumber.'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            Image::make($image)->save(public_path('assets/images/challan_image/').$name);
        } else {
            $name = '';
        }

        $challan = new Challan();
        $challan->ChallanNumber = $request->ChallanNumber;
        $challan->ChallanImage  = $name;
        $challan->EntryBy       = Auth::user()->UserId;
        $challan->EntryDate     = Carbon::now();
        $challan->save();
        return response()->json([
           'status'  => 'success',
           'message' => 'Successfully Inserted'
        ]);
    }

    public function updateChallan(Request $request){
        $this->validate($request,[
           'ChallanNumber' => 'required',
           'ChallanImage' => 'required',
        ]);
        $challan = Challan::where('ChallanID',$request->ChallanID)->first();
        //FOR IMAGE
        $image = $request->ChallanImage;
        if ($image != $challan->ChallanImage) {
            if ($request->has('ChallanImage')) {
                $destinationPath = 'assets/images/challan_image/';
                $file_old = public_path() . $destinationPath . $challan->ChallanImage;
                if (file_exists($file_old)) {
                    unlink($file_old);
                }
                $name = uniqid() . time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                Image::make($image)->save(public_path('assets/images/challan_image/') . $name);
            } else {
                $name = $challan->ChallanImage;
            }
        } else {
            $name = $challan->ChallanImage;
        }

        $challan->ChallanNumber = $request->ChallanNumber;
        $challan->ChallanImage  = $name;
        $challan->EntryBy       = Auth::user()->UserId;
        $challan->EntryDate     = Carbon::now();
        $challan->save();
        return response()->json([
           'status'  => 'success',
           'message' => 'Successfully Updated'
        ]);
    }
}
