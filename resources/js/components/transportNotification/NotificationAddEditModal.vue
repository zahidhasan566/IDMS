<template>
  <div id="rat-page">
    <div class="modal fade" id="add-edit-dept" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <div class="modal-title modal-title-font" id="exampleModalLabel">{{ title }}</div>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="closeModal">
              Close
            </button>
          </div>
          <ValidationObserver v-slot="{ handleSubmit }">
            <form class="form-horizontal" style="padding-bottom: 30px" id="formProduction"
                  @submit.prevent="handleSubmit(onSubmit)"
                  @keydown.enter="$event.preventDefault()">
              <div class="modal-body">
                <div class="row">
                  <div class="col-12 col-md-4">
                    <ValidationProvider name="challanNo" mode="eager" rules="required"
                                        v-slot="{ errors }">
                      <div class="form-group">

                        <label for="challanNo">Challan No</label>
                        <input type="text" class="form-control"
                               id="challanNo"
                               v-model="challanNo" name="challanNo" data-index="0" placeholder="Challan No"
                               @input="getAllChallanInfo($event)">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-4">
                    <ValidationProvider name="dealerName" mode="eager" rules="required"
                                        v-slot="{ errors }">
                      <div class="form-group">
                        <label for="dealerName">Dealer Name<span class="error">*</span></label>
                        <multiselect v-model="dealer" :options="dealers"
                                     :multiple="false"
                                     :close-on-select="true"
                                     :clear-on-select="false" :preserve-search="false"
                                     placeholder="Select Dealer"
                                     label="CustomerName" track-by="CustomerCode">

                        </multiselect>
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>

                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-4">
                    <ValidationProvider name="phone" mode="eager" rules="required"
                                        v-slot="{ errors }">
                      <div class="form-group">

                        <label for="phone">Dealer Mobile No</label>
                        <input type="number" class="form-control"
                               id="phone"
                               data-required="false"
                               v-model="phone" name="phone" placeholder="Phone No"
                               maxlength="11" >
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-4">
                    <ValidationProvider name="transport" mode="eager" rules="required"
                                        v-slot="{ errors }">
                      <div class="form-group">
                        <label for="transport">Transport Name<span class="error">*</span></label>
                        <multiselect v-model="transport" :options="transports"
                                     :multiple="false"
                                     :close-on-select="true"
                                     :clear-on-select="false" :preserve-search="false"
                                     placeholder="Select Transport"
                                     label="TransportName" track-by="TransportID">

                        </multiselect>
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>

                    </ValidationProvider>
                  </div>

                  <div class="col-12 col-md-4">
                    <ValidationProvider name="contact No" mode="eager" rules="required"
                                        v-slot="{ errors }">
                      <div class="form-group">

                        <label for="contactNo">Contact No</label>
                        <input type="number" class="form-control"
                               id="contactNo"
                               data-required="false"
                               v-model="contactNo" name="contactNo" placeholder="Contact No" maxlength="11"
                               pattern="\d{11}">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>

                  <div class="col-12 col-md-4">
                    <ValidationProvider name="driverName" mode="eager" rules="required"
                                        v-slot="{ errors }">
                      <label for="driverName">Driver Name</label>
                      <input type="text" class="form-control"
                             id="driverName"
                             data-required="true"
                             v-model="driverName" name="driverName" placeholder="Driver Name">
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-4">
                    <ValidationProvider name="driverContactNo" mode="eager" rules="required"
                                        v-slot="{ errors }">
                      <div class="form-group">

                        <label for="driverContactNo">Driver Contact No</label>
                        <input type="text" class="form-control"
                               id="driverContactNo"
                               data-required="false"
                               v-model="driverContactNo" name="driverContactNo" placeholder="Driver Contact No"
                               maxlength="11" pattern="\d{11}">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-4">
                    <ValidationProvider name="truckNo" mode="eager" rules="required"
                                        v-slot="{ errors }">
                      <div class="form-group">

                        <label for="truckNo">Truck No.</label>
                        <input type="text" class="form-control"
                               id="truckNo"
                               data-required="false"
                               v-model="truckNo" name="truckNo" placeholder="Truck No">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-4">
                    <ValidationProvider name="Address" mode="eager" rules="required"
                                        v-slot="{ errors }">
                      <div class="form-group">

                        <label for="approximateDeliveryTime">Approximate Delivery Time</label>
                        <input type="datetime-local" class="form-control" id="approximateDeliveryTime"
                               data-required="false" v-model="approximateDeliveryTime" name="approximateDeliveryTime">

                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
                  </div>
                  <div class="col-12 col-md-4">
                    <ValidationProvider name="mobile" mode="eager" rules="required"
                                        v-slot="{ errors }">
                      <div class="form-group">

                        <label for="MOTMNumber">MO/TM Number </label>
                        <input type="number" class="form-control"
                               id="MOTMNumber"
                               data-required="true"
                               v-model="MOTMNumber" name="MOTMNumber" placeholder="MOTM Number">
                        <span class="error-message"> {{ errors[0] }}</span>
                      </div>
                    </ValidationProvider>
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
import {instances} from "chart.js";
import moment from "moment/moment";
import Login from "../../views/auth/Login.vue";

