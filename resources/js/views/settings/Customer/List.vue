<template>
  <div class="container-fluid">
    <breadcrumb :options="['Customer List']">
      <router-link :to="{name: 'CustomerCreate'}" class="btn btn-primary btn-sm">Add Customer</router-link>
      <button type="button" class="btn btn-success btn-sm" @click="exportCustomer">Export</button>
    </breadcrumb>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="datatable" v-if="!isLoading">
            <div class="card-body">
              <div class="d-flex">
                <div class="flex-grow-1">
                  <ValidationObserver v-slot="{ handleSubmit }">
                    <form @submit.prevent="handleSubmit(getAllCustomer)" @keydown.enter="$event.preventDefault()">
                      <div class="row">
                        <div class="col-md-2">
                          <ValidationProvider name="ChassisNo" mode="eager" v-slot="{ errors }" rules="">
                            <div class="form-group">
                              <label>Chassis No<span class="error">*</span></label>
                              <input class="form-control" type="text" value="" v-model="form.ChassisNo" placeholder="Enter Chassis No">
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-md-2">
                          <ValidationProvider name="DateFrom" mode="eager" v-slot="{ errors }" rules="">
                            <div class="form-group">
                              <label>Date From</label>
                              <date-picker v-model="form.DateFrom" valueType="format"></date-picker>
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-md-2">
                          <ValidationProvider name="DateTo" mode="eager" v-slot="{ errors }" rules="">
                            <div class="form-group">
                              <label>Date To</label>
                              <date-picker v-model="form.DateTo" valueType="format"></date-picker>
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
                <div class="card-tools">
                  <div class="row">
                    <div class="col-md-12">
<!--                      <input v-model="query" type="text" class="form-control" placeholder="Search">-->
                    </div>
                  </div>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                  <thead class="thead-dark">
                  <tr>
                    <th v-for="(item, index) in headers">
                      {{formatHeading(item.toString())}}
                    </th>
                    <th style="width: 12%">Action</th>
                  </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in contents" >
                      <td v-for="(item2, index) in headers" v-bind:class="isInt(item[item2]) === true ? 'text-right' : '' ">
                        {{ item[item2] }}
                      </td>
                      <td>
                        <router-link :to="`invoice-print/${item.InvoiceID}`" style="height: 18px;padding: 0px 3px 18px 3px;" class="btn btn-primary btn-sm small">
                          <i class="mdi mdi-printer"></i>
                        </router-link>
                        <router-link :to="`invoice-show/${item.InvoiceID}`" style="height: 18px;padding: 0px 3px 18px 3px;" class="btn btn-info btn-sm">
                          <i class="mdi mdi-eye"></i>
                        </router-link>
                        <router-link :to="`brta-invoice-print/${item.InvoiceID}`" style="height: 18px;padding: 0px 3px 18px 3px;" class="btn btn-info btn-sm">
                          <i class="mdi mdi-printer"></i> H Form(BRTA)
                        </router-link>
                        <button @click="destroy(item.InvoiceID)" style="height: 18px;padding: 0px 3px 18px 3px;" class="btn btn-danger btn-sm" v-if="isAdmin === '1'"><i class="fas fa-trash"></i></button>
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
                      @paginate="getAllCustomer()"
                  ></report-pagination>
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
      headers: [],
      contents: [],
      query: '',
      errors: [],
      isLoading: false,
      exportShow: false,
      form: new Form({
        DateFrom: moment().subtract(1, 'months').format('yyyy-MM-DD'),
        DateTo: moment().format('yyyy-MM-DD'),
        ChassisNo: '',
        pagination: {
          current_page: 1,
          from: 1,
          to: 1,
          total: 1,
          last_page: 1,
        },
        Export :'',
      }),
      isAdmin : '',
    }
  },
  created() {
    //
  },
  computed:{
    //
  },
  mounted() {
    document.title = 'Customer List | DMS';
    this.getAllInvoice();
  },
  methods: {
    getAllInvoice(){
      this.isLoading = true
      this.form.Export = '';
      this.form.post(baseurl + "api/get-all-customer", this.config()).then(response => {
        console.log(response.data)
       let invoice = response.data.invoice.original;
        if (invoice.data.length > 0){
          this.headers = Object.keys(invoice.data[0])
          this.contents = invoice.data
          this.isAdmin = response.data.isAdmin
          this.exportShow = false;
          this.isLoading = false
        }else {
          this.contents = []
          this.exportShow = true;
          this.isLoading = false
        }

        this.form.pagination.current_page = invoice.paginationData[0].current_page;
        this.form.pagination.from = invoice.paginationData[0].from;
        this.form.pagination.to = invoice.paginationData[0].to;
        this.form.pagination.total = invoice.paginationData[0].total;
        this.form.pagination.last_page = invoice.paginationData[0].last_page;
      }).catch(e => {
        //
      });
    },
    exportInvoice(){
      this.form.Export = 'Y';
      this.form.post(baseurl + "api/get-all-invoice", this.config())
          .then((response)=>{
            let invoice = response.data.invoice.original;
            let dataSets = invoice.data;
            if (dataSets.length > 0) {
              let columns = Object.keys(dataSets[0]);
              columns = columns.filter((item) => item !== 'row_num');
              let rex = /([A-Z])([A-Z])([a-z])|([a-z])([A-Z])/g;
              columns = columns.map((item) => {
                let title = item.replace(rex, '$1$4 $2$3$5')
                return {title, key: item}
              });
              bus.$emit('data-table-import', dataSets, columns, 'Invoice Export')
            }
          }).catch((error)=>{
      })
    },
    loadInvoice(){
      this.form.ChassisNo = '';
      this.getAllInvoice();
    },
    destroy(id){
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.get(baseurl + 'api/invoice-delete/'+ id, this.config()).then((response)=>{
            this.getAllInvoice();
            Swal.fire(
                'Deleted!',
                'Your file has been deleted.',
                'success'
            )
          })
        }
      })
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