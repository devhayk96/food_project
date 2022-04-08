<template>
  <div class="d-flex flex-grow-1 flex-column">
    <v-row class="flex-grow-0" dense>
      <v-col cols="12">
        <v-card>
          <v-card-title>
            Product Subgroup List

            <v-btn
              v-if="userCan('product_subgroups-create')"
              class="mx-2"
              dark
              outlined
              color="primary"
              :to="{
                name: 'product-subgroup-manage',
                query: { action: 'add', id: 0 },
              }"
            >
              <v-icon dark> mdi-plus </v-icon>
              Add Product Subgroup
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
            </template>
            <template #item.is_blocked="{ item }">
              <v-icon v-if="parseInt(item.is_blocked) === 1" class="red--text"
                >mdi-close</v-icon
              >
              <v-icon v-else>mdi-check</v-icon>
            </template>
            <template #item.actions="{ item }">
              <v-btn
                v-if="userCan('product_subgroups-read')"
                icon
                class="text-center"
                :to="{
                  name: 'product-subgroup-manage',
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
import mixin from '../../helpers/mixin';
export default {
  name: "ProductSubgroupList",
  mixins:[mixin],
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
        { text: "Group", value: "product_groups.name" },
        { text: "Active", value: "is_blocked" },
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
        .get("/web/v1/subgroup")
        .then(function (response) {
          console.log(response.data.data);
          _this.dataList = response.data.data;
        })
        .catch(function (resp) {
          console.log(resp);
        });
    },
  },
};
</script>
