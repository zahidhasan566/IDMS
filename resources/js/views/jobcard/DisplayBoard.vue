<template>
    <div class="content">
        <div class="container-fluid">
            <breadcrumb :options="['Active Job Card List']">
            </breadcrumb>
            <div class="row" style="padding:8px 0px;">
                <div class="col-md-4">
                </div>
            </div>
            <div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm ">
                        <thead>
                        <tr>
                            <th class="text-center" >SL</th>
                            <th class="text-center">Serial No </th>
                            <th class="text-center">JobDate</th>
                            <th class="text-center">CustomerName</th>
                            <th class="text-center">Job Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(activeList, i) in activeLists"  v-if="activeLists.length">
                            <td class="text-center">{{ ++i }}</td>
                            <td class="text-center">{{ activeList.SerialNo }}</td>
                            <td class="text-center">{{ activeList.JobDate }}</td>
                            <td class="text-center">{{ activeList.CustomerName }}</td>
                            <td class="text-center">{{ activeList.JobStatus }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <br>

                </div>
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
            activeLists:[],
            loading: false,
            cpLoading: false,
            tagLoading:false,
        }
    },
    mounted() {
        bus.$off('changeStatus',function () {
            this.changeStatus()
        })
        this.getActiveJobCardList()
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
                bus.$emit('add-edit-jobCard-bay', row);
            })
        },
        getActiveJobCardList(){
            this.axiosGet('jobCard/job-display-board/', (response) => {
                this.activeLists = response.data
            }, function (error) {

            });
        },
        exportData() {
            bus.$emit('export-data','bay-list-'+moment().format('YYYY-MM-DD'))
        }
    }
}
</script>

<style scoped>

</style>