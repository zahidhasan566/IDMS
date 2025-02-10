<template>
    <div class="content">
        <div class="container-fluid">
            <breadcrumb :options="['Pending Lost Document History']">
                <button type="button" class="btn btn-success btn-sm" @click="exportReport">Export to Excel</button>
            </breadcrumb>
            <div class="card">
                <div class="datatable">
                    <div class="card-body">
                        <template v-if="!isLoading">

                            <div v-if="invoiceData.length > 0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped" id="datatable">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>
                                                    <input 
                                                        type="checkbox" 
                                                        @click="toggleAll" 
                                                        :checked="isAllSelected"
                                                    >
                                                </th>
                                                <th>Invoice No</th>
                                                <th>Lost Document ID</th>
                                                <th>Engine No</th>
                                                <th>Chassis No</th>
                                                <th>Product Code</th>
                                                <th>Dealer name</th>
                                                <th>Dealer Code</th>
                                                <th>G.D Copy</th>
                                                <th>Delivery Chalan</th>
                                                <th>Other Document</th>
                                                <th>Lost Document Reason</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in invoiceData" :key="index">
                                                <td>
                                                    <input 
                                                        type="checkbox" 
                                                        v-model="selectedItems" 
                                                        :value="index" 
                                                        :disabled="item.SendDocument === 'Y'"
                                                    >
                                                </td>
                                                <td>{{ item.InvoiceNo }}</td>
                                                <td>{{ item.LostDocumentID }}</td>
                                                <td>{{ item.EngineNo }}</td>
                                                <td>{{ item.ChassisNo }}</td>
                                                <td>{{ item.ProductCode }}</td>
                                                <td>{{ item.UserName }}</td>
                                                <td>{{ item.EntryBy }}</td>
                                                <td>
                                                    <a v-if="item.GDCopy" :href="item.GDCopy" :download="getFileName(item.GDCopy)">Download</a>
                                                </td>
                                                <td>
                                                    <a v-if="item.DeliveryChalan" :href="item.DeliveryChalan" :download="getFileName(item.DeliveryChalan)">Download</a>
                                                </td>
                                                <td>
                                                    <a v-if="item.OtherDocument" :href="item.OtherDocument" :download="getFileName(item.DeliveryChalan)">Download</a>
                                                </td>
                                                <td>{{ item.LostDocumentReason }}</td>
                                                <td>{{ getApprovalStatus(item.Approved) }}</td>
                                                <td>
                                                    <template v-if="item.SendDocument === 'Y'">Sent</template>
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
                                                @paginate="loadLostInvoice"
                                            ></report-pagination>
                                        </div>
                                </div>
                                <button type="submit" class="btn btn-success mt-4" @click="sendCopy('Y')">Send Copy</button>
                            </div>
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
            invoiceData: [],
            selectedItems: [], // Stores selected row indexes
        }
    },
    computed: {
        isAllSelected() {
            // Check if all selectable items are selected
            const selectableIndexes = this.invoiceData
                .map((item, index) => ({ index, disabled: item.SendDocument === 'Y' }))
                .filter(({ disabled }) => !disabled)
                .map(({ index }) => index);

            return selectableIndexes.length > 0 &&
                selectableIndexes.every(index => this.selectedItems.includes(index));
        }
    },
    methods: {

        async loadLostInvoice(exportFlag = '') {
            this.isLoading = true;

            await axios.post(
                `${baseurl}api/logistics/get-lost-invoice`,
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
                    this.invoiceData = response.data.data;
                    this.pagination = response.data.paginationData;
                }
                this.isLoading = false;
            }).catch(error => {
                this.isLoading = false;
                this.errorNoti(error.response.data.message || 'Error fetching invoice data');
            });
        },

        async exportReport() {
            await this.loadLostInvoice('Y');
        },
        exportToCSV(data) {
            if (data.length > 0) {
                const csv = Papa.unparse(data);
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.href = url;
                link.setAttribute('download', 'Lost_Document_History.csv');
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
        sendCopy() {
            if (!this.selectedItems.length) {
                this.errorNoti('Select at least one item');
                return;
            }
            const selectedData = this.selectedItems.map(index => this.invoiceData[index].EngineNo);
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "Send Copy"
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post(
                            `${baseurl}api/logistics/sed-lost-invoice-copy`,
                            { 
                                selectedItems: selectedData,
                            },
                            this.config()
                        ).then(response => {
                            this.selectedItems = []; // Clear selection after update
                            this.successNoti(response.data.message);
                            this.loadPendingLostInvoice();
                        }).catch(error => {
                            this.errorNoti(error.response.data.message);
                        });
                    }
                })
        },
        config() {
            let token = localStorage.getItem('token');
            return {
                headers: { Authorization: `Bearer ${token}` }
            };
        },
        toggleAll(event) {
            const checked = event.target.checked;
            const selectableIndexes = this.invoiceData
                .map((item, index) => ({ index, disabled: item.SendDocument === 'Y' }))
                .filter(({ disabled }) => !disabled)
                .map(({ index }) => index);

            this.selectedItems = checked ? [...selectableIndexes] : [];
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
    created() {
        this.loadLostInvoice();
    }
}
</script>
