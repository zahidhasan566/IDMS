<template>
    <div class="content" id="jobCard">
        <div class="container-fluid">
            <breadcrumb :options="['Scrap Product']">
                <button class="btn btn-primary" @click="addModal()">Add Scrap Product</button>
            </breadcrumb>
            <div class="row" style="padding:8px 0px;">
                <div class="col-md-4">
                    <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
                </div>
            </div>
            <advanced-datatable :options="tableOptions">
                <template slot="Approve Status" slot-scope="row">
                    <span v-if="row.item.ApproveStatus==='Pending'" class="btn-warning" style="    padding: 3px;border-radius: 7px;">
                        {{row.item.ApproveStatus}}
                    </span>
                    <span v-if="row.item.ApproveStatus==='Approved'" class="btn-success" style="    padding: 3px;border-radius: 7px;">
                        {{row.item.ApproveStatus}}
                    </span>
                    <span v-if="row.item.ApproveStatus==='Rejected'" class="btn-danger" style="  padding: 3px;border-radius: 7px;">
                        {{row.item.ApproveStatus}}
                    </span>
                </template>

                <template slot="action" slot-scope="row">
                    <a href="javascript:" v-if="row.item.ApproveStatus==='Pending'" @click="addModal(row.item)"> <i class="ti-pencil-alt">Edit</i></a>
                </template>
            </advanced-datatable>
            <add-edit-scrap-product @changeStatus="changeStatus" v-if="loading"/>
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
                source: 'invoice-spare-parts/get-scrap-product-data',
                search: true,
                slots: [13,14],
                hideColumn: ['Id'],
                slotsName: ['Approve Status','action'],
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
                bus.$emit('add-edit-scrap-product', row);
            })
        },
        exportData() {
            bus.$emit('export-data','scrap-product  -list-'+moment().format('YYYY-MM-DD'))
        }
    }
}
</script>

<style scoped>

#jobCard table tr td:nth-child(12){
    width: 145px !important;
}
</style>
