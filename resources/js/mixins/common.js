import Swal from 'sweetalert2'

const {format} = require('number-currency-format');
import moment from 'moment'
import XLSX from "xlsx";

export const Common = {
    data() {
        return {
            data: {},
            headers: [],
            dataSets: []
        }
    },
    methods: {
        config() {
            let token = localStorage.getItem('token');
            return {
                headers: {Authorization: `Bearer ${token}`, 'Content-Type': 'application/json; charset=UTF-8','Accept': 'application/json',}
            };
        },
        axiosGet(route, success, error) {
            axios.get(this.mainOrigin + 'api/' + route, this.config())
                .then((response) => {
                    success(response.data);
                }).catch((err) => {
                if (err.response.status === 401) {
                    localStorage.setItem("token", "");
                    this.redirect(this.mainOrigin + 'login');
                } else {
                    error(err);
                }
            });
        },
        axiosGetWithoutToken(route, success, error) {
            axios.get(this.mainOrigin + 'api/' + route)
                .then((response) => {
                    success(response.data);
                }).catch((err) => {
                error(err);
            });
        },
        axiosPost(route, data, success, error) {
            axios.post(this.mainOrigin + 'api/' + route, data, this.config())
                .then((response) => {
                    success(response.data);
                }).catch((err) => {
                if (err.response.status === 401) {
                    localStorage.setItem("token", "");
                    this.redirect(this.mainOrigin + 'login');
                } else {
                    error(err);
                }
            });
        },
        axiosPostWithoutToken(route, data, success, error) {
            axios.post(this.mainOrigin + 'api/' + route, data)
                .then((response) => {
                    success(response.data);
                }).catch((err) => {
                error(err);
            });
        },
        axiosDelete(route, id, success, error) {
            axios.delete(this.mainOrigin + 'api/' + route + '/' + id, this.config())
                .then((response) => {
                    success(response.data);
                }).catch((err) => {
                if (err.response.status === 401) {
                    localStorage.setItem("token", "");
                    this.redirect(this.mainOrigin + 'login');
                } else {
                    error(err);
                }
            });
        },
        axiosPut(route, data, success, error) {
            axios.put(this.mainOrigin + 'api/' + route, data, this.config())
                .then((response) => {
                    success(response.data);
                }).catch((err) => {
                if (err.response.status === 401) {
                    localStorage.setItem("token", "");
                    this.redirect(this.mainOrigin + 'login');
                } else {
                    error(err);
                }
            });
        },
        redirect(route) {
            window.location.href = route;
        },
        successNoti(msg) {
            this.$toaster.success(msg)
        },
        errorNoti(msg) {
            if (msg.response === undefined) {
                this.$toaster.error(msg);
            } else if (msg.response.data.message === undefined) {
                this.$toaster.error(msg);
            } else {
                this.$toaster.error(msg.response.data.message);
            }
        },
        numberFormat(value) {
            if (value == null) {
                return '';
            } else {
                return format(value, {
                    currency: '',
                    spacing: false,
                    currencyPosition: 'LEFT'
                });
            }
        },
        weightFormat(value) {
            return format(value, {
                currency: ' Kg.',
                spacing: false,
                currencyPosition: 'right'
            })
        },
        dateFormat(date) {
            return date ? moment(date, 'YYYY-MM-DD').format("DD-MM-YYYY") : '';
        },
        dateTimeFormat(date) {
            return date ? moment(date, 'YYYY-MM-DD h:mm:ss').format("DD-MM-YYYY h:mm a") : '';

        },
        periodFormat(data) {
            return data ? moment(data).format('MM-YYYY') : ''
        },
        approveAlert(callback) {
            Swal.fire({
                title: 'Are you sure to approve?',
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Approve it!',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.value) {
                    callback();
                }
            })
        },
        deleteAlert(callback) {
            Swal.fire({
                title: 'Are you sure to cancel?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Cancel it!',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.value) {
                    callback();
                }
            })
        },
        infoAlert(title, message, confirmButtonText, callback) {
            Swal.fire({
                title: title,
                text: message,
                icon: 'info',
                showConfirmButton: true,
                confirmButtonColor: '#3085d6',
                confirmButtonText: confirmButtonText,
                showCancelButton: true,
                cancelButtonColor: '#d33',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.value) {
                    callback()
                }
            })
        },
        infoAlert2(title, message) {
            Swal.fire({
                title: title,
                text: message,
                icon: 'info',
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonColor: '#d33',
                cancelButtonText: 'Close',
            })
        },
        infoSuccess(title, message) {
            Swal.fire({
                title: title,
                text: message,
                icon: 'success',
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonColor: '#d33',
                cancelButtonText: 'Close',
            })
        },
        infoFailed(title, message) {
            Swal.fire({
                title: title,
                text: message,
                icon: 'danger',
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonColor: '#d33',
                cancelButtonText: 'Close',
            })
        },
        processText(value) {
            let rex = /([A-Z])([A-Z])([a-z])|([a-z])([A-Z])/g;
            value = value.replace(rex, '$1$4 $2$3$5');
            value = value.replace('-', ' ');
            return value;
        },
        numberWithCommas(x) {
            if (x !== undefined) {
                x = Number(x)
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        },
        focusNext(elem) {
            const currentIndex = Array.from(elem.form.elements).indexOf(elem);
            elem.form.elements.item(
                currentIndex < elem.form.elements.length - 1 ?
                    currentIndex + 1 :
                    0
            ).focus();
        },
        focusPrevious(elem) {
            const currentIndex = Array.from(elem.form.elements).indexOf(elem);
            elem.form.elements.item(
                currentIndex < elem.form.elements.length - 1 ?
                    currentIndex - 1 :
                    0
            ).focus();
        },
        onLoadModalElementSelected(index) {
            setTimeout(() => {
                $(`[data-index="${index}"]`).focus()
            },500)
        },
        arrayKeyChange(oldArray){
            const keyMapping = {
                CustomerCode: 'value',
                CustomerName: 'text'
            };
            let updatedData = oldArray.map(item => {
                return Object.keys(item).reduce((acc, key) => {
                    const newKey = keyMapping[key] || key; // Use the mapped key or default to the original key
                    acc[newKey] = item[key];
                    return acc;
                }, {});
            });
            return updatedData;
        },
        rearrangeDataIndex(tableId,maxIndexNumber) {
            let indexInc = maxIndexNumber
            const elem = document.querySelectorAll('#'+tableId+' select,#'+tableId+' input')
            elem.forEach((item,index) => {
                if ($(item).attr('data-disabled') !== 'true') {
                    indexInc += 1
                    $(item).attr('data-index',indexInc)
                }
            })
        },
        inWords(num) {
            let a = ['','one ','two ','three ','four ', 'five ','six ','seven ','eight ','nine ','ten ','eleven ','twelve ','thirteen ','fourteen ','fifteen ','sixteen ','seventeen ','eighteen ','nineteen '];
            let b = ['', '', 'twenty','thirty','forty','fifty', 'sixty','seventy','eighty','ninety'];

            if ((num = num.toString()).length > 9) return 'overflow';
            let n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
            if (!n) return;
            let str = '';
            str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + ' ' + a[n[1][1]]) + 'crore ' : '';
            str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + ' ' + a[n[2][1]]) + 'lakh ' : '';
            str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + ' ' + a[n[3][1]]) + 'thousand ' : '';
            str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + ' ' + a[n[4][1]]) + 'hundred ' : '';
            str += (n[5] != 0) ? ((str != '') ? 'and ' : '') + (a[Number(n[5])] || b[n[5][0]] + ' ' + a[n[5][1]]) + 'only ' : '';
            return str;
        },
        generateExport(exportExcelData,columnHeader,excelFileName) {
            try {
                this.dataSets = [];
                let header = columnHeader;
                exportExcelData.forEach((dataValue, dataIndex) => {
                    let object = [];
                    header.forEach((value, index) => {
                        let title = value.title;
                        if (this.headers.length > 0) {
                            if (this.headers.indexOf(title) === -1) {
                                this.headers.push(title);
                            }
                        }
                        object[title] = dataValue[value.key];
                    });
                    this.dataSets.push(Object.assign({}, object));
                });
                let name = excelFileName + ".xlsx";
                let posWS = XLSX.utils.json_to_sheet(this.dataSets);

                //Excel width set
                let columnWidth = [];

                this.headers.forEach((value, index) => {
                    const max = this.dataSets.reduce((prev, current) => {
                        if (prev[value] === undefined || prev[value] === null)
                            prev[value] = "";
                        if (current[value] === undefined || current[value] === null)
                            current[value] = "";
                        return prev[value].length > current[value].length ? prev : current;
                    });

                    if (max[value] === undefined || max[value] === null) {
                        max[value] = "";
                    }

                    let maxLength =
                        max[value].toString().length > value.length
                            ? max[value].toString().length
                            : value.length;
                    columnWidth.push(Object.assign({wch: maxLength + 2}));
                });

                posWS["!cols"] = columnWidth;
                let wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, posWS, "Sheet");
                XLSX.writeFile(wb, name);
            } catch (error) {
                console.log(error)
            }
        }
    },
}
