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
                                        <ValidationProvider name="CustomerName" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <div class="form-group">

                                                <label for="CustomerName">Customer Name<span class="error">*</span></label>
                                                <input type="text" class="form-control"
                                                       id="CustomerName"
                                                       data-required="true"
                                                       v-model="customerName" name="CustomerName" data-index="0" placeholder="Customer Name">
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <ValidationProvider name="mobile" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                          <div class="form-group">

                                            <label for="mobile">Customer Mobile</label>
                                            <input type="number" class="form-control"
                                                   id="mobile"
                                                   data-required="true"
                                                   v-model="mobile" name="mobile" placeholder="mobile" maxlength="11"  pattern="\d{11}">
                                            <span class="error-message"> {{ errors[0] }}</span>
                                          </div>
                                        </ValidationProvider>
                                    </div>

                                  <div class="col-12 col-md-4">
                                    <ValidationProvider name="dealerPoint" mode="eager" rules="required"
                                                        v-slot="{ errors }">
                                      <div class="form-group">
                                        <label for="dealerPoint">Select Agent <span class="error">*</span></label>
                                        <multiselect v-model="defaultAgents" :options="agents"
                                                     :multiple="false"
                                                     :close-on-select="true"
                                                     :clear-on-select="false" :preserve-search="false"
                                                     placeholder="Select Agent"
                                                     label="Name" track-by="AgentId">

                                        </multiselect>
                                        <span class="error-message"> {{ errors[0] }}</span>
                                      </div>

                                    </ValidationProvider>
                                  </div>
                                  <div class="col-12 col-md-4">
                                    <ValidationProvider name="type" mode="eager" rules="required"
                                                        v-slot="{ errors }">
                                      <label for="agentType">Type</label>
                                      <select class="form-control" name="active"
                                              v-model="agentType">
                                        <option value="Agent">Agent</option>
                                        <option value="FZ-X">FZ-X</option>
                                      </select>
                                    </ValidationProvider>
                                  </div>
                                  <div class="col-12 col-md-4">
                                    <ValidationProvider name="Address" mode="eager" rules="required"
                                                        v-slot="{ errors }">
                                      <div class="form-group">

                                        <label for="profession">Address</label>
                                        <input type="text" class="form-control"
                                               id="Address"
                                               data-required="false"
                                               v-model="address" name="Address" placeholder="Address">
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
            agents: [],
            title: '',
            buttonText: '',
            status: '',
            confirm: '',
            type: 'add',
            actionType: '',
            buttonShow: false,
            errors: [],
            defaultAgents:'',
            address:'',
            mobile:'',
            agentId:'',
            agentType:'Agent',
            customerName:'',
        }
    },
    computed: {},
    created() {},
    mounted() {

      this.getAllAgentsList()
        $('#add-edit-dept').modal({backdrop: 'static', keyboard: false});
        $('#add-edit-dept').on('hidden.bs.modal', () => {
            this.$emit('changeStatus')
        });
        bus.$on('add-edit-testrider', (row) => {
          console.log('edit',row);
          this.rideId = row.RideId;
            if (row) {
                let instance = this;
                this.axiosGet('test-ride/edit-rider/' + this.rideId , function (response) {
                    instance.title = 'Update Test Rider';
                    instance.buttonText = "Update";
                    instance.buttonShow = true;
                    instance.actionType = 'edit';
                    if(response){
                        instance.customerName= response.agents.CustomerName;
                        instance.mobile = response.agents.CustomerMobile;
                        instance.address = response.agents.CustomerAddress;
                        instance.type = response.agents.CustomerType;
                        instance.defaultAgents= [{
                          'AgentId': response.agents.AgentId,
                          'Name': response.agents.Name,
                          'DealerId': response.agents.DealerId,
                        }
                        ];

                    }
                }, function (error) {

                });
            } else {
                this.title = 'Add Test Rider';
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
        bus.$off('add-edit-testrider')
    },
    methods: {
        closeModal() {
            $("#add-edit-dept").modal("toggle");
        },
      getAllAgentsList() {
        let instance = this;
        this.axiosGet('test-ride/get-all-agents', function (response) {
          instance.agents = response.agents;
        }, function (error) {
        });
      },
        onSubmit() {
                this.$store.commit('submitButtonLoadingStatus', true);
                let url = '';
                var returnData = $('#return').prop('checked');
                var submitUrl = '';
                if (this.actionType === 'add') {
                    submitUrl = 'test-ride/store-rider';
                }
                if (!returnData && this.actionType === 'edit') {
                    submitUrl = 'test-ride/update-rider';
                }
                this.axiosPost(submitUrl, {
                  rideId: this.rideId ,
                  customerName: this.customerName,
                  mobile: this.mobile,
                  agents: this.defaultAgents,
                  address: this.address,
                  agentType: this.agentType,
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
