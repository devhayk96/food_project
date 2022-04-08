<template>
  <v-card>
    <v-card-title>
      Courier Types
      <v-btn
        v-if="userCan('courier_types-create')"
        class="mx-2"
        dark
        outlined
        color="primary"
        :to="{
          name: 'dashboard-operations-courier-type-manage',
          query: { action: 'add', id: 0 },
        }"
      >
        <v-icon dark> mdi-plus </v-icon>
        Add Courier Type
      </v-btn>
      <v-spacer></v-spacer>
      <v-text-field
        v-model="search"
        append-icon="mdi-magnify"
        label="Search"
        single-line
        hide-details
      ></v-text-field>
    </v-card-title>
    <v-data-table
      :headers="headers"
      :items="dataList === '' ? [] : dataList"
      :search="search"
      :loading="dataList === ''"
      loading-text="Loading... Please wait"
      :footer-props="{
        'items-per-page-options': [10, 20, 50, -1],
      }"
      :items-per-page="itemsPerPage"
    >
      <template v-slot:item.is_active="{ item }">
        <v-icon v-if="item.is_active === 0" class="red--text">
          mdi-close
        </v-icon>
        <v-icon v-if="item.is_active === 1"> mdi-check </v-icon>
      </template>
      <template #item.actions="{ item }">
        <v-btn
          v-if="userCan('courier_types-read')"
          icon
          class="text-center"
          :to="{
            name: 'dashboard-operations-courier-type-manage',
            query: { action: 'edit', id: item.id },
          }"
        >
          <v-icon>mdi-open-in-new</v-icon>
        </v-btn>
      </template>
    </v-data-table>
  </v-card>
</template>

<script>
import mixin from "../../../helpers/mixin";
export default {
  mixins: [mixin],
  data() {
    return {
      search: "",
      headers: [
        {
          text: "Poshub ID",
          align: "start",
          value: "id",
        },
        { text: "Code", value: "code" },
        { text: "Name", value: "name" },
        { text: "Source", value: "source_type.name" },
        { text: "Parent", value: "parent.name" },
        { text: "Active", value: "is_active" },
        {
          text: "Actions",
          align: "end",
          value: "actions",
          sortable: false,
        },
      ],
      dataList: "",
      itemsPerPage: 10,
    };
  },

  created() {
    this.initialize();
    // set number of lines
    this.itemsPerPage = +this.getNumberOfLines() || 10;
  },

  methods: {
    initialize() {
      let self = this;
      axios
        .get("/web/v1/courier-types")
        .then(function (response) {
          console.log(response.data.data);
          self.dataList = response.data.data;
        })
        .catch(function (err) {
          console.log(err);
        });
    },
  },
};
</script>
