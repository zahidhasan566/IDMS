<template>
    <div class="content">
        <div class="container-fluid">
            <breadcrumb :options="['Common Document List']">
            </breadcrumb>
            <div class="row" style="padding:8px 0px;">
                <div class="col-md-4">
                    <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
                </div>
            </div>
                <advanced-datatable :options="tableOptions">
                    <template slot="action" slot-scope="row">
                      <button  style="height: 18px;padding: 0px 3px 18px 3px;" class="btn btn-success btn-sm " @click="openPdf(row.item.DocumentFileName)"> <i class="fa fa-eye"> Show </i></button>
                    </template>
                </advanced-datatable>
            </div>
    </div>
</template>

<script>
import {Common} from "../../mixins/common";
import {AWS_S3_COMMON_DOCUMENT_LINK, baseurl} from "../../base_url";
import {bus} from "../../app";
import moment from "moment";

export default {
    mixins: [Common],
    data() {
        return {
            tableOptions: {
                source: 'logistics/dealer-document-report',
                search: true,
                slots: [5],
                hideColumn: [],
                slotsName: ['action'],
                sortable: [],
                pages: [20, 50, 100],
                addHeader: ['Action']
            },
            loading: false,
            cpLoading: false,
            tagLoading:false,
        }
    },
    mounted() {
        bus.$off('changeStatus',function () {
            this.changeStatus()
        })
    },
    destroyed() {
        bus.$off('export-data')
    },
    methods: {
        changeStatus() {
            this.loading = false
        },
        openPdf(file) {
            const mainOrigin = window.location.origin; // Get the current origin (e.g., http://localhost)

            // Dynamically determine the base path (e.g., '/idms' or '')
            const basePath = window.location.pathname.split('/')[1];
            // const pdfUrl = `${mainOrigin}/${basePath}/uploads/dealerdocument/${file}`; // Construct the dynamic URL
            const pdfUrl = `https://dms.ifadmotors.com/uploads/dealerdocument/${file}`; // Construct the dynamic URL

            window.open(pdfUrl, '_blank'); // Open the PDF in a new tab
        },
        exportData() {
            bus.$emit('export-data','bay-list-'+moment().format('YYYY-MM-DD'))
        }
    }
}
</script>

<style scoped>

</style>