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
                                    <div class="col-12 col-md-6">
                                      <ValidationProvider name="product" mode="eager" rules="required"
                                                          v-slot="{ errors }">
                                        <div class="form-group">
                                          <label for="product">Product <span class="error">*</span></label>
                                          <v-select :filterable="false" :options="products" label="productname" v-model="productCode" track-by="productcode"
                                                    @search="getAllStockProduct" @input="setAllocation($event)"></v-select>
                                            <span class="error-message"> {{ errors[0] }}</span>
                                        </div>
                                      </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-6">
                                            <div class="form-group">

                                                <label for="allocation">Allocation </label>
                                                <input type="text" class="form-control"
                                                       id="Allocation "
                                                       data-required="true"
                                                       v-model="rackName" name="Allocation " placeholder="Allocation ">
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
import Login from "../../views/auth/Login.vue";

export default {
    mixins: [Common],
    data() {
        return {
            title: '',
            buttonText: '',
            status: '',
            confirm: '',
            type: 'add',
            actionType: '',
            buttonShow: false,
            errors: [],
            products: [],
            productCode:'',
            rackName:'',
            active:'',

        }
    },
    computed: {},
    created() {},
    mounted() {
        $('#add-edit-dept').modal({backdrop: 'static', keyboard: false});
        $('#add-edit-dept').on('hidden.bs.modal', () => {
            this.$emit('changeStatus')
        });

        bus.$on('add-edit-stock-allocation', (row) => {
                this.title = 'Add Rack Allocation';
                this.buttonText = "Add";
                this.transferNo = '';
                this.status = '';
                this.buttonShow = true;
                this.actionType = 'add'
            $("#add-edit-dept").modal("toggle");
        })
    },
    destroyed() {
        bus.$off('add-edit-stock-allocation')
    },
    methods: {
        closeModal() {
            $("#add-edit-dept").modal("toggle");
        },
      getAllStockProduct(val) {
        let instance = this;
        instance.Code = val;
        if (val.length > 0) {
          this.axiosGet('stock/get-all-stock-product/' + instance.Code, function (response) {
            console.log(response)
              instance.products = response.data;
          }, function (error) {
          });
        }
      },
      setAllocation(e) {
       this.rackName = e.rackname;

      },
        onSubmit() {
          this.axiosPost('stock/store-allocation',{
            rackName:this.rackName,
            productCode:this.productCode
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
