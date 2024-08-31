<template>
  <div class="container-fluid">
    <breadcrumb :options="['Customer Create']">
      <router-link :to="{name: 'CustomerList'}" class="btn btn-primary btn-sm">Back</router-link>
    </breadcrumb>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="datatable" v-if="!isLoading">
            <div class="card-body" id="customer_form">
              <ValidationObserver v-slot="{ handleSubmit }">
                <form @submit.prevent="handleSubmit(onSubmit)">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-divider m-b-15">
                        <div class="form-divider-title">
                          <p style="width: 200px">Customer Details</p>
                        </div>
                        <div class="row">
                          <div class="col-md-5">
                            <ValidationProvider name="CustomerCode" mode="eager" rules="required" v-slot="{ errors }">
                              <div class="form-group row" style="padding-bottom: 10px">
                                <label class="col-lg-4 col-form-label text-right">Customer Code<span style="color: red">*</span></label>
                                <div class="col-lg-8">
                                  <input name="CustomerCode" type="text" class="form-control" v-model="form.CustomerCode" />
                                  <div class="error" v-if="form.errors.has('CustomerCode')" v-html="form.errors.get('CustomerCode')" />
                                  <span class="error-message"> {{ errors[0] }}</span>
                                </div>
                              </div>
                            </ValidationProvider>
                            <ValidationProvider name="CustomerName" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group row" style="padding-bottom: 10px">
                              <label class="col-lg-4 col-form-label text-right">Customer Name<span style="color: red">*</span></label>
                              <div class="col-lg-8">
                                <input name="CustomerName" type="text" class="form-control" v-model="form.CustomerName" />
                                <div class="error" v-if="form.errors.has('CustomerName')" v-html="form.errors.get('CustomerName')" />
                                <span class="error-message"> {{ errors[0] }}</span>
                              </div>
                            </div>
                            </ValidationProvider>
                            <ValidationProvider name="DistrictCode" mode="eager" rules="required" v-slot="{ errors }">
                              <div class="form-group row" style="padding-bottom: 10px">
                                <label class="col-lg-4 col-form-label text-right">District<span style="color: red">*</span></label>
                                <div class="col-lg-8">
                                  <select name="DistrictCode" class="form-control" v-model="form.DistrictCode" style="margin: 0" @change="districtWiseThana">
                                    <option :value="district.DistrictCode" v-for="(district , index) in districts" :key="index">{{ district.DistrictName }}</option>
                                  </select>
                                  <div class="error" v-if="form.errors.has('District')" v-html="form.errors.get('District')" />
                                  <span class="error-message"> {{ errors[0] }}</span>
                                </div>
                              </div>
                            </ValidationProvider>
                            <ValidationProvider name="Gender" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group row" style="padding-bottom: 10px">
                              <label class="col-lg-4 col-form-label text-right">Gender<span style="color: red">*</span></label>
                              <div class="col-lg-8">
                                <select name="Gender" class="form-control" v-model="form.Gender" style="margin: 0">
                                  <option value="Male">Male</option>
                                  <option value="Female">Female</option>
                                  <option value="Other">Other</option>
                                </select>
                                  <span class="error-message"> {{ errors[0] }}</span>
                              </div>
                            </div>
                            </ValidationProvider>
                            <ValidationProvider name="Add1" mode="eager" rules="required" v-slot="{ errors }">
                              <div class="form-group row" style="padding-bottom: 10px">
                                <label class="col-lg-4 col-form-label text-right">Address 1<span style="color: red">*</span></label>
                                <div class="col-lg-8">
                                  <input name="Add1" type="text" class="form-control" v-model="form.Add1" />
                                  <div class="error" v-if="form.errors.has('Add1')" v-html="form.errors.get('Add1')" />
                                  <span class="error-message"> {{ errors[0] }}</span>
                                </div>
                              </div>
                            </ValidationProvider>
                            <ValidationProvider name="Add2" mode="eager" rules="" v-slot="{ errors }">
                              <div class="form-group row" style="padding-bottom: 10px">
                                <label class="col-lg-4 col-form-label text-right">Address 2</label>
                                <div class="col-lg-8">
                                  <input name="Add2" type="text" class="form-control" v-model="form.Add2" />
                                  <div class="error" v-if="form.errors.has('Add2')" v-html="form.errors.get('Add2')" />
                                  <span class="error-message"> {{ errors[0] }}</span>
                                </div>
                              </div>
                            </ValidationProvider>
                            <ValidationProvider name="Email" mode="eager" rules="required" v-slot="{ errors }">
                              <div class="form-group row" style="padding-bottom: 10px">
                                <label class="col-lg-4 col-form-label text-right">Email <span style="color: red">*</span></label>
                                <div class="col-lg-8">
                                  <input name="Email" type="text" class="form-control" v-model="form.Email" />
                                  <div class="error" v-if="form.errors.has('Email')" v-html="form.errors.get('Email')" />
                                  <span class="error-message"> {{ errors[0] }}</span>
                                </div>
                              </div>
                            </ValidationProvider>
                          </div>

                          <div class="col-md-7">
                            <ValidationProvider name="ContactPerson" mode="eager" rules="required" v-slot="{ errors }">
                              <div class="form-group row" style="padding-bottom: 10px">
                                <label class="col-lg-4 col-form-label text-right">Contact Person</label>
                                <div class="col-lg-8">
                                  <input name="ContactPerson" type="text" class="form-control" v-model="form.ContactPerson" />
                                  <div class="error" v-if="form.errors.has('ContactPerson')" v-html="form.errors.get('ContactPerson')" />
                                  <span class="error-message"> {{ errors[0] }}</span>
                                </div>
                              </div>
                            </ValidationProvider>
                            <ValidationProvider name="Mobile" mode="eager" rules="required" v-slot="{ errors }">
                              <div class="form-group row" style="padding-bottom: 10px">
                                <label class="col-lg-4 col-form-label text-right">Mobile<span style="color: red">*</span></label>
                                <div class="col-lg-8 text-left">
                                  <input name="Mobile" type="text" class="form-control" v-model="form.Mobile" />
                                  <div class="error" v-if="form.errors.has('Mobile')" v-html="form.errors.get('Mobile')" />
                                  <span class="error-message"> {{ errors[0] }}</span>
                                </div>
                              </div>
                            </ValidationProvider>
                            <ValidationProvider name="ThanaCode" mode="eager" rules="required" v-slot="{ errors }">
                              <div class="form-group row" style="padding-bottom: 10px">
                                <label class="col-lg-4 col-form-label text-right">Thana<span style="color: red">*</span></label>
                                <div class="col-lg-8 text-left">
                                  <select name="ThanaCode" class="form-control" v-model="form.ThanaCode " style="margin: 0">
                                    <option :value="thana.UpazillaCode" v-for="(thana , index) in thanas" :key="index">{{ thana.UpazillaName }}</option>
                                  </select>
                                  <div class="error" v-if="form.errors.has('ThanaCode')" v-html="form.errors.get('ThanaCode')" />
                                  <span class="error-message"> {{ errors[0] }}</span>
                                </div>
                              </div>
                            </ValidationProvider>
                            <ValidationProvider name="NID" mode="eager" rules="" v-slot="{ errors }">
                              <div class="form-group row" style="padding-bottom: 10px">
                                <label class="col-lg-4 col-form-label text-right">N.I.D/Passport No./Birth Certificate<span style="color: red">*</span></label>
                                <div class="col-lg-8 text-left">
                                  <input name="NID" type="text" class="form-control" v-model="form.NID" />
                                  <div class="error" v-if="form.errors.has('NID')" v-html="form.errors.get('NID')" />
                                  <span class="error-message"> {{ errors[0] }}</span>
                                </div>
                              </div>
                            </ValidationProvider>
                            <ValidationProvider name="CustomerType" mode="eager" rules="required" v-slot="{ errors }">
                              <div class="form-group row" style="padding-bottom: 10px">
                                <label class="col-lg-4 col-form-label text-right">Customer Type<span style="color: red">*</span></label>
                                <div class="col-lg-8">
                                  <select name="CustomerType" class="form-control" v-model="form.CustomerType" style="margin: 0">
                                    <option :value="type.CustomerType" v-for="(type , index) in CustomerType" :key="index">{{ type.CustTypeName }}</option>
                                  </select>
                                  <div class="error" v-if="form.errors.has('CustomerType')" v-html="form.errors.get('CustomerType')" />
                                  <span class="error-message"> {{ errors[0] }}</span>
                                </div>
                              </div>
                            </ValidationProvider>
                            <ValidationProvider name="PaymentMode" mode="eager" rules="required" v-slot="{ errors }">
                              <div class="form-group row" style="padding-bottom: 10px">
                                <label class="col-lg-4 col-form-label text-right">Payment Mode<span style="color: red">*</span></label>
                                <div class="col-lg-8">
                                  <select name="PaymentMode" class="form-control" v-model="form.PaymentMode" style="margin: 0">
                                    <option value="CR">Credit</option>
                                    <option value="CA">Cash</option>
                                    <option value="DP">Deposit</option>
                                  </select>
                                  <div class="error" v-if="form.errors.has('PaymentMode')" v-html="form.errors.get('PaymentMode')" />
                                  <span class="error-message"> {{ errors[0] }}</span>
                                </div>
                              </div>
                            </ValidationProvider>
                          </div>
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
import {baseurl} from '../../../base_url'
import {Common} from "../../../mixins/common";
import moment from "moment";
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

