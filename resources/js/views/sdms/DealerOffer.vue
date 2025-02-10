<template>
  <div class="container-fluid" id="users" style="height: 400px;">
    <breadcrumb :options="['Upload Dealer offer']">
    </breadcrumb>
    <div class="payment">
      <div class="row">
        <div class="col-md-2">
          <div>
            <label>Select Date</label>
            <datepicker v-model="startDate" :dayStr="dayStr" placeholder="YYYY-MM-DD" :firstDayOfWeek="0"/>
          </div>
        </div>
        <div class="col-md-2">
          <label>Dealer Offer File (Excel)</label>
          <input type="file" class="form-control" style="height: 45px;" @change="readExcelFile">
        </div>
        <div class="offset-2 col-md-2">
          <div style="background: #fff;padding: 15px 15px;">
            <label>Sample Dealer Offer File:</label>
            <br>
            <a :href="filepath" class="btn btn-primary"><i class="ti ti-export"></i> Download</a>
          </div>
        </div>
      </div>
      <div class="row" style="padding: 10px 20px;">
        <button class="btn btn-primary" @click="uploadData"><i class="ti-upload"></i> Upload</button>
        &nbsp;
        <button class="btn btn-info" @click="reset"><i class="ti-reload"></i> Reset</button>
      </div>
    </div>
  </div>
</template>
<script>

import {bus} from "../../app";
import {Common} from "../../mixins/common";
import XLSX from "xlsx";

export default {
  name: 'DealerOffer',
  components: {},
  mixins: [Common],
  data() {
    return {
      startDate: '',
      endDate: '',
      customers: [],
      customer: '',
      dayStr: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
      filepath: this.mainOrigin + 'assets/uploads/dealer_offer_sample/dealer_offer_file_upload_format.xlsx',
      dataSet: []
    }
  },
  mounted() {
    bus.$off('changeStatus',function () {
      this.changeStatus()
    })
  },
  methods: {
    uploadData() {
      this.axiosPost('sdms-report/upload-offer-list',{
        dataSet: this.dataSet,
        startDate: this.startDate
      },(response) => {
        this.successNoti(response.message)
      })
    },
    reset() {
      this.startDate = ''
      this.endDate = ''
    },
    readExcelFile(e) {
      let files = e.target.files, f = files[0];
      let reader = new FileReader();
      reader.onload = (e) => {
        let data = new Uint8Array(e.target.result);
        let workbook = XLSX.read(data, {type: 'array'});
        let sheetName = workbook.SheetNames[0]
        let worksheet = workbook.Sheets[sheetName];
        this.dataSet = XLSX.utils.sheet_to_json(worksheet);
        console.log(XLSX.utils.sheet_to_json(worksheet))
      };
      reader.readAsArrayBuffer(f);
    },
  }
}
</script>

<style>
#users table tr td:nth-child(1),table tr td:nth-child(2),table tr td:nth-child(3),table tr td:nth-child(4),table tr td:nth-child(5),table tr td:nth-child(6),table tr td:nth-child(7) {
  text-align: left;
}
#content {
  height: 300px;
}
</style>
