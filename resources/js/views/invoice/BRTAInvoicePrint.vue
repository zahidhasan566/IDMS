<template>
    <div>
      <div class="container-fluid">
      <div class="head">
        <h3 style="color: black;text-align: center;font-size: 20px;margin: 0" >FORM OF APPLICATION FOR THE REGISTRATION OF MOTOR VEHICLE</h3>
        <p style="font-size: 18px;text-align: center;margin: 0">To be filled in by the office</p>
        <p style="font-size: 18px;text-align: center;margin: 0">Section-I</p>
      </div>

      <div style="display: flex;justify-content:space-between;" class="brta">
        <div style="width: 33%">
          <p style="margin: 0;font-size: 18px">Regn No : </p>
          <p style="margin: 0;font-size: 18px">Issue No : </p>
          <p style="margin: 0;font-size: 18px">Diary No : </p>
          <p style="margin: 0;font-size: 18px">Customer ID : </p>
          <p style="margin: 0;font-size: 18px">Veh. Description : </p>
          <p style="margin: 0;font-size: 18px">Refusal Date : </p>
          <p style="margin: 0;font-size: 18px">P.O./Bank : </p>
          <p style="margin: 0;font-size: 18px">Remarks (if any) : </p>
        </div>
        <div style="width: 33%">
          <p style="margin: 0;font-size: 18px">Date : </p>
          <p style="margin: 0;font-size: 18px">Date : </p>
          <p style="margin: 0;font-size: 18px">Date : </p>
          <p style="margin: 0;font-size: 18px">District : </p>
          <p style="margin: 0;font-size: 18px"> </p>
          <p style="margin: 0;font-size: 18px"> Refusal : </p>
          <p style="margin: 0;font-size: 18px"> </p>
          <p style="margin: 0;font-size: 18px"> </p>
        </div>
        <div style="width: 33%">
          <p style="margin: 0;font-size: 18px">Prev. Regn. No. (If : </p>
          <p style="margin: 0;font-size: 18px">Issue by : </p>
          <p style="margin: 0;font-size: 18px">Received by : </p>
          <p style="margin: 0;font-size: 18px">Vehicle ID : </p>
          <p style="margin: 0;font-size: 18px">Call non Date : </p>
          <p style="margin: 0;font-size: 18px">Refused by : </p>
          <p style="margin: 0;font-size: 18px">Index : </p>
          <p style="margin: 0;font-size: 18px">Index No. : </p>
        </div>
      </div>

        <div class="second">
          <h3 style="color: black;text-align: center;font-size: 18px;margin: 0" >To be filled in by the Owner</h3>
          <p style="font-size: 18px;text-align: center;margin: 0">Section II</p>
          <p style="font-size: 18px;text-align: center;margin: 0">(Owner information)</p>
        </div>

        <div class="row" style="display: flex;justify-content:start;margin: 0px">
          <p style="margin: 0;font-size: 18px">1. Name of owner : <span style="font-weight: bold">{{invoice.customername}}</span></p>
        </div>
        <div style="display: flex;justify-content:space-between;">
          <div style="width: 48%">
            <p style="margin: 0;font-size: 18px">2. Father/Husband : <span style="font-weight: bold">{{invoice.fathername}}</span></p>
            <p style="margin: 0;font-size: 18px">4. Sex : <span style="font-weight: bold;text-transform: uppercase">{{invoice.gender}}</span></p>
            <p style="margin: 0;font-size: 18px">6. Guardian's name : </p>
          </div>
          <div style="width: 48%">
            <p style="margin: 0;font-size: 18px">3. Date of birth : <span style="font-weight: bold">{{invoice.DateOfBirth}}</span></p>
            <p style="margin: 0;font-size: 18px">5. Nationality : <span style="font-weight: bold">BANGLADESHI</span></p>
          </div>
        </div>
        <div style="display: flex;justify-content:space-between;">
          <div style="width: 99%">
            <p style="margin: 0;font-size: 18px">7. Owner's Address (One only) : <span style="font-weight: bold">{{invoice.peraddress}}</span></p>
          </div>
        </div>
        <div style="display: flex;justify-content:space-between;">
          <div style="width: 48%">
            <p style="margin: 0;font-size: 18px">8. Phone No. (if any) : <span style="font-weight: bold">{{invoice.mobileno}}</span></p>
            <p style="margin: 0;font-size: 18px">10. Joint owner : </p>
            <p style="margin: 0;font-size: 18px">12. Hire: </p>
          </div>
          <div style="width: 48%">
            <p style="margin: 0;font-size: 18px">9. PO/Bank :  </p>
            <p style="margin: 0;font-size: 18px">11. Owner type :  <span style="font-weight: bold">{{invoice.ownertype}}</span></p>
            <p style="margin: 0;font-size: 18px"> 13. Hire purchase : </p>
          </div>
        </div>
        <div>
          <h3 style="color: black;text-align: center;font-size: 18px;margin: 0" >To be filled in by the Owner</h3>
          <p style="font-size: 18px;text-align: center;margin: 0">Section III</p>
          <p style="font-size: 18px;text-align: center;margin: 0">(Owner information)</p>
        </div>
        <div style="display: flex;justify-content:space-between;">
          <div style="width: 48%">
            <p style="margin: 0;font-size: 18px">14. Vehicle or trailer : VEHICLE</p>
            <p style="margin: 0;font-size: 18px">14a. Class of vehicle : <span style="font-weight: bold;text-transform: uppercase">{{invoice.classofvehicle}}</span></p>
            <p style="margin: 0;font-size: 18px">16. Type of body : <span style="font-weight: bold">{{invoice.BodyType}}</span></p>
            <p style="margin: 0;font-size: 18px">18. Color (cabin/body) : <span style="font-weight: bold">{{invoice.color}}</span></p>
            <p style="margin: 0;font-size: 18px">20. Number of cylinders : <span style="font-weight: bold">1</span></p>
            <p style="margin: 0;font-size: 18px">22. Engine number : <span style="font-weight: bold">{{invoice.engineno}}</span></p>
            <p style="margin: 0;font-size: 18px">24. Horse power: <span style="font-weight: bold">{{invoice.horsepower}}</span></p>
            <p style="margin: 0;font-size: 18px">26. Cubic capacity: <span style="font-weight: bold">{{invoice.cubiccapacity}}</span></p>
            <p style="margin: 0;font-size: 18px">28. No. of Standee: <span style="font-weight: bold">{{invoice.Standee}}</span></p>
            <p style="margin: 0;font-size: 18px">30. Unladen weight (kg): <span style="font-weight: bold">{{invoice.weight}}</span></p>
          </div>
          <div style="width: 48%">
            <p style="margin: 0;font-size: 18px">15. Prev. Regn. No. (if any): </p>
            <p style="margin: 0;font-size: 18px">15a. Maker's name : <span style="font-weight: bold"> IFAD MOTORS LIMITED</span></p>
            <p style="margin: 0;font-size: 18px">17. Maker's Country : <span style="font-weight: bold">{{invoice.ManufacturingCountry}}</span></p>
            <p style="margin: 0;font-size: 18px">19. Year of manufacture :  <span style="font-weight: bold">2023</span></p>
            <p style="margin: 0;font-size: 18px">21. Chassis number : <span style="font-weight: bold">{{invoice.chassisno}}</span></p>
            <p style="margin: 0;font-size: 18px">23. Fuel used : <span style="font-weight: bold">{{invoice.fuelused}}</span></p>
            <p style="margin: 0;font-size: 18px">25. RPM : <span style="font-weight: bold">{{invoice.rpm}}</span></p>
            <p style="margin: 0;font-size: 18px">27. Seats (incl. driver) : 2</p>
            <p style="margin: 0;font-size: 18px">29. Wheel base : <span style="font-weight: bold">{{invoice.wheelbase}}</span></p>
            <p style="margin: 0;font-size: 18px">31. Maximum laden/train weight (kg) : <span style="font-weight: bold">{{invoice.WeightMax}}</span></p>
          </div>
        </div>
        <div>
          <p style="font-size: 18px;text-align: center;margin: 0">Section IV</p>
          <p style="font-size: 18px;text-align: center;margin: 0">(Additional information for transport vehicle)</p>
        </div>
        <br>
        <div style="display: flex;justify-content:space-between;">
          <div style="width: 48%">
            <p style="font-size: 18px;margin: 0">32. No. of tyres : 2 </p>
            <br>
            <p style="font-size: 18px;margin: 0">34. No. of axle : </p>
          </div>
          <div style="width: 48%">
            <p style="font-size: 18px;margin: 0">33. Tyres size : <span style="font-weight: bold">Front : {{invoice.TireSizeFront}}</span>
              <br>
              <span style="margin-left: 85px;font-weight: bold;font-size: 18px">Rear : {{invoice.TireSizeRear}}</span>
            </p>
            <p style="font-size: 18px;margin: 0">35. Maximum axle weight (kg) <br>
              a) Front axle <span style="margin-left: 10px">(1)</span> <span style="margin-left: 20px">(2)</span> <br>
              b) Central axle (1) <span style="margin-left: 20px">(2)</span> <span style="margin-left: 20px">(3)</span> <br>
              b) Central axle <span style="margin-left: 2px">(1)</span> <span style="margin-left: 20px">(2)</span> <span style="margin-left: 20px">(3)</span>
            </p>
          </div>
        </div>
        <div style="display: flex;justify-content:space-between;">
          <div style="width: 100%">
            <p style="margin: 0;font-size: 18px">36. Dimensions (mm) : a) Overall length <span style="margin-left: 70px">b) Overall width</span>  <span style="margin-left: 70px">c) Overall height</span></p>
            <p style="margin: 0;font-size: 18px">37. Overhangs (%) <span style="margin-left: 20px">a) Front</span> <span style="margin-left: 115px">b) Rear</span> <span style="margin-left: 115px">c) Other</span></p>
          </div>
        </div>
        <br>
        <div>
          <p style=";font-size: 18px">38. A copy of the drawing showing the vehicle dimension specifications of the body and of the seating arrangements approved by
            ....................................................on...............................................................is attached herewith.</p>
        </div>

        <div>
          <p style="font-size: 18px;text-align: center;margin: 0">Section V</p>
        </div>
        <div style="display: flex;justify-content:space-between;">
          <div style="width: 48%">
            <p style="font-size: 18px;margin: 0">39. Hire purchase/hypothecation information :</p>
            <p style="font-size: 18px;margin: 0">a) Name : <br>
              Address :
            </p>
          </div>
          <div style="width: 48%">
            <br>
            <p style="font-size: 18px;margin: 0">b) Date : </p>
          </div>
        </div>
        <div style="display: flex;justify-content:space-between;">
          <div style="width: 33%">
            <p style="font-size: 18px;margin: 0">40. Insurance information :</p>
            <p style="font-size: 18px;margin: 0">a) Policy no : <br>
              d) Date of expiry :
            </p>
          </div>
          <div style="width: 33%">
            <br>
            <p style="font-size: 18px;margin: 0">b) Type of policy : </p>
          </div>
          <div style="width: 33%">
            <br>
            <p style="font-size: 18px;margin: 0">c) Insurer's name & address : </p>
          </div>
        </div>
        <div style="display: flex;justify-content:space-between;">
          <div style="width: 48%">
            <p style="font-size: 18px;margin: 0">41. Joint owner information :</p>
            <p style="font-size: 18px;margin: 0">a) Name : <br>
              Father/Husband :
            </p>
          </div>
          <div style="width: 48%">
            <br>
            <p style="font-size: 18px;margin: 0">b) Name : <br>
              Father/Husband :
            </p>
          </div>
        </div>
        <div>
          <p style="font-size: 18px;margin: 0">42. Declaration by owner </p>
          <p style="margin-left: 50px;font-size: 18px;margin-bottom: 0">a.
            I the undersigned do hereby declare that to the best of my knowledge and belief, the information given and the
            documents enclosed (as per list attached) are true. I also declare that in case the papers/documents and information
            furnished are found to be incorrect at any later stage, I shall be liable for legal action.
          </p>
        </div>

        <div style="display: flex;justify-content:space-between;">
          <div style="width: 48%">
            <p style="font-size: 18px;margin: 0">Date : </p>
            <p style="font-size: 18px;margin: 0">Encl : List of documents  </p>
          </div>
          <div style="width: 48%">
            <p style="text-align: center;font-size: 18px;margin: 0">
              <br>
              <br>
              Signature of owner <br>
              Seal
            </p>
          </div>
        </div>

        <div>
          <p style="font-size: 18px;margin: 0">43. Registered dealer's certificate :</p>
          <p style="margin-left: 50px;font-size: 18px;margin-bottom: 0">a.
            I the undersigned do hereby certify that the vehicle in question has been sold by me/my firm and the ownership documents
            attached with the application for registration are true. The information/specifications pertaining to the vehicle are correct
            and the vehicle complies with all the requirements of the registration
          </p>
        </div>
        <div style="display: flex;justify-content:space-between;">
          <div style="width: 48%">
            <p style="font-size: 18px;margin: 0">Date : </p>
            <p style="font-size: 18px;margin: 0">Encl : List of documents  </p>
          </div>
          <div style="width: 48%">
            <p style="text-align: center;font-size: 18px;margin: 0">
              <br>
              <br>
              Signature of registered dealer<br>
              Seal
            </p>
          </div>
        </div>

        <div>
          <p style="font-size: 18px;margin: 0">44. certificate by the Inspector of Motor Vehicles :</p>
          <p style="margin-left: 50px;font-size: 18px;margin-bottom: 0">a.
            Certificate that the particulars pertaining to the owner and the vehicle (Chassis No. : <span style="font-weight: bold">{{invoice.chassisno}}</span> Engine No. :
            <span style="font-weight: bold">{{invoice.engineno}}</span>) given in the application match with the ownership documents attached to this application. It is further
            certified that the vehicle complies with the registration requirements specified in the MV Act and the Rules and/or
            Regulations made thereunder and the vehicle is not mechanically defective. The necessary documents/papers are
            available as per list enclosed.
          </p>
        </div>

        <div style="display: flex;justify-content:space-between;">
          <div style="width: 48%">
            <p style="font-size: 18px;margin: 0">Date : </p>
            <p style="font-size: 18px;margin: 0">Encl : List of documents  </p>
          </div>
          <div style="width: 48%">
            <p style="text-align: center;font-size: 18px;margin: 0">
              <br>
              Signature of Inspector of Motor Vehicles <br>
              Official Seal
            </p>
          </div>
        </div>

        <div style="display: flex;justify-content:space-between;">
          <div style="width: 48%">
            <p style="font-size: 18px;margin: 0">45. Registration Status </p>
            <p style="font-size: 18px;margin: 0">Registration allowed/not allowed : </p>
          </div>
          <div style="width: 48%">
            <p style="text-align: center;margin: 0;font-size: 18px">
              <br>
              <br>
              Signature of Registering Authority <br>
              Seal
            </p>
          </div>
        </div>

        <div>
          <p style="font-size: 18px;margin: 0">46. Fees and Tax Accounts :</p>
          <p style="margin-left: 50px;font-size: 18px;margin-bottom: 0">a.
            Necessary fees and taxes amounting to taka.............................................................................................has been paid to
            PO/Bank...................................................................vide vouchers and receipts enclosed
          </p>
        </div>
        <div style="display: flex;justify-content:space-between;margin-top: 30px;">
          <div style="width: 48%">
            <br>
            <p style="font-size: 18px;margin: 0">Signature of owner
              of his representative
            </p>
          </div>
          <div style="width: 48%">
            <br>
            <p style="text-align: center;font-size: 18px;margin: 0">Signature of dealing assistant</p>
          </div>
        </div>

        <p style="text-align: center;font-size: 18px;margin-top: 10px">Counter signature by the registering authority</p>

        <div style="page-break-before:always">&nbsp;</div>
        <br>
        <br>
        <br>
        <br>
        <p style="text-align: center;font-weight: bold;font-size: 22px">OWNER'S PARTICULARS/SPECIMEN SIGNATURE</p>
        <br>
        <div style="display: flex;justify-content:space-between;">
          <div style="width: 48%">
            <p style="font-size: 20px">1. NAME : {{invoice.customername}}</p>
            <p style="font-size: 20px">2. FATHER/HUSBAND : {{invoice.fathername}}</p>
            <p style="font-size: 20px">3. ADDRESS : {{invoice.preaddress}}</p>
            <p style="font-size: 20px">4. SEX : <span style="text-transform: uppercase;">{{invoice.gender}}</span></p>
            <p style="font-size: 20px">5. PHONE NO : {{invoice.mobileno}}</p>
            <p style="font-size: 20px">6. NATIONALITY : BANGLADESHI</p>
            <p style="font-size: 20px">7. DATE OF BIRTH : {{invoice.DateOfBirth}}</p>
            <p style="font-size: 20px">8. GUARDIAN'S NAME : </p>
            <p style="font-size: 20px">9. CHASSIS NO. : <span style="font-weight: bold">{{invoice.chassisno}}</span></p>
            <p style="font-size: 20px">10. ENGINE NO. : <span style="font-weight: bold">{{invoice.engineno}}</span></p>
            <p style="font-size: 20px">11. YEAR OF MFG : {{invoice.importyear}}</p>
            <p style="font-size: 20px">12. REGN. NO. (IF ANY) : </p>
            <p style="font-size: 20px">13. P.O./BANK : </p>
          </div>
          <div style="width: 48%" class="owner">
            <p style="text-align: center;
            border: 1px solid;
            width: 134px;
            margin: 0 auto;
            height: 150px;">Passport Size Photo 4 Copy</p>
          </div>
        </div>
        <br>
        <br>
        <br>
        <p style="font-size: 18px">SPECIMEN SIGNATURE</p>
        <br>
        <br>
        <div style="display: flex;justify-content:space-between;">
          <div style="width: 48%" class="sign">
            <p style="text-align: center;border: 1px solid;
            padding: 21px;
            width: 300px;
            margin: 0 auto;"></p>
          </div>
          <div style="width: 48%" class="sign">
            <p style="text-align: center;border: 1px solid;
            padding: 21px;
            width: 300px;
            margin: 0 auto;"></p>
          </div>
        </div>
        <br>
        <br>
        <br>
        <br>
        <div style="display: flex;justify-content:space-between;">
          <div style="width: 48%" class="sign">
            <p style="text-align: center;border: 1px solid;
            padding: 21px;
            width: 300px;
            margin: 0 auto;"></p>
          </div>
          <div style="width: 48%" class="sign">
            <p style="text-align: center;border: 1px solid;
            padding: 21px;
            width: 300px;
            margin: 0 auto;"></p>
          </div>
        </div>
    </div>
    </div>
