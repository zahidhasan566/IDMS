<template>
    <div class="content">
        <div class="container-fluid">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <h4 class="page-title">Users</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active">Welcome to DMS</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <data-export/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import {Common} from "../../mixins/common";
import {baseurl} from "../../base_url";

export default {
    mixins: [Common, baseurl],
    data() {
        return {
            menus: [],
            authUser: {},
            myModel:false,
            updatePassword:'',
            confirmUpdatePassword: '',
        }
    },
    mounted() {
    },
    computed: {
        me() {
            return this.$store.state.me
        }
    },
    methods: {
        logout() {
            this.axiosPost("logout", {}, (response) => {
                    localStorage.setItem("token", "");
                    this.$router.push(this.mainOrigin + "login");
                    this.successNoti(response.message)
                },
                (error) => {
                    this.errorNoti(error);
                }
            );
        },
        openUserModel(){
            this.myModel = true;
        },
        //Update User Password
        updateUserModel(){
            let url = 'user/password-change';
            this.axiosPost(url, {
                userId: this.me.UserID,
                updatePassword: this.updatePassword,
                confirmUpdatePassword: this.confirmUpdatePassword,
            }, (response) => {
                this.successNoti(response.message);
                this.myModel = false;
            }, (error) => {
                this.errorNoti(error);
            })
        }
    }
}
</script>
