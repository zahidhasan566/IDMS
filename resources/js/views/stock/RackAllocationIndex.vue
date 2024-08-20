<template>
    <div class="content">
        <div class="container-fluid">
            <breadcrumb :options="['Stock Rack Allocation List']">
                <button class="btn btn-primary" @click="addModal()">Add & Edit Rack Allocation</button>
            </breadcrumb>

                <advanced-datatable :options="tableOptions">
                </advanced-datatable>
            <add-edit-stock-allocation @changeStatus="changeStatus" v-if="loading"/>
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
                source: 'stock/allocation',
                search: true,
                slots: [],
                hideColumn: [],
                slotsName: [],
                sortable: [],
                pages: [20, 50, 100],
                addHeader: []
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
                bus.$emit('add-edit-stock-allocation', row);
            })
        },
    }
}
</script>

<style scoped>

</style>