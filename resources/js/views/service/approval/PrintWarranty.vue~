<template>
    <div>
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-12">
            <div class="table-responsive">
              <table>
                <thead>
                  <tr>
                    <th style="text-align: center"><img :src="yamahaLogo()"></th>
                    <th style="text-align: center"><h2>WARRANTY CLAIM REPORT</h2></th>
                    <th style="text-align: center;"><img :src="aciLogo()"></th>
                  </tr>
                </thead>
              </table>
              </div>

            <table>
              <tbody>
              <tr>
                <td colspan="6" style="background-color: #CCC; color: black; font-weight: bold;text-align: center;">DEALER INFORMATION</td>
              </tr>
              <tr>
                <td class="bold" style="width: 16%">Dealer Name </td>
                <td style="width: 16%">{{ details.DealarName }}</td>
                <td class="bold" style="width: 16%">Dealer Code</td>
                <td style="width: 8%">{{ details.DealarCode }}</td>
                <td class="bold" style="width: 16%">Location</td>
                <td style="width: 24%">{{ details.Add1 }}</td>
              </tr>
              <tr>
                <td colspan="6" style="background-color: #CCC; color: black; font-weight: bold;text-align: center;">CUSTOMER INFORMATION</td>
              </tr>
              <tr>
                <td class="bold">Sold Date</td>
                <td>{{ details.SoldDate }}</td>
                <td class="bold">Occurance Date</td>
                <td>{{ details.OccuranceDate }}</td>
                <td class="bold">Claim Date</td>
                <td>{{ details.ClamDate }}</td>
              </tr>
              <tr>
                <td class="bold">Type of Warranty</td>
                <td>{{ details.WarrantyTypeName }}</td>
                <td class="bold">Seriousness</td>
                <td>{{ details.WarrantySeriousnessName }}</td>
                <td class="bold">Source of Information</td>
                <td>{{ details.WarrantySourceName }}</td>

              </tr>
              <tr>
                <td class="bold">Customer Name</td>
                <td>{{ details.CustomerName }}</td>
                <td class="bold">Mobile No</td>
                <td>{{ details.MobileNo }}</td>
                <td class="bold">Address</td>
                <td>{{ details.PreAddress }}</td>

              </tr>

              <tr>
                <td class="bold">Model</td>
                <td>{{ details.ProductName }}</td>
                <td class="bold">Color</td>
                <td>{{ details.Color }}</td>
                <td class="bold">Mileage</td>
                <td>{{ details.Mileage }}</td>

              </tr>

              <tr>
                <td class="bold">Technician Name</td>
                <td>{{ details.TechnicianName }}</td>
                <td class="bold">Chassis No</td>
                <td>{{ details.ChassisNo }}</td>
                <td class="bold">Free Service Schedule</td>
                <td>{{ details.FreeServiceSchedule }}</td>
              </tr>

              <tr>
                <td colspan="2" class="bold">Job Card No</td>
                <td colspan="4">{{ details.JobCardNo }}</td>
              </tr>

              <tr>
                <td colspan="2" class="bold">Problem Name/ Nature of Defect</td>
                <td colspan="4">{{ details.ProblemDetails }}</td>
              </tr>

              <tr>
                <td colspan="6" style="background-color: #CCC; color: black; font-weight: bold;text-align: center;">RIDER INFORMATION</td>
              </tr>

              <tr>
                <td class="bold">Sex</td>
                <td>{{ details.RiderSex }}</td>
                <td class="bold">Age</td>
                <td>{{ details.RiderAge }}</td>
                <td class="bold">Weight</td>
                <td>{{ details.RiderWeight }}</td>
              </tr>
              <tr>
                <td class="bold">Riding Style</td>
                <td>{{ details.RidingStyle }}</td>
                <td class="bold">Road Condition</td>
                <td >{{ details.RoadCondition }}</td>
                <td class="bold">Rider Profession</td>
                <td >{{ details.OccupationName }}</td>
              </tr>

              <tr>
                <td colspan="6" style="background-color: #CCC; color: black; font-weight: bold;text-align: center;">Problem Details</td>
              </tr>

              <tr>
                <td class="bold">Customer Comments</td>
                <td>{{ details.WarrantyProblemName }}</td>
                <td class="bold">Customer Comments</td>
                <td>{{ details.WarrantyRemedyName }}</td>
                <td class="bold">Customer Comments</td>
                <td>{{ details.WarrantyProblemResultName }}</td>
              </tr>

              <tr>
                <td class="bold">Customer Comments</td>
                <td colspan="5">{{ details.CustomerComment }}</td>
              </tr>
              <tr>
                <td class="bold">Diagnosis & Failure Analysis</td>
                <td colspan="5">{{ details.DiagnosisFailureAnalysis }}</td>
              </tr>
              <tr>
                <td class="bold">Remedy & Result</td>
                <td colspan="5">{{ details.RemedyResult }}</td>
              </tr>
              <tr>
                <td class="bold">Suspected Cause of Failure</td>
                <td colspan="5">{{ details.SuspectedCauseOfFailure }}</td>
              </tr>
              <tr>
                <td class="bold">Additional Comments</td>
                <td colspan="5">{{ details.AdditionalComments }}</td>
              </tr>

              <tr>
                <td colspan="6" style="background-color: #CCC; color: black; font-weight: bold; text-align: center;">Photos of Failure Parts / Documents</td>
              </tr>
              <tr id="img-warrantyclaim">
                <td colspan="2" style="height: 150px; overflow: hidden;" v-for="(image, i) in imagesData" :key="image.DCWCDetailID" v-if="imagesData.length">
                  <img :src="imageLoad(image.ProImagePath)" style="width: 100%; height: 150px;" title="Click for Large Image" class="imageUrlPopupButton" data-imageName="">
                </td>
              </tr>
              </tbody>
            </table>

            <table>
              <tr>
                <td class="bold">Sl No.</td>
                <td class="bold">Invoice Type</td>
                <td class="bold">Part Name</td>
                <td class="bold">Part No</td>
                <td class="bold">Quantity</td>
                <td class="bold">Unit Price</td>
                <td class="bold">Total Price</td>
                <td class="bold">Service Charge</td>
              </tr>

              <tr v-for="(part, i) in partsData" :key="part.PartNo" v-if="partsData.length">
                <td>{{ ++i }}</td>
                <td>{{part.WarrantyInvoiceName}}</td>
                <td>{{part.ProductNameDetails}}</td>
                <td>{{part.PartNo}}</td>
                <td style="text-align: right;" id="quantity">{{part.Quantity}}</td>
                <td style="text-align: right;" id="unitprice">{{part.UnitPrice}}</td>
                <td style="text-align: right;" id="totalunitprice">{{ part.UnitPrice * part.Quantity }}</td>
                <td style="text-align: right;" id="servicecharge">{{part.ServiceCharge}}</td>
              </tr>

              <tr>
                <td colspan="6" style="text-align: right;">Total</td>
                <td style="text-align: right;" id="totalprice">{{ totalPrice(partsData) }}</td>
                <td style="text-align: right;" id="totalservicecharge">{{ totalService(partsData) }}</td>
              </tr>

              <tr>
                <td colspan="6" style="text-align: right;">Grand Total</td>
                <td colspan="2" style="text-align: right;" id="grandtotal">{{ grandTotal(partsData) }}</td>
              </tr>
            </table>

            <table>
              <tbody>
              <tr>
                <td class="bold" style="width: 16%">Name</td>
                <td style="width: 16%">{{ details.UserName }}</td>
                <td class="bold" style="width: 16%">Name</td>
                <td style="width: 16%"></td>
              </tr>
              <tr>
                <td class="bold" style="width: 16%">Designation</td>
                <td style="width: 16%">{{ details.Designation }}</td>
                <td class="bold" style="width: 16%">Designation</td>
                <td style="width: 16%"></td>
              </tr>
              <tr>
                <td class="bold" style="width: 16%">Signature</td>
                <td style="width: 16%"></td>
                <td class="bold" style="width: 16%">Signature</td>
                <td style="width: 16%"></td>
              </tr>
              <tr>
                <td colspan="2" style="width: 16%; height: 100px;" v-if="details.Status && details.Status === 1">
                  <img :src="loadSignature(details.UserId)" height="100px;">
                </td>
                <td colspan="2" style="width: 16%; height: 100px;"></td>
              </tr>
              <tr>
                <td class="bold" style="width: 16%">Date</td>
                <td style="width: 16%">{{ details.ApproveDate }}</td>
                <td class="bold" style="width: 16%">Date</td>
                <td style="width: 16%"></td>
              </tr>
              <tr>
                <td class="bold" colspan="2" style="width: 16%;">Service Engineer </td>
                <td class="bold" colspan="2" style="width: 16%;">Dealer Authorised Personnel</td>
              </tr>
              </tbody>
            </table>

            <!-- service history -->
            <div style="page-break-after: always;display: block;"></div>
            <h4 style="text-align: center;">Service History</h4>
            <table id="histTable">
              <thead class="thead-dark">
              <tr style="color: black">
                <th class='text-center' style="color: black;">Service Dealer</th>
                <th class='text-center' style="color: black;">Service Date</th>
                <th class='text-center' style="color: black;">Job Card Number</th>
                <th class='text-center' style="color: black;">Job Type</th>
                <th class='text-center' style="color: black;">Schedule Title</th>
                <th class='text-center' style="color: black;">Mileage</th>
              </tr>
              </thead>
              <tbody>
                <tr v-for="(history, i) in histories" :key="history.Id" v-if="histories.length">
                  <td class='text-center'>{{history.CustomerName}}</td>
                  <td class='text-center'>{{history.JobDate}}</td>
                  <td class='text-center'>{{history.JobCardNo}}</td>
                  <td class='text-center'>{{history.JobTypeName}}</td>
                  <td class='text-center'>{{history.ScheduleTitle}}</td>
                  <td class='text-center'>{{history.Mileage}}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
