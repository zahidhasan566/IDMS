6<template>
  <div id="rat-page">
    <div class="modal fade" id="add-credit-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <div class="modal-title modal-title-font" id="exampleModalLabel">{{ title }}</div>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="closeModal">
              Close
            </button>
          </div>
          <ValidationObserver v-slot="{ handleSubmit }">
            <form class="form-horizontal" style="padding-bottom: 30px" id="formProduction" @submit.prevent="handleSubmit(onSubmit)"
                  @keydown.enter="$event.preventDefault()">
              <div class="modal-body">
                <div class="row">
                  <div class="col-12 col-md-4">
                    <ValidationProvider name="customerName" mode="eager" rules="required"
                                        v-slot="{ errors }">
                      <div class="form-group">
                        <label for="Customer">Customer Name <span class="error">*</span></label>
                        <select data-required="true"  name="customerName" class="form-control" v-model="customer">
                          <option value="">Select</option>
                          <option :value="item"   v-for="(item, index) in customers" :key="index">{{ item.CustomerCode }} -{{ item.CustomerName }}</option>
                        </select>
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-4">
                    <ValidationProvider name="Business" mode="eager" rules="required"
                                        v-slot="{ errors }">
                      <div class="form-group">
                        <label for="Business">Business <span class="error">*</span></label>
                        <select data-required="true"  name="Business" class="form-control" v-model="businessCode"  @change ="salesTypeFunction()">
                          <option value="">Select</option>
                          <option :value="item.Business" v-for="(item, index) in business" :key="index">{{ item.BusinessName }}</option>
                        </select>
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-4">
                    <ValidationProvider name="payment" mode="eager" rules="required" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="Payment">Payment<span class="error">*</span></label>
                        <input type="number" class="form-control"
                               id="payment"
                               v-model="payment" name="payment" placeholder="Payment Amount" min="1">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-4">
                    <ValidationProvider name="reference" mode="eager" rules="required"
                                        v-slot="{ errors }">
                      <div class="form-group">
                        <label for="Reference">Payment Type<span class="error">*</span></label>
                        <select data-required="true"  name="reference" class="form-control" v-model="reference">
                          <option value="">Select</option>
                          <option value="Cash">Cash</option>
                          <option value="RTGS">RTGS</option>
                          <option value="BEFTN">BEFTN</option>
                          <option value="Fund Transfer">Fund Transfer</option>
                          <option value="Agent Bank (Cash)">Agent Bank (Cash</option>
                          <option value="Agent Bank (Fund Transfer)">Agent Bank (Fund Transfer)</option>
                          <option value="Cheque Transfer">Cheque Transfer</option>
                        </select>
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-4">
                    <ValidationProvider name="paymentMode" mode="eager" rules="required" v-slot="{ errors }">
                      <label for="Payment">Payment Mode</label>
                      <select data-required="true"  name="paymentMode" class="form-control" v-model="paymentMode" @change="requiredData(paymentMode)">
                        <option value="">Select</option>
                        <option value="Online">Online</option>
                        <option value="Deposit Slip">Deposit Slip</option>
                      </select>
                      <span class="error-message"> {{ errors[0] }}</span>
                    </ValidationProvider>
                  </div>


                  <div class="col-12 col-md-4" >
                    <ValidationProvider name="chequeNo" mode="eager" v-slot="{ errors }">
                      <div class="form-group">
                        <label for="Cheque">Cheque No/Transection No<span class="error">*</span></label>
                        <input type="text" class="form-control"
                               id="chequeNo"
                               :required="false"
                               v-model="chequeNo" name="chequeNo" placeholder="cheque No">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>

                  <div class="col-12 col-md-4">
                    <ValidationProvider name="Cheque Date" rules="required"
                                        mode="eager" v-slot="{ errors }">
                      <label for="chequeDate">Deposited / Cheque Date <span class="error">*</span></label>
                      <input type="date" class="form-control" :max="maxDate"  data-required="true" v-model="chequeDate" name="toDate">
                      <span class="error-message"> {{ errors[0] }}</span>
                    </ValidationProvider>
                  </div>

                  <div class="col-12 col-md-4">
                    <ValidationProvider name="bank" mode="eager" rules="required"
                                        v-slot="{ errors }">
                      <div class="form-group">
                        <label for="Bank">Bank <span class="error">*</span></label>
                        <multiselect v-model="bankCode"
                                     :options="banks"
                                     :multiple="false"
                                     :close-on-select="true"
                                     :clear-on-select="false"
                                     :preserve-search="true"
                                     placeholder="Bank list"
                                     label="BankName" track-by="BankCode">
                        </multiselect>
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>

                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-4">
                    <ValidationProvider name="branch" mode="eager" rules="required"
                                        v-slot="{ errors }">
                      <div class="form-group">

                        <label for="tagNo">Deposited Branch<span class="error">*</span></label>
                        <input type="text" class="form-control"
                               id="branch"
                               data-required="true"
                               v-model="branch" name="Branch" placeholder="Branch">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-4">
                      <div class="form-group">
                        <label for="chequeImage">Cheque Image<span class="error">*</span></label>
                        <input type="file" class="form-control"
                               @change="fileUpload($event)"
                               id="chequeImage"
                               name="chequeImage" accept=".png, .jpg, .jpeg" required>
                        <img v-if="chequeImage" :src="showImage(chequeImage)" alt="" height="40px" width="40px">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>

                  </div>
                </div>
              </div>
              <div class="col-md-12" style="text-align: end;margin-top:10px">
                <submit-form v-if="buttonShow" :name="buttonText"/>
              </div>
            </form>
          </ValidationObserver>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import {bus} from "../../app";
import {Common} from "../../mixins/common";
import {mapGetters} from "vuex";
import moment from "moment";
import {baseurl} from "../../base_url";

export default {
  mixins: [Common],
  data() {
    return {
      title: '',
      buttonText: '',
      customers: [],
      business: [],
      banks: [],
      user: [],
      sales: [],
      customer:'',
      businessCode:'',
      reference:'',
      paymentMode:'',
      chequeNo:'',
      bankCode:'',
      chequeDate:'',
      chequeImage:'',
      branch:'',
      payment:0,
      status: '',
      confirm: '',
      type: 'add',
      actionType: '',
      buttonShow: false,
      selected: false,
      requiredStatus: true,
      maxDate: this.getTodayDate(), // Max date for input

      errors: [],
    }
  },
  computed: {},
  created() {},
  mounted() {
    this.getCustomerWiseBusiness()
    this.getCustomer()
    this.getBanks()

    $('#add-credit-modal').modal({backdrop: 'static', keyboard: false});
    $('#add-credit-modal').on('hidden.bs.modal', () => {
      this.$emit('changeStatus')
    });
    bus.$on('add-credit-payment', (row) => {
      this.title = 'Add Credit Payment';
      this.buttonText = "Add";
      this.transferNo = '';

      this.status = '';
      this.buttonShow = true;
      this.actionType = 'add'
      $("#add-credit-modal").modal("toggle");
    })
  },
  destroyed() {
    bus.$off('add-credit-payment')
  },
  methods: {
    requiredData(val){
      if (val === "Online"){
        this.requiredStatus = false ;
      }else {
        this.requiredStatus = true
      }
    },
      getTodayDate() {
          const today = new Date();
          const year = today.getFullYear();
          const month = String(today.getMonth() + 1).padStart(2, '0');
          const day = String(today.getDate()).padStart(2, '0');
          return `${year}-${month}-${day}`;
      },
    getCustomer() {
      let instance = this;
      this.axiosGet('payment/get-customer-list', function (response) {
          console.log(response)
        instance.customers = response.data;
      }, function (error) {
      });
    },
    getCustomerWiseBusiness() {
      let instance = this;
      this.axiosGet('payment/get-customer-wise-business', function (response) {
        instance.business = response.data;
      }, function (error) {
      });
    },
    getBanks() {
      let instance = this;
      this.axiosGet('payment/get-all-bank', function (response) {
        instance.banks = response.data;
      }, function (error) {
      });
    },
    salesTypeFunction(){
      let instance = this;
      this.axiosGet('payment/get-sales-type?businessCode='+this.businessCode , function (response) {
        instance.sales = response.data;
      }, function (error) {
      });
    },
    closeModal() {
      $("#add-credit-modal").modal("toggle");
    },
    fileUpload(e) {
      var input = e.target
      var file = input.files[0]
      if (file.size) {
        this.processImage(file)
      }
    },
    processImage(file) {
      let instance = this
      var reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = function () {
        instance.chequeImage = reader.result
        instance.AttachmentFlag = 1
      };
      reader.onerror = function (error) {
        console.log('Error: ', error);
      };
    },
    showImage() {
      let img = this.chequeImage;
      if (img.length > 100) {
        return this.chequeImage;
      } else {
        return baseurl + "images/payment/" + this.chequeImage;
      }
    },
    onSubmit() {
      this.$store.commit('submitButtonLoadingStatus', true);
      this.axiosPost('payment/store-credit-payment', {
        customer: this.customer,
        businessCode: this.businessCode,
        reference: this.reference,
        chequeNo: this.chequeNo,
        bankCode: this.bankCode,
        chequeDate: this.chequeDate,
        chequeImage: this.chequeImage,
        branch: this.branch,
        payment: this.payment,
        paymentMode: this.paymentMode,
        active: this.active,
      }, (response) => {
        this.successNoti(response.message);
        $("#add-credit-modal").modal("toggle");
        bus.$emit('refreshDatatable');
        this.$store.commit('submitButtonLoadingStatus', false);
      }, (error) => {
        this.errorNoti(error);
        this.$store.commit('submitButtonLoadingStatus', false);
      })

    }
  }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css">
.card-header {
  background: linear-gradient(269deg, rgb(0 0 0), #007bffb8) !important;
}
</style>
<style>
.datepicker .vue-input, .date-range-picker .vue-input, .timepicker .vue-input, .datetime-picker .vue-input {
  padding: 7px !important;
}
</style>
