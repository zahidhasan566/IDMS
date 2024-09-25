<template>
    <div class="container-fluid">
        <breadcrumb :options="['Credit Payment List']">
            <button class="btn btn-primary" @click="addModal()" v-if="user == '0'">Add Credit Payment</button>
            <button type="button" class="btn btn-success" @click="creditList('Y')">Export to Excel</button>

        </breadcrumb>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-12">
                        <ValidationObserver v-slot="{ handleSubmit }">
                            <form class="form-horizontal" @submit.prevent="handleSubmit(creditList)" @keydown.enter="$event.preventDefault()">

                                <div class="row">
                                    <div class="col-md-2" v-if="user == '1'">
                                        <label>Customer</label>
                                        <ValidationProvider name="customer Name" rules="required" mode="eager"
                                                            v-slot="{ errors }">
                                            <select data-required="true" name="customerName" class="form-control" v-model="customerCode">
                                                <option value="">Select</option>
                                                <option :value="item.CustomerCode" v-for="(item, index) in customers" :key="index">{{ item.CustomerCode }} :{{ item.CustomerName }}
                                                </option>
                                            </select>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-md-2">
                                        <ValidationProvider name="From Date" rules="required" mode="eager" v-slot="{ errors }">
                                            <label>From Date</label>
                                            <input type="date" class="form-control" data-required="true" v-model="fromDate" name="toDate">
                                        </ValidationProvider>
                                    </div>

                                    <div class="col-md-2">
                                        <ValidationProvider name="To Date" rules="required" mode="eager" v-slot="{ errors }">
                                            <label>To Date</label>
                                            <input type="date" class="form-control" data-required="true" v-model="toDate" name="toDate">
                                        </ValidationProvider>
                                    </div>

                                    <div class="col-md-2">
                                        <label></label>
                                        <submit-form v-if="buttonShow" :name="buttonText"/>
                                    </div>
                                </div>

                            </form>
                        </ValidationObserver>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-12">
                    <ValidationObserver v-slot="{ handleSubmit }">
                            <div class="col-lg-12 col-md-12 table-responsive p-0 m-0" style="margin-top: 20px;">
                                <table class="table  table-bordered table-striped nowrap dataTable no-footer dtr-inline table-sm small" style="max-height: 200px; overflow: scroll;">
                                    <thead class="thead-dark" v-if="headers.length > 0">
                                    <tr>
                                      <th>Status</th>
                                      <th v-for="(item, index) in headers" v-if="index !== 1 && index !== 15">
                                        {{ item.replace(/_/g, ' ', " $1").trim() }}
                                      </th>
                                      <th>Image</th>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    <tr v-for="(item, index) in contents">
                                      <td class="text-left">
                                        <span class="badge badge-success" v-if="item.Approved=='Y'" >Approved</span>
                                        <span class="badge badge-danger" v-if="item.Approved=='C'" >Cancel</span>
                                        <span class="badge badge-warning" v-else>Pending</span>
                                      </td>
                                      <td v-for="(item2, index) in headers" v-bind:class="isInt(item[item2]) === true ? 'text-right' : '' " v-if="index !== 1 && index !== 15">
                                        {{ item[item2] }}
                                      </td>
                                      <td class="text-center">
                                        <img v-if="item.ChequeImage" height="40" width="40" :src="tableImage(item.ChequeImage)" alt="" @click="createimage(item.ChequeImage)">
                                      </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                    </ValidationObserver>
                </div>
            </div>
        </div>
        <add-credit-payment @changeStatus="changeStatus" v-if="loading"/>

        <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <img width="100%" :src="tableImage(modalImage)" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import {bus} from "../../app";
import {Common} from "../../mixins/common";
import moment from "moment";
import {AWS_S3_COMMON_DOCUMENT_LINK, AWS_S3_PAYMENT_IMAGE_LINK, baseurl} from "../../base_url";

export default {
    mixins: [Common],
    data() {
        return {
            contents: [],
            title: '',
            user: '',
            customers: [],
            headers: [],
            customerCode: '',
            fromDate: moment().format("yyyy-MM-DD"),
            toDate: moment().format("yyyy-MM-DD"),
            modalImage: '',
            type: 'add',
            actionType: '',
            buttonShow: true,
            buttonText: 'Show',
            buttonShow2: false,
            buttonText2: 'Submit',
            allowedVisible: false,
            isMenuDisabled: false,
            loading: false,
            filename: 'credit-payment-' + moment().format('yyyy-MM-DD')
        }
    },
    mounted() {
      this.creditList();
      this.getCustomer();
      this.getData();
      bus.$on('refreshDatatable',() => {
        this.creditList();
      })
    },
    created() {
    },
    destroyed() {
        bus.$off('export-data')
    },
    methods: {
        creditList(ex) {
          axios.get(baseurl + 'api/payment/credit-payment-list?customerCode=' +this.customerCode
                +'&fromDate='+this.fromDate
                +'&toDate='+this.toDate,this.config()).then((response) => {
                if (response.data.data.length > 0){
                  if (ex === 'Y') {
                    let dataSets = response.data.data;
                    if (dataSets.length > 0) {
                      let columns = Object.keys(dataSets[0]);
                      columns = columns.filter((item) => item !== 'row_num');
                      let rex = /([A-Z])([A-Z])([a-z])|([a-z])([A-Z])/g;
                      columns = columns.map((item) => {
                        let title = item.replace(rex, '$1$4 $2$3$5')
                        return {title, key: item}
                      });
                      this.generateExport(dataSets, columns, this.filename)
                    }
                  } else {
                    this.headers = Object.keys(response.data.data[0])
                    this.contents = response.data.data;
                  }
                }

            })
        },

      isInt(value) {
        return !isNaN(parseInt(value * 1))
      },
        getData() {
            this.axiosPost('me', {}, (response) => {
                this.user = response.grpAdd
            }, (error) => {
                this.errorNoti(error);
            });
        },
        getCustomer() {
            let instance = this;
            this.axiosGet('payment/get-all-customer', function (response) {
                instance.customers = response.data;
            }, function (error) {
            });
        },
        addModal(row = '') {
            this.loading = true;
            setTimeout(() => {
                bus.$emit('add-credit-payment', row);
            })

        },
        tableImage(ChequeImage) {
            // return baseurl + "uploads/payment/" + ChequeImage;
            return AWS_S3_PAYMENT_IMAGE_LINK  + ChequeImage;
        },

        closeModal() {
            $("#imageModal").modal("hide");
        },

        createimage(ChequeImage) {
            this.editMode = false;
            this.modalImage = ChequeImage;
            $("#imageModal").modal("show");
            this.creditList();
        },
        changeStatus() {
            this.loading = false
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

<style scoped></style>

