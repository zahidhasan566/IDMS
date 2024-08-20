<template>
  <div class="container-fluid">
<!--    <breadcrumb :options="['Claim Warranty Report']">-->
<!--      <button type="button" class="btn btn-success btn-sm" @click="exportPendingWarranty">Export</button>-->
<!--    </breadcrumb>-->
    <br>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="datatable" v-if="!isLoading">
            <div class="card-body">
              <div class="col-md-12">
                <div class="d-flex">
                  <div class="flex-grow-1">
                    <ValidationObserver v-slot="{ handleSubmit }">
                      <form @submit.prevent="handleSubmit(getAllWarrantyReport)" @keydown.enter="$event.preventDefault()">
                        <div class="row">
                          <div class="col-12 col-md-2">
                            <ValidationProvider name="DateFrom" mode="eager" rules="" v-slot="{ errors }">
                              <div class="form-group">
                                <label>Date From</label>
                                <date-picker v-model="form.DateFrom" valueType="format"></date-picker>
                                <div class="error" v-if="form.errors.has('DateFrom')" v-html="form.errors.get('DateFrom')" />
                                <span class="error-message"> {{ errors[0] }}</span>
                              </div>
                            </ValidationProvider>
                          </div>
                          <div class="col-12 col-md-2">
                            <ValidationProvider name="DateTo" mode="eager" rules="" v-slot="{ errors }">
                              <div class="form-group">
                                <label>Date To</label>
                                <date-picker v-model="form.DateTo" valueType="format"></date-picker>
                                <div class="error" v-if="form.errors.has('DateTo')" v-html="form.errors.get('DateTo')" />
                                <span class="error-message"> {{ errors[0] }}</span>
                              </div>
                            </ValidationProvider>
                          </div>
                          <div class="col-12 col-md-2">
                            <ValidationProvider name="CustomerCode" mode="eager" rules="" v-slot="{ errors }">
                              <div class="form-group">
                                <label>Customer</label>
                                <select name="CustomerCode" class="form-control" v-model="form.CustomerCode " style="margin: 0">
                                  <option value="">All</option>
                                  <option :value="customer.CustomerCode" v-for="(customer , index) in customers" :key="index">{{ customer.CustomerCode }}-{{ customer.CustomerName }}</option>
                                </select>
                                <div class="error" v-if="form.errors.has('CustomerCode')" v-html="form.errors.get('CustomerCode')" />
                                <span class="error-message"> {{ errors[0] }}</span>
                              </div>
                            </ValidationProvider>
                          </div>
                          <div class="col-12 col-md-2">
                            <ValidationProvider name="Region" mode="eager" rules="" v-slot="{ errors }">
                              <div class="form-group">
                                <label>Region</label>
                                <select name="Region" class="form-control" v-model="form.Region " style="margin: 0">
                                  <option :value="region.RegionName" v-for="(region , index) in regions" :key="index">{{ region.RegionName }}</option>
                                </select>
                                <div class="error" v-if="form.errors.has('Region')" v-html="form.errors.get('Region')" />
                                <span class="error-message"> {{ errors[0] }}</span>
                              </div>
                            </ValidationProvider>
                          </div>
                          <div class="col-12 col-md-2">
                            <ValidationProvider name="ApprovedType" mode="eager" rules="" v-slot="{ errors }">
                              <div class="form-group">
                                <label>Approved Type</label>
                                <select name="ApprovedType" class="form-control" v-model="form.ApprovedType " style="margin: 0">
                                  <option value=""> Select One</option>
                                  <option value="0">Pending</option>
                                  <option value="1">Approved</option>
                                  <option value="2">Cancel</option>
                                </select>
                                <div class="error" v-if="form.errors.has('ApprovedType')" v-html="form.errors.get('ApprovedType')" />
                                <span class="error-message"> {{ errors[0] }}</span>
                              </div>
                            </ValidationProvider>
                          </div>
                          <div class="col-md-2" style="margin-top: 30px">
                            <button type="submit" class="btn btn-success"><i class="mdi mdi-filter"></i>Filter</button>
                            <button type="button" class="btn btn-info" @click="loadReport"><i class="mdi mdi-reload"></i>Reload</button>
                          </div>
                        </div>
                      </form>
                    </ValidationObserver>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="datatable" v-if="!isLoading">
            <div class="card-body">
              <div class="d-flex">
                <div class="flex-grow-1">
                  <button type="submit" class="btn btn-success btn-sm" @click="exportWarrantyReport"><i class="mdi mdi-filter"></i>Export</button>
                </div>
                <div class="card-tools">
                  <div class="row">
                    <div class="col-md-12">
                     <input v-model="query" type="text" class="form-control" placeholder="Search">
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="contents.length > 0">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                    <thead class="thead-dark">
                    <tr>
                      <th>Print</th>
                      <th v-for="(item, index) in headers" v-if="index !== 0 && index !== 32">
                        {{ item.replace(/_/g, ' ', " $1").trim() }}
                      </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, index) in contents" >
                      <td>
                        <router-link :to="`${mainOrigin}approval/warranty-print/${item.DCWarrantyId}`" target="_blank" style="height: 18px;padding: 0px 3px 18px 3px;" ta class="btn btn-info btn-sm">
                          <i class="mdi mdi-printer"></i> Print
                        </router-link>
                      </td>
                      <td
                          v-for="(item2, index) in headers.slice(1)" v-bind:class="isInt(item[item2]) === true ? 'text-right' : '' " v-if="index !== 32">
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
                        @paginate="form.Query === '' ? getAllWarrantyReport() : getAllWarrantyReport()"
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
      customers: [],
      regions: [],
      headers: [],
      contents: [],
      ChassisNo: '',
      query: '',
      errors: [],
      isLoading: false,
      exportShow: false,
      isAdmin : '',
      form: new Form({
        Query :'',
        Export :'',
        DateFrom :moment().startOf('month').format('YYYY-MM-DD'),
        DateTo :moment().format('YYYY-MM-DD'),
        CustomerCode :'',
        ApprovedType :'',
        Region :'',
        pagination: {
          current_page: 1,
          from: 1,
          to: 1,
          total: 1,
          last_page: 1,
        },
      }),
    }
  },
  created() {
    //
  },
  computed:{
    //
  },
  mounted() {
    document.title = 'Claim Warranty Report | DMS';
    this.getAllWarrantyReport();
    this.getAllCustomer();
    this.getAllRegion();
  },
  methods: {
    getAllWarrantyReport(){
      this.isLoading = true
      this.form.Export = '';
      this.form.post(baseurl + "api/get-all-claim-warranty-report", this.config()).then(response => {
        console.log(response.data)
        if (response.data.data.length > 0){
          this.headers = Object.keys(response.data.data[0])
          this.contents = response.data.data
          this.exportShow = false;
          this.isLoading = false
        }else {
          this.contents = []
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
    exportWarrantyReport(){
      this.form.Export = 'Y';
      this.exportShow = true;
      this.form.post(baseurl + "api/get-all-claim-warranty-report", this.config()).then(response => {
        let dataSets = response.data.data;
        if (dataSets.length > 0) {
          let columns = Object.keys(dataSets[0]);
          columns = columns.filter((item) => item !== 'row_num');
          let rex = /([A-Z])([A-Z])([a-z])|([a-z])([A-Z])/g;
          columns = columns.map((item) => {
            let title = item.replace(rex, '$1$4 $2$3$5')
            return {title, key: item}
          });
          //this.generateExport(dataSets, columns, 'Job Card Report')
          bus.$emit('data-table-import', dataSets, columns, 'Bike Sales Report')
          this.exportShow = false;
        }
      }).catch(e => {
        //
      });
    },
    getAllCustomer(){
      axios.get(baseurl + 'api/get-all-customer', this.config() ).then((response)=>{
        this.customers = response.data.customers;
      }).catch((error)=>{

      })
    },
    getAllRegion(){
      axios.get(baseurl + 'api/get-all-region', this.config() ).then((response)=>{
        this.regions = response.data.regions;
      }).catch((error)=>{

      })
    },
    loadReport(){
      this.ChassisNo = '';
      this.getAllWarrantyReport();
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