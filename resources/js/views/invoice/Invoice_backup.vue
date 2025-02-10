<template>
  <div class="container-fluid">
    <breadcrumb :options="['Invoice Create']"></breadcrumb>

    <div class="row">
      <div class="col-xl-12">
        <div class="card">
          <div class="datatable" v-if="!isLoading">
            <div class="card-body" id="customer_form">
              <ValidationObserver v-slot="{ handleSubmit }">
                <form @submit.prevent="handleSubmit(onSubmit)" @keydown.enter="$event.preventDefault()">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="row form-divider m-b-15">
                        <div class="form-divider-title">
                          <p style="width: 160px">Bike Details</p>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="ChassisNo" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Chassis No.</label>
                              <input type="text" class="form-control" name="ChassisNo" v-model="form.ChassisNo" @change="doCheckChassisNo">
                              <div class="error" v-if="form.errors.has('ChassisNo')" v-html="form.errors.get('ChassisNo')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="EngineNo" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Engine No.</label>
                              <input type="text" class="form-control" readonly name="EngineNo" v-model="form.EngineNo">
                              <div class="error" v-if="form.errors.has('EngineNo')" v-html="form.errors.get('EngineNo')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="ProductCode" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Product Code.</label>
                              <input type="text" class="form-control" readonly name="ProductCode" v-model="form.ProductCode">
                              <div class="error" v-if="form.errors.has('ProductCode')" v-html="form.errors.get('ProductCode')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="Model" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Model.</label>
                              <input type="text" class="form-control" readonly name="Model" v-model="form.Model">
                              <div class="error" v-if="form.errors.has('Model')" v-html="form.errors.get('Model')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="Color" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Color.</label>
                              <input type="text" class="form-control" readonly name="Color" v-model="form.Color">
                              <div class="error" v-if="form.errors.has('Color')" v-html="form.errors.get('Color')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="Price" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Price.</label>
                              <input type="text" class="form-control" readonly name="Price" v-model="form.Price">
                              <div class="error" v-if="form.errors.has('Price')" v-html="form.errors.get('Price')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="Discount" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Discount.</label>
                              <input type="number" class="form-control" name="Discount" v-model="form.Discount">
                              <div class="error" v-if="form.errors.has('Discount')" v-html="form.errors.get('Discount')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2">
                          <ValidationProvider name="SalesType" mode="eager" rules="required" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Sales Type</label>
                              <select name="SalesType" class="form-control" v-model="form.SalesType" @change="changeSalesType">
                                <option value="cash">Cash</option>
                                <option value="credit">Credit</option>
                                <option value="exchange">Exchange</option>
                                <option value="test_ride">Test Ride</option>
                              </select>
                              <div class="error" v-if="form.errors.has('SalesType')" v-html="form.errors.get('SalesType')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2" v-if="CreditFieldEnable">
                          <ValidationProvider name="CreditAmount" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Credit Amount.</label>
                              <input type="number" class="form-control" name="CreditAmount" v-model="form.CreditAmount">
                              <div class="error" v-if="form.errors.has('CreditAmount')" v-html="form.errors.get('CreditAmount')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
                        <div class="col-12 col-md-2" v-if="CreditFieldEnable">
                          <ValidationProvider name="Tenure" mode="eager" rules="" v-slot="{ errors }">
                            <div class="form-group">
                              <label>Tenure.</label>
                              <input type="number" class="form-control" name="Tenure" v-model="form.Tenure">
                              <div class="error" v-if="form.errors.has('Tenure')" v-html="form.errors.get('Tenure')" />
                              <span class="error-message"> {{ errors[0] }}</span>
                            </div>
                          </ValidationProvider>
                        </div>
<!--                        <div class="col-12 col-md-4">-->
<!--                          <ValidationProvider name="Business" mode="eager" rules="required" v-slot="{ errors }">-->
<!--                            <div class="form-group">-->
<!--                              <label>Business</label>-->
<!--                              <select name="Business" class="form-control" v-model="form.Business" data-index="1" data-required="true">-->
<!--                                <option :value="business.Business" v-for="(business , index) in businesses" :key="index">{{ business.BusinessName }}</option>-->
<!--                              </select>-->
<!--                              <div class="error" v-if="form.errors.has('Business')" v-html="form.errors.get('Business')" />-->
<!--                              <span class="error-message"> {{ errors[0] }}</span>-->
<!--                            </div>-->
<!--                          </ValidationProvider>-->
<!--                        </div>-->
                      </div>
                    </div>
                  </div>
                  <br>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="row form-divider m-b-15">
                        <div class="form-divider-title">
                          <p style="width: 200px">Customer Details</p>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group row">
                              <label for="txtFirstNameBilling" class="col-lg-3 col-form-label">Contact Person</label>
                              <div class="col-lg-6">
                                <input id="txtFirstNameBilling" name="txtFirstNameBilling" type="text" class="form-control" />
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group row">
                              <label for="txtLastNameBilling" class="col-lg-3 col-form-label">Mobile No.</label>
                              <div class="col-lg-9"><input id="txtLastNameBilling" name="txtLastNameBilling" type="text" class="form-control" /></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-primary float-right">Submit</button>
                </form>
              </ValidationObserver>
              <br>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import {baseurl} from '../../base_url'
import {Common} from "../../mixins/common";
import moment from "moment";
export default {
  name: "Invoice",
  mixins: [Common],
  data() {
    return {
      businesses: [],
      invoices: [],
      pagination: {
        current_page: 1,
        from: 1,
        to: 1,
        total: 1,
      },
      form: new Form({
        ChassisNo :'',
        EngineNo :'',
        ProductCode :'',
        Model :'',
        Color :'',
        Price :'',
        Discount : 0,
        SalesType :'cash',
        CreditAmount : 0,
        Tenure : 0,
        PaymentDate : moment().format('yyyy-MM-DD'),
      }),
      errors: [],
      isLoading: false,
      buttonShow: false,
      CreditFieldEnable: false,
    }
  },
  created() {
    //
  },
  mounted() {
    document.title = 'Invoice Create | DMS';
    //this.getAllUserBusiness();
  },
  methods: {
    onSubmit(){
      this.form.post(baseurl + "api/payment/submit-cash-payment", this.config()).then(response => {
        if (response.data.status === 'success'){
          this.$toaster.success(response.data.message);
          window.location.href = baseurl + 'payment/cash-payment-create';
        }else {
          this.$toaster.error(response.data.message);
        }
      }).catch(e => {
        this.$toaster.error(e.data.message);
      });
    },
    doCheckChassisNo(){
      this.form.post(baseurl + "api/check-chassis", this.config()).then(response => {
        console.log(response.data)
        if (response.data.status === 'success'){
          this.$toaster.success(response.data.message);
          this.form.EngineNo = response.data.chassis_info.EngineNo
          this.form.ProductCode = response.data.chassis_info.ProductCode
          this.form.Model = response.data.chassis_info.ProductName
          this.form.Color = response.data.chassis_info.Color
          this.form.Price = response.data.chassis_info.UnitPrice
        }else {
          this.form.EngineNo = ''
          this.form.ProductCode = ''
          this.form.Model = ''
          this.form.Color = ''
          this.form.Price = ''
          this.$toaster.error(response.data.message);
        }
      }).catch(e => {
        this.$toaster.error(e.data.message);
      });
    },
    getAllUserBusiness(){
      axios.get(baseurl + 'api/get-all-user-business', this.config() ).then((response)=>{
        this.businesses = response.data.user_business;
      }).catch((error)=>{

      })
    },
    changeSalesType(){
      if (this.form.SalesType === 'credit'){
        this.CreditFieldEnable = true;
      }else {
        this.CreditFieldEnable = false;
      }
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