</template>

<script>
import {baseurl} from '../../base_url'
import moment from "moment";
import {Common} from "../../mixins/common";
export default {
  name: "List",
  mixins: [Common],
  data() {
    return {
      invoice: {}
    }
  },
  created() {
    //this.getSingleInvoice()
  },

  mounted() {
    document.title = 'BRTA Invoice Print | DMS';
    this.getSingleInvoice()
  },
  methods: {
    getSingleInvoice(){
      axios.get(baseurl + `api/get-single-invoice/${this.$route.params.InvoiceId}`,this.config()).then((response)=>{
        console.log(response.data)
        this.invoice = response.data.invoice
        setTimeout(function(){
          window.print()
        },2000)
      });
    },
    config() {
      let token = localStorage.getItem('token');
      return {
        headers: {Authorization: `Bearer ${token}`}
      };
    },
  },
}
</script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<style scoped>
.left_box{
  padding: 10px;
  margin-bottom: 10px;
  border: 1px solid;
  border-radius: 8px;
}
.left_box1{
  border: 1px solid;
  border-radius: 0px;
}
.brta p{
  font-size: 20px!important;
}
p{
  margin: 0px;
  font-size: 20px!important;
  font-weight: bold!important;
}
.owner p{
  text-align: center;
  border: 1px solid;
  width: 134px;
  margin: 0 auto;
  height: 150px;
}
.sign p {
  border: 1px solid;
  padding: 21px;
  width: 226px;
  margin: 0 auto;
}
</style>