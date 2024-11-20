<template>
    <div class="content" id="jobCard" style="font-family: Helvetica, Arial, sans-serif; !important;">
        <div class="container-fluid">


            <table id="InquiryPrint">
                <thead style="background:none">
                <tr>
                    <th style="text-align: left; width: 33%;">
                      <img :src="`${'https://dms.ifadmotors.com'+'/assets/images/reLogo.png'}`" style="height: 100px;width: 100px">
                    </th>
                    <th style="width: 33%;color:#000000; text-align: center;"><h2>Inquiry</h2></th>
                    <th style="text-align: right; width: 33%;">
                        <img :src="`${'https://dms.ifadmotors.com'+'/assets/images/logo-svg.png'}`" style="height: 30px;width: 100px">
                    </th>
                </tr>
                </thead>
            </table>
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
            jobCardNo: '',
            // baseUrl: Object.freeze(baseurl)
            baseUrl: baseurl,
            title: '',
            buttonText: '',
            status: '',
            confirm: '',
            type: 'add',
            actionType: '',
            buttonShow: false,
            errors: [],

        }
    },
    mounted() {
        this.inquiryItem = this.$route.query.item

        console.log( this.$route.query.item)


    },
    destroyed() {
        bus.$off('export-data')
    },
    methods: {
        changeStatus() {
            this.loading = false
        },
        print() {
            this.d.print(this.$el, [this.cssText])
        },
    }
}
</script>

<style scoped>

#jobCardPrint {
    width: 100%;
}

#jobCardPrint table {
    width: 100%;
    background: none !important
}

#jobCardPrint table h2 {
    font-size: 22px;
    line-height: 60px;
}

#jobCardPrint table th {
    padding: 0px;
    margin: 0px;
}

#jobCardPrintFirstBody {
    border-collapse: collapse;
}

#jobCardPrintFirstBody table {
    width: 100px;
}

#jobCardPrintFirstBody .bold {
    font-weight: bold;
}

#jobCardPrintFirstBody table tr, td {
    border: 1px solid #000000;
}
</style>

<style>
#jobCard table tr td:nth-child(12) {
    width: 145px !important;
}
</style>