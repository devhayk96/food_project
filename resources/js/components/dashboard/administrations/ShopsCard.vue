<!-- eslint-disable vue/no-unused-vars -->
<template>
  <v-card>
    <v-card-title>
      Shop List
      <v-btn
        v-if="userCan('shops-create')"
        class="mx-2"
        dark
        outlined
        color="primary"
        :to="{ name: 'dashboard-administrations-shop-add' }"
      >
        <v-icon dark> mdi-plus </v-icon>
        Add Shop
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
      :search="search"
      :headers="tableHeaders"
      :items="shops"
      :single-expand="singleExpand"
      :expanded.sync="expanded"
      item-key="name"
      show-expand
      class="elevation-1"
      :footer-props="{
        'items-per-page-options': [10, 20, 50, -1],
      }"
      :items-per-page="itemsPerPage"
    >
      <template v-slot:item.is_active="{ item }">
        <v-icon v-if="item.is_active === 0" class="red--text">mdi-close</v-icon>
        <v-icon v-if="item.is_active === 1">mdi-check</v-icon>
      </template>
      <template v-slot:item.is_visible="{ item }">
        <v-icon v-if="item.is_visible === 1">mdi-minus</v-icon>
        <v-icon v-if="item.is_visible === 0">mdi-check</v-icon>
      </template>

      <template v-slot:expanded-item="{ headers, item }">
        <td :colspan="headers.length">
          <h3 class="ml-5">Connected Sources:</h3>
          <ul id="v-for-object">
            <li v-for="source in item.order_sources">
              <b>{{ $t("shop.name") | capitalize }}:</b> {{ source.name }} -
              <b>{{ $t("shop.type") | capitalize }}:</b>
              {{ source.order_source_type.name }} -
              <b>{{ $t("shop.is_active") | capitalize }}:</b>
              <v-icon v-if="source.is_active === 0" class="red--text"
                >mdi-close</v-icon
              >
              <v-icon v-if="source.is_active === 1">mdi-check</v-icon>
            </li>
          </ul>
        </td>
      </template>

      <template #item.actions="{ item }">
        <v-btn
          v-if="userCan('shops-read')"
          icon
          class="text-center"
          :to="{
            name: 'dashboard-administrations-shop-details',
            params: { shopFromTable: item },
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
      expanded: [],
      singleExpand: false,
      shops: [],
      itemsPerPage:10
    };
  },

  computed: {
    tableHeaders() {
      return [
        {
          text: this.$t("shop.poshub_id"),
          align: "start",
          sortable: false,
          value: "id",
        },
        { text: this.$t("shop.name"), value: "name" },
        { text: this.$t("shop.address"), value: "address.street" },
        { text: this.$t("shop.post_code"), value: "address.postcode" },
        { text: this.$t("shop.city"), value: "address.city" },
        { text: this.$t("shop.active"), value: "is_active", align: "center" },
        { text: this.$t("shop.hidden"), value: "is_visible", align: "center" },
        {
          text: this.$t("shop.actions"),
          align: "end",
          value: "actions",
          sortable: false,
        },
        { text: "", value: "data-table-expand" },
      ];
    },
  },

  created() {
    this.initialize();
    // set number of lines
    this.itemsPerPage = +this.getNumberOfLines() || 10;
  },

  methods: {
    initialize() {
      let self = this;
      axios.get("/web/v1/shops").then(function (response) {
        console.log(response.data.data);
        self.shops = response.data.data;
      });
    },
  },
};
</script>
