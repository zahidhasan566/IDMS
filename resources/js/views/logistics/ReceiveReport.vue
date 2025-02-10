<template>
  <div class="container-fluid">
    <breadcrumb :options="['Receive Report']">
            <button type="button" class="btn btn-success btn-sm" @click="exportReport">Export to Excel</button>
    </breadcrumb>

    <div class="row">
      <div class="col-xl-12">
        <div class="card">
          <div class="datatable" v-if="!isLoading">
            <div class="card-body">
              <ValidationObserver v-slot="{ handleSubmit }">
                <form @submit.prevent="handleSubmit(filterAllReport)" @keydown.enter="$event.preventDefault()">
                  <div class="row">
                    <div class="col-md-2">
                      <ValidationProvider name="DateFrom" mode="eager" v-slot="{ errors }" rules="required">
                        <div class="form-group">
                          <label>Date From<span class="error">*</span></label>
                            <date-picker v-model="form.DateFrom" valueType="format"></date-picker>
                            <div class="error" v-if="form.errors.has('DateFrom')" v-html="form.errors.get('DateFrom')" />
                          <span class="error-message"> {{ errors[0] }}</span>
                        </div>
                      </ValidationProvider>
                    </div>
                      <div class="col-md-2">
                          <ValidationProvider name="DateTo" mode="eager" v-slot="{ errors }" rules="required">
                              <div class="form-group">
                                  <label>Date To<span class="error">*</span></label>
                                  <date-picker v-model="form.DateTo" valueType="format"></date-picker>
                                  <div class="error" v-if="form.errors.has('DateTo')" v-html="form.errors.get('DateTo')" />
                                  <span class="error-message"> {{ errors[0] }}</span>
                              </div>
                          </ValidationProvider>
                      </div>
                      <div class="col-md-2">
                          <ValidationProvider name="Customer" mode="eager" v-slot="{ errors }">
                          <div class="form-group">
                              <label>Customer <span class="error">*</span></label>
                              <select name="jobStatus" class="form-control" v-model="form.CustomerCode" style="margin: 0">
                                  <option :value="null" selected>All</option>
                                  <option :value="singleCustomer.value" v-for="(singleCustomer , index) in customer"
                                          :key="index">{{singleCustomer.value}}- {{ singleCustomer.text }}</option>
                              </select>
                              <div class="error" v-if="form.errors.has('CustomerCode')" v-html="form.errors.get('CustomerCode')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                          </div>
                          </ValidationProvider>
                      </div>
                      <div class="col-md-2">
                          <div class="form-group">
                              <label>Chassis No</label>
                              <input type="text" class="form-control"
                                     id=""
                                     data-required="true"
                                     v-model="form.chassisNo" name="chassisNo" placeholder="chassis No" >
                              <div class="error" v-if="form.errors.has('jobTypes')" v-html="form.errors.get('chassisNo')" />
                          </div>
                      </div>
                      <div class="col-md-2">
                          <div class="form-group">
                              <label>Report type</label>
                              <select name="Report type" class="form-control" v-model="form.reportType" style="margin: 0">
                                  <option value="">Select</option>
                                  <option value="not receive">Not Receive</option>
                                  <option value="receive">Receive</option>

                              </select>
                              <div class="error" v-if="form.errors.has('jobStatus')" v-html="form.errors.get('reportType')" />
                          </div>
                      </div>
                    <div class="col-md-2" style="margin-top: 30px">
                      <button type="submit" class="btn btn-success"><i class="mdi mdi-filter"></i>Filter</button>
                    </div>
                  </div>
                </form>
              </ValidationObserver>
              <div v-if="contents.length > 0">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                    <thead class="thead-dark">
                    <tr>
                      <th style="background: #333547" v-for="(item, index) in headers" v-if="index !== 0 && index !== 32">
                        {{ item.replace(/_/g, ' ', " $1").trim() }}
                      </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, index) in contents" >
                      <td v-for="(item2, index) in headers.slice(1)" v-bind:class="isInt(item[item2]) === true ? 'text-right' : '' " v-if="index !== 32">
                        {{ item[item2] }}
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
                <div class="row">
                  <div class="col-4">
                    <div class="data-count">
                      Show {{ form.pagination.from }} to {{ form.pagination.to }} of {{ form.pagination.total }} rows
                    </div>
                  </div>
                  <div class="col-8">
                    <report-pagination
                        v-if="form.pagination.last_page > 1"
                        :pagination="form.pagination"
                        :offset="5"
                        @paginate="form.Query === '' ? filterAllReport() : filterAllReport()"
                    ></report-pagination>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-else>
            <skeleton-loader :row="14"/>
          </div>
        </div>
      </div>
    </div>
    <data-export/>
  </div>
