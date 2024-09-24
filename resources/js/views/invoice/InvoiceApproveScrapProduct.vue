<template>
    <div class="content">
        <div class="container-fluid">
            <breadcrumb :options="['Pending Scrap Products']">
                <button type="button" class="btn btn-success btn-sm" @click="exportReport">Export to Excel</button>
            </breadcrumb>
            <div class="card">
                <div class="datatable">
                    <div class="card-body">
                        <template v-if="!isLoading">
                            <ValidationObserver v-slot="{ handleSubmit }">
                            <form class="form-horizontal" style="padding-bottom: 30px" id="approveScrappingForm"
                                  @submit.prevent="handleSubmit(finaSubmission)"
                                  @keydown.enter="$event.preventDefault()">
                            <div v-if="pendingScrapping.length > 0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped" id="datatable">
                                        <thead class="thead-dark">
                                        <tr>
                                            <th>
                                                <input
                                                        type="checkbox"
                                                        @click="toggleAll"
                                                >
                                            </th>
                                            <th>ScrapID</th>
                                            <th>Particulars</th>
                                            <th>Requested Dealer</th>
                                            <th>Dealer Name</th>
                                            <th>Requested Date</th>
                                            <th>Unit Price</th>
                                            <th>Vat</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Requested Qnty</th>
                                            <th>Approve Amount</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(item, index) in pendingScrapping" :key="index">
                                            <td>
                                                <input
                                                        type="checkbox"
                                                        :value="index"
                                                        :checked="item.checked"
                                                        @change="checkboxClick($event,item)"
                                                >
                                            </td>
                                            <td>{{ item.ScrapID }}</td>
                                            <td>{{ item.ProductName }}</td>
                                            <td>{{ item.RequestedBy }}</td>
                                            <td>  {{ item.CustomerName }}</td>
                                            <td>{{ item.RequestedDate }}</td>
                                            <td>{{ item.UnitPrice }}</td>
                                            <td>{{ item.Vat }}</td>
                                            <td>{{ item.Reason }}</td>
                                            <td>{{ item.ApproveStatus}}</td>
                                            <td>{{ item.RequestToReturnQnty }}</td>
                                            <td>
                                                <ValidationProvider :name="`Approve Qnty- ${index} th row`" mode="eager"
                                                                    :rules="`required|min_value:0|max_value:${item.RequestToReturnQnty}`"
                                                                    v-slot="{ errors }">
                                                <input
                                                    class="form-control"
                                                    type="number"
                                                    v-model="item.approveQty"
                                                >
                                                    <span class="error-message"> {{ errors[0] }}</span>
                                                </ValidationProvider>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="data-count">
                                            Show {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} rows
                                        </div>
                                    </div>
                                    <div class="col-8">
                                        <report-pagination
                                                v-if="pagination.last_page > 1"
                                                :pagination="pagination"
                                                :offset="5"
                                                @paginate="loadPendingScrap"
                                        ></report-pagination>
                                    </div>
                                </div>
                                <div class="col-md-12" style="float:left;text-align: end;margin-top:10px">

                                    <button type="submit" name="approve" class="btn btn-success mt-4 mb-4">Approve</button>
                                    <button type="submit" name="reject" class="btn btn-warning mt-4 mb-4">Reject</button>


<!--                                    <submit-form v-if="buttonShow" :name="buttonText"/>-->
<!--                                    <submit-form v-if="buttonShow" name="Reject"/>-->
                                </div>
                            </div>
                                <div v-else>
                                    <h4 style="text-align: center">No Data Found</h4>
                                </div>
                            </form>
                            </ValidationObserver>
                        </template>

                        <template v-else>
                            <skeleton-loader :row="14" />
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { baseurl } from "../../base_url";
import { Common } from "../../mixins/common";
import Papa from 'papaparse';
import {bus} from "../../app";

export default {
    mixins: [Common],
    data() {
        return {
            Query: '',
            isLoading: false,
            pagination: {
                from: 0,
                to: 0,
                total: 0,
                last_page: 1
            },
            buttonShow: true,
            buttonText:'Approve',
            pendingScrapping: [],
            selectedItems: [], // Stores selected row indexes
        }
    },
    mounted() {
        this.loadPendingScrap();
    },
    methods: {
        checkboxClick(e,item) {
            item.checked = !!e.target.checked;
            item.approveQty = item.checked ? item.RequestToReturnQnty : 0;
        },
        async loadPendingScrap(exportFlag = '') {
            this.isLoading = true;
            await axios.post(
                `${baseurl}api/invoice-spare-parts/pending-scrap-products`,
                {
                    status: 'Y',
                    export: exportFlag,
                    pagination: this.pagination
                },
                this.config()
            ).then(response => {
                if (exportFlag === 'Y') {
                    this.exportToCSV(response.data.data);
                } else {
                    response.data.data.map((item, index) => {
                        item.checked = false
                        item.approveQty = 0
                    })
                    this.pendingScrapping = response.data.data;
                    this.pagination = response.data.paginationData;
                }
                this.isLoading = false;
            }).catch(error => {
                this.isLoading = false;
                this.errorNoti(error.response.data.message || 'Error fetching invoice data');
            });
        },

        async exportReport() {
            await this.loadPendingScrap('Y');
        },
        exportToCSV(data) {
            if (data.length > 0) {
                const csv = Papa.unparse(data);
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.href = url;
                link.setAttribute('download', 'pending_scrap.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                this.errorNoti('No data available to export');
            }
        },
        getFileName(url) {
            return url.split('/').pop();
        },
        finaSubmission() {
            this.selectedItems = []
            if (this.pendingScrapping.length > 0) {
              this.pendingScrapping.forEach((item) => {
                  console.log(item.checked)
                  if (item.checked) {
                      this.selectedItems.push(item)
                  }
              })
            }
            if (!this.selectedItems.length) {
                this.errorNoti('Select at least one item');
                return;
            }
            else{
                const buttonName = event.submitter.name;
                console.log(buttonName)
                let submitUrl=''
                if (buttonName === 'approve') {
                    submitUrl = 'invoice-spare-parts/approve-scrap-products';
                }
                else{
                    submitUrl = 'invoice-spare-parts/reject-scrap-products';
                }
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: buttonName === 'approve' ? 'Approve' : 'Reject',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.axiosPost(submitUrl, {
                                selectedItems: this.selectedItems,
                            }, (response) => {
                                this.successNoti(response.message);
                                this.loadPendingScrap();
                                this.$store.commit('submitButtonLoadingStatus', false);
                            }, (error) => {
                                this.errorNoti(error);
                                this.$store.commit('submitButtonLoadingStatus', false);
                            })
                        }
                    })


            }

            // const selectedData = this.selectedItems.map(index => this.invoiceData[index].EngineNo);
            // Swal.fire({
            //     title: 'Are you sure?',
            //     text: "You won't be able to revert this!",
            //     icon: 'success',
            //     showCancelButton: true,
            //     confirmButtonColor: '#3085d6',
            //     cancelButtonColor: '#d33',
            //     confirmButtonText: "Send Copy"
            // }).then((result) => {
            //     if (result.isConfirmed) {
            //         axios.post(
            //             `${baseurl}api/logistics/sed-lost-invoice-copy`,
            //             {
            //                 selectedItems: selectedData,
            //             },
            //             this.config()
            //         ).then(response => {
            //             this.selectedItems = []; // Clear selection after update
            //             this.successNoti(response.data.message);
            //             this.loadPendingLostInvoice();
            //         }).catch(error => {
            //             this.errorNoti(error.response.data.message);
            //         });
            //     }
            // })
        },
        config() {
            let token = localStorage.getItem('token');
            return {
                headers: { Authorization: `Bearer ${token}` }
            };
        },
        toggleAll(event) {
            const checked = event.target.checked;
            if (checked) {
                this.pendingScrapping.map((item) => {
                    item.checked = true
                    item.approveQty = item.RequestToReturnQnty
                })
            } else {
                this.pendingScrapping.map((item) => {
                    item.checked = false
                    item.approveQty = 0
                })
            }
            // this.selectedItems = checked ? this.pendingScrapping.map((item, index) => index) : [];
            this.selectedItems.forEach((item)=>{
                this.pendingScrapping[item].approveQty = this.pendingScrapping[item].RequestToReturnQnty;
            });

        },
        getApprovalStatus(status) {
            if (status === 'N') {
                return 'Pending';
            } else if (status === 'Y') {
                return 'Accepted';
            } else {
                return 'Rejected';
            }
        },
    },
}
</script>
