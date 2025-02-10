<template>
    <div class="content">
        <div class="container-fluid">
            <breadcrumb :options="['User Customer List']">
                <button class="btn btn-primary" @click="addModal()">Add User Customer</button>
            </breadcrumb>
            <div class="row" style="padding:8px 0px;">
                <div class="col-md-4">
                    <button type="button" class="btn btn-success btn-sm" @click="exportData">Export to Excel</button>
                </div>
            </div>
                <advanced-datatable :options="tableOptions">
                    <template slot="action" slot-scope="row">
                        <a href="javascript:" @click="deleteUserCustomer(row.item)" style="color: red" ><i class="ti-trash bold" >Delete</i></a>
                    </template>
                </advanced-datatable>
            <add-edit-user-customer @changeStatus="changeStatus" v-if="loading"/>
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
                source: 'jobCard/user-customer',
                search: true,
                slots: [6],
                hideColumn: ['UserCustomerId'],
                slotsName: ['action'],
                sortable: [],
                pages: [20, 50, 100],
                addHeader: ['Action']
            },
            loading: false,
            cpLoading: false,
            tagLoading:false,
            error:'',
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
                bus.$emit('add-edit-user-customer', row);
            })
        },
      deleteUserCustomer(row = '') {
          this.info = row;
        this.deleteAlert(() => {
          this.axiosPost('jobCard/user-customer/delete', {
            row: this.info,
          }, (response) => {
            this.successNoti(response.message);
            this.$store.commit('departmentDelete', row);
            bus.$emit('refresh-datatable');
          }, (error) => {
            this.errorNoti(error);
          })
        });
        },

        exportData() {
            bus.$emit('export-data','user-customer-list-'+moment().format('YYYY-MM-DD'))
        }
    }
}
</script>

<style scoped>

</style>