export default {
  name: "Invoice",
  mixins: [Common],
  components: { DatePicker },
  data() {
    return {
      businesses: [],
      districts: [],
      thanas: [],
      CustomerType: [],
      mechanics: [],
      form: new Form({
        CustomerCode : '',
        CustomerName :'',
        Gender :'',
        Add1 :'',
        Add2 :'',
        DistrictCode :'',
        Email :'',
        ContactPerson :'',
        Mobile :'',
        ThanaCode :'',
        NID :'',
        Business :'',
        DepotCode :'',
        CustomerType :'',
        PaymentMode :'',
      }),
      errors: [],
      isLoading: false,
      buttonShow: false,
      PreLoader: false,
    }
  },
  created() {
    //
  },
  computed:{
    //
  },
  mounted() {
    document.title = 'Customer Create | DMS';
    this.getAllDistrict();
    this.getAllCustomerType();
  },
  methods: {
    onSubmit(){
      this.PreLoader = true;
      this.form.post(baseurl + "api/settings/customer-store", this.config()).then(response => {
        this.$toaster.success(response.data.message);
        this.PreLoader = false;
        this.$router.go(0)
      }).catch(e => {
        this.PreLoader = false;
        this.$toaster.error(e.data.message);
      });
    },

    getAllDistrict(){
      axios.get(baseurl + 'api/get-all-district', this.config() ).then((response)=>{
        this.districts = response.data.districts;
      }).catch((error)=>{

      })
    },
    districtWiseThana(){
      axios.get(baseurl+'api/district-wise-thana?DistrictCode=' + this.form.DistrictCode, this.config() ).then((response)=>{
        this.thanas = response.data.thanas
      }).catch((error)=>{

      })
    },
    getAllCustomerType(){
      axios.get(baseurl + 'api/settings/get-all-customer-type', this.config() ).then((response)=>{
        this.CustomerType = response.data.customerType;
      }).catch((error)=>{

      })
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