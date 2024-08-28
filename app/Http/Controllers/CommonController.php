<?php

namespace App\Http\Controllers;

use App\Models\Banks;
use App\Models\BikeCC;
use App\Models\CauseForBuyingNewBike;
use App\Models\CompititorBrand;
use App\Models\CustomerMapping;
use App\Models\District;
use App\Models\InterestInProduct;
use App\Models\Menu;

use App\Models\MonthlyIncome;
use App\Models\Occupation;
use App\Models\OldBikeBrand;
use App\Models\OldBikeModel;
use App\Models\PreviousBikeUsage;
use App\Models\PreviouslyUsedBike;
use App\Models\ProductIntroducingMedia;
use App\Models\tblDealarMechanics;
use App\Models\Upazilla;

use App\Models\Product;
use App\Services\BusinessService;
use App\Services\DepartmentService;
use App\Services\RoleService;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Table;
use PhpParser\Node\Stmt\DeclareDeclare;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class CommonController extends Controller
{
    use CommonTrait;
    public function userModalData() {
        return response()->json([
            'status' => 'success',
            'roles' => RoleService::list(),
            'business' => BusinessService::list(),
            'department' => DepartmentService::list(),
            'allSubMenus' => Menu::whereNotIn('MenuID',['Dashboard','Users'])->with('allSubMenus')->orderBy('MenuOrder','asc')->get()
        ]);
    }
    public function bikeList(){
        $products= Product::select(DB::raw("Product.ProductCode + ' - ' + Product.ProductName as BikeName"),'ProductCode','ProductName')
            ->where('Business','C')
            ->where('Active','Y')
            ->where('SMSOrder','Y')
            ->where('MRP','>','0')
            ->where('UnitPrice','>','10')
          ->orderBy('ProductCode','desc')->get();

        return  $products;

    }
    public function searchProduct(Request $request){
        $ProductCode = $request->ProductCode;
        $products= Product::select(DB::raw("Product.ProductCode + ' - ' + Product.ProductName as BikeName"),'ProductCode','ProductName')
            ->where('Business','C')
            ->where('Active','Y')
            ->where('MRP','>','0')
            ->where('UnitPrice','>','10')
            ->where(function ($q) use ($ProductCode){
                $q->where('ProductName', 'LIKE', $ProductCode . '%');
                $q->orWhere('ProductCode', 'LIKE', $ProductCode . '%');
            })->orderBy('ProductCode','desc')->get();
        return  $products;

    }
    public function getBikeByProductCode(Request $request){

        $ProductCode =$request->ProductCode;
        $product = Product::select('ProductCode','ProductName','Vat','UnitPrice')
            ->where('ProductCode',$ProductCode)->first();
        return  $product;
    }
    public function searchParts(Request $request){

        $ProductCode = $request->ProductCode;
        $userId =Auth::user()->UserId;

        $products= DB::table('Product as p')->select(
            DB::raw("p.ProductCode + ' - ' + p.ProductName as PartName"),
            'p.ProductCode','p.ProductName')
//            ->leftjoin('DealarStock as d', function ($join) use($userId){
//              $join->on('d.ProductCode','=','p.ProductCode')
//              ->where('d.MasterCode','=',$userId);
//              //->where('d.MasterCode', $userId) ;
//            })
            ->where('p.Business','P')
            ->where('p.Active','Y')
            ->where('p.MRP','>','0')
            ->where('p.UnitPrice','>','10')
            ->where('p.SMSOrder','Y')
            ->where(function ($q) use ($ProductCode){
                $q->where('p.ProductName', 'LIKE', $ProductCode . '%');
                $q->orWhere('p.ProductCode', 'LIKE', $ProductCode . '%');
                $q->orWhere('p.PartNo', 'LIKE', $ProductCode . '%');
            })->orderBy('p.ProductCode','ASC')->get();
        return  $products;

    }
    public function getPartsByProductCode(Request $request){
        $userId =Auth::user()->UserId;
        $ProductCode =$request->ProductCode;
        $product = Product::select('Product.ProductCode','Product.ProductName','Product.PartNo','Product.Vat','Product.UnitPrice',
            DB::raw("ISNUll(d.CurrentStock,0) as CurrentStock"))
            ->leftjoin('DealarStock as d', function ($join) use($userId){
              $join->on('d.ProductCode','=','Product.ProductCode')
              ->where('d.MasterCode','=',$userId);
              //->where('d.MasterCode', $userId) ;
            })
            ->where('Product.ProductCode',$ProductCode)->first();
        return  $product;
    }
    public function encode($param) {

    }

    public function decode($param) {

    }

    public function checkChassis(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $chassis = $request->ChassisNo;

        $chassis_info = DB::table('DealarReceiveInvoiceDetails as D')
            ->select("D.ReceiveDetailsId", "D.ReceiveID", "D.ProductCode", "D.ReceivedQnty", "D.SoldQnty",
                DB::raw("CAST(P.MRP AS DECIMAL(18, 2)) AS UnitPrice"),
                "D.Vat","D.ChassisNo", "D.EngineNo", "D.Color", "D.FuelUsed", "D.HorsePower", "D.RPM", "D.CubicCapacity","D.WheelBase",
                "D.Weight", "D.TireSizeFront","D.TireSizeRear","D.Seats","D.NoofTyre","D.NoofAxel","D.ClassOfVehicle","D.MakerName","D.MakerCountry",
                "D.EngineType","D.NumberofCylinders","D.ImportYear", "P.ProductName")
            ->join('Product as P', 'D.ProductCode','=','P.ProductCode')
            ->join('DealarReceiveInvoiceMaster as M', 'D.ReceiveID','=','M.ReceiveID')
            ->where('D.ChassisNo',$chassis)
            ->where('D.SoldQnty',0)
            ->where('M.MasterCode',$user->UserId)
            ->first();

        if ($chassis_info){
            return response()->json([
                'status'=>'success',
                'message'=>'Successfully Match',
                'chassis_info' => $chassis_info
            ]);
        }else{
            return response()->json([
                'status'=>'error',
                'message'=>'No Match',
                'chassis_info' => []
            ]);
        }
    }

    public function filterChassis(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $chassis = $request->ChassisNo;

        $chassis_info = DB::select("SELECT rid.chassisno, p.productcode, p.productname, p.packsize, 
                          CAST(p.mrp AS NUMERIC(18, 2)) AS unitprice, rid.engineno, rid.color 
                        FROM DealarReceiveInvoiceMaster rim 
                            INNER JOIN DealarReceiveInvoiceDetails rid
                                ON rim.ReceiveId = rid.ReceiveId
                          INNER JOIN Product p
                            ON rid.ProductCode = p.ProductCode
                        WHERE rim.MasterCode = '$user->UserId' 
                            AND rid.Chassisno LIKE '$chassis'+'%'
                            AND rid.SOldQnty = 0
                            AND P.Business = 'C'");

        if ($chassis_info){
            return response()->json([
                'status'=>'success',
                'chassis_info' => $chassis_info
            ]);
        }else{
            return response()->json([
                'status'=>'error',
                'chassis_info' => []
            ]);
        }
    }
    public function getAllBank(){
        $bank =Banks::where('DepotCode','=','H')->get();
        return response()->json([
            'data' => $bank
        ]);
    }
    public function getcustomerWiseBusiness(){
        $user = Auth::user()->UserId;
        $business =CustomerMapping::select('CustomerMapping.Business','b.BusinessName')->join('Business as b','CustomerMapping.Business','b.Business')
            ->where('CustomerMapping.CustomerMasterCode',$user)->get();
        return response()->json([
            'data' => $business
        ]);
    }
    public function getAllDistrict(){
        $districts = District::query()->select('DistrictCode','DistrictName')->get();
        return response()->json([
            'districts' => $districts
        ]);
    }

    public function getAllOccupation(){
        $occupations = Occupation::query()->select('OccupationId','OccupationName','Active')->where('Active',1)->get();
        return response()->json([
            'occupations' => $occupations
        ]);
    }

    public function districtWiseThana(Request $request){
        $districtCode = $request->DistrictCode;
        $thanas = Upazilla::query()->select('DistrictCode','UpazillaCode','UpazillaName')->where('DistrictCode',$districtCode)->get();
        return response()->json([
            'thanas' => $thanas
        ]);
    }

    public function getAllMonthlyIncome(Request $request){
        $monthly_income = MonthlyIncome::query()->get();
        return response()->json([
            'monthly_income' => $monthly_income
        ]);
    }

    public function productIntroducingMedias(){
        $ProductIntroducingMedia = ProductIntroducingMedia::query()->get();
        return response()->json([
            'productIntroducingMedias' => $ProductIntroducingMedia
        ]);
    }

    public function getAllInterestInProduct(){
        $interestInProduct = InterestInProduct::query()->get();
        return response()->json([
            'interestInProduct' => $interestInProduct
        ]);
    }

    public function previouslyUsedBike(){
        $previouslyUsedBike = PreviouslyUsedBike::query()->get();
        return response()->json([
            'previouslyUsedBike' => $previouslyUsedBike
        ]);
    }

    public function previousBikeCC(){
        $previousBikeCC = BikeCC::query()->get();
        return response()->json([
            'previousBikeCC' => $previousBikeCC
        ]);
    }

    public function previousBikeUsage(){
        $previousBikeUsage = PreviousBikeUsage::query()->get();
        return response()->json([
            'previousBikeUsage' => $previousBikeUsage
        ]);
    }

    public function causeForBuyingNewBike(){
        $causeForBuyingNewBike = CauseForBuyingNewBike::query()->get();
        return response()->json([
            'causeForBuyingNewBike' => $causeForBuyingNewBike
        ]);
    }

    public function getAllEmiInstallment(){
        $emi_installment = DB::select("exec usp_LoadEMIInstallment");
        return response()->json([
            'emi_installment' => $emi_installment
        ]);
    }

    public function getAllEmiBank(){
        $emi_bank = DB::select("exec usp_LoadEMIBank");
        return response()->json([
            'emi_bank' => $emi_bank
        ]);
    }

    public function getAllExchangeBrand(){
        $exchange_brand = CompititorBrand::query()->where('Active','Y')->get();
        return response()->json([
            'exchange_brand' => $exchange_brand
        ]);
    }

    public function getAllMechanics(){
        $user = JWTAuth::parseToken()->authenticate();
        $mechanics = DB::select("SELECT 
                    M.ServiceCenterCode AS Dealar,
                    D.DistrictCode,
                    D.DistrictName,
                    U.UpazillaCode,
                    U.UpazillaName,
                    MechanicsCode,
                    MechanicsName,
                    MechanicsPhone AS Mobile,
                    LEFT(JoiningDate,11) AS Joining_Date,
                    Address,
                    EducationQualification AS Educational_Qualification,
                    MechanicsShopName,
                    CASE WHEN Active = 'Y' THEN 'Active' ELSE 'Inactive' END Active_Status,
                    Active
                FROM tblDealarMechanics M
                    INNER JOIN District D
                        ON M.DistrictCode = D.DistrictCode
                    LEFT JOIN Upazilla U 
                        ON U.UpazillaCode = M.UpazillaCode
                WHERE ServiceCenterCode = '$user->UserId' ");
        return response()->json([
            'mechanics' => $mechanics
        ]);
    }

    public function getAllTenderType(){
        $sql = "SELECT 
                    EMIBankID TenderId, EMIBankName TenderType,0 as CardAmount,
                    SwipeCharge 
                 FROM EMIBank 
                 WHERE SwipeCharge IS NOT NULL
                 ORDER BY 1";
        $tender_type = DB::select($sql);
        return response()->json([
            'tender_type' => $tender_type
        ]);
    }

    public function getAllCustomer(){
        $userID = Auth::user()->UserId;
        $sql = "SELECT *
                FROM Customer C
                INNER JOIN UserCustomer UC
                ON C.CustomerCode = UC.CustomerCode AND UC.UserType = 'SE'
                AND UserId = '$userID'
                WHERE CustomerType IN ('E','D','R') AND LEFT(C.CustomerCode,2) = 'HC'";
        $customers = DB::select($sql);
        return response()->json([
            'customers' => $customers
        ]);
    }

    public function getAllRegion(){
        $userID = Auth::user()->UserId;
        $sql = "SELECT 
                DISTINCT RegionName 
                FROM Customer C
                INNER JOIN UserCustomer UC
                ON C.CustomerCode = UC.CustomerCode AND UC.UserType = 'SE'
                AND UserId = '$userID'
                WHERE CustomerType IN ('E','D','R') AND LEFT(C.CustomerCode,2) = 'HC'";
        $regions = DB::select($sql);
        return response()->json([
            'regions' => $regions
        ]);
    }

    public function getAllOldBikeBrand(){
        $brands = OldBikeBrand::query()->get();
        return response()->json([
            'brands' => $brands
        ]);
    }

    public function getAllOldBikeModel(Request $request){
        $models = OldBikeModel::query()->where('BrandName',$request->BrandName)->get();
        return response()->json([
            'models' => $models
        ]);
    }

    public function getUserAccess(){
        $userAccess = $this->userWiseAcess();
        return $userAccess;
    }
}
