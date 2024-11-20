<template>
    <div class="content" id="inquiryPrintTotal" style="font-family: Helvetica, Arial, sans-serif; !important;">
        <div class="container-fluid">
            <table id="InquiryPrint" style="width: 100%">
                <thead style="background:none">
                <tr>
                    <th style="text-align: left; width: 23%;">
                      <img :src="`${'https://dms.ifadmotors.com'+'/assets/images/reLogo.png'}`" style="height: 100px;width: 100px">
                    </th>
                    <th style="width: 63%;color:#000000; "><h2>Inquiry Summary - {{existingInquiry.Customer_Name}}</h2></th>
                    <th style="text-align: right; width: 23%;">
                        <img :src="`${'https://dms.ifadmotors.com'+'/assets/images/logo-svg.png'}`" style="height: 30px;width: 100px">
                    </th>
                </tr>
                </thead>
            </table>



            <div class="content" id="inquiry" style="font-family: Helvetica, Arial, sans-serif; !important;">
                <div class="container-fluid">
                    <div style="text-align: center;margin: 0 auto; width: 50%">
                        <!--            Inquiry Information-->
                        <table id="InquiryPrintInformation" style="text-align: center;padding-top: 50px;border: 2px solid">
                            <thead style="background:none;color: #000000">
                            <tr style="border: 1px solid">
                                <th style="padding:10px;color: #000000">Inquiry Id</th>
                                <td style="padding:10px;color: #000000">{{existingInquiry.InquiryId}}</td>
                            </tr>
                            <tr  style="border: 1px solid">
                                <th style="padding:10px;color: #000000">Customer Name</th>
                                <td style="padding:10px;color: #000000">{{existingInquiry.Customer_Name}}</td>
                            </tr>
                            <tr  style="border: 1px solid">
                                <th style="padding:10px;color: #000000">Address</th>
                                <td style="padding:10px;color: #000000">{{existingInquiry.Address}}</td>
                            </tr>
                            <tr  style="border: 1px solid">
                                <th style="padding:10px;color: #000000">Contact No</th>
                                <td style="padding:10px;color: #000000">{{existingInquiry.Contact_No}}</td>
                            </tr>
                            <tr  style="border: 1px solid">
                                <th style="padding:10px;color: #000000">Profession </th>
                                <td style="padding:10px;color: #000000">{{existingInquiry.Profession}}</td>
                            </tr>
                            <tr  style="border: 1px solid">
                                <th style="padding:10px;color: #000000">First Inquiry</th>
                                <td style="padding:10px;color: #000000">{{existingInquiry.First_Inquiry}}</td>
                            </tr>
                            <tr  style="border: 1px solid">
                                <th style="padding:10px;color: #000000">Convert to Sales</th>
                                <td style="padding:10px;color: #000000">{{existingInquiry.Convert_to_Sales}}</td>
                            </tr>
                            <tr  style="border: 1px solid">
                                <th style="padding:10px;color: #000000">Visit Point </th>
                                <td style="padding:10px;color: #000000">{{existingInquiry.Dealer_Name}}</td>
                            </tr>
                            <tr  style="border: 1px solid">
                                <th style="padding:10px;color: #000000">Inquiry Level </th>
                                <td style="padding:10px;color: #000000">{{existingInquiry.Inquiry_Level}}</td>
                            </tr>
                            <tr  style="border: 1px solid">
                                <th style="padding:10px;color: #000000">Next Call </th>
                                <td style="padding:10px;color: #000000">{{existingInquiry.Next_Call}}</td>
                            </tr>
                            <tr  style="border: 1px solid">
                                <th style="padding:10px;color: #000000">Product </th>
                                <td style="padding:10px;color: #000000">{{existingInquiry.Product}}</td>
                            </tr>
                            <tr  style="border: 1px solid">
                                <th style="padding:10px;color: #000000">Color </th>
                                <td style="padding:10px;color: #000000">{{existingInquiry.Color}}</td>
                            </tr>
                            <tr  style="border: 1px solid">
                                <th style="padding:10px;color: #000000">Received Amount </th>
                                <td style="padding:10px;color: #000000">{{existingInquiry.ReceivedAmount}}</td>
                            </tr>
                            </thead>
                        </table>
                    </div>

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
            baseUrl: baseurl,
            title: '',
            buttonText: '',
            status: '',
            confirm: '',
            type: 'add',
            actionType: '',
            buttonShow: false,
            errors: [],
            inquiryId:'',
            existingInquiry:{},

        }
    },
    mounted() {
        this.inquiryId =  this.decodeConvert(this.$route.query.item)
        let instance = this;
        this.axiosGet('inquiry/get/print-data/' + this.inquiryId, function (response) {
            let data = response.existingInquiry[0]
            instance.existingInquiry = data || []
            instance.print()




    }, function (error) {

    });


    },
    destroyed() {
        bus.$off('export-data')
    },
    methods: {
        changeStatus() {
            this.loading = false
        },
        print() {
            $("#inquiryPrintTotal").printThis({
                importCSS: true,
                importStyle: true,
                loadCSS: "",
                footer: $(".footer")
            });
        },
        decodeConvert(val) {
            let convertVal = atob(val);
            return convertVal
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