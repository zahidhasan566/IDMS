<template>
    <div class="content">
        <div class="container-fluid">
            <breadcrumb :options="['Lost Document Report']">
                <button type="button" class="btn btn-success btn-sm" @click="exportReport">Export to Excel</button>
            </breadcrumb>
            <div class="card">
                <div class="datatable">
                    <div class="card-body">
                        <div class="row">
                            <!-- Date From -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <date-picker v-model="dateFrom" valueType="format"></date-picker>
                                </div>
                            </div>

                            <!-- Date To -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <date-picker v-model="dateTo" valueType="format"></date-picker>
                                </div>
                            </div>

                            <!-- Customer -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Customer</label>
                                    <select name="customer" class="form-control" v-model="customer">
                                        <option value="">All</option>
                                        <option :value="c.CustomerCode" v-for="c in customerList" :key="c.CustomerCode">
                                            {{ c.CustomerCode }} - {{ c.CustomerName }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Chassis No -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Chassis No</label>
                                    <input class="form-control" type="text" v-model="chassisNo" placeholder="Enter Chassis No">
                                </div>
                            </div>

                            <!-- Invoice No -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Invoice No</label>
                                    <input class="form-control" type="text" v-model="invoiceNo" placeholder="Enter Invoice No">
                                </div>
                            </div>

                            <!-- Report Type -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Report Type</label>
                                    <select name="reportType" class="form-control" v-model="reportType">
                                        <option value="A">All Reissue</option>
                                        <option value="B">Monthly Reissue</option>
                                        <option value="C">Yearly Reissue</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Filter Button -->
                            <div class="col-md-2" style="margin-top: 1.7rem">
                                <button type="button" class="btn btn-success" @click="filterReports">
                                    <i class="mdi mdi-filter"></i> Filter
                                </button>
                            </div>
                        </div>

                        <template v-if="!isLoading">
                            <div v-if="reportDocuments.length > 0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>SL No</th>
                                                <th v-for="(value, key) in firstDocument" :key="key">
                                                    {{ formatHeader(key) }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(document, index) in reportDocuments" :key="index">
                                                <td>{{ index + 1 }}</td> <!-- Serial Number -->
                                                <td v-for="(value, key) in document" :key="key">
                                                    <span v-if="['Delivery Chalan', 'G.D Copy', 'Other Document'].includes(key) && value">
                                                        <a v-if="value" :href="value" :download="getFileName(value)">Download</a> 
                                                    </span>
                                                    <span v-else>
                                                        {{ value }}
                                                    </span>
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
                                            @paginate="filterReports"
                                        ></report-pagination>
                                    </div>
                                </div>
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
import DatePicker from 'vue2-datepicker';
import Papa from 'papaparse';
import moment from 'moment';
import 'vue2-datepicker/index.css';

export default {
    mixins: [Common],
    components: { DatePicker },

    data() {
        return {
            dateFrom: moment().subtract(1, 'months').format('YYYY-MM-DD'),
            dateTo: moment().format('YYYY-MM-DD'),
            reportType: 'A',
            invoiceNo: "",
            chassisNo: "",
            customer: "",
            customerList: [],
            reportDocuments: [],
            Query: '',
            isLoading: false,
            pagination: {
                from: 0,
                to: 0,
                total: 0,
                last_page: 1
            }
        }
    },
    computed: {
        firstDocument() {
            return this.reportDocuments.length > 0 ? this.reportDocuments[0] : {};
        }
    },
    methods: {
        async loadCustomerList() {
            try {
                const response = await axios.post(
                    `${baseurl}api/logistics/get-customer-list`,
                    {},
                    this.config()
                );
                this.customerList = response.data;
            } catch (error) {
                this.errorNoti(error.response.data.message || 'Error fetching customer data');
            }
        },

        config() {
            const token = localStorage.getItem('token');
            return {
                headers: { Authorization: `Bearer ${token}` }
            };
        },

        async fetchReports(exportFlag = '') {
            const data = {
                dateFrom: this.dateFrom,
                dateTo: this.dateTo,
                customer: this.customer,
                chassisNo: this.chassisNo,
                invoiceNo: this.invoiceNo,
                reportType: this.reportType,
                pagination: this.pagination,
                Query: this.Query,
                Export: exportFlag
            };

            this.isLoading = true;

            try {
                const response = await axios.post(
                    `${baseurl}api/logistics/get-lost-document-report`,
                    data,
                    this.config()
                );
                this.isLoading = false;

                this.reportDocuments = response.data.data;
                this.pagination = response.data.paginationData[0];
            } catch (error) {
                this.isLoading = false;
                this.errorNoti(error.response.data.message || 'Error fetching report data');
            }
        },

        filterReports() {
            this.fetchReports();
        },

        async exportReport() {
            await this.fetchReports('Y');

            if (this.reportDocuments.length > 0) {
                const csv = Papa.unparse(this.reportDocuments);
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.href = url;
                link.setAttribute('download', 'Lost_Document_Report.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }, 
        formatHeader(key) {
            return key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase());
        },
        getFileName(url) {
            return url.split('/').pop();
        }
    },
    created() {
        this.loadCustomerList();
    }
}
</script>