export default {
  mixins: [Common],
  data() {
    return {
      transports: [],
      dealers: [],
      challans: [],
      numbers: [],
      title: '',
      buttonText: '',
      status: '',
      confirm: '',
      type: 'add',
      actionType: '',
      buttonShow: false,
      errors: [],
      defaultAgents: '',
      dealer: '',
      challanNo: '',
      driverName: '',
      driverContactNo: '',
      phone: '',
      transport: '',
      truckNo: '',
      contactNo: '',
      MOTMNumber: '',
      approximateDeliveryTime: '',
      notificationID: '',
      TransportID: '',
      TransportD: '',
    }
  },
  computed: {},
  created() {
  },
  mounted() {
    let date = new Date()
    this.approximateDeliveryTime = this.dateTimeFormatHTML(date)
    this.getDealerList()
    this.getTransportList()
    $('#add-edit-dept').modal({backdrop: 'static', keyboard: false});
    $('#add-edit-dept').on('hidden.bs.modal', () => {
      this.$emit('changeStatus')
    });
    bus.$on('add-edit-notify', (row) => {
      this.notificationID = row.NotificationID;
      if (row) {
        let instance = this;
        this.axiosGet('transport-notification/edit/' + this.notificationID, function (response) {
          instance.title = 'Update Transport Notification';
          instance.buttonText = "Update";
          instance.buttonShow = true;
          instance.actionType = 'edit';
          if (response) {
            console.log(response)
            instance.challanNo = response.list.ChallanNo;
            instance.contactNo = response.list.ContactNo;
            instance.customerCode = response.list.CustomerCode;
            instance.phone = response.list.CustomerMobile;
            instance.deliveryDate = response.list.DeliveryDate;
            instance.deliveryTime = response.list.DeliveryTime;
            instance.driverContactNo = response.list.DriverContactNo;
            instance.driverName = response.list.DriverName;
            instance.MOTMNumber = response.list.MOTMNumber;
            instance.truckNo = response.list.TruckNo;
            instance.dealer = [{
              'CustomerCode': response.list.CustomerCode,
              'CustomerName': response.list.CustomerName,
            }
            ];
            instance.transport = [{
              'TransportID': response.list.TransportID,
              'TransportName': response.list.TransportName,
            }
            ];

          }
        }, function (error) {

        });
      } else {
        this.title = 'Add Transport Notification';
        this.buttonText = "Add";
        this.transferNo = '';

        this.status = '';
        this.buttonShow = true;
        this.actionType = 'add'
      }
      $("#add-edit-dept").modal("toggle");
    })

  },
  destroyed() {
    bus.$off('add-edit-notify')
  },
  methods: {
      dateTimeFormatHTML(date) {
        let localDate = new Date(date - date.getTimezoneOffset() * 60000);
        localDate.setSeconds(null);
        localDate.setMilliseconds(null);
        return localDate.toISOString().slice(0, -1);
      },
    // CHHC2200002
    closeModal() {
      $("#add-edit-dept").modal("toggle");
    },
    getAllChallanInfo(e) {
      let instance = this;
      instance.challanNo = e.target.value
      this.axiosGet('transport-notification/get-challan-information?ChallanNo=' + instance.challanNo + '&CustomerCode=' + this.dealer, function (response) {
        instance.dealer = [{
          'CustomerCode': response.numbers[0].CustomerCode,
          'CustomerName': response.numbers[0].CustomerName,
        }];
        instance.phone = response.numbers[0].Phone;
        instance.driverName = response.challans.DriverName;
        instance.driverContactNo = response.challans.DriverPhoneNo;

        instance.truckNo = response.challans.TransportNo;
        instance.MOTMNumber = response.numbers[0].MOTMNumber;
        this.getTransportList(transport);
      }, function (error) {
      });
    },
    getDealerList() {
      let instance = this;
      this.axiosGet('transport-notification/get-all-dealer', function (response) {
        instance.dealers = response.dealer;
      }, function (error) {
      });
    },
    getTransportList(transport) {
      let instance = this;
      this.axiosGet('transport-notification/get-transport-list', function (response) {
        instance.transports = response.list;
      }, function (error) {
      });
    },

    onSubmit() {
      this.$store.commit('submitButtonLoadingStatus', true);
      let url = '';
      var returnData = $('#return').prop('checked');
      var submitUrl = '';
      if (this.actionType === 'add') {
        this.TransportD=this.transport['TransportID'];
        submitUrl = 'transport-notification/store';
      }
      if (!returnData && this.actionType === 'edit') {
        this.TransportD=this.transport[0].TransportID;
        // console.log('sdsd',this.transport[0].TransportID)
        submitUrl = 'transport-notification/store';
      }
      this.axiosPost(submitUrl, {
        ActionType: this.actionType,
        NotificationID: this.notificationID,
        ChallanNo: this.challanNo,
        CustomerCode: this.dealer[0]['CustomerCode'],
        CusCode: this.dealer['CustomerCode'],
        CustomerMobile: this.phone,
        TransportID: this.TransportD,
        ContactNo: this.contactNo,
        DriverName: this.driverName,
        TruckNo: this.truckNo,
        DriverContactNo: this.driverContactNo,
        DeliveryDate: this.approximateDeliveryTime,
        DeliveryTime: this.approximateDeliveryTime,
        MOTMNumber: this.MOTMNumber,

      }, (response) => {
        this.successNoti(response.message);
        $("#add-edit-dept").modal("toggle");
        bus.$emit('refresh-datatable');
        this.$store.commit('submitButtonLoadingStatus', false);
      }, (error) => {
        this.errorNoti(error);
        this.$store.commit('submitButtonLoadingStatus', false);
      })


    }
  }
}
</script>

<style src="../../../../node_modules/vue-multiselect/dist/vue-multiselect.min.css">
.card-header {
  background: linear-gradient(269deg, rgb(0 0 0), #007bffb8) !important;
}
</style>
<style>
.datepicker .vue-input, .date-range-picker .vue-input, .timepicker .vue-input, .datetime-picker .vue-input {
  padding: 7px !important;
}
</style>
