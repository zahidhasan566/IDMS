<template>
  <div class="container-fluid">
    <breadcrumb :options="['Claim Warranty Summary Report']">
    </breadcrumb>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="datatable" v-if="!isLoading">
            <div class="card-body">
              <div class="d-flex">
                  <div class="flex-grow-1">
                    <ValidationObserver v-slot="{ handleSubmit }">
                      <form @submit.prevent="handleSubmit(warrantyClaimSummaryReport)" @keydown.enter="$event.preventDefault()">
                        <div class="row">
                          <div class="col-md-2">
                            <ValidationProvider name="DateFrom" mode="eager" v-slot="{ errors }" rules="required">
                              <div class="form-group">
                                <label>Date From<span class="error">*</span></label>
                                <date-picker v-model="DateFrom" valueType="format"></date-picker>
                                <span class="error-message"> {{ errors[0] }}</span>
                              </div>
                            </ValidationProvider>
                          </div>
                          <div class="col-md-2">
                            <ValidationProvider name="DateTo" mode="eager" v-slot="{ errors }" rules="required">
                              <div class="form-group">
                                <label>Date To<span class="error">*</span></label>
                                <date-picker v-model="DateTo" valueType="format"></date-picker>
                                <span class="error-message"> {{ errors[0] }}</span>
                              </div>
                            </ValidationProvider>
                          </div>
                          <div class="col-md-2" style="margin-top: 30px">
                            <button type="submit" class="btn btn-success"><i class="mdi mdi-filter"></i>Filter</button>
                            <button type="button" class="btn btn-info" @click="loadInvoice"><i class="mdi mdi-reload"></i>Reload</button>
                          </div>
                        </div>
                      </form>
                    </ValidationObserver>
                  </div>
              </div>
              <div>

              </div>
            </div>
          </div>
          <div v-else>
            <skeleton-loader :row="14"/>
          </div>
        </div>
      </div>
      <div class="col-sm-12">
        <div class="card">
          <div class="datatable">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <piechart
                      v-if="chartShow"
                      :labels="headers"
                      :dataSets="dataSets"
                  ></piechart>
                </div>
                <div class="col-md-6">
                  <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                    <thead class="thead-dark">
                    <tr>
                      <th>Status</th>
                      <th>Total Amount</th>
                      <th>Total Claimed Warranty</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, index) in claim_warranty" >
                      <td>{{ item.Approved_Status }}</td>
                      <td>{{ item.Total_Cost }}</td>
                      <td>{{ item.Total }}</td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {baseurl} from '../../../base_url'
import {Common} from "../../../mixins/common";
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import moment from "moment";

export default {
  name: "Invoice",
  mixins: [Common],
  components: { DatePicker },
  data() {
    return {
      claim_warranty: [],
      headers: [],
      dataSets: [],
      DateFrom: moment().startOf('month').format('yyyy-MM-DD'),
      DateTo: moment().format('yyyy-MM-DD'),
      errors: [],
      isLoading: false,
      chartShow: false
    }
  },
  created() {
    //
  },
  mounted() {
    document.title = 'Claim Warranty Summary Report | DMS';
    this.warrantyClaimSummaryReport();
  },
  methods: {
    warrantyClaimSummaryReport(){
      this.chartShow = false
      axios.get(baseurl + 'api/warranty-claim-summary-report?DateFrom=' + this.DateFrom
          + "&DateTo=" + this.DateTo
          , this.config() ).then((response)=>{
            this.claim_warranty = response.data.summary;
        this.headers = response.data.summary.map(item => item.Approved_Status);
        let dataSet = [];
        dataSet.push({backgroundColor: ['red','orange'],data: response.data.summary.map(item => item.Total)});
        this.dataSets = dataSet
        this.chartShow = true
      }).catch((error)=>{

      })
    },
    loadInvoice(){
      this.ChassisNo = '';
      this.getAllClaimWarranty();
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
#customer_form .form-control {
  font-size: 10px;
  height: 29px;
}
#customer_form .form-group {
  margin-bottom: 0;
}
#customer_form label {
  font-size: 11px!important;
}
.form-divider {
  padding: 6px 5px 5px 5px;
  border: 1px solid #4d87f64f;
  border-radius: 13px;
  margin: 0 auto;
}
#invoice2 .auto-complete2 {
  position: relative;
  display: block;
}
#invoice2 .auto-complete2 ul {
  list-style: none;
  margin: 0;
  padding: 5px 0 0 0px;
  position: absolute;
  width: 100%;
  border: 1px solid #0000000d;
  background: #ffffff;
  max-height: 200px;
  overflow-y: scroll;
  z-index: 999;
}
#invoice2 .auto-complete2 ul li{
  border-bottom: 1px solid #b7b7b7;
  background: #cbc4c4;
  padding: 5px;
  cursor: pointer;
}
#invoice2 .auto-complete2 ul li a{
  color: #000000;
}
#invoice2 .auto-complete2 ul li:hover{
  background: #fff3cd;
}
#invoice2 :focus{
  background: #fff3cd;
}
.form-divider-title {
  position: relative;
  top: -20px;
}

.form-divider-title p {
  position: absolute;
  padding: 0 25px;
  background: #ffffff;
  text-transform: uppercase;
  font-weight: bold;
  color: #4d79f6b5  !important;
  font-size: 12px;
}
.tableFixHead {
  overflow-y: auto; /* make the table scrollable if height is more than 200 px  */
  height: 200px; /* gives an initial height of 200px to the table */
}
.tableFixHead thead th {
  position: sticky; /* make the table heads sticky */
  top: 0px; /* table head will be placed from the top of the table and sticks to it */
}
table {
  border-collapse: collapse; /* make the table borders collapse to each other */
  width: 100%;
}
th,
td {
  padding: 8px 16px;
  border: 1px solid #ccc;
}
th {
  background: #000000;
}
</style>