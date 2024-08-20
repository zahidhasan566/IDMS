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
                        <form class="form-horizontal" style="padding-bottom: 30px" id="formProduction" @submit.prevent="handleSubmit(onSubmit)"
                              @keydown.enter="$event.preventDefault()">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <ValidationProvider name="agentName" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <div class="form-group">

                                                <label for="agentName">Agent Name<span class="error">*</span></label>
                                                <input type="text" class="form-control"
                                                       id="agentName"
                                                       data-required="true"
                                                       v-model="agentName" name="agentName" data-index="0" placeholder="Agent Name">
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <ValidationProvider name="Mobile" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                          <div class="form-group">

                                            <label for="Mobile">Agent Mobile</label>
                                            <input type="number" class="form-control"
                                                   id="Mobile"
                                                   data-required="true"
                                                   v-model="Mobile" name="Mobile" placeholder="Mobile" maxlength="11"  pattern="\d{11}">
                                            <span class="error-message"> {{ errors[0] }}</span>
                                          </div>
                                        </ValidationProvider>
                                    </div>

                                  <div class="col-12 col-md-4">
                                    <ValidationProvider name="bank" mode="eager" rules="required"
                                                        v-slot="{ errors }">
                                      <div class="form-group">
                                        <label for="yrcRegion">YRC Region <span class="error">*</span></label>
                                        <multiselect v-model="defaultYRCRegion" :options="yrcRegions"
                                                     :multiple="false"
                                                     :close-on-select="true"
                                                     :clear-on-select="false" :preserve-search="false"
                                                     placeholder="Select YRCRegion"
                                                     label="YRCRegion" track-by="YRCRegion">

                                        </multiselect>
                                        <span class="error-message"> {{ errors[0] }}</span>
                                      </div>

                                    </ValidationProvider>
                                  </div>
                                  <div class="col-12 col-md-4">
                                    <ValidationProvider name="dealerPoint" mode="eager" rules="required"
                                                        v-slot="{ errors }">
                                      <div class="form-group">
                                        <label for="dealerPoint">Dealer Point <span class="error">*</span></label>
                                        <multiselect v-model="defaultDealerPoint" :options="customers"
                                                     :multiple="false"
                                                     :close-on-select="true"
                                                     :clear-on-select="false" :preserve-search="false"
                                                     placeholder="Select Dealer"
                                                     label="CustomerInfo" track-by="CustomerCode">

                                        </multiselect>
                                        <span class="error-message"> {{ errors[0] }}</span>
                                      </div>

                                    </ValidationProvider>
                                  </div>
                                  <div class="col-12 col-md-4">
                                    <ValidationProvider name="PresentBike" mode="eager" rules="required"
                                                        v-slot="{ errors }">
                                      <div class="form-group">
                                        <label for="PresentBike">Present Bike<span class="error">*</span></label>

                                        <multiselect v-model="defaultPresentBike" :options="presentBikes"
                                                     :multiple="false"
                                                     :close-on-select="true"
                                                     :clear-on-select="false" :preserve-search="false"
                                                     placeholder="Select Present Bike"
                                                     label="PresentBike" track-by="PresentBike">

                                        </multiselect>
                                        <span class="error-message"> {{ errors[0] }}</span>
                                      </div>

                                    </ValidationProvider>
                                  </div>
                                  <div class="col-12 col-md-4">
                                    <ValidationProvider name="chassisNo" mode="eager" rules="required"
                                                        v-slot="{ errors }">
                                      <div class="form-group">
                                        <div class="form-group">
                                          <label for="chassisNo">Chassis No</label>
                                          <input type="text" class="form-control"
                                                 id="chassisNo"
                                                 data-required="true"
                                                 v-model="chassisNo" name="chassisNo" placeholder="chassisNo">
                                          <span class="error-message"> {{ errors[0] }}</span>
                                        </div>
                                      </div>
                                    </ValidationProvider>
                                  </div>
                                  <div class="col-12 col-md-4">
                                    <ValidationProvider name="profession" mode="eager" rules="required"
                                                        v-slot="{ errors }">
                                      <div class="form-group">

                                        <label for="profession">Profession</label>
                                        <input type="text" class="form-control"
                                               id="profession"
                                               data-required="false"
                                               v-model="profession" name="profession" placeholder="profession">
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
export default {
    mixins: [Common],
    data() {
        return {
            yrcRegions: [],
            customers: [],
            presentBikes: [],

            title: '',
            buttonText: '',
            status: '',
            confirm: '',
            type: 'add',
            actionType: '',
            buttonShow: false,
            errors: [],

            agentName:'',
            dealerPoint:'',
            defaultPresentBike:'',
            defaultYRCRegion:'',
            defaultDealerPoint:'',
            chassisNo:'',
            profession:'',
            Mobile:'',
            agentId:'',


        }
    },
    computed: {},
    created() {},
    mounted() {

      this.getAllList()
        $('#add-edit-dept').modal({backdrop: 'static', keyboard: false});
        $('#add-edit-dept').on('hidden.bs.modal', () => {
            this.$emit('changeStatus')
        });
        bus.$on('add-edit-testrideagents', (row) => {
          this.agentId = row.AgentId;
            if (row) {
                let instance = this;
                this.axiosGet('test-ride/edit/' + this.agentId , function (response) {
                    instance.title = 'Update Test Ride Agent';
                    instance.buttonText = "Update";
                    instance.buttonShow = true;
                    instance.actionType = 'edit';
                    if(response){
                        instance.agentName= response.agents.Name;
                        instance.Mobile = response.agents.Mobile;
                        instance.chassisNo = response.agents.ChassisNo;
                        instance.profession = response.agents.Profession;
                        instance.defaultPresentBike= [{
                          'PresentBike': response.agents.PresentBike,
                        }
                        ];
                        instance.defaultYRCRegion= [{
                          'YRCRegion': response.agents.YRCRegion,
                        }
                        ];
                        instance.defaultDealerPoint= [{
                          'CustomerCode': response.agents.DealerId,
                          'CustomerName': response.agents.CustomerName,
                          'CustomerInfo': response.agents.CustomerInfo,
                        }
                        ];

                    }

                    // instance.getItemByCategory();
                }, function (error) {

                });
            } else {
                this.title = 'Add Test Ride Agent';
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
        bus.$off('add-edit-testrideagents')
    },
    methods: {
        closeModal() {
            $("#add-edit-dept").modal("toggle");
        },
      getAllList() {
        let instance = this;
        this.axiosGet('test-ride/get-all-list', function (response) {
          instance.customers = response.Customers;
          instance.presentBikes = response.PresentBikes;
          instance.yrcRegions = response.YRCRegions;
        }, function (error) {
        });
      },
        onSubmit() {
                this.$store.commit('submitButtonLoadingStatus', true);
                let url = '';
                var returnData = $('#return').prop('checked');
                var submitUrl = '';
                if (this.actionType === 'add') {
                    submitUrl = 'test-ride/store';
                }
                if (!returnData && this.actionType === 'edit') {
                    submitUrl = 'test-ride/update';
                }
                this.axiosPost(submitUrl, {
                     agentId: this.agentId ,
                    agentName: this.agentName,
                    Mobile: this.Mobile,
                    dealerPoint: this.defaultDealerPoint,
                    presentBike: this.defaultPresentBike,
                    yrcRegion: this.defaultYRCRegion,
                    chassisNo: this.chassisNo,
                    profession: this.profession,
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
