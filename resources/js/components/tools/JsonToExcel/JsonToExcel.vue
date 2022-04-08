<template>
  <JsonExcel :data="json" :fields="excelStructure" :name="fileName+'.xls'">
    Download Excel
  </JsonExcel>
</template>

<script>
import JsonExcel from "vue-json-excel";

export default {
  name: "JsonToExcel",

  components: {
      JsonExcel
  },

  props: {
    json: {
      type: Array,
      required: true,
    },
    excelStructure: {
      type: Object,
      required: true,
    },
    fileName:{
        type:String,
        required:true
    }
  },
};
</script>
