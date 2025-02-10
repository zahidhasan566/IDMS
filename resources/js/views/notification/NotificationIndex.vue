<template>
    <div class="content">
        <div class="container-fluid">
            <breadcrumb :options="['Notification List ']">
                <button class="btn btn-primary" @click="addModal()">Add Notification List</button>
            </breadcrumb>
            <div class="row" style="padding:8px 0px;">
                <div class="col-md-4">
                    <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
                </div>
            </div>
                <advanced-datatable :options="tableOptions">
                    <template slot="action" slot-scope="row">
                        <a href="javascript:" @click="addModal(row.item)"> <i class="ti-pencil-alt">Edit</i></a>

                    </template>
                </advanced-datatable>
            <add-edit-notify @changeStatus="changeStatus" v-if="loading"/>
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
                source: 'transport-notification/notification-list',
                search: true,
                slots: [14],
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
        addModal(row = '') {
            this.loading = true;
            setTimeout(() => {
                bus.$emit('add-edit-notify', row);
            })
        },

        exportData() {
            bus.$emit('export-data','transport-notification-list-'+moment().format('YYYY-MM-DD'))
        }
    }
}
</script>

<style scoped>

</style>