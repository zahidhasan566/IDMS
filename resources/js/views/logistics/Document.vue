<template>
  <div class="container-fluid">
    <breadcrumb :options="['Bike Document']">
      <button type="button" class="btn btn-info" @click="loadInvoice"><i class="mdi mdi-reload"></i>Reload</button>
    </breadcrumb>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="datatable" v-if="!isLoading">
            <div class="card-body" id="customer_form">
              <ValidationObserver v-slot="{ handleSubmit }">
                <form @submit.prevent="handleSubmit(onSubmit)">
                  <div class="row">
                    <div class="col-md-3">
                      <ValidationProvider name="InvoiceNo" mode="eager" v-slot="{ errors }" rules="required">
                        <div class="form-group">
                          <label>Invoice No <span class="error">*</span></label>
                          <input class="form-control" type="text" value="" v-model="form.InvoiceNo"
                                 placeholder="Enter Invoice No">
                          <span class="error-message"> {{ errors[0] }}</span>
                        </div>
                      </ValidationProvider>
                    </div>
                    <div class="col-md-2" style="margin-top: 30px">
                      <button type="button" @click="getInvoiceDetails()" class="btn btn-success"><i
                          class="mdi mdi-filter"></i>Preview
                      </button>
                      <button type="button" class="btn btn-info" @click="loadInvoice"><i class="mdi mdi-reload"></i>Reload
                      </button>
                    </div>
                  </div>
                  <br>
                  <div class="row">
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>Send Date<span class="error">*</span></label>
                        <date-picker v-model="sendDate" valueType="format"></date-picker>
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </div>
                  </div>
                  <br>

                  <div class="row" v-if="headers.length>0">
                    <div class="col-md-12">
                      <div class="row m-b-15">

                        <div class="col-2">
                          <div class="form-group ">
                            <input type="checkbox" id="checkbox" v-model="checked" @change="markToggle">
                            <span>Mark All</span>
                          </div>
                        </div>
                        <br>
                        <div class="table-responsive">
                          <table
                              class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                            <thead class="thead-dark">
                            <tr>
                              <th v-for="(item, index) in headers">
                                {{ formatHeading(item.toString()) }}
                              </th>
                              <th style="width: 10%">Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr v-for="(item, index) in contents">
                              <td v-for="(item2, index) in headers"
                                  v-bind:class="isInt(item[item2]) === true ? 'text-right' : '' ">
                                {{ item[item2] }}
                              </td>
                              <td>
                                <input type="checkbox" :value="item.chassisno" v-model="allOptions">
                              </td>
                            </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                  <br>

                  <button type="submit" class="btn btn-primary float-right submit_on_enter">Submit</button>
                </form>
              </ValidationObserver>
              <br>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div>
      <loader v-if="PreLoader" object="#ff9633" color1="#ffffff" color2="#17fd3d" size="5" speed="2" bg="#343a40" objectbg="#999793" opacity="80" name="circular"></loader>
    </div>
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
  components: {DatePicker},
  data() {
    return {
      allData: [{
        chassisno: '',
        invoiceno: '',
        engineno: '',
        productcode: '',
      }],
      allOptions: [],
      pagination: {
        current_page: 1,
        from: 1,
        to: 1,
        total: 1,
      },
      sendDate: moment().format('yyyy-MM-DD'),
      CustomerCode: '',
      form: new Form({
        InvoiceNo: '',
        InvoiceDate: '',
        CheckedBox: false,
      }),
      errors: [],
      headers: [],
      contents: [],
      isAdmin: '',
      checked: false,
      isLoading: false,
      buttonShow: false,
      PreLoader: false,
    }
  },
  created() {
    //
  },
  computed: {
    //
  },
  mounted() {
    document.title = 'Document | DMS';
  },
  methods: {

    getInvoiceDetails() {
      this.PreLoader = true;
      this.form.post(baseurl + "api/logistics/get-invoice-details", this.config()).then(response => {

        if (response.data.data.length >0) {
          this.PreLoader = false;
          this.CustomerCode = response.data.data[0].customercode
          this.contents = []
          this.headers = Object.keys(response.data.data[0])
          response.data.data.forEach((item) => {
            this.contents.push(item)
          })
        } else {
          this.PreLoader = false;
          this.headers = []
          this.$toaster.error('No Document Found');
        }
      }).catch(e => {
        this.$toaster.error(e.data.message);
      });
    },
    loadInvoice() {
      this.form.InvoiceNo = '';
      this.headers = '';
    },
    onSubmit() {
      this.PreLoader = true;
      this.allData = []
      this.contents.forEach((item) => {
        let data = this.allOptions.find((a) => {
          return a === item.chassisno
        })
        if (data) {
          this.allData.push(item)
        }
      })
      this.axiosPost('logistics/store-document', {
        // chassis: this.allOptions,
        allData: this.allData,
        sendDate: this.sendDate,
        customerCode: this.CustomerCode
      }, (response) => {
        if (response.status === 'success') {
          this.PreLoader = false;
          this.successNoti(response.message);
          this.allData = []
          this.headers = '';
        } else {
          this.PreLoader = false;
          this.errorNoti(response.message);
        }
      })
    },
    markToggle(e) {
      if (e.target.checked) {
        this.allOptions = []
        this.contents.map((content) => {
          this.allOptions.push(content.chassisno)
        })
      } else {
        this.allOptions = []
      }
    },

    formatHeading(item) {
      let rex = /([A-Z])([A-Z])([a-z])|([a-z])([A-Z])/g;
      let title = item.replace(rex, '$1$4 $2$3$5')
      return title.replace('_', ' ')
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
.permission-grp {
  padding: 10px;
  border: 1px solid #e2e2e2;
  border-radius: 5px;
}

#customer_form .form-control {
  font-size: 15px;
  height: 40px;
}

#customer_form .form-group {
  margin-bottom: 0;
}

#customer_form label {
  font-size: 11px !important;
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

#invoice2 .auto-complete2 ul li {
  border-bottom: 1px solid #b7b7b7;
  background: #cbc4c4;
  padding: 5px;
  cursor: pointer;
}

#invoice2 .auto-complete2 ul li a {
  color: #000000;
}

#invoice2 .auto-complete2 ul li:hover {
  background: #fff3cd;
}

#invoice2 :focus {
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
  color: #4d79f6b5 !important;
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