<template>
    <div class="content">
        <div class="container-fluid">
            <breadcrumb :options="['Pending Lost Document Requisition']"></breadcrumb>
            <div class="card">
                <div class="card-body">
                    <div v-if="invoiceData.length>0">
                        <table class="table table-bordered table-hover table-striped" id="datatable">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" @click="toggleAll" :checked="isAllSelected">
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
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in invoiceData" :key="index">
                                    <td>
                                        <input 
                                            type="checkbox" 
                                            :value="index" 
                                            v-model="selectedItems"
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
                                </tr>
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-success mt-4" @click="updateLostInvoiceStatus('Y')">Approve</button>
                        <button type="submit" class="btn btn-danger mt-4" @click="updateLostInvoiceStatus('R')">Reject</button>
                    </div>
                    <div v-else> No data found </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { baseurl } from "../../base_url";
import { Common } from "../../mixins/common";

export default {
    mixins: [Common],
    data() {
        return {
            invoiceData: [],
            selectedItems: [], // Store selected indices
        }
    },
    computed: {
        isAllSelected() {
            return this.invoiceData.length > 0 && this.selectedItems.length === this.invoiceData.length;
        }
    },
    mounted() {
        this.loadPendingLostInvoice();
    },
    methods: {
        loadPendingLostInvoice() {
            axios.post(
                `${baseurl}api/logistics/get-lost-invoice`,
                { status: 'N' },
                this.config()
            ).then(response => {
                this.invoiceData = response.data.data;
                this.selectedItems = []; // Reset selection when data is loaded
            }).catch(error => {
                this.errorNoti(error.response.data.message || 'Error fetching invoice data');
            });
        },

        updateLostInvoiceStatus(status) {

            if (!this.selectedItems.length) {
                this.errorNoti('Select at least one item');
                return;
            }
            
            let statusTitle = "Approve";

            if(status == 'R') {
                statusTitle = "Reject"
            }

            let confirmButtonText = 'Yes, ' + statusTitle + ' it!'

            const selectedData = this.selectedItems.map(index => this.invoiceData[index].EngineNo);
            Swal.fire({
                title: 'Are you sure?',
                text: status == 'R' ? "You are about to reject this item!" : "You won't be able to approve this!",
                    icon: status == 'R'? 'warning' : 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: confirmButtonText
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post(
                            `${baseurl}api/logistics/update-lost-invoice-status`,
                            { 
                                selectedItems: selectedData,
                                status: status
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
            if (event.target.checked) {
                // Select all indices
                this.selectedItems = this.invoiceData.map((_, index) => index);
            } else {
                // Deselect all items
                this.selectedItems = [];
            }
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
        getFileName(url) {
            return url.split('/').pop();
        }
    },
}
</script>
