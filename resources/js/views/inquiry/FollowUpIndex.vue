<template>
    <div class="content">
        <div class="container-fluid">
            <breadcrumb :options="['Inquiry Follow Up And Progress Card']">
                <router-link class="btn btn-primary" :to="{path: `${baseUrl}`+'inquiry-progress-card'}">Add Progress Card</router-link>
            </breadcrumb>
            <div class="row" style="padding:8px 0px;">
                <div class="col-md-4">
                    <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
                </div>
            </div>
            <advanced-datatable :options="tableOptions">
                <template slot="action" slot-scope="row">
                    <a href="javascript:"  class="btn btn-success" style="font-size: 12px;" @click="addModal(row.item)"> <i class="ti-pencil-alt">Follow</i></a>
                    <!--                        <a href="javascript:" title="Edit"   style="color: #101010;"  @click="addModal(row.item)">  </a> |-->
<!--                    <router-link class="btn btn-success" style="font-size: 12px;width:65px" :to="{path: `${baseUrl}`+'inquiry-follow-up?action_type=follow&InquiryId='+encodeConvert(row.item.InquiryId)}"><i class="fa fa-edit">Follow</i></router-link>-->

                </template>
            </advanced-datatable>
            <add-edit-inquiry-follow-up @changeStatus="changeStatus" v-if="loading"/>
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
                source: 'inquiry/follow-up-list',
                search: true,
                slots: [16],
                hideColumn: ['VisitResultId'],
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
                bus.$emit('add-edit-inquiry-follow-up', row);
            })
        },
        encodeConvert(val){
            let convertVal = btoa(val);
            return convertVal
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
