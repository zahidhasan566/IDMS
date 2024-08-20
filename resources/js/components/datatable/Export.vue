<template></template>
<script>
import XLSX from "xlsx";
import {bus} from "../../app";
import {saveExcel} from '@progress/kendo-vue-excel-export';
import Papa from "papaparse";

export default {
  data: () => ({
    data: [],
    headers: [],
    exportExcelData: [],
    columnHeader: [],
    excelFileName: "",
  }),
  mounted() {
    bus.$on("data-table-import", (data, columns, fileName) => {
      // console.log(this.typeIdentifier("1000"))
      // for (let i = 0; i < data.length;i++) {
      //   for (let j = 0; j < columns.length; j++) {
      //     console.log(data[i][data[i][j]])
      //     if (this.typeIdentifier(data[i][j]) === 'number') {
      //       data[i][j] = parseFloat(data[i][j])
      //       this.exportExcelData.push(data[i])
      //     } else {
      //       this.exportExcelData.push(data[i])
      //     }
      //   }
      // }
      this.exportExcelData = data;
      this.columnHeader = columns;
      this.excelFileName = fileName;
      console.log(this.exportExcelData)
      this.excelExport();
    });
  },
  destroyed() {
    bus.$off('data-table-import');
  },
  methods: {
    excelExport() {
      this.data = [];
      let headers = [];
      this.columnHeader.forEach((item) => {
        headers.push({
          field: item.key,
          title: item.title
        })
      })
      // this.exportExcelData.forEach((dataValue, dataIndex) => {
      //   let object = [];
      //   header.forEach((value, index) => {
      //     let title = value.title;
      //
      //     if (this.headers.indexOf(title) === -1) {
      //       this.headers.push(title);
      //     }
      //     object[title] = dataValue[value.key];
      //   });
      //
      //   this.data.push(Object.assign({}, object));
      // });
      // saveExcel({
      //   data: this.exportExcelData,
      //   fileName: this.excelFileName,
      //   columns: headers
      // });
      let dataSets = this.exportExcelData
      if (dataSets.length > 0) {
        const csv = Papa.unparse(dataSets);
        // Create a Blob containing the CSV data
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        // Create a download link and trigger the download
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.href = url;
        link.setAttribute('download', this.excelFileName+'.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        this.exportShow = false;
      }
    },
    typeIdentifier(value) {
      return '"' + typeof value + '"';
    },
    dataExport() {
      this.data = [];
      let header = this.columnHeader;
      this.exportExcelData.forEach((dataValue, dataIndex) => {
        let object = [];
        header.forEach((value, index) => {
          let title = value.title;

          if (this.headers.indexOf(title) === -1) {
            this.headers.push(title);
          }
          object[title] = dataValue[value.key];
        });

        this.data.push(Object.assign({}, object));
      });
      let name = this.excelFileName + ".xlsx";
      let posWS = XLSX.utils.json_to_sheet(this.data, {
        raw: true
      });

      //Excel width set
      let columnWidth = [];

      this.headers.forEach((value, index) => {
        const max = this.data.reduce((prev, current) => {
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
      XLSX.utils.book_append_sheet(wb, posWS, "AMS");
      XLSX.writeFile(wb, name);
      this.$emit("resetExport", false);
    },
  },
};
</script>
