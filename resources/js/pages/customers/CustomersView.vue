<template>
  <v-card class="px-2 py-2" width="100%">
    <v-card-title>
      <date-picker setLabel="Filter By Date" @date="getDate" />
      <v-text-field
        v-model="search"
        @input="find"
        class="ml-2"
        placeholder="Filter By City"
      />
      <v-btn dark outlined color="primary" class="ml-3">
        <json-to-excel
          :json="customers"
          :excelStructure="json_fields"
          :fileName="excelFileName"
        />
      </v-btn>
    </v-card-title>
    <v-data-table
      :headers="headers"
      :items="customers"
      class="elevation-1"
      :loading="customers == []"
      :footer-props="{
        'items-per-page-options': [10, 20, 50, -1],
      }"
      loading-text="Loading... Please wait"
    >
      <template #item.country="{ item, index }">
        {{ item.address.country }}
      </template>
      <template #item.city="{ item, index }">
        {{ item.address.city }}
      </template>
      <template #item.street="{ item, index }">
        {{ item.address.street }}
      </template>
      <template #item.street_extra="{ item, index }">
        {{ item.address.street_extra }}
      </template>
      <template #item.postcode="{ item, index }">
        {{ item.address.postcode }}
      </template>
    </v-data-table>
  </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatePicker from "@/components/datePicker/DatePicker";
import JsonToExcel from "@/components/tools/JsonToExcel/JsonToExcel";
export default {
  components: {
    DatePicker,
    JsonToExcel,
  },

  data: () => ({
    search: "",
    customers: [],
    headers: [
      {
        text: "name",
        align: "start",
        value: "name",
      },
      {
        text: "country",
        align: "start",
        value: "country",
      },
      {
        text: "postcode",
        align: "start",
        value: "postcode",
      },
      {
        text: "city",
        align: "start",
        value: "city",
      },
      {
        text: "street #1",
        align: "start",
        value: "street",
      },
      {
        text: "street #2",
        align: "start",
        value: "street_extra",
      },
      {
        text: "company",
        align: "start",
        value: "company",
      },
      {
        text: "email",
        align: "start",
        value: "email",
      },
      {
        text: "phone",
        align: "start",
        value: "phone",
      },
      {
        text: "note",
        align: "start",
        value: "note",
      },
      {
        text: "created_by",
        align: "center",
        value: "created_by_id",
      },
    ],
    excelFileName: "customers",
    json_fields: {
      Name: "name",
      Country: {
        field: "address.country",
        callback: (value) => {
          return value;
        },
      },
      Postcode: {
        field: "address.postcode",
        callback: (value) => {
          return value;
        },
      },
      City: {
        field: "address.city",
        callback: (value) => {
          return value;
        },
      },
      "Street #1": {
        field: "address.street",
        callback: (value) => {
          return value;
        },
      },
      "Street #2": {
        field: "address.street_extra",
        callback: (value) => {
          return value;
        },
      },
      Company: "company",
      Email: "email",
      Phone: "phone",
      Note: "note",
      "Created By": "created_by_id",
    },
    json_meta: [
      [
        {
          key: "charset",
          value: "utf-8",
        },
      ],
    ],
  }),

  async created() {
    await this.fetchCustomers();
    this.customers = this.customersList;
  },

  computed: {
    ...mapGetters({
      customersList: "customers/getCustomers",
    }),
  },

  methods: {
    ...mapActions("customers", ["fetchCustomers"]),

    find() {
      let regexp = new RegExp(this.search, "ig");
      this.customers = this.customersList;
      this.customers = this.customers.filter((item) => {
        if (item.address.city) {
          if (item.address.city.search(regexp) != -1) {
            return true;
          } else {
            return false;
          }
        }
      });
    },

    getDate(value) {
      console.log(value);
    },
  },
};
</script>