</template>

<script>
import {baseurl} from '../../base_url'
import {Common} from "../../mixins/common";
import XLSX from "xlsx";
import {bus} from "../../app";
import moment from "moment";
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import Papa from "papaparse";
export default {
  name: "Report",
  mixins: [Common],
  components: { DatePicker },
  data() {
    return {
      businesses: [],
      sample_for: [],
      headers: [],
      contents: [],
      Depots: [],
      customer:[],
      jobTypes:[],
      jobStatus:[],
      ReportName: '',
      user:'',
      loaderTag:false,
      baseUrl: baseurl,
      form: new Form({
        DateFrom : moment().format('yyyy-MM-DD'),
        DateTo : moment().format('yyyy-MM-DD'),
        CustomerCode:'',
        chassisNo:'',
        reportType:'',
        Query :'',
        Export :'',
        pagination: {
          current_page: 1,
          from: 1,
          to: 1,
          total: 1,
        },
      }),
      isLoading: false,
      errors: [],
      exportShow: false,
    }
  },
  created() {
    //
  },
  mounted() {
    this.getData()
    this.getSupportingData();
    this.filterAllReport();
  },
  methods: {
    filterAllReport(){
      this.isLoading = true
      this.form.Export = '';
      this.form.post(baseurl + "api/logistics/receive-report", this.config()).then(response => {
        if (response.data.data.length > 0){
          this.headers = Object.keys(response.data.data[0])
          this.contents = response.data.data
          this.exportShow = true;
          this.isLoading = false
        }else {
          this.contents = []
          this.exportShow = false;
          this.isLoading = false
        }

        this.form.pagination.current_page = response.data.paginationData[0].current_page;
        this.form.pagination.from = response.data.paginationData[0].from;
        this.form.pagination.to = response.data.paginationData[0].to;
        this.form.pagination.total = response.data.paginationData[0].total;
        this.form.pagination.last_page = response.data.paginationData[0].last_page;

        if (response.data.report.SearchEnable === '1'){
          this.SearchEnable = true;
        }
      }).catch(e => {
        //
      });
    },
    exportReport(){
      this.form.Export = 'Y';
      this.exportShow = true;
      this.form.post(baseurl + "api/logistics/receive-report", this.config()).then(response => {
        let dataSets = response.data.data;
        if (dataSets.length > 0) {
          const csv = Papa.unparse(dataSets);
          // Create a Blob containing the CSV data
          const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
          // Create a download link and trigger the download
          const link = document.createElement('a');
          const url = URL.createObjectURL(blob);
          link.href = url;
          link.setAttribute('download', 'Receive Report.csv');
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
          this.exportShow = false;
        }
      }).catch(e => {
        //
      });
    },
    encodeConvert(val){
      let convertVal = btoa(val);
      return convertVal
    },
    getData() {
          this.axiosGet('app-supporting-data', (response) => {
              this.user = response.user.UserId;
          }, (error) => {
              this.errorNoti(error)
          })
      },
    getSupportingData() {
          let instance = this;
          this.axiosGet('logistics/receive-report-supporting-data', function (response) {
              instance.customer = instance.arrayKeyChange(response.user)
              if(response.user.length===1){
                  instance.form.CustomerCode =  response.user[0].value
              }
          }, function (error) {
          });
      },
    isInt(value) {
      return !isNaN(parseInt(value * 1))
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
#ceilingModal .form-control {
  font-size: 10px;
  height: 25px;
}
#ceilingModal label {
  font-size: 11px!important;
}
.form-divider {
  padding: 6px 0px 5px 5px;
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

</style>