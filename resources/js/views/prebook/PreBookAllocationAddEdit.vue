<template>
    <div class="container-fluid">
        <breadcrumb :options="{title}">
            <router-link class="btn btn-primary" :to="{name:'ReportPreBookAllocation'}">Back</router-link>
        </breadcrumb>
        <div class="row" v-if="actionType==='add'">
            <div class="col-12 col-md-6">
                <ValidationProvider name="Invoice Adjustment File" mode="eager" rules="required"
                                    v-slot="{ errors }">

                    <label style="font-weight:bold" for="inputExcelFile">* PreBook Allocation File (Excel) <span
                            class="error">*</span></label>
                    <input type="file" ref="inputFile" @change="readExcelFile($event)" class="btn btn-info btn-sm">
                    <span class="error-message"> {{ errors[0] }}</span>

                </ValidationProvider>
            </div>
            <div class="col-md-3">
                <label style="font-weight:bold" for="downloadExcelFile">Sample Prebook Allocation File: <span
                        class="error">*</span></label>
                <a href="#" style="float: right;padding: 6px;margin-right: 5px" @click="downloadDemoExcel"
                   class="btn btn-success btn-sm">Download Sample</a>
            </div>
        </div>

        <div class="row" style="padding-top: 15px" v-if="form.ExcelData.importStatus">
            <div class="row col-md-12">
                <div class="col-md-4"><p>Uploaded Data: </p></div>
                <div class="col-md-2"></div>
                <div class="col-md-6">
                </div>


            </div>
            <div class="row col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm small">
                        <thead class="thead-dark">
                        <tr>
                            <th>SL</th>
                            <th>Booking Code</th>
                            <th>Dealer Code</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(excelData, i) in form.ExcelData" :key="i" v-if="form.ExcelData.length>0">
                            <td scope="row">{{ ++i }}</td>
                            <td><input type="text" class="form-control" :readOnly="actionType==='edit'" v-model="excelData.Booking_Code"></td>
                            <td><input type="text" class="form-control" :readOnly="actionType==='edit'" v-model="excelData.Dealer_Code"></td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm"
                                        @click="removeRow(i)"><i
                                        class="ti-close"></i></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12" style="padding-top: 15px;text-align:end">
                    <button type="button" id="submitPreBookAllocation" style="padding:6px" class="btn btn-success btn-sm" @click="onSubmit">{{buttonText}}
                    </button>
                </div>
            </div>

        </div>
        <div>
            <loader v-if="PreLoader" object="#ff9633" color1="#ffffff" color2="#17fd3d" size="5" speed="2"
                    bg="#343a40" objectbg="#999793" opacity="80" name="circular"></loader>
        </div>
    </div>
</template>

<script>
import {Common} from "../../mixins/common";
import {baseurl} from "../../base_url";
import XLSX from "xlsx";
import moment from "moment";

