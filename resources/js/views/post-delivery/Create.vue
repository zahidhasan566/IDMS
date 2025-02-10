<template>
  <div class="container-fluid" id="spare-parts">
    <breadcrumb :options="['Post Delivery Checklist']"></breadcrumb>

    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body" id="delivery_form">
            <ValidationObserver v-slot="{ handleSubmit }">
              <form @submit.prevent="handleSubmit(onSubmit)" @keydown.enter="$event.preventDefault()">
                <div class="row">
                  <div class="col-md-3">
                    <div class="row form-divider m-b-15">
                      <div class="form-divider-title">
                        <p style="width: 160px">Customer</p>
                      </div>
                      <div class="col-12">
                          <div class="form-group">
                              <label for="chassis">Dealer Code</label>
                             <span style="padding-left: 25px;color: red">{{me.UserId}}</span>
                          </div>

                        <ValidationProvider name="Customer Name" mode="eager" rules="required" v-slot="{ errors }">
                          <div class="form-group">
                            <label for="customerName">Customer Name<span style="color: red">*</span></label>
                            <input type="text" class="form-control" name="customerName" id="customerName"
                                   v-model="customerName">
                            <span class="error-message"> {{ errors[0] }}</span>
                          </div>
                        </ValidationProvider>
                        <ValidationProvider name="Customer Mobile" mode="eager" rules="required" v-slot="{ errors }">
                          <div class="form-group">
                            <label for="Customer Mobile">Customer Mobile</label>
                            <input type="text" class="form-control" name="customerMobile" id="customerMobile"
                                   v-model="customerMobile">
                            <span class="error-message"> {{ errors[0] }}</span>
                          </div>
                        </ValidationProvider>
                        <ValidationProvider name="Chassis No" mode="eager" rules="required" v-slot="{ errors }">
                          <div class="form-group">
                            <label for="chassis">Chassis No</label>
                            <input type="text" class="form-control" name="chassis" id="chassis"
                                   v-model="chassisNo">
                            <span class="error-message"> {{ errors[0] }}</span>
                          </div>
                        </ValidationProvider>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-9">
                    <div class="row m-b-15">
                      <div class="col-12 datatable">
                        <table
                            class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm">
                          <tr class="thead-dark">
                            <th>Serial</th>
                            <th>Inspection Areas</th>
                            <th>Inquiry</th>
                            <th>Yes</th>
                            <th>No</th>
                            <th>Remarks</th>
                          </tr>
                          <tbody v-if="fields.length > 0">
                          <tr v-for="(f,i) in fields" :key="i">
                            <td style="text-align: right;">{{ f.ChecklistId }}</td>
                            <td>{{ f.InspectionAreas }}</td>
                            <td>{{ f.Inquiry }}</td>
                            <td>
                              <input type="radio" :name="`status-${i}`" value="Y" v-model="f.Status">
                            </td>
                            <td>
                              <input type="radio" :name="`status-${i}`" value="N" v-model="f.Status">
                            </td>
                            <td>
                              <textarea class="form-control" v-model="f.Remarks"></textarea>
                            </td>
                          </tr>
                          </tbody>
                          <tbody v-else>
                          <tr>
                            <td colspan="6" style="text-align: center">No Items</td>
                          </tr>
                          </tbody>
                        </table>
                        <submit-form v-if="buttonShow" :name="buttonText"/>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </ValidationObserver>
            <br>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {baseurl} from '../../base_url';
import {Common} from "../../mixins/common";
import moment from "moment";
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import "vue-select/dist/vue-select.css";
import Dropdown from 'vue-simple-search-dropdown';
import PostDeliveryPrint from "./PostDeliveryPrint.vue";
export default {
  name: "Invoice",
  mixins: [Common],
  components: {DatePicker, Dropdown},
  data() {
    return {
      fields: [],
      customerName: '',
      customerMobile: '',
      chassisNo: '',
      buttonShow: true,
      buttonText: 'Submit'
    }
  },
  created() {
    //
  },
  mounted() {
    document.title = 'Create Post Delivery Checklist | DMS';
    this.getData();
  },
    computed: {
        me() {
            return this.$store.state.me
        }
    },
  methods: {
    getData() {
      this.axiosGet('post-delivery/create', (response) => {
        this.fields = response.data
      }, (error) => {

      })
    },
    onSubmit() {
      this.$store.commit('submitButtonLoadingStatus', true);
      this.axiosPost('post-delivery/store',{
        customerName: this.customerName,
        customerMobile: this.customerMobile,
        chassisNo: this.chassisNo,
        fields: this.fields
      },(response) => {
          console.log(response)
        this.$router.push({name: 'PostDeliveryPrint', params: {inquiryId: response.inquiryId}})
        this.clearForm()
        this.$store.commit('submitButtonLoadingStatus', false);
      },(error) => {
        this.$store.commit('submitButtonLoadingStatus', false);
        this.errorNoti(error.response.data.message)
      })
    },
    clearForm() {
      this.customerName = ''
      this.customerMobile = ''
      this.chassisNo = ''
      this.fields = []
      this.getData()
    },
    checkout() {
      if (this.fields.length > 0) {
        this.axiosPost('invoice-spare-parts/checkout',{
          customerName: this.form.customerName,
          customerAddress: this.form.customerAddress,
          customerMobile: this.form.customerMobile,
          affiliator: this.form.affiliator,
          reference: this.form.reference,
          affiliatorDiscount: this.form.affiliatorDiscount,
          fields: this.fields
        },(response) => {
          this.clearForm()
          this.form.affiliator = ''
          this.form.affiliatorDiscount = 0
          this.form.reset()
          this.form.clear()
          this.fields = []
          this.successNoti(response.message)
          this.$router.push({name: 'InvoiceSparePartsPrint', params: {invoiceId: response.invoiceId}})
        },(error) => {
          this.errorNoti(error.response.data.message)
        })
      }
    }
  }
}
</script>

<style scoped>
#spare-parts #quantity {
  font-size: 20px;
  margin-left: -10px;
  margin-right: -10px;
  z-index: 0;
  text-align: center;
}

#delivery_form .form-control {
  font-size: 10px;
  height: 29px;
}

#delivery_form .form-group {
  margin-bottom: 0;
}

#delivery_form label {
  font-size: 11px !important;
  margin-bottom: 0 !important;
  margin-top: 10px !important;
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

input[type="radio"] {
  cursor: pointer;
}

</style>