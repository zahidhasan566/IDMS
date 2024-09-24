<template>
    <div class="content" id="jobCard">
        <div class="container-fluid">
            <breadcrumb :options="['Job Card']">
                <router-link class="btn btn-primary" :to="{path: `${baseUrl}`+'job-card-add-edit?action_type=add'}">Add Job Card</router-link>
<!--                <button class="btn btn-primary" @click="addModal()"></button>-->
            </breadcrumb>
            <div class="row" style="padding:8px 0px;">
                <div class="col-md-4">
                    <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
                </div>
            </div>
                <advanced-datatable :options="tableOptions">

                    <template slot="jobStatus" slot-scope="row">
                          <span v-if="row.item.JobStatus==='Close'" style="background: #ffa9a9; padding: 4px 5px; border-radius: 4px;font-weight: bold; border: 1px solid #ffa9a9">
                              {{row.item.JobStatus}}
                        </span>
                        <span v-else>
                            {{row.item.JobStatus}}
                        </span>

                    </template>
                    <template slot="updateStatus" slot-scope="row">
                          <span v-if="row.item.JobStatus==='Close'">

                        </span>
                        <span v-else>
                            <button type="button" style="padding: 2px 5px" @click="closeJobCard(row.item.JobCardNo)" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Close</button>
                        </span>

                    </template>
                    <template slot="action" slot-scope="row">
                        <span v-if="row.item.JobStatus==='Close'">
                        <router-link class="btn btn-primary" style="font-size: 12px;width:65px;padding: 2px 0px" :to="{path: `${baseUrl}`+'job-card-print?action_type=print&jobCardNo='+encodeConvert(row.item.JobCardNo)}"><i class="fa fa-print">Print</i></router-link>
                        </span>
                        <span v-else>
                        <router-link class="btn btn-success" style="font-size: 12px;width:65px;padding: 2px 0px" :to="{path: `${baseUrl}`+'job-card-add-edit?action_type=edit&jobCardNo='+encodeConvert(row.item.JobCardNo)}"><i class="fa fa-edit">Edit</i></router-link>
                        <router-link class="btn btn-primary" style="font-size: 12px;width:65px;padding: 2px 0px" target='_blank' :to="{path: `${baseUrl}`+'job-card-print?action_type=print&jobCardNo='+encodeConvert(row.item.JobCardNo)}"><i class="fa fa-print">Print</i></router-link>
                        </span>

                    </template>
                </advanced-datatable>
          <div>
            <loader v-if="PreLoader" object="#ff9633" color1="#ffffff" color2="#17fd3d" size="5" speed="2" bg="#343a40" objectbg="#999793" opacity="80" name="circular"></loader>
          </div>
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
                source: 'jobCard/jobCard-list',
                search: true,
                slots: [9,10,11],
                hideColumn: ['Id'],
                slotsName: ['jobStatus','updateStatus','action'],
                sortable: [4],
                pages: [20, 50, 100],
                addHeader: ['Update Status','Action']
            },
            loading: false,
            cpLoading: false,
            tagLoading:false,
            // baseUrl: Object.freeze(baseurl)
            baseUrl: baseurl,
            PreLoader:false
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
          //preloader
            this.PreLoader = true;
            let  submitUrl = 'jobCard/job-close';
            this.axiosPost(submitUrl, {
                jobCardNo: jobCardNo,
            }, (response) => {
              this.PreLoader = false;
                this.successNoti(response.message);
                bus.$emit('refresh-datatable');
            }, (error) => {
              this.PreLoader = true;
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
