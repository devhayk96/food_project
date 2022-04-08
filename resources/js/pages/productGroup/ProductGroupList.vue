<template>
  <div class="d-flex flex-grow-1 flex-column">
    <v-row class="flex-grow-0" dense>
      <v-col cols="12">
        <v-card>
          <v-card-title>
            Product Group List
            <v-btn
              v-if="userCan('product_groups-create')"
              class="mx-2"
              dark
              outlined
              color="primary"
              :to="{
                name: 'product-group-manage',
                query: { action: 'add', id: 0 },
              }"
            >
              <v-icon dark> mdi-plus </v-icon>
              Add Product Group
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
            <template #item.image="{ item }">
              <v-img max-height="60" max-width="100" :src="item.image"></v-img>
              <!--                           <v-img :src='item.image' alt="cat-img" />-->
            </template>
            <template #item.is_blocked="{ item }">
              <v-icon v-if="parseInt(item.is_blocked) === 1" class="red--text"
                >mdi-close</v-icon
              >
              <v-icon v-else>mdi-check</v-icon>
            </template>
            <template #item.actions="{ item }">
              <v-btn
                v-if="userCan('product_groups-read')"
                icon
                class="text-center"
                :to="{
                  name: 'product-group-manage',
                  query: { action: 'edit', id: item.number },
                }"
              >
                <v-icon>mdi-open-in-new</v-icon>
              </v-btn>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import mixin from "../../helpers/mixin";

export default {
  name: "ProductGroupList",
  mixins: [mixin],
  props: {
    name: {
      type: String,
      default: "",
    },
  },
  components: {},
  data() {
    return {
      search: "",
      headers: [
        {
          text: "ID",
          align: "start",
          value: "id",
        },
        { text: "Number", value: "number" },
        { text: "Name", value: "name" },
        { text: "Image", value: "image" },
        { text: "Description", value: "description" },
        { text: "Kitchen1", value: "kitchen_1_id" },
        { text: "Kitchen2", value: "kitchen_2_id" },
        { text: "Kitchen3", value: "kitchen_3_id" },
        { text: "Active", value: "is_blocked" },
        { text: "Food", value: "is_food" },
        { text: "Drink", value: "is_drink" },
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

  mounted() {
    this.fetchList();
    // set number of lines
    this.itemsPerPage = +this.getNumberOfLines() || 10;
  },

  beforeDestroy() {},

  methods: {
    fetchList: function () {
      const _this = this;
      axios
        .get("/web/v1/group")
        .then(function (response) {
          console.log(response);
          _this.dataList = response.data.data;
        })
        .catch(function (resp) {
          console.log(resp);
        });
    },
  },
};
</script>