import {baseurl} from '../../../base_url'
import {Common} from "../../../mixins/common";
import {bus} from "../../../app";
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import moment from "moment";

export default {
  name: "Invoice",
  mixins: [Common],
  components: { DatePicker },
  data() {
    return {
      details: {},
      partsData: [],
      histories: [],
      imagesData: [],
    }
  },
  created() {
    axios.get(baseurl + `api/warranty-print/${this.$route.params.DCWarrantyId}`,this.config()).then((response)=>{
      this.details = response.data.detailsData;
      this.partsData = response.data.partsData;
      this.histories = response.data.histories[0];
      this.imagesData = response.data.imagesData;
      setTimeout(function(){
        window.print()
      },2000)
    });
  },
  mounted() {
    document.title = 'Print Warranty | DMS';
  },
  methods: {
    totalPrice(values){
      return values.reduce((acc, val) => {
        return acc + parseFloat(((val.UnitPrice * val.Quantity) * val.ServiceCharge));
      }, 0);
    },
    grandTotal(values){
      return values.reduce((acc, val) => {
        return acc + parseFloat(val.UnitPrice * val.Quantity);
      }, 0);
    },
    totalService(values){
      return values.reduce((acc, val) => {
        return acc + parseFloat(val.ServiceCharge);
      }, 0);
    },
    yamahaLogo(){
      return baseurl + "warranty/"+ 'yamahalogo.jpg';
    },
    aciLogo(){
      return baseurl + "warranty/"+ 'aci.jpg';
    },
    imageLoad(image){
      let image_s3_base_url = 'https://yamaha-dms.s3.ap-southeast-1.amazonaws.com/';
      return image_s3_base_url + 'warranty-claim/' + image;
      // let localImage = baseurl + "assets/images/claim_warranty/"+ image;
      // if (localImage.ok) {
      //  return localImage;
      // }else {
      //   return image_s3_base_url + 'warranty-claim/' + image;
      // }
    },
    loadSignature(UserId){
      return  baseurl + "upload/signature/"+ UserId + '.jpg';
    },
    config() {
      let token = localStorage.getItem('token');
      return {
        headers: {Authorization: `Bearer ${token}`}
      };
    },
  }
}
</script>

<style scoped>
body{
  font-family: Helvetica, Arial, sans-serif;
}
table{
  width: 100%;
}
table h2{
  font-size: 24px;
  line-height: 60px;
}
table th{
  padding: 0px;
  margin: 0px;
}

#body {
  border-collapse: collapse;
}
#header, th, td {
  border: 0px solid #CCC;
}
td {
  border: 1px solid #CCC;
  padding: 3px;
  font-size : 12px;
}
.bold{
  font-weight: bold;
}
#histTable, th, td {
  border: 1px solid #CCC;
  border-collapse: collapse;
}
</style>