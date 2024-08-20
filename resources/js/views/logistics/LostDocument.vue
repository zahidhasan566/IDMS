<template>
    <div class="content">
        <div class="container-fluid">
            <breadcrumb :options="['Lost Document']"></breadcrumb>
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-center align-items-center" style="padding:8px 0px;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="fw-bold" for="invoiceNo">Invoice No</label>
                                <input type="text" class="form-control" v-model="invoiceNo" placeholder="Invoice No">
                            </div>
                        </div>
                        <div class="col-md-2 mt-2">
                            <button type="button" class="btn btn-success" @click="invoicePreview" :disabled="!invoiceNo">Preview</button>
                        </div>
                    </div>
                    <div v-if="invoiceData.length > 0">
                        <div class="col-md-12 mb-4">
                            <div class="row">
                                <div class="col-md-2 fw-bold">Invoice no</div>
                                <div class="col-md-10" id="divinvoiceno">: {{ invoiceNo }}</div>
                                <div class="col-md-2 fw-bold">Invoice Date</div>
                                <div class="col-md-10" id="divinvoicedate">: {{ invoiceData[0].invoicedate }}</div>
                                <div class="col-md-2 fw-bold">Customer</div>
                                <div class="col-md-10" id="divcustomer">: {{ invoiceData[0].customercode }} - {{ invoiceData[0].customername }}</div>
                            </div>
                        </div>
                        <ValidationObserver v-slot="{ handleSubmit }">
                            <form @submit.prevent="handleSubmit(onSubmit)" @keydown.enter="$event.preventDefault()">
                                <table class="table table-bordered table-hover table-striped" id="datatable">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" @click="toggleAll"></th>
                                            <th>Sl</th>
                                            <th>Engine no</th>
                                            <th>Chassis no</th>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in invoiceData" :key="index">
                                            <td><input type="checkbox" v-model="selectedItems" :value="item"></td>
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ item.engineno }}</td>
                                            <td>{{ item.chassisno }}</td>
                                            <td>{{ item.productcode }} - {{ item.productname }}</td>
                                            <td>{{ item.quantity }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <span class="error-message" v-if="isSubmit && !selectedItems.length">Engine No field is required</span>

                                <div class="form-group mt-4" style="width: 40%;">
                                    <label for="lostdocumentreason" class="form-label fw-bold">Lost Document Reason<span style="color: red">*</span></label>
                                    <select id="lostdocumentreason" v-model="lostDocumentReason" class="form-control">
                                        <option value="">Select an option</option>
                                        <option value="Lost of Document">Lost Of Document</option>
                                        <option value="Accidental Issue">Accidental Issue</option>
                                        <option value="Changed Document">Changed Document</option>
                                        <option value="Others">Others</option>
                                    </select>
                                    <span class="error-message" v-if="isSubmit && !lostDocumentReason">Lost Document Reason field is required</span>
                                </div>

                                <div class="mt-4">
                                    <label for="gdCopy" class="form-label fw-bold">Upload G.D Copy<span style="color: red">*</span></label>
                                    <input type="file" name="gdCopy" ref="gdCopyInput" class="form-control-file border" accept=".jpeg,.png,.pdf" @change="handleFileUpload($event, 'gdCopy')">
                                    <span class="error-message" v-if="isSubmit && !gdCopy">G.D Copy field is required</span>
                                </div>
                                <div class="mt-4">
                                    <label for="deliveryChalan" class="form-label fw-bold">Upload Delivery Challan<span style="color: red">*</span></label>
                                    <input type="file" ref="deliveryChalanInput" class="form-control-file border" accept=".jpeg,.png,.pdf" @change="handleFileUpload($event, 'deliveryChalan')">
                                    <span class="error-message" v-if="isSubmit && !deliveryChalan">Delivery Challan field is required</span>
                                </div>
                                <div class="mt-4">
                                    <label for="othersDocument" class="form-label fw-bold">Upload Others Document<span style="color: red">*</span></label>
                                    <input type="file" ref="othersDocumentInput" class="form-control-file border" accept=".jpeg,.png,.pdf" @change="handleFileUpload($event, 'othersDocument')">
                                    <span class="error-message" v-if="isSubmit && !othersDocument">Others Document field is required</span>
                                </div>
                                <button type="submit" class="btn btn-primary mt-4">Submit</button>
                            </form>
                        </ValidationObserver>
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

export default {
    mixins: [Common],
    data() {
        return {
            invoiceNo: '',
            invoiceData: [],
            selectedItems: [],
            lostDocumentReason: '',
            gdCopy: null,
            deliveryChalan: null,
            othersDocument: null,
            isSubmit: false,
            errors: [],
        }
    },
    methods: {
        invoicePreview() {
            axios.post(
                `${baseurl}api/logistics/get-lost-invoice-details`,
                { invoiceNo: this.invoiceNo },
                this.config()
            ).then(response => {
                this.invoiceData = response.data.invoiceData;
            }).catch(error => {
                this.errorNoti(error.response.data.message || 'Error fetching invoice data');
            });
        },
        config() {
            let token = localStorage.getItem('token');
            return {
                headers: { Authorization: `Bearer ${token}` }
            };
        },
        toggleAll(event) {
            this.selectedItems = event.target.checked ? this.invoiceData : [];
        },
        handleFileUpload(event, type) {
            this[type] = event.target.files[0];
        },
        onSubmit() {
            this.isSubmit = true;
            if (!this.gdCopy || !this.deliveryChalan || !this.othersDocument || !this.selectedItems.length) return;

            const formData = new FormData();
            formData.append('invoiceNo', this.invoiceNo);
            formData.append('lostDocumentReason', this.lostDocumentReason);
            formData.append('selectedItems', JSON.stringify(this.selectedItems));

            if (this.gdCopy) formData.append('gdCopy', this.gdCopy);
            if (this.deliveryChalan) formData.append('deliveryChalan', this.deliveryChalan);
            if (this.othersDocument) formData.append('othersDocument', this.othersDocument);

            axios.post(
                `${baseurl}api/logistics/submit-lost-invoice-details`,
                formData,
                this.config()
            ).then(response => {
                this.successNoti(response.data.message || 'Form submitted successfully');
                this.resetForm();
            }).catch(error => {
                this.errorNoti(error.response.data.message || 'Error submitting form');
            });
        },
        resetForm() {
            this.invoiceNo = '';
            this.invoicePreview();
            this.lostDocumentReason = '';
            this.selectedItems = [];
            this.gdCopy = null;
            this.deliveryChalan = null;
            this.othersDocument = null;
            this.$refs.gdCopyInput.value = '';
            this.$refs.deliveryChalanInput.value = '';
            this.$refs.othersDocumentInput.value = '';
            this.isSubmit = false;
        },
    },
}
</script>

<style scoped>
.fw-bold {
    font-weight: bold;
}
.border {
    padding: 5px;
}
.error-message {
    color: red;
    font-size: 0.875rem;
}
</style>
