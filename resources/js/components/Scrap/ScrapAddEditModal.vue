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
                                    <div class="col-md-3">
                                        <ValidationProvider name="product" mode="eager" v-slot="{ errors }"
                                                            rules="required">
                                            <div class="form-group">
                                                <label>Product <span class="error">*</span></label>
                                                <v-select :filterable="false"
                                                          :mutiselect="false"
                                                          :disabled="actionType==='edit'"
                                                          v-model="product"
                                                          :options="allProduct" label="ProductName"
                                                          @search="searchProduct"
                                                          @input="selectProducts">

                                                </v-select>
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label for="currentStock">Current Stock<span class="error">*</span></label>
                                        <input type="number" readonly class="form-control"
                                               id="currentStock"
                                               v-model="currentStock" name="currentStock" placeholder="Current Stock">
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label for="unitPrice">Unit Price</label>
                                        <input type="number" readonly class="form-control"
                                               id="currentStock"
                                               v-model="unitPrice" name="unitPrice" placeholder="Unit Price">
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label for="vat">Vat</label>
                                        <input type="number" readonly class="form-control"
                                               id="vat"
                                               v-model="vat" name="vat" placeholder="Vat">
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label for="alreadyRequest">Already Requested<span
                                                class="error">*</span></label>
                                        <input type="number" readonly class="form-control"
                                               id="alreadyRequest"
                                               v-model="alreadyRequest" name="alreadyRequest"
                                               placeholder="Already Requested">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <ValidationProvider name="Reason" mode="eager"
                                                            rules="required"
                                                            v-slot="{ errors }">
                                            <label for="alreadyRequest">Reason<span class="error">*</span></label>
                                            <select class="form-control" v-model="reason">
                                                <option value="">Select</option>
                                                <option value="Damaged">Damaged</option>
                                            </select>
                                            <span class="error-message"> {{ errors[0] }}</span>
                                        </ValidationProvider>
                                    </div>

                                    <div class="col-12 col-md-2">
                                        <ValidationProvider name="Quantity" mode="eager"
                                                            :rules="`required|min_value:1|max_value:${currentStock-alreadyRequest}`"
                                                            v-slot="{ errors }">
                                            <label for="requestToReturn">Request to Return <span class="error">*</span></label>
                                            <input type="number" class="form-control"
                                                   id="requestToReturn"
                                                   @input="totalCalculate"
                                                   v-model="requestToReturn" name="requestToReturn"
                                                   placeholder="Request to Return">
                                            <span class="error-message"> {{ errors[0] }}</span>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label for="total">Total<span class="error">*</span></label>
                                        <input type="number" readonly class="form-control"
                                               id="total"
                                               v-model="total" name="total" placeholder="Total">
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
            status: '',
            confirm: '',
            type: 'add',
            actionType: '',
            buttonShow: false,
            errors: [],
            bayName: '',
            comments: '',
            active: '',
            bayCode: '',
            dealerCode: '',
            product: '',
            customers: [],
            allProduct: [],
            currentStock: 0,
            alreadyRequest: 0,
            requestToReturn: 0,
            reason: '',
            unitPrice: 0,
            vat: 0,
            total: 0,
            scrapId:''

        }
    },
    computed: {},
    created() {
    },
    mounted() {
        this.getSupportingData()
        $('#add-edit-dept').modal({backdrop: 'static', keyboard: false});
        $('#add-edit-dept').on('hidden.bs.modal', () => {
            this.$emit('changeStatus')
        });
        bus.$on('add-edit-scrap-product', (row) => {
            if (row) {
                let instance = this;
                this.axiosGet('invoice-spare-parts/get/scrap-product/modal/' + row.ScrapID, function (response) {
                    instance.title = 'Update Scrap Product';
                    instance.buttonText = "Update";
                    instance.buttonShow = true;
                    instance.actionType = 'edit';
                    if (response.existingScrapInfo) {
                        console.log(response.existingScrapInfo)
                        instance.product = {
                            id: response.existingScrapInfo.id,
                            ProductName:  response.existingScrapInfo.ProductName
                        }
                        instance.scrapId = response.existingScrapInfo.ScrapID
                        instance.currentStock = parseFloat(response.existingScrapInfo.StockQty)
                        instance.unitPrice = parseFloat(response.existingScrapInfo.UnitPrice)
                        instance.vat = parseFloat(response.existingScrapInfo.Vat)
                        instance.alreadyRequest = parseFloat(response.alreadyRequested[0].alreadyReturnQnty)
                        instance.reason = response.existingScrapInfo.Reason
                        instance.requestToReturn = parseFloat(response.existingScrapInfo.RequestToReturnQnty)
                        instance.total = parseFloat((parseFloat(response.existingScrapInfo.UnitPrice) + parseFloat(response.existingScrapInfo.Vat)) * parseFloat(response.existingScrapInfo.RequestToReturnQnty)).toFixed(4)
                    }
                }, function (error) {

                });
            } else {
                this.title = 'Add Scrap Product';
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
        bus.$off('add-edit-scrap-product')
    },
    methods: {
        closeModal() {
            $("#add-edit-dept").modal("toggle");
        },
        getSupportingData() {
            let instance = this;
            this.axiosGet('jobCard/bay-supporting-data', function (response) {
                instance.customers = response.customers

            }, function (error) {
            });
        },
        searchProduct(val) {
            let productCode = val;
            if (productCode.length > 2) {
                axios.get(baseurl + 'api/invoice-spare-parts/search-product/' + productCode
                    , this.config()).then((response) => {
                    if (response.data) {
                        this.allProduct = response.data.allProduct;
                    }

                }).catch((error) => {

                })
            }
        },
        selectProducts(val) {
            this.currentStock =val.StockQty == null ? 0 : val.StockQty;
            this.alreadyRequest = val.alreadyReturnQnty == null ? 0 : val.alreadyReturnQnty;
            this.unitPrice = val.UnitPrice == null ? 0 : parseFloat(val.UnitPrice);
            this.vat = val.Vat == null ? 0 : parseFloat(val.Vat);
        },
        totalCalculate() {
            if (parseFloat(this.unitPrice) > 0 && parseFloat(this.requestToReturn) > 0) {
                this.total = parseFloat((parseFloat(this.unitPrice) + parseFloat(this.vat)) * parseFloat(this.requestToReturn)).toFixed(4)
            } else {
                this.total = 0
            }
        },
        onSubmit() {
            this.$store.commit('submitButtonLoadingStatus', true);
            let url = '';
            var returnData = $('#return').prop('checked');
            var submitUrl = '';
            if (this.actionType === 'add') {
                submitUrl = 'invoice-spare-parts/scrap-product-add';
            }
            if (!returnData && this.actionType === 'edit') {
                submitUrl = 'invoice-spare-parts/scrap-product-update';
            }
            this.axiosPost(submitUrl, {
                scrapId: this.scrapId,
                product: this.product,
                reason: this.reason,
                alreadyRequest:this.alreadyRequest,
                requestToReturn: this.requestToReturn,
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
