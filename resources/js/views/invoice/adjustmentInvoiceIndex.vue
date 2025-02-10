<template>
    <div class="content" id="jobCard">
        <div class="container-fluid">
            <breadcrumb :options="['Adjustment Invoice']">
                <router-link class="btn btn-primary"
                             :to="{path: `${baseUrl}`+'invoice/adjustinvoice-add-edit?action_type=add'}">Add Adjustment
                </router-link>
                <!--                <button class="btn btn-primary" @click="addModal()"></button>-->
            </breadcrumb>
            <div class="row" style="padding:8px 0px;">
                <div class="col-md-4">
                    <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
                </div>
            </div>
            <advanced-datatable :options="tableOptions">
                <template slot="ReturnStatus" slot-scope="row">
                <span v-if="row.item.ReturnStatus === 'N'">
                    No
                </span>
                    <span v-else>
                   Yes
                </span>
                </template>
                <template slot="action" slot-scope="row">
                    <!--                        <a href="javascript:" title="Edit"   style="color: #101010;"  @click="addModal(row.item)">  </a> |-->
                    <router-link class="btn btn-success" style="font-size: 12px;"
                                 :to="{path: `${baseUrl}`+'invoice/adjustinvoice-add-edit?action_type=edit&adjustmentInvoiceNo='+encodeConvert(row.item.AdjustmentInvoiceNo)}">
                        <i class="fa fa-edit">Return</i></router-link>

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
                source: 'invoice-spare-parts/adjustment-list',
                search: true,
                slots: [5,6],
                hideColumn: ['ReturnStatus'],
                slotsName: ['ReturnStatus','action'],
                sortable: [4],
                pages: [20, 50, 100],
                addHeader: ['ReturnStatus','Action']
            },
            loading: false,
            cpLoading: false,
            tagLoading: false,
            // baseUrl: Object.freeze(baseurl)
            baseUrl: baseurl
        }
    },
    mounted() {
        bus.$off('changeStatus', function () {
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
        encodeConvert(val) {
            let convertVal = btoa(val);
            return convertVal
        },
        closeJobCard(jobCardNo) {
            this.infoAlert('Close JobCard', 'Are you sure?', 'Yes, Close JobCard.', () => {
                this.postData(jobCardNo);
            })
        },
        postData(jobCardNo) {
            let submitUrl = 'jobCard/job-close';
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
            bus.$emit('export-data', 'job-card-list-' + moment().format('YYYY-MM-DD'))
        }
    }
}
</script>

<style scoped>

#jobCard table tr td:nth-child(12) {
    width: 145px !important;
}
</style>