export default {
    mixins: [Common],
    data() {
        return {
            ceilings: [],
            businesses: [],
            Depots: [],
            results: [],
            title:'',
            query: "",
            editMode: false,
            isLoading: false,
            form: new Form({
                // ExcelData: [],
                ExcelData: [{
                    Booking_Code: '',
                    Dealer_Code: '',
                    importStatus:false,
                }],
                masterCode:'',

            }),
            loadTempData: [],
            allDealer: [],
            errors: [],
            returnExcelData:[],
            buttonShow: false,
            masterCode: '',
            actionType: '',
            buttonText:'',
            PreLoader: false,
        }
    },
    created() {
    },
    mounted() {
        this.title= 'Add PreBook Allocation'
        this.actionType = 'add'
        this.buttonText = 'Submit'
    },
    computed: {
        formattedDealerCode: {
            get() {
                // Ensure Dealer_Code starts with '00'
                if (!this.excelData.Dealer_Code.startsWith("00")) {
                    return "00" + this.excelData.Dealer_Code;
                }
                return this.excelData.Dealer_Code;
            },
            set(value) {
                // Always update Dealer_Code without adding extra '00'
                if (value.startsWith("00")) {
                    this.excelData.Dealer_Code = value;
                } else {
                    this.excelData.Dealer_Code = "00" + value;
                }
            },
        },
    },
    methods: {
        decodeConvert(val){
            let convertVal = atob(val);
            return convertVal
        },
        getSupportingData(val){
            if (val.length > 2){
                this.selectBox = false;
                axios.get(baseurl + "api/invoice-spare-parts/get-adjustment-dealer-data?CustomerCode=" + val , this.config()).then(response => {
                    this.allDealer = response.data.allDealers
                }).catch(e => {

                });
            }
        },
        checkFieldValue(){
            let tempBooking_Code = this.form.ExcelData[0]['Booking_Code']
            let tempDealer_Code = this.form.ExcelData[0]['Dealer_Code']
        },
        removeRow(i) {
            this.form.ExcelData.splice(i-1, 1)
            if (this.errors[i-1] !== undefined) {
                this.errors.splice(i-1, 1)
            }
        },
        readExcelFile(e) {
            var files = e.target.files, f = files[0];
            var reader = new FileReader();
            reader.onload = (e) => {
                var data = new Uint8Array(e.target.result);
                var workbook = XLSX.read(data, { type: 'array' });
                let sheetName = workbook.SheetNames[0];
                let worksheet = workbook.Sheets[sheetName];

                // Read and preprocess Excel data
                let rawData = XLSX.utils.sheet_to_json(worksheet);

                // Preprocess data to ensure Dealer_Code starts with "00"
                this.form.ExcelData = rawData.map(row => {
                    if (row.Dealer_Code && !row.Dealer_Code.toString().startsWith("00")) {
                        row.Dealer_Code = "00" + row.Dealer_Code.toString();
                    }
                    return row;
                });

                // Set import status
                this.form.ExcelData.importStatus = true;
            };
            reader.readAsArrayBuffer(f);
        },
        downloadDemoExcel() {
            axios.get(baseurl + "api/prebook/prebook-allocation-demo-excel", this.config()).then((res) => {
                console.log(res.data)

                const downloadAnchor = document.createElement("a");
                downloadAnchor.setAttribute("href", res.data);
                downloadAnchor.setAttribute("download", "prebook_allocation_file_upload.xls");
                document.body.appendChild(downloadAnchor);
                downloadAnchor.click();
                //remove anchor download
                document.body.removeChild(downloadAnchor);
            })
                .catch((error) => {
                    console.log(error);
                });
        },
        onSubmit() {
            this.PreLoader = true;
            var submitUrl = 'api/prebook/store-prebook-allocation-data';
            if(this.errors.length === 0){
                $("#submitPreBookAllocation").hide();
                this.form.post(baseurl + submitUrl, this.config()).then(response => {
                    if(response){
                        this.PreLoader = false;
                        this.successNoti(response.data.message);
                        this.form.ExcelData = []
                        if (this.actionType === 'edit') {
                            this.$router.push({name: 'ReportPreBookAllocation',})
                        }
                        else{
                            this.$router.go(this.$router.currentRoute)
                        }


                    }
                }).catch(e => {
                    this.PreLoader = false;
                    this.errorNoti(e);
                });
            }

        },
    }
}
</script>

<style scoped>
#ceilingModal .form-control {
    font-size: 10px;
    height: 25px;
}

#ceilingModal label {
    font-size: 11px !important;
}

.form-divider {
    padding: 6px 0px 5px 5px;
    border: 1px solid #4d87f64f;
    border-radius: 13px;
    margin: 0 auto;
}

#invoice2 .auto-complete2 {
    position: relative;
    display: block;
}

#invoice2 .auto-complete2 ul {
    list-style: none;
    margin: 0;
    padding: 5px 0 0 0px;
    position: absolute;
    width: 100%;
    border: 1px solid #0000000d;
    background: #ffffff;
    max-height: 200px;
    overflow-y: scroll;
    z-index: 999;
}

#invoice2 .auto-complete2 ul li {
    border-bottom: 1px solid #b7b7b7;
    background: #cbc4c4;
    padding: 5px;
    cursor: pointer;
}

#invoice2 .auto-complete2 ul li a {
    color: #000000;
}

#invoice2 .auto-complete2 ul li:hover {
    background: #fff3cd;
}

#invoice2 :focus {
    background: #fff3cd;
}

</style>