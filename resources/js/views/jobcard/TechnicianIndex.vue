<template>
    <div class="content" id="technician">
        <div class="container-fluid">
            <breadcrumb :options="['Technician List']">
                <button class="btn btn-primary" @click="addAddEditModal()">Add Technician</button>
            </breadcrumb>
            <div class="row" style="padding:8px 0px;">
                <div class="col-md-4">
                    <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
                </div>
            </div>
            <advanced-datatable :options="tableOptions">
                <template slot="active" slot-scope="row">
                <span v-if="row.item.Active === 'N'">
                    No
                </span>
                    <span v-else>
                   Yes
                </span>
                </template>

                <template slot="action" slot-scope="row">
                  <span v-if="row.item.ResignationDate">
                    Resigned
                 </span>
                 <span v-else>
                                       <a href="javascript:" style="font-size: 11px;padding: 4px" class="btn btn-success" @click="addAddEditModal(row.item)"> <i class="ti-pencil-alt"> Edit</i></a>
                    <a href="javascript:" style="font-size: 11px;padding: 4px" class="btn btn-primary" @click="trainingModal(row.item)"> <i class="ti-notepad"> Training</i></a>
                    <a href="javascript:" style="font-size: 11px;padding: 4px" class="btn btn-danger" @click="resignationModal(row.item)"> <i class="ti-package"> Resign</i></a>
                </span>
                </template>
            </advanced-datatable>
            <add-edit-job-card-technician @changeStatus="changeStatus" v-if="loading"/>
            <resignation-job-card-technician @changeStatus="changeStatus" v-if="loading"/>
            <training-job-card-technician @changeStatus="changeStatus" v-if="loading"/>
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
                source: 'jobCard/technician-list',
                search: true,
                slots: [11, 13],
                hideColumn: ['ResignationDate'],
                slotsName: ['active', 'action'],
                sortable: [4],
                pages: [20, 50, 100],
                addHeader: ['Action']
            },
            loading: false,
            cpLoading: false,
            tagLoading: false,
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
        addAddEditModal(row = '') {
            console.log("log")
            this.loading = true;
            setTimeout(() => {
                bus.$emit('add-edit-jobCard-technician', row);
            })
        },
        trainingModal(row = '') {
            this.loading = true;
            setTimeout(() => {
                bus.$emit('training-job-card-technician', row);
            })
        },
        resignationModal(row = '') {
            this.loading = true;
            setTimeout(() => {
                bus.$emit('resignation-job-card-technician', row);
            })
        },

        exportData() {
            bus.$emit('export-data', 'technician-list-' + moment().format('YYYY-MM-DD'))
        }
    }
}
</script>

<style>
#technician th:nth-child(14){
    width: 200px !important;
}
</style>