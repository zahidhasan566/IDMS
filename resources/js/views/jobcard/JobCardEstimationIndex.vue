<template>
    <div class="content" id="jobCard">
        <div class="container-fluid">
            <breadcrumb :options="['Job Card Estimation']">
                <router-link class="btn btn-primary" :to="{path: `${baseUrl}`+'job-card-estimation?action_type=add'}">Add Estimation</router-link>
                <!--                <button class="btn btn-primary" @click="addModal()"></button>-->
            </breadcrumb>
            <div class="row" style="padding:8px 0px;">
                <div class="col-md-4">
                    <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
                </div>
            </div>
            <advanced-datatable :options="tableOptions">
                <template slot="action" slot-scope="row">
                    <router-link class="btn btn-primary" style="font-size: 12px;width:65px" :to="{path: `${baseUrl}`+'job-card-estimation-print?action_type=print&jobCardEstimationNo='+row.item.EstimationNo}"><i class="fa fa-print">Print</i></router-link>

                </template>
            </advanced-datatable>
        </div>
    </div>
</template>

<script>
import {Common} from "../../mixins/common";
import {baseurl} from "../../base_url";
import {bus} from "../../app";
import moment from "moment";

export default {
    mixins: [Common],
    data() {
        return {
            tableOptions: {
                source: 'jobCard/estimation-list',
                search: true,
                slots: [7],
                hideColumn: [],
                slotsName: ['action'],
                sortable: [4],
                pages: [20, 50, 100],
                addHeader: ['Action']
            },
            loading: false,
            cpLoading: false,
            tagLoading:false,
            // baseUrl: Object.freeze(baseurl)
            baseUrl: baseurl
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
        addModal(row = '') {
            this.loading = true;
            setTimeout(() => {
                bus.$emit('add-edit-jobCard', row);
            })
        },
        closeJobCard(jobCardNo){
            this.infoAlert('Close JobCard', 'Are you sure?', 'Yes, Close JobCard.', () => {
                this.postData(jobCardNo);
            })
        },
        postData(jobCardNo){
            let  submitUrl = 'jobCard/job-close';
            this.axiosPost(submitUrl, {
                jobCardNo: jobCardNo,
            }, (response) => {
                this.successNoti(response.message);
                bus.$emit('refresh-datatable');
            }, (error) => {
                this.errorNoti(error);
            })
        },

        exportData() {
            bus.$emit('export-data','job-card-list-'+moment().format('YYYY-MM-DD'))
        }
    }
}
</script>

<style scoped>

#jobCard table tr td:nth-child(12){
    width: 145px !important;
}
</style>
