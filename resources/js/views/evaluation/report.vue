<template>
  <div class="container-fluid">
    <breadcrumb :options="['Evaluation Report']">
      <button type="button" class="btn btn-success btn-sm"
              :disabled="exportShow" @click="exportEvaluationReport">Export to Excel </button>
      <router-link :to="{name: 'Dashboard'}" class="btn btn-primary btn-sm">Back</router-link>
    </breadcrumb>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="datatable" v-if="!isLoading">
            <div class="card-body" id="customer_form">
              <ValidationObserver v-slot="{ handleSubmit }">
                <form @submit.prevent="handleSubmit(getTheReport)">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="row form-divider m-b-15">
                        <div class="form-divider-title">
                          <p style="width: 160px">Dealer Details</p>
                        </div>
                        <div class="col-12 col-md-3">
                          <ValidationProvider name="Customer" mode="eager" v-slot="{ errors }"
                          >
                            <div class="form-group">
                              <label>Dealer <span class="error">*</span></label>
                              <select name="jobStatus" class="form-control" v-model="form.customerCode" style="margin: 0; font-size: 14px">
                                <option :value="null" v-if="user == 0" selected>All</option>
                                <option :value="singleCustomer.CustomerCode" v-for="(singleCustomer , index) in allDealer"
                                        :key="index">{{ singleCustomer.CustomerCode }} - {{ singleCustomer.CustomerName }}</option>
                              </select>
                              <div class="error" v-if="form.errors.has('CustomerCode')" v-html="form.errors.get('CustomerCode')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="DateFrom" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Date From <span style="color: red">*</span></label>
                              <date-picker v-model="form.dateFrom" valueType="format"></date-picker>
                              <div class="error" v-if="form.errors.has('dateFrom')" v-html="form.errors.get('dateFrom')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>

                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="DateTo" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Date To<span style="color: red">*</span></label>
                              <date-picker v-model="form.dateTo" valueType="format" ></date-picker>
                              <div class="error" v-if="form.errors.has('dateTo')" v-html="form.errors.get('dateTo')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>

                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="Report By" mode="eager" rules="required" v-slot="{ errors }" >
                            <div class="form-group">
                              <label>Report By<span style="color: red">*</span></label>
                              <select class="form-control" name="active" v-model="form.reportType" style="height: 34px; font-size: 14px">
                                <option value="Sales" selected>Sales</option>
                                <option value="Service4p">Service4p</option>
                              </select>
                              <div class="error" v-if="form.errors.has('reportBy')" v-html="form.errors.get('reportBy')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                      </div>
                      <br>
                      <button type="submit" class="btn btn-primary float-right submit_on_enter" >Submit</button>
                      <button type="button" class="btn btn-info" @click="loadService"><i class="mdi mdi-reload"></i>Reload</button>

                    </div>
                  </div>


                </form>
              </ValidationObserver>
            </div>
            <div v-if="contents.length > 0">
              <div class="table-responsive">
                <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                  <thead class="thead-dark">
                  <tr>

                    <th v-for="(item, index) in headers" v-if="index !== 0 && index !== 32">
                      {{ item.replace(/_/g, ' ', " $1").trim() }}
                    </th>
                    <th style="width: 9%">Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(item, index) in contents" >

                    <td
                        v-for="(item2, index) in headers.slice(1)" v-bind:class="isInt(item[item2]) === true ? 'text-right' : '' " v-if="index !== 32">
                      {{ item[item2] }}
                    </td>
                    <td>
                      <div v-if="form.reportType ==='Sales'">
                        <router-link class="btn btn-primary" :to="{path: `${baseUrl}`+'evaluation/details-report?evaluationId='+item.EvalutionId}">
                          <i class="ti-eye">Details</i>
                        </router-link>
                      </div>
                      <div v-if="form.reportType ==='Service4p'">
                        <router-link class="btn btn-primary" :to="{path: `${baseUrl}`+'evaluation/details-report?evaluationId='+item.EvalutionId}">
                          <i class="ti-eye">Details</i>
                        </router-link>
                      </div>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div v-if="noData===true">
              <div class="row">
                <div class="col-md-12" >
                  <h4 class="text-center error text-center" >No data Found</h4>
                  <br>
                  <br>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
    <div>
      <loader v-if="PreLoader" object="#ff9633" color1="#ffffff" color2="#17fd3d" size="5" speed="2" bg="#343a40"
              objectbg="#999793" opacity="80" name="circular"></loader>
    </div>
    <data-export/>
  </div>
</template>

<script>
import {baseurl} from '../../base_url'
import {Common} from "../../mixins/common";
import moment from "moment";
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import {bus} from "../../app";
export default {
  name: "Invoice",
  mixins: [Common],
  components: { DatePicker },
  data() {
    return {
      user: '',
      allDealer: [],
      headerDatas: [],
      headers: [],
      contents: [],
      list: [],
      query: '',
      form: new Form({
        customerCode:'',
        reportType:'',
        dateFrom : moment().format('yyyy-MM-DD'),
        dateTo : moment().format('yyyy-MM-DD'),
      }),
      errors: [],
      isLoading: false,
      buttonShow: false,
      PreLoader: false,
      submitStatus: false,
      loading : false,
      exportShow: false,
      noData: false,
      baseUrl: baseurl
    }
  },
  created() {
    //
  },
  computed:{
    //
  },
  mounted() {
    document.title = 'Evaluation Report| DMS';
    this.getDealers()
    this.getData();
  },
  methods: {
    getData() {
      this.axiosGet('app-supporting-data', (response) => {
        this.user = response.user.grpUser;
      }, (error) => {
        this.errorNoti(error)
      })
    },
    getDealers() {
      let instance = this;
      this.axiosGet('evaluation/get-all-dealer', function (response) {
        instance.allDealer = response.dealer;
      }, function (error) {
      });
    },
    getTheReport(){
      this.isLoading = true
      this.form.Export = '';
      this.form.post(baseurl + "api/evaluation/report", this.config()).then(response => {
        if (response.data.data.length > 0){
          this.headers = Object.keys(response.data.data[0])
          this.contents = response.data.data
          this.exportShow = false;
          this.isLoading = false
        }else {
          this.contents = []
          this.noData = true;
          this.exportShow = true;
          this.isLoading = false
        }

        this.form.pagination.current_page = response.data.paginationData[0].current_page;
        this.form.pagination.from = response.data.paginationData[0].from;
        this.form.pagination.to = response.data.paginationData[0].to;
        this.form.pagination.total = response.data.paginationData[0].total;
        this.form.pagination.last_page = response.data.paginationData[0].last_page;
      }).catch(e => {
        //
      });
    },
    exportEvaluationReport(){
      this.form.Export = 'Y';
      this.exportShow = true;
      this.form.post(baseurl + "api/evaluation/report", this.config()).then(response => {
        let dataSets = response.data.data;
        if (dataSets.length > 0) {
          let columns = Object.keys(dataSets[0]);
          columns = columns.filter((item) => item !== 'row_num');
          let rex = /([A-Z])([A-Z])([a-z])|([a-z])([A-Z])/g;
          columns = columns.map((item) => {
            let title = item.replace(rex, '$1$4 $2$3$5')
            return {title, key: item}
          });
          bus.$emit('data-table-import', dataSets, columns, 'evaluation report')
          this.exportShow = false;
        }
      }).catch(e => {
        //
      });
    },
    loadService(){
      this.form.customerCode=''
      this.form.reportType=''
      this.form.dateFrom = moment().format('yyyy-MM-DD')
      this.form.dateTo = moment().format('yyyy-MM-DD')
      this.getDealers();
    },
    changeStatus() {
      this.loading = false
    },
    formatHeading(item) {
      let rex = /([A-Z])([A-Z])([a-z])|([a-z])([A-Z])/g;
      let title = item.replace(rex, '$1$4 $2$3$5')
      return title.replace('_',' ')